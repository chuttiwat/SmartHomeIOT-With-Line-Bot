<?php


require_once("Config.php");

//คำสั่งอ่านค่า
$GeneralCommand =	$_GET["GenCommand"];

// หลอดไฟห้องนอน
$LEDCommand1 	=	$_GET["LEDCommand1"];
$LEDColor1 		=	$_GET["LEDColor1"];
$LEDValue1	 	= 	$_GET["LEDValue1"];

// หลอดไฟห้องครัว
$LEDCommand2 	=	$_GET["LEDCommand2"];
$LEDColor2 		=	$_GET["LEDColor2"];
$LEDValue2	 	= 	$_GET["LEDValue2"];

// ตรวจจับแก๊ส
$GasCommand		= 	$_GET["GasCommand"];
$GasValue		= 	$_GET["GasValue"];

// ตรวจจับการเคลื่อนไหว
$MotionCommand 	= 	$_GET["MotionCommand"];

//ตรวจจับอุณหภูมิ
$TEMPCommand 	=	$_GET["TEMPCommand"];
$TEMPValue	 	= 	$_GET["TEMPValue"];

// อุปกรณ์ที่เชื่อมต่อ
$Device			=	$_GET["Device"];

if($GeneralCommand != Null)
{
	if($TEMPValue < 10)
	{
		$sql = "UPDATE IOTBoard SET Led_Value1 = '$LEDValue1', Led_Value2 = '$LEDValue2' WHERE Id = 1;";
	}else{
		$sql = "UPDATE IOTBoard SET Temp = $TEMPValue, `Led_Value1` = '$LEDValue1', `Led_Value2` = '$LEDValue2' WHERE Id = 1;";
	}
	$result = mysql_query($sql);
	$sql = "SELECT * FROM `IOTBoard`";
	$result = mysql_query($sql);
	$resultfetch = mysql_fetch_array($result,MYSQL_ASSOC);
	echo json_encode($resultfetch);	
}
?>