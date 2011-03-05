<?php
require_once 'config.php';
require_once 'dbconfig.php';
$session = $facebook->getSession();
$access = 0;
if ($session) {
  try {
    $uid = $facebook->getUser();
    $me = $facebook->api('/me');
    $myGroups = $facebook->api('/me/groups');
	$thisgroup = $_GET['group']; 
	$bd = $_GET['bd']; 
	$ed = $_GET['ed']; 


$select = "SELECT * FROM tr LEFT JOIN tm ON tr.id = tm.trid where tm.groupid = ".$thisgroup." and timestamp between '".$bd."' and '".$ed."'";;
$export = mysql_query($select); 
$fields = mysql_num_fields($export); 

for ($i = 0; $i < $fields; $i++) {
	$csv_output .= mysql_field_name($export, $i) . "\t";
}

while($row = mysql_fetch_row($export)) {
	$line = '';
	foreach($row as $value) {
		if ((!isset($value)) OR ($value == "")) {
			$value = "\t"; 
		} else {
			$value = str_replace('"', '""', $value);
			$value = '"' . $value . '"' . "\t"; 
		}
		$line .= $value;
	}
	$data .= trim($line)."\n";
}
$data = str_replace("\r","",$data);

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=database_dump.xls");
header("Pragma: no-cache");
header("Expires: 0");
print $csv_output."\n".$data;
exit;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Download MySQL Table Code</title>
</head>
<body>

</body>
</html>
<?php
  } catch (FacebookApiException $e) {
    error_log($e);
  }
}
?>
