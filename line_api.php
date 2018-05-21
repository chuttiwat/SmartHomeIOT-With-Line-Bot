<?php
require_once("Config.php");

$strAccessToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
 
$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
 
$strUrl = "https://api.line.me/v2/bot/message/reply";



$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";


$UserId = $arrJson['events'][0]['source']['userId'];
if($UserId != null)
{
	$sql = "SELECT Line_Id FROM LineId WHERE Line_Id = '$UserId';";
	$result = mysql_query($sql);
	$row = mysql_num_rows($result); 
	if($row == 1) {
	
	}else {
		$sql = "INSERT INTO LineId (`Id`, `Line_Id`) VALUES (NULL, '$UserId');";
		$result = mysql_query($sql);
	} 
}
 
if($arrJson['events'][0]['message']['text'] == "สวัสดี"){
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "สวัสดี ID คุณคือ ".$arrJson['events'][0]['source']['userId'];
}else if($arrJson['events'][0]['message']['text'] == "ชื่ออะไร"){
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ฉันยังไม่มีชื่อนะ";
}else if($arrJson['events'][0]['message']['text'] == "ทำอะไรได้บ้าง"){
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ฉันสามารถสั่งการทำงานระบบเตือนภัยภายในบ้านได้นะ";
}else if(preg_match("/คำสั่ง/",$arrJson['events'][0]['message']['text'])){
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "คำสั่งทำงานมีดังนี้\n1.เปิดระบบเตือนภัย\n2.ปิดระบบเตือนภัย\n3.เปิดไฟในห้องนอน\n4.ปิดไฟในห้องนอน\n5.เปิดไฟในห้องครัว\n6.ปิดไฟในห้องครัว\n7.เปิดระบบตรวจแก๊ส\n8.ปิดระบบตรวจแก๊ส \n9.เปิดระบบกันขโมย\n10.ปิดระบบกันขโมย\n11.เปิดระบบตรวจอุณหภูมิ\n12.ปิดระบบตรวจอุณหภูมิ\n13เปลียนสีหลอดไฟห้องนอน ชื่อสี\n14.เปลียนสีหลอดไฟห้องครัว ชื่อสี\nTip :: หลอดไฟเปลียนได้หลายสีนะ เช่น สีแดง สีเขียว สีขาว สีส้ม สีม่วง และ สีน้ำเงิน";
}else if(preg_match("/เปิดระบบเตือนภัย/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET `Led1` = '0', `Led2` = '0', `Gas` = '1', `Gas_Value` = '1', `PIR` = '1', `Temp` = '0', `Temp_status` = '1', `Led_Value1` = '0', `Led_Value2` = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $sql = "UPDATE IOTBoard SET enable = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดระบบเตือนภัยทุกระบบในบ้านแล้วครับ!!";
}else if(preg_match("/ปิดระบบเตือนภัย/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET `Led1` = '0', `Led2` = '0', `Gas` = '0', `PIR` = '0', `Temp_status` = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $sql = "UPDATE IOTBoard SET enable = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดระบบเตือนภัยทุกระบบในบ้านแล้วครับ!!";
}else if(preg_match("/เปิดไฟห้องนอน/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1 = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดไฟในห้องนอนให้แล้วนะครับ!!";
}else if(preg_match("/ปิดไฟห้องนอน/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1 = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดไฟในห้องนอนให้แล้วนะครับ!!";
}else if(preg_match("/เปิดไฟห้องครัว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2 = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดไฟในห้องครัวให้แล้วนะครับ!!";
}else if(preg_match("/ปิดไฟห้องครัว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2 = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดไฟในห้องครัวให้แล้วนะครับ!!";
}else if(preg_match("/เปิดระบบตรวจแก๊ส/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Gas = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดระบบตรวจแก๊สให้แล้วนะครับ!!";
}else if(preg_match("/ปิดระบบตรวจแก๊ส/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Gas = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดระบบตรวจแก๊สให้แล้วนะครับ!!";
}else if(preg_match("/เปิดระบบกันขโมย/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET PIR = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดระบบกันขโมยให้แล้วนะครับ!!";
}else if(preg_match("/ปิดระบบกันขโมย/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET PIR = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดระบบกันขโมยให้แล้วนะครับ!!";
}else if(preg_match("/เปิดระบบตรวจอุณหภูมิ/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Temp_status = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปิดระบบตรวจอุณหภูมิให้แล้วนะครับ!!";
}else if(preg_match("/ปิดระบบตรวจอุณหภูมิ/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Temp_status = '0' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ปิดระบบตรวจอุณหภูมิให้แล้วนะครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีแดง/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีแดง ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีเขียว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '2' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีเขียว ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีขาว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '3' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีขาว ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีส้ม/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '4' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีส้ม ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีม่วง/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '5' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีม่วง ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องนอน/",$arrJson['events'][0]['message']['text']) && preg_match("/สีน้ำเงิน/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led1_Color = '6' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องนอนเป็น สีน้ำเงิน ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/แดง/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '1' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีเขียว ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/สีเขียว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '2' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีขาว ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/สีขาว/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '3' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีส้ม ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/สีส้ม/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '4' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีม่วง ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/สีม่วง/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '5' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีน้ำเงิน ให้แล้วครับ!!";
}else if(preg_match("/เปลียนสีไฟห้องครัว/",$arrJson['events'][0]['message']['text']) && preg_match("/สีน้ำเงิน/",$arrJson['events'][0]['message']['text'])){
  $sql = "UPDATE IOTBoard SET Led2_Color = '6' WHERE Id = 1;";
  $result = mysql_query($sql);
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "เปลียนสีหลอดไฟในห้องครัวเป็น สีน้ำเงิน ให้แล้วครับ!!";
}else if(preg_match("/สถานะ/",$arrJson['events'][0]['message']['text'])){
  //====================================================================================
  $sql = "SELECT * FROM `IOTBoard` LIMIT 1";
  $result = mysql_query($sql);
  $resultfetch = mysql_fetch_array($result);
  $Msg = "รายงานสถานะ\n";
  if($resultfetch["enable"] == "1")
  {
	if($resultfetch["Led1"] == "1"){
		$Msg.= "สถานะหลอดไฟห้องนอน :  เปิด\n";
	}else{
		$Msg.= "สถานะหลอดไฟห้องนอน :  ปิด\n";
	}
	$Msg.= "ค่าแสงห้องนอนปัจจุบัน   : ".$resultfetch["Led_Value1"]." Lux\n";
	if($resultfetch["Led2"] == "1"){
		$Msg.= "สถานะหลอดไฟห้องครัว :  เปิด\n";
	}else{
		$Msg.= "สถานะหลอดไฟห้องครัว :  ปิด\n";
	}
	if($resultfetch["Gas"] == "1"){
		$Msg.= "ระบบตรวจจับแก๊ส :  เปิด\n";
	}else{
		$Msg.= "ระบบตรวจจับแก๊ส :  ปิด\n";
	}
	if($resultfetch["PIR"] == "1"){
		$Msg.= "ระบบตรวจจับการเคลื่อนไหว :  เปิด\n";
	}else{
		$Msg.= "ระบบตรวจจับการเคลื่อนไหว :  ปิด\n";
	}
	if($resultfetch["Temp_status"] == "1"){
		$Msg.= "ระบบตรวจจับอุณหภูมิ :  เปิด\n";
	}else{
		$Msg.= "ระบบตรวจจับอุณหภูมิ :  ปิด\n";
	}
	$Msg.= "อุณหภูมิห้องครัวปัจจุบัน : ".$resultfetch["Temp"];
  }else{
		$Msg = "ระบบเตือนภัยถูกปืดการใช้งานอยู่ในขณะนี้ครับ\n";
  }
  //====================================================================================
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = $Msg;
}else{
  $arrPostData = array();
  $arrPostData['replyToken'] = $arrJson['events'][0]['replyToken'];
  $arrPostData['messages'][0]['type'] = "text";
  $arrPostData['messages'][0]['text'] = "ขอโทษด้วย ฉันไม่เข้าใจคำสั่งจริงๆ";
}
 
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,$strUrl);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $arrHeader);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($arrPostData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close ($ch);
 
?>