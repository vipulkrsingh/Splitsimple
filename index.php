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

  } catch (FacebookApiException $e) {
    error_log($e);
  }
}

// login or logout url will be needed depending on current user state.
if ($me) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}

?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
	<script type='text/javascript' src='http://code.jquery.com/jquery-1.4.4.min.js'></script>
	<script type='text/javascript' src='../js/timeago.js'></script>
	<script type='text/javascript' src='../js/main.js'></script>
	<title>php-sdk</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
	  
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
		label {
    padding-left: 1px;
    text-indent: -19px;
	
}
.chkbox {
    width: 13px;
    height: 13px;
    padding: 0;
    margin:0;
    vertical-align: bottom;
    position: relative;
    top: 0px;
    *overflow: hidden;
}
	  }
    </style>
  </head>
  <body style="font-family: 'lucida grande', tahoma, verdana, arial, sans-serif;font-size: 11px;text-align: left;">
    <!--
      We use the JS SDK to provide a richer user experience. For more info,
      look here: http://github.com/facebook/connect-js
    -->
    <div id="fb-root"></div>
    <script>
	
      window.fbAsyncInit = function() {
        FB.init({
          appId   : '<?php echo $facebook->getAppId(); ?>',
          session : <?php echo json_encode($session); ?>, // don't refetch the session when PHP already has it
          status  : true, // check login status
          cookie  : true, // enable cookies to allow the server to access the session
          xfbml   : true // parse XFBML
        });
		
		FB.Canvas.setAutoResize();
        // whenever the user logs in, we refresh the page
        FB.Event.subscribe('auth.login', function() {
          window.location.reload();
        });
      };
	    
      (function() {
        var e = document.createElement('script');
        e.src = document.location.protocol + '//connect.facebook.net/en_US/all.js';
        e.async = true;
        document.getElementById('fb-root').appendChild(e);
      }());
    </script>

    <?php if ($me): 
 

	$query = sprintf("SELECT * FROM user WHERE userid='%s'",
    mysql_real_escape_string($me['id']));

	// Perform Query
	$result = mysql_query($query,$conn);

if (!$result) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}

$group_array = array();
while ($row = mysql_fetch_assoc($result)) {
	foreach($myGroups as $groups){
	 foreach($groups as $group){
		if( $group['id'] == $row['groupId']){
			array_push($group_array,$group[id]);
			$access = 1;
			}
	 }
	}
}

