<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php
	if(isset($_GET["student"]) && isset($_GET["course"])) {

		$student = find_student_by_id($_GET["student"]);
		$course = find_course_by_id($_GET["course"]);

		//Make sure student is enrolled
		$query_enrollment_check = "SELECT * FROM students_courses WHERE student_id = {$student["id"]} AND course_id = {$course["id"]} ";
		$result_enrollment_check = mysqli_query($connection, $query_enrollment_check);
		confirm_query($result_enrollment_check);
		if(mysqli_num_rows($result_enrollment_check) != 1) {
			$errors["not_enrolled"] = "Enrollment Error: Student may not be enrolled in this course.";
		}
		var_dump($errors);

		if(empty($errors)) {
			//Executing query
			$query  = "DELETE FROM students_courses ";
			$query .= "WHERE student_id = {$student["id"]} ";
			$query .= "AND course_id = {$course["id"]} ";
			$result = mysqli_query($connection, $query);

			if($result && mysqli_affected_rows($connection) >= 0) { //Success!
				$_SESSION["message"] = "Unenrollment Occured.";
			}
			else { //Failure!
				$_SESSION["message"] = "Unenrollment Failed.";
			}
		}
		if($_SESSION["caller"] == "manage_courses") {
			redirect_to("manage_courses.php?course=" . urlencode($course["id"]));
		}
		else {
			redirect_to("enrollment.php?student=" . urlencode($student["id"]));
		}

	} else {
		redirect_to("admin.php");
	}
 ?>
