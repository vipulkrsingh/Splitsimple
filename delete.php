<?php
require_once 'dbconfig.php';


$trid = $_POST['trId'];


$query = sprintf("DELETE FROM tm WHERE trId = %s",
					mysql_real_escape_string(htmlspecialchars(urldecode($trid))));
 $result = mysql_query($query,$conn);
 if($result){
	$query = sprintf("DELETE FROM tr WHERE id = %s",
					mysql_real_escape_string(htmlspecialchars(urldecode($trid))));
   $result2 = mysql_query($query,$conn);
   if($result2){ '{  "result" : 1}';}
   else {echo '{  "result" : 0}';}

 }
 else{ echo '{  "result" : 0}';}
 
?>