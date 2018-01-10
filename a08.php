<?php
/*
20130627
每10人才開始走正常機率
使用設置
奬品起始量大於99999時將該商品當成無限送商品-無限送商品的中奬率為 設20萬＝200
*/
header("Content-Type:text/html;charset=utf-8");//全程式總編碼指定
date_default_timezone_set('Asia/Taipei');//設定系統時區
$_file = basename(__FILE__);		//自行取得本程式名稱
//獎品和中獎機率設置只能填整數
$prize_arr = array( 
    '0' => array('id'=>1,'prize'=>'第1獎 禮金10萬','v'=>1), 
    '1' => array('id'=>2,'prize'=>'第2獎 禮金5萬','v'=>1), 
    '2' => array('id'=>3,'prize'=>'第3獎 禮金3萬','v'=>1), 
    '3' => array('id'=>4,'prize'=>'第4獎 禮金1萬','v'=>2), 
    '4' => array('id'=>5,'prize'=>'第5獎 禮金5千','v'=>5), 
    '5' => array('id'=>6,'prize'=>'第6獎 禮金1千','v'=>10),
	'6' => array('id'=>7,'prize'=>'第7獎 禮金500','v'=>20),
	'7' => array('id'=>8,'prize'=>'第8獎 禮金200','v'=>50),
	'8' => array('id'=>9,'prize'=>'銀幣100萬','v'=>1),
	'9' => array('id'=>10,'prize'=>'銀幣80萬	','v'=>1),
	'10' => array('id'=>11,'prize'=>'銀幣60萬	','v'=>1),
	'11' => array('id'=>12,'prize'=>'銀幣50萬','v'=>2),
	'12' => array('id'=>13,'prize'=>'銀幣30萬	','v'=>3),
	'13' => array('id'=>14,'prize'=>'銀幣20萬	','v'=>5),
	'14' => array('id'=>15,'prize'=>'銀幣10萬	','v'=>10),
	'15' => array('id'=>16,'prize'=>'銀幣5萬','v'=>20),
	'16' => array('id'=>17,'prize'=>'銀幣3萬','v'=>30),
	'17' => array('id'=>18,'prize'=>'銀幣1萬','v'=>40),
	'18' => array('id'=>19,'prize'=>'銀幣5千','v'=>100),
	'19' => array('id'=>20,'prize'=>'橙色寶藏','v'=>150),
	'20' => array('id'=>21,'prize'=>'白色寶藏','v'=>200000),
	'21' => array('id'=>22,'prize'=>'銘謝惠顧','v'=>120000)
);

 
//$prize_arr陣列顯示用而已其真正計算機率表為$arr

foreach ($prize_arr as $key => $val) { 
    $arr[$val['id']] = $val['v'];//為了算總和
	
	if($val['v']>=99999){
		
		//設置無限數量的獎項與其得獎率
		$vv=floor($val['v']/1000);
		$u[$val['id']]=$val['v'];
		$arr[$val['id']]=$vv;//真正計算機率的表
	}
}

/*
print_r($u);
print_r($prize_arr);
exit;
*/

$global_arr=$arr;//最初中獎率

	$global_arrb=$global_arr;//去掉無限獎項後的中獎率表
	if(is_array($u)){		
		foreach($u as $key =>$v){
		unset($global_arrb[$key]);
		}
	}

//	print_r($global_arrb);
//	exit;
function get_rand($proArr) { 
    $result = '';//選中的值
    //概率數組的總概率精度
	if(is_array($proArr)){ 
    $proSum = array_sum($proArr);//計算陣列值的總數
 	//print_r($proArr);exit;
    //概率數組循環 
    foreach ($proArr as $key => $proCur) {
		//$proCur中獎率
        $randNum = mt_rand(1, $proSum);//1~1000挑1 
        if ($randNum <= $proCur) { 
            $result = $key;//小於設定的中獎率就成立
            break; 
        } else { 
			//不中同時把該項目中獎率從總中獎率減掉
            $proSum -= $proCur;//中獎率總和減該項目中獎率
        } 
    }
    unset ($proArr);
	}
    return $result; 
} 



