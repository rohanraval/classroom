<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
	$student = find_student_by_id($_GET["id"]);
	if(!$student)
		//student ID was missing, invalid, or not in DB
		redirect_to("manage_students.php");

	$course_set = find_courses_for_student($student["id"]);
	if(mysqli_num_rows($course_set) > 0) {
		$_SESSION["message"] = "Cannot delete a student enrolled in courses. Unenroll student from courses first.";
		redirect_to("manage_students.php");
	}

	$query  = "DELETE FROM students ";
	$query .= "WHERE id = {$student["id"]} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && mysqli_affected_rows($connection) == 1) {
		//Success!
		$_SESSION["message"] = "Student deleted.";
		redirect_to("manage_students.php");
	} else {
		//Failure!
		$_SESSION["message"] = "Student deletion failed.";
		redirect_to("manage_students.php");
	}
 ?>
