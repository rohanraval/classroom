<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
	$course = find_course_by_id($_GET["course"]);
	if(!$course)
		//course ID was missing, invalid, or not in DB
		redirect_to("manage_courses.php");

	$student_set = find_students_in_course($course["id"]);
	if(mysqli_num_rows($student_set) > 0) {
		$_SESSION["message"] = "Cannot delete a course with enrolled students. Unenroll students from course first.";
		redirect_to("manage_courses.php?course=" . urlencode($course["id"]));
	}

	$query  = "DELETE FROM courses ";
	$query .= "WHERE id = {$course["id"]} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && mysqli_affected_rows($connection) == 1) {
		//Success!
		$_SESSION["message"] = "Course deleted.";
		redirect_to("manage_courses.php");
	} else {
		//Failure!
		$_SESSION["message"] = "Course deletion failed.";
		redirect_to("manage_courses.php?course=" . urlencode($course["id"]));
	}
 ?>