if($access = 1){
	?>
	
	<div class='leftpanel' style='float:left;width:160px'>
    <!--<img src="https://graph.facebook.com/<?php echo $uid; ?>/picture">
	<div style='border-bottom:solid 1px #EEEEEE;margin-top:11px;'></div>
	<h3> Your Group </h3>-->
	<?php $thisGroup = $facebook->api('/'.$group_array[0]); ?>
	<!-- <img src="https://graph.facebook.com/<?php echo $group_array[0]; ?>/picture"> -->
	<h3><a href="<?php echo $thisGroup['link']?>"> <?php echo $thisGroup['name'] ?></a></h3>
	<div style='border-bottom:solid 1px #EEEEEE;margin-top:11px;'></div>
	<h3> Members </h3>
	<?php $thisGroupMembers = $facebook->api('/'.$group_array[0].'/members'); 
	foreach($thisGroupMembers as $members)
		foreach($members as $member)
		{
			?>
			<a href="http://www.facebook.com/profile.php?id=<?php echo $member[id]?>" target="_new"> <img src="https://graph.facebook.com/<?php echo $member[id]; ?>/picture">  </a>
			<div style="float:right;width:102px;font-family: 'lucida grande', tahoma, verdana, arial, sans-serif;font-size: 11px;text-align: left;color: #3B5998;cursor: pointer;font-weight:bold;"> 
			<div style='float:left;width:90%'>
			<?php echo $member[name]; ?>
			</div>
			<?php
			$query = sprintf("SELECT sum(tm.amount) as amt FROM tm LEFT JOIN tr ON tr.id = tm.trId WHERE tr.approved=1 and tm.groupId = %s and tm.userId = %s group by tm.userId ",
				mysql_real_escape_string($group_array[0]),
				mysql_real_escape_string($member[id]));
			$resulttotal = mysql_query($query,$conn);
			if (!$resulttotal) {
				$message  = 'Invalid query: ' . mysql_error() . "\n";
				$message .= 'Whole query: ' . $query;
				die($message);
			}	
			else{
			while ($rowtotal = mysql_fetch_assoc($resulttotal)) {
		
			?>
			<div style='float:left;width:90%;padding-top:2px;color:
			<?php if($rowtotal['amt'] > 0 ) echo '#00aa00'; else echo '#aa0000'; ?>'>
			<?php
			echo $rowtotal['amt'];
			}
			}
			
			?> </div>
			</div>
			<?php
		}
?>
</div>
<div class='rightpanel' style='float:left;width:70%;border:solid #bbbbbb 1px;'>
<div style='float:right;text-align:left;width:100%;background-color:#e11c30;border-bottom:solid 4px #fa3160;'> 
<div style='text-align:center;padding:5px;background-color:none;font-weight: bold;color:#ffffff'> Pending Approval </div> </div>
<?php
$query = sprintf("SELECT * FROM tr WHERE approved=0 and groupid = %s order by id asc",
    mysql_real_escape_string($group_array[0]));
$resulttr = mysql_query($query,$conn);
if (!$resulttr) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}	
else{
while ($rowtr = mysql_fetch_assoc($resulttr)) {
$addedby = $facebook->api('/'.$rowtr['userid']);
//echo $rowtr['userid'].print_r($addedby);
?>

<div style='float: left;width:95%;margin:2.5%;border-bottom:solid 1px #cccccc;padding-bottom:2.5%;'>
<a href="http://www.facebook.com/profile.php?id=<?php echo $rowtr['userid']?>" target="_new"> <img src="https://graph.facebook.com/<?php echo $rowtr['userid']; ?>/picture">  </a>
<div style='float: right;text-align:left;width:89%;'>
<div style='float: left;text-align:left;width:100%;height:auto;padding-left:10px;padding-bottom:13px'>
<a style='color:#3B5998;font-weight:bold;'href="http://www.facebook.com/profile.php?id=<?php echo $rowtr['userid']?>" target="_new"><?php echo $addedby['name']; ?></a>
<?php echo "<span style='color:#999'> paid Rs </span> ".$rowtr['amount']." <span style='color:#999'> for </span> ".$rowtr['comment'];?>
<?php if($rowtr['userid'] == $me['id']){ ?>
<div class='deltr' id='<?php echo $rowtr['id']; ?>' style = 'float: right;text-align:left;width:17px;cursor: pointer;color:#aaa;'> 
X
</div>  
<?php } ?>

</div>
<?php
//echo print_r($rowtr);
$query = sprintf("SELECT * FROM tm WHERE trId='%s' order by userId limit 10",
    mysql_real_escape_string($rowtr['id']));
$resulttm = mysql_query($query,$conn);
if (!$resulttm) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}
else{
$approveFlag = 0;
while ($rowtm = mysql_fetch_assoc($resulttm)) {
$query = sprintf("SELECT sum(amount) as amt FROM tm WHERE approved=1 and groupId = %s and userId = %s and trId <= %s group by userId ",
			mysql_real_escape_string($rowtm['groupId']),
			mysql_real_escape_string($rowtm['userId']),
			mysql_real_escape_string($rowtr['id']));
$resulttotal1 = mysql_query($query,$conn);
?>
<div style='float:left;width:100px;border-right:solid 1px #eeeeee;margin-right:5px;padding-left:10px;text-align:left;'>
<a href="http://www.facebook.com/profile.php?id=<?php echo $rowtm['userId']?>" target="_new"> <img src="https://graph.facebook.com/<?php echo $rowtm['userId']; ?>/picture" width='25px' height='25px'>  </a>
<div style='float:right;padding-right:5px;padding-left:2px;text-align:left;width:60%;'>
<div style='float:right;padding-right:5px;text-align:left;width:100%;color:
<?php if($rowtm['amount'] > 0 ) echo '#00aa00'; else echo '#aa0000'; ?>'>
<?php echo $rowtm['amount']; 
while ($rowtotal = mysql_fetch_assoc($resulttotal1)){
?></div><div style='float:right;padding-right:5px;text-align:left;width:100%;color:
<?php
if($rowtm['approved'] != 1){
if($rowtm['userId'] == $me['id'] /*and $rowtr['userid'] != $me['id']*/){echo "#4469de'><span style='display:none;' id='approveval'>".$rowtm['trId'].",".$rowtm['userId'].",".$rowtm['groupId']."</span><span class='approve' name='".$rowtm['trId'].",".$rowtm['userId'].",".$rowtm['groupId']."' style='cursor:pointer;'>Approve!</span>";$approveFlag = 1;}
if($rowtm['userId'] != $me['id'] /*and $rowtm['approved'] != 1*/){echo "#e11c30'>Pending.";}
}
else {echo "#06a117'>Approved.";}
//if($rowtm['userId'] == $me['id'] and $rowtr['userid'] != $me['id'] and $rowtm['approved'] != 1) {echo "Approve.";$approveFlag = 1;}
//echo $rowtotal['amt'];
?></div><?php
}
?>

</div>
</div>
<?php
}
}
?></div>
<div style='float:left;padding-top:5px;padding-left:65px;color:#999999;'><abbr class="timeago" title="<?php echo str_replace(' ','T',$rowtr['timestamp']).'Z';?>"></abbr> </div>
</div><?php
}
}
?>
<div style='float:right;text-align:left;margin-bottom:10px;width:100%;background-color:#4469de;border-bottom:solid 4px #6499fe;'> 
<div style='text-align:center;padding:5px;background-color:none;font-weight: bold;color:#ffffff'> Create New Split </div> </div>
    
