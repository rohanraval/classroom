<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>

 <?php
 	$username = "";
 	if(isset($_POST["submit"])) {
 		//Process this form

 		//validations
 		$required_fields = ["username", "password"];
 		validate_presences($required_fields);

 		if(empty($errors)) {

			$username = $_POST["username"];
			$password = $_POST["password"];

			//Attempt login
			if($_GET["admin"] == 1) {
				$found_admin = attempt_admin_login($username, $password);
			} elseif($_GET["admin"] == 0) {
				$found_student = attempt_student_login($username, $password);
			} else {
				redirect_to("index.php");
			}

			if(isset($found_admin) && $found_admin == true) { //Success!
				$_SESSION["admin_id"] = $found_admin["id"];
				$_SESSION["username"] = $found_admin["username"];
				redirect_to("admin.php");
			} elseif(isset($found_student) && $found_student == true) { //Success!
				$_SESSION["student_id"] = $found_student["id"];
				$_SESSION["username"] = $found_student["username"];
				redirect_to("student.php?student=" . urlencode($found_student["id"]));
			} else { //Failure!
				$_SESSION["message"] = "Username/password not found!";
			}
 		}
 	} else {
 		//This is probably a GET request
 	}
  ?>

<?php include("../includes/layouts/header.php"); ?>

<div id="main">
	<div id="navigation">
		<br>
		<a href="index.php">&laquo; Home Page</a> <br>
	</div>
	<div id="page">
		<?php echo message(); ?>
		<?php echo form_errors($errors); ?>
		<h2><?php echo $_GET["admin"] == 1 ? "Admin" : "Student"; ?> Login</h2>
		<form action="login.php?admin=<?php echo $_GET["admin"]; ?>" method="post">
			<p>Username:
				<input type="text" name="username" value="<?php echo htmlentities($username); ?>">
			</p>
			<p>Password:
				<input type="password" name="password" value="">
			</p>
			<input type="submit" name="submit" value="Sign in">
		</form>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
