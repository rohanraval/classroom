<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(); ?>
<?php
	if(!$current_course)
		redirect_to("manage_courses.php");
 ?>

<?php
	if(isset($_POST["submit"])) {
		//Process this form

		//validations
		$required_fields = ["department", "number", "course_name", "instructor_name"];
		validate_presences($required_fields);
		$fields_with_max_lengths = ["course_name" => 100, "department" => 6];
		validate_max_lengths($fields_with_max_lengths);

		if(empty($errors)) {
			$id = (int)($current_course["id"]);
			$department = mysql_prep($_POST["department"]);
			$number = (int)($_POST["number"]);
			$course_name = mysql_prep($_POST["course_name"]);
			$description = mysql_prep($_POST["description"]);
			$instructor_name = mysql_prep($_POST["instructor_name"]);

			$query  = "UPDATE courses SET ";
			$query .= "department = '{$department}', ";
			$query .= "number = {$number}, ";
			$query .= "course_name = '{$course_name}', ";
			$query .= "description = '{$description}', ";
			$query .= "instructor_name = '{$instructor_name}' ";
			$query .= "WHERE id = {$id} ";
			$query .= "LIMIT 1";
			$result = mysqli_query($connection, $query);

			if($result && mysqli_affected_rows($connection) >= 0) { //Success!
				$_SESSION["message"] = "Course updated.";
				redirect_to("manage_courses.php?course=" . urlencode($id));
			}
			else { //Failure!
				$_SESSION["message"] = "Course update failed.";
				redirect_to("edit_course.php?course=" . urlencode($id));
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
		<h2>Edit Course: <?php echo urlencode($current_course["department"]) . " " . urlencode($current_course["number"])?></h2>
		<form action="edit_course.php?course=<?php echo urlencode($current_course["id"])?>" method="post">
			<p>Department: <input type="text" name="department" value="<?php echo htmlentities($current_course["department"]); ?>"></p>
			<p>Course Number: <input type="number" name="number" min="1000" max="9999" value="<?php echo htmlentities($current_course["number"]); ?>"></p>
			<p>Course Name: <input type="text" name="course_name" value="<?php echo htmlentities($current_course["course_name"]); ?>"></p>
			<p>Instructor Name: <input type="text" name="instructor_name" value="<?php echo htmlentities($current_course["instructor_name"]); ?>"></p>
			<p>Course Description: <br><br><textarea name="description" rows="3" cols="50"><?php echo htmlentities($current_course["description"]); ?></textarea></p>
			<input type="submit" name="submit" value="Edit Course">
		</form>
		<br>
		<a href="manage_courses.php?course=<?php echo urlencode($current_course["id"])?>">Cancel</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
