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
		$required_fields = ["department", "number", "course_name", "instructor_name"];
		validate_presences($required_fields);
		$fields_with_max_lengths = ["course_name" => 100, "department" => 6];
		validate_max_lengths($fields_with_max_lengths);

		if(empty($errors)) {
			$department = mysql_prep($_POST["department"]);
			$number = (int)($_POST["number"]);
			$course_name = mysql_prep($_POST["course_name"]);
			$description = mysql_prep($_POST["description"]);
			$instructor_name = mysql_prep($_POST["instructor_name"]);

			$query  = "INSERT INTO courses (";
			$query .= "  department, number, course_name, description, instructor_name )";
			$query .= " VALUES (";
			$query .= " '{$department}', {$number}, '{$course_name}', '{$description}', '{$instructor_name}' ";
			$query .= ")";
			$result = mysqli_query($connection, $query);

			if($result && mysqli_affected_rows($connection) >= 0) { //Success!
				$_SESSION["message"] = "Course created.";
				redirect_to("manage_courses.php");
			}
			else { //Failure!
				$_SESSION["message"] = "Course creation failed.";
				redirect_to("new_course.php");
			}
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
		<?php echo form_errors($errors); ?>
		<h2>Create Course</h2>
		<form action="new_course.php" method="post">
			<p>Department: <input type="text" name="department" value="" placeholder="PHYS, CS, COMM, etc."></p>
			<p>Course Number: <input type="number" name="number" min="1000" max="9999" placeholder="xxxx"></p>
			<p>Course Name: <input type="text" name="course_name" value=""></p>
			<p>Instructor Name: <input type="text" name="instructor_name" value=""></p>
			<p>Course Description: <br><br><textarea name="description" rows="3" cols="50"></textarea></p>
			<input type="submit" name="submit" value="Create Course">
		</form>
		<br>
		<a href="manage_courses.php">Cancel</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
