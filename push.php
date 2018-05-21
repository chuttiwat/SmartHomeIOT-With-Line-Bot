<?php

require_once("Config.php");
$Msg 		= $_GET["Message"];

$strAccessToken = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";

$strUrl = "https://api.line.me/v2/bot/message/push";

$arrHeader = array();
$arrHeader[] = "Content-Type: application/json";
$arrHeader[] = "Authorization: Bearer {$strAccessToken}";

$arrPostData = array();
$arrPostData['to'] = "";
$arrPostData['messages'][0]['type'] = "text";
$arrPostData['messages'][0]['text'] = $Msg;

$sql = "SELECT * FROM `LineId`";
$result1 = mysql_query($sql);
while($resultfetch = mysql_fetch_array($result1))
{
	$arrPostData['to'] = $resultfetch["Line_Id"];
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
}

?>