/*
$rid = get_rand($arr);//根據概率獲取獎項id
print_r($rid);//返回中獎id號
exit;
*/

/*
$rid = get_rand($arr);//根據概率獲取獎項id
$res['yes'] = $prize_arr[$rid-1]['prize']; //中獎項 
unset($prize_arr[$rid-1]); //將中獎項從數組中剔除，剩下未中獎項 
shuffle($prize_arr); //打亂數組順序 
for($i=0;$i<count($prize_arr);$i++){ 
    $pr[] = $prize_arr[$i]['prize']; 
} 
$res['no'] = $pr; 

$u=array(21=>10,22=>10);
print_r($u);
print_r($arr);
exit;
*/
//echo json_encode($res); 



//該獎項抽完就從獎項表中移除  參數1=中獎機率陣列，參數2=中獎結果陣列 
function checking($arr,$sum=array()){
	global $global_arr,$global_arrb,$u;//原始中奬率表，去掉無限獎品後的中獎機率表，無限數量的獎品項陣列，
	//以下都以獎項id為統計
	//$sumb=$sum;
	foreach($u as $key =>$v){
	unset($sum[$key]);//去掉無限獎項後的得獎數量表
	}
	
	//如何判斷-數量有限獎品以被抽完
	if(array_sum($sum) >= array_sum($global_arrb)){
		//echo "所有獎項以抽完";exit;
	return $u;//返回預設
	}
	$b="";
	//用最原始陣列的去跑
	foreach($global_arr as $key =>$v){
		if(empty($v)){
		}else{
			//如果設定數數沒大於設定數
			if(empty($sum[$key])){
				$b[$key]=$global_arr[$key];//無值用原機率表
			}else{
				//有值用新機率表
				if($v > $sum[$key]){
				$cc=$global_arr[$key]-$sum[$key];//中一個少一個機率
				$b[$key]=$cc;//新機率表
				}
			}
		}
	}
	 return $b;

}


if(isset($_POST['end']))	
$end=$_POST['end'];
else
$end=3000;//預設抽獎幾次


if(empty($_POST['a_or_b']))	
$a_or_b=0;
else
$a_or_b=1;//是否限制獎品數量

if(isset($_POST['hiddenField123']))
$hiddenField123=$_POST['hiddenField123'];
 else 
 $hiddenField123="";

if($hiddenField123=='yes50' && $end)
$yes=1;


if(isset($_POST['people']))	
$people=$_POST['people'];
else
$people=10;//吞幣率設置


//print_r($_POST);
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>模擬抽獎1000次</title>
<style type="text/css">
body {
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
	background-color: #FFFBB5;
}
div {
	margin: 0px;
	padding: 0px;
}
</style>
</head>

<body>
<div align="center">
<form action="<?=$_file?>" method="post" enctype="application/x-www-form-urlencoded" target="_self" id="aab">
  請輸入你要模擬的抽獎
  <input name="end" type="text" id="end" value="<?=$end?>">
  <input name="hiddenField123" type="hidden" id="hiddenField" value="yes50">
  次數<br><?php
  $checked=($a_or_b)?"":'checked="CHECKED"';
  ?>
  <input name="a_or_b" type="radio" id="aorb2" value="0" <?=$checked?> >
  獎品數量無限制
  
  <?php
  $checked=($a_or_b)?'checked="CHECKED"':"";
  ?>
  <input type="radio" name="a_or_b" id="aorb" value="1" <?=$checked?> >
  <label for="a_or_b" ></label>
  獎品數量有限制<br>
  <label for=""></label>
  吞幣率設置
  <input name="people" type="text" id="people" value="<?=$people?>" size="5" maxlength="5">
  次不中之後，才開始走正常派獎機率<br>
  <input name="送出" type="submit" value="送出">
  
  </form>
<HR>
<p>1.獎品數量即為中獎機率<br>
  2.但獎品數量設置大於99999時表示獎品數量無限，中該奬機率為設置值除1000。<br>
  3.大獎送完後，將只送數量無限商品