<div class='toppanel'  style='  background-color: #ffffff; border:1px solid #d4dae8;color: #333333;padding: 10px; font-size: 13px; font-weight: bold;'> 
<div style='float:left;width:95%; background-color: #ecefff; border:1px solid #d4dae8;color: #333333;border-bottom:none;padding: 10px;font-size: 12px;font-weight: bold;'>
	<form id='inputpanel'>
	<input type="hidden" name='groupid' value='<?php echo $group_array[0]?>'>
	<input type="hidden" name='userid' value='<?php echo $me['id']?>'>
	
	Amount : <input type='text' name='amount' id='amount'>
	Paid By : <SELECT  name='credit'>
	
	<?php
	foreach($thisGroupMembers as $members)
		foreach($members as $member)
		{
			?>
			 <option value='<?php echo $member[id]?>' <?php if($me[id] == $member[id]) echo 'SELECTED' ?>><?php echo $member[name]?>
			<?php
		}
	?>
	</select>
	</div>
	<div style='float:left;width:95%; background-color: #ecefff; border:1px solid #d4dae8;color: #333333;border-bottom:none;
	padding: 10px;  
    font-size: 12px;  
    font-weight: bold;'><div style='padding-bottom:10px'>Shared By : <span id='debittoggle' style='cursor:pointer;text-align:center;color:#aaa;font-size: 9px; text-decoration: underline'> ( Toggle CheckBox ) </span> </div>
	<?php
	foreach($thisGroupMembers as $members)
		foreach($members as $member)
		{
			?>
			<div style='float:left;padding-left:0px;padding-bottom:5px;margin-bottom:5px;width:150px'><input class='chkbox' type='checkbox' name=debit[] value='<?php echo $member[id]?>'><label><?php echo $member[name]?></label></div>
			<?php
		}
	?>
	</div>
	<div style=' float:left;width:95%; background-color: #ecefff; border:1px solid #d4dae8;color: #333333;  
	padding: 10px;  
    font-size: 12px;  
    font-weight: bold;'>Add Comment (please be specific) : <textarea id='desc' name='comment' style='width:100%'></textarea></div>
	<div> <input type="button" id="submit" value="Add Expense"> </div>
	</form>
	</div>

