<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/functions.php"); ?>

<?php
	//simple logout
	if(isset($_SESSION["admin_id"]))
		$_SESSION["admin_id"] = null;
	if(isset($_SESSION["student_id"]))
		$_SESSION["student_id"] = null;
	$_SESSION["username"] = null;
	redirect_to("index.php");
 ?>

 <?php
	//complete logout - destroy session entirely
	// session_start();
	// $_SESSION = [];
	// if(isset($_COOKIE[session_name()])) {
	// 	setcookie(session_name(), '', time() - 1, '/'); //sets cookie to nothingness
	// }
	// session_destroy();
	// redirect_to("login.php");
  ?>