</p>
<HR>
<table border="1" align="center" cellspacing="0">
  <tr>
    <th bgcolor="#66CC00">獎項設置一覽表</th>
    <th bgcolor="#66CC00">實抽統計結果</th>
    </tr>
  <tr>
    <td valign="top"><table border="1" cellspacing="0">
      <tr>
        <td align="center" bgcolor="#FFFF66">&nbsp;</td>
        <td align="center">獎項</td>
        <td align="center">獎品數量</td>
      </tr>
<?php
$sum2=0;
$AA=array();
$top1=0;
$top2=0;//獎品第幾次發完的統計
$xxx=0;//獎品第幾次發完的統計用
$sum2=0;
foreach($arr as $key =>$v){
	$sum[$key]=0;//先建一個-獎項id-統計數空陣列
}
//print_r($sum);
foreach($prize_arr as $key =>$v){
	$sum2 += $v['v'];
?>
      <tr>
        <td align="center" bgcolor="#FFFF66"><?=$v['id']?></td>
        <td align="center"><?=$v['prize']?></td>
        <td align="center"><?=$v['v']?></td>
      </tr>
      <?php
}
?>
      <tr>
        <td align="center" bgcolor="#FFFF66">&nbsp;</td>
        <td align="center">數量合計</td>
        <td align="center"><?=$sum2?></td>
      </tr>
    </table></td>
    <td valign="top">  
<?php
if(!empty($yes)){
	
$no=0;//沒中獎的累計次數
	for($i=0;$i<$end;$i++){
			if($a_or_b){
			//該獎項抽完就從獎項表中移除
				$arr=$u;//小獎給他抽
				//沒人抽中大獎的累計次數
				if($no>=$people){
				$arr=checking($arr,$sum);//正常中獎機率
				}
			}	
				$rid = get_rand($arr);//根據概率獲取獎項id
				if($rid<=20){
				//小於等於20的id表示有中大獎
				$no=0;//歸零
				}else{
				$no++;//沒中獎的次數累計
				}
				
	//獎品第幾次發完的統計
	if(empty($xxx))
	{
		$sum7=$sum;
		foreach($u as $key =>$v){
		unset($sum7[$key]);//去掉無限獎項後的得獎數量表
		}
			if(array_sum($sum7) == array_sum($global_arrb)){
			$top2=$i;
			$xxx=1;//結速統計
		}
	}
	//獎品第幾次發完的統計END
	
		$rid2=$rid-1;
		$AA[$i]['prize']=$prize_arr[$rid2]['prize']; //中獎項
		$AA[$i]['id']=$prize_arr[$rid2]['id'];
		$sum[$rid]++;//以獎項id記錄
	}
	echo '<table border="1" cellspacing="0">';
    echo "<tr>";
    echo "<td>獎品</td>";
    echo "<td>抽到總數統計</td>";
		for($ii=0;$ii<count($prize_arr);$ii++){
		echo "</tr>";
      	echo "<tr>";
        echo "<td>".$prize_arr[$ii]['prize']."</td>";
        echo "<td>".$sum[$prize_arr[$ii]['id']]."</td>";
      	echo "</tr>";
		}
	echo "<tr>";
	echo "<td>總計</td>";
    echo "<td>".array_sum($sum)."</td>";
    echo "</tr>";
	echo "</table>";
}
?>
    </td>
    </tr>
</table>
<hr>
<?php
//第1大獎的ID
	for($i=0;$i<count($AA);$i++){
	if($AA[$i]['id']==4)
	$top1=$i+1;	
	}
?>
第1大獎在第 <?=$top1?> 次抽出<br>
全部大獎項在第 <?=$top2?> 次發完
<hr>
實抽結果詳細列表<br>
<textarea name="abc" cols="50" rows="20">
<?php
if(!empty($yes)){
		for($i=0;$i<count($AA);$i++){
		echo "第".($i+1)."抽，抽到獎品為".$AA[$i]['prize'];
		echo "\n";
		}
}
?>  
</textarea>
</div></body></html>