<div style='float:right;text-align:left;margin-bottom:5px;width:100%;background-color:#06a117;border-bottom:solid 4px #26c137;'> 
<div style='text-align:center;padding:5px;background-color:none;font-weight: bold;color:#ffffff'> Last 10 Approved Splits </div> </div>
    
<?php
$query = sprintf("SELECT * FROM tr WHERE approved=1 and groupid = %s order by id desc limit 10",
    mysql_real_escape_string($group_array[0]));
$resulttr = mysql_query($query,$conn);
if (!$resulttr) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}	
else{
while ($rowtr = mysql_fetch_assoc($resulttr)) {
$addedby = $facebook->api('/'.$rowtr['userid']);
//echo $rowtr['userid'].print_r($addedby);
?>

<div style='float: left;width:95%;margin:2.5%;border-bottom:solid 1px #cccccc;padding-bottom:2.5%;'>
<a href="http://www.facebook.com/profile.php?id=<?php echo $rowtr['userid']?>" target="_new"> <img src="https://graph.facebook.com/<?php echo $rowtr['userid']; ?>/picture">  </a>
<div style='float: right;text-align:left;width:89%;'>
<div style='float: left;text-align:left;width:100%;height:auto;padding-left:10px;padding-bottom:13px'>
<a style='color:#3B5998;font-weight:bold;'href="http://www.facebook.com/profile.php?id=<?php echo $rowtr['userid']?>" target="_new"><?php echo $addedby['name']; ?></a>
<?php echo "<span style='color:#999'> paid Rs </span> ".$rowtr['amount']." <span style='color:#999'> for </span> ".$rowtr['comment'];?>
</div>
<?php
//echo print_r($rowtr);
$query = sprintf("SELECT * FROM tm WHERE approved=1 and trId='%s' order by userId limit 10",
    mysql_real_escape_string($rowtr['id']));
$resulttm = mysql_query($query,$conn);
if (!$resulttm) {
    $message  = 'Invalid query: ' . mysql_error() . "\n";
    $message .= 'Whole query: ' . $query;
    die($message);
}
else{
while ($rowtm = mysql_fetch_assoc($resulttm)) {
$query = sprintf("SELECT sum(tm.amount) as amt FROM tm LEFT JOIN tr ON tr.id = tm.trId WHERE tr.approved=1 and tm.groupId = %s and tm.userId = %s and tm.trId <= %s group by tm.userId ",
			mysql_real_escape_string($rowtm['groupId']),
			mysql_real_escape_string($rowtm['userId']),
			mysql_real_escape_string($rowtr['id']));
$resulttotal1 = mysql_query($query,$conn);
//echo $query;
?>
<div style='float:left;width:100px;border-bottom:solid 1px #eeeeee;margin-right:5px;margin-left:10px;text-align:left;margin-bottom:4px;'>
<a href="http://www.facebook.com/profile.php?id=<?php echo $rowtm['userId']?>" target="_new"> <img src="https://graph.facebook.com/<?php echo $rowtm['userId']; ?>/picture" width='25px' height='25px'>  </a>
<div style='float:right;padding-right:5px;padding-left:2px;text-align:left;width:60%;'>
<div style='float:right;padding-right:5px;text-align:left;width:100%;color:
<?php if($rowtm['amount'] > 0 ) echo '#00aa00'; else echo '#aa0000'; ?>'>
<?php echo $rowtm['amount']; 
while ($rowtotal = mysql_fetch_assoc($resulttotal1)){
?></div><div style='float:right;padding-right:5px;text-align:left;width:100%;color:
<?php if($rowtotal['amt'] > 0 ) echo '#00aa00'; else echo '#aa0000'; ?>'>
<?php
echo $rowtotal['amt'];
?></div><?php
}
?>

</div>
</div>
<?php
}
}
?></div>
<div style='float:left;padding-top:5px;padding-left:65px;color:#999999;'><abbr class="timeago" title="<?php echo str_replace(' ','T',$rowtr['timestamp']).'Z';?>"></abbr> </div>
</div><?php
}
}
?>
</div>

<?php
}
else{
?> <h3> Coming Soon !!</h3><?php
}

endif ?>

 </body>
</html>
