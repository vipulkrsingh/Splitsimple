<?php
require_once 'dbconfig.php';


$gid = $_POST['groupId'];
$uid = $_POST['userId'];
$trid = $_POST['trId'];


$query = sprintf("UPDATE tm SET approved=1 WHERE trId = %s and userId = %s and groupId = %s",
					mysql_real_escape_string(htmlspecialchars(urldecode($trid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($uid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))));
 $result = mysql_query($query,$conn);
 if($result){
 $query = sprintf("SELECT count(*) as cnt,sum(approved) as sa from tm WHERE trId = %s and groupId = %s Group by trId",
					mysql_real_escape_string(htmlspecialchars(urldecode($trid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))));
 $result1 = mysql_query($query,$conn);
 $row = mysql_fetch_assoc($result1);
 //echo $query.";";
 if($row['cnt'] === $row['sa']){
	$query = sprintf("UPDATE tr SET approved=1 WHERE id = %s and groupid = %s",
					mysql_real_escape_string(htmlspecialchars(urldecode($trid))),
					mysql_real_escape_string(htmlspecialchars(urldecode($gid))));
   $result2 = mysql_query($query,$conn);

   if($result2){ '{  "result" : 1}';}
   else {echo '{  "result" : 0}';}

 }
 
 }
 else{ echo '{  "result" : 0}';}
 
?>