<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php
	if(isset($_POST["submit"])) {
		//Process this form

		//validations
		$password_match = confirm_password($_POST["password"], $_POST["password_confirm"]);
		$required_fields = ["first_name", "last_name", "username", "password"];
		validate_presences($required_fields);
		$fields_with_max_lengths = ["username" => 50];
		validate_max_lengths($fields_with_max_lengths);

		$first_name = mysql_prep($_POST["first_name"]);
		$last_name = mysql_prep($_POST["last_name"]);
		$username = mysql_prep($_POST["username"]);
		$hashed_password = password_encrypt($_POST["password"]);

		if(!empty($errors)) {
			$_SESSION["errors"] = $errors;
			redirect_to("new_student.php");
		}

		$query  = "INSERT INTO students (";
		$query .= "  first_name, last_name, username, hashed_password )";
		$query .= " VALUES (";
		$query .= " '{$first_name}', '{$last_name}', '{$username}', '{$hashed_password}' ";
		$query .= ")";
		$result = mysqli_query($connection, $query);

		if($result && mysqli_affected_rows($connection) >= 0) { //Success!
			$_SESSION["message"] = "Student created.";
			redirect_to("manage_students.php");
		}
		else { //Failure!
			$_SESSION["message"] = "Student creation failed.";
			redirect_to("new_student.php");
		}

	} else {
		//This is probably a GET request
		//redirect_to("new_subject.php");
	}
 ?>


<div id="main">
	<div id="navigation">
	</div>
	<div id="page">
		<?php echo message(); ?>
		<?php echo form_errors(errors()); ?>
		<h2>Create Student</h2>
		<form action="new_student.php" method="post">
			<p>First Name: <input type="text" name="first_name" value=""></p>
			<p>Last Name: <input type="text" name="last_name" value=""></p>
			<p>Username: <input type="text" name="username" value=""></p>
			<p>Password: <input type="password" name="password" value=""></p>
			<p>Confirm Password: <input type="password" name="password_confirm" value=""></p>
			<input type="submit" name="submit" value="Create Student">
		</form>
		<br>
		<a href="manage_students.php">Cancel</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
