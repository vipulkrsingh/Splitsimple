<?php
require_once 'dbconfig.php';

$gid = $_POST['groupid'];
$uid = $_POST['userid'];
$amount = $_POST['amount'];
$comment = $_POST['comment'];
$credit = $_POST['credit'];
$debit = $_POST['debit'];

$query = sprintf("INSERT INTO tr VALUES (null,%s,%s,'%s',%s,0,now())",
					mysql_real_escape_string(htmlspecialchars(urldecode($credit))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($comment))),
					mysql_real_escape_string(htmlspecialchars(urldecode($amount)))
					);
 $result = mysql_query($query,$conn);
 $trid = mysql_insert_id();
 $splitAmount = -1*round($amount/sizeof($debit),2);
 $ownerSplit = $amount;
 if($result and $trid != null){
 foreach ($debit as $value) {
 
 if($value == $uid) $apVal = 1;
 else $apVal = 0;
 
if($value != $credit){ 
 $query = sprintf("INSERT INTO tm VALUES (null,%s,%s,%s,%s,%s)",
					$trid,
					mysql_real_escape_string(htmlspecialchars(urldecode($value))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($splitAmount))),
					mysql_real_escape_string(htmlspecialchars(urldecode($apVal)))
					);
 $result1 = mysql_query($query,$conn);
 }
 else{
 $ownerSplit = $amount + $splitAmount;;
 }
 
 }
 
 if($credit == $uid) $apVal = 1;
 else $apVal = 0;
 
  $query = sprintf("INSERT INTO tm VALUES (null,%s,%s,%s,%s,%s)",
					$trid,
					mysql_real_escape_string(htmlspecialchars(urldecode($credit))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($ownerSplit))),
					mysql_real_escape_string(htmlspecialchars(urldecode($apVal)))
					);
 $result1 = mysql_query($query,$conn);

 }
 
echo $trid;
 
?>