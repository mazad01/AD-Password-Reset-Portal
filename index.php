<?php

//Active Directory Password Reset Portal with Duo Authentication

error_reporting(0);
include('duo_web.php');
include ('config.php');

//Values should be retrieved from Duo Admin Dashboard
define('AKEY',"");
define('IKEY',"");
define('SKEY',"");
define('HOST',"");

echo "<center><h1>Duo Security + Active Directory Password Reset</h1></center><hr>";

//Step 3: Once secondary authentication has completed you may redirect the user
if(isset($_POST['sig_response'])){//Verify sig response and log in user
  /*
  -Make sure that verifyResponse does not return NULL
  -If it is NOT NULL then it will return a username
  -You can then set any cookies/session data for that username
  -And complete the login/redirect process
  */
  try {
    $resp = Duo::verifyResponse(IKEY, SKEY, AKEY, $_POST['sig_response']);
  }
  catch (Exception $err){
    echo $err->getMessage();
    $resp = NULL;
  }
  if($resp == NULL){
    echo "Not validated!";
  }
  else {//Password Protected content (in this case, we redirect the page to an iframe that contains our PWM app running on Apache Tomcat)
    echo '<center><iframe src="http://localhost:8080/pwm/public/ForgottenPassword" width="800" height="500"><p>Your browser does not support iframes.</p></iframe></center>';
  }	
}

//Step 2: Verify username(email) from Active directory, then generate a sig_request and load up the Duo iframe for secondary authentication
else if(isset($_POST['user'])){
  $server = $config['server'];
  $user = $config['user'];
  $psw = $config['pass'];
  $dn = $config['dn'];
  $search = "mail=".$_POST['user'];                    
  $ds = ldap_connect($server);
  $r = ldap_bind($ds, $user , $psw); 
  $sr = ldap_search($ds, $dn, $search);
  $data = ldap_get_entries($ds, $sr);
  $stringTemp2;    
  for ($i = 0; $i < $data["count"]; $i++){//Checking if email exists in Active Directory
    $stringTemp2 = $data[$i]["mail"][0];
  }
  ldap_close($ds);
  if (empty($stringTemp2)) echo "<script>window.location = 'http://localhost'</script>";
  else if($_POST['user'] == $stringTemp2){ 
    $sig_request = Duo::signRequest(IKEY, SKEY, AKEY, $_POST['user']);
?>
<script src="Duo-Web-v1.bundled.min.js"></script>
<script>
  Duo.init({
    'host': <?php echo "'" . HOST . "'"; ?>,
    'post_action':'index.php',
    'sig_request':<?php echo "'" . $sig_request . "'"; ?>
  });
</script>
<center><iframe id="duo_iframe" width="620" height="500" frameborder="0" allowtransparency="true" style="background: transparent;"></iframe></center>
<?php
  }
}

//Step 1: Simple Form handling for email input
else {
  echo "<form action='index.php' method='post'>";
  echo "<center><h2>Username (Active Directory Email Address)</h2></center><br \>";
  echo "<center><input type='email' name='user' required/></center><br \>";
  echo "<center><input type='submit' value='Submit' /></center>";
  echo "</form>";
}
?>
