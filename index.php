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
	<script type='text/javascript' src="http://ajax.microsoft.com/ajax/jquery.templates/beta1/jquery.tmpl.min.js"></script>
	<script type='text/javascript' src='../js/timeago.js'></script>
	<script type='text/javascript' src='../js/main.js'></script>
	<link rel="stylesheet" type="text/css" href="../css/main.css">
	<title>Splitsimple</title>
  </head>
  <body>
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
?>
 </body>
</html>
