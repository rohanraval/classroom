<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php
	if(isset($_GET["student"])) {
		$student = find_student_by_id($_GET["student"]);
		$course = null;
	} elseif(isset($_GET["course"])) {
		$student = null;
		$course = find_course_by_id($_GET["course"]);
	} else {
		redirect_to("admin.php");
	}
 ?>

<?php
	if(isset($_POST["submit"])) {
		//Process this form

		//getting the correct student and course ID's
		if($student) {
			$student_id = (int)$student["id"];
			$course_id = (int)$_POST["course_select"];
		}
		else {
			$student_id = (int)$_POST["student_select"];
			$course_id = (int)$course["id"];
		}

		//Making sure the student isn't already enrolled in this course
		is_enrolled($student_id, $course_id);

		if(empty($errors)) {
			//Executing query
			$query  = "INSERT INTO students_courses (";
			$query .= " student_id, course_id )";
			$query .= " VALUES (";
			$query .= " {$student_id}, {$course_id} ";
			$query .= ")";
			$result = mysqli_query($connection, $query);

			if($result && mysqli_affected_rows($connection) >= 0) { //Success!
				$_SESSION["message"] = "Enrollment Successful.";
			}
			else { //Failure!
				$_SESSION["message"] = "Enrollment failed.";
			}

			if($student) redirect_to("enrollment.php?student=" . urlencode($student["id"]));
			else redirect_to("manage_courses.php?course=" . urlencode($course["id"]));
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

		<h2>Enroll <?php if(!$student) echo "New "; ?>Student in <?php if(!$course) echo "New "; ?>Course</h2>

		<?php
		if($student) { ?>

			<form action="enroll_student.php?student=<?php echo urlencode($student["id"]) ?>" method="post">
				<p>Student: <input type="text" disabled="true" value="<?php echo htmlentities($student["first_name"]) . " " . htmlentities($student["last_name"]) ?>"></p>
				<p>Course:
					<select name="course_select" required="true">
					<?php
						$course_set = find_all_courses();
						while($course = mysqli_fetch_assoc($course_set)) {
							echo "<option value=" . htmlentities($course["id"]) . ">";
							echo htmlentities($course["department"]) . " " . htmlentities($course["number"]);
							echo "</option>";
						}
						mysqli_free_result($course_set);
					 ?>
				 	</select>
				</p>
				<input type="submit" name="submit" value="Enroll">
			</form>
			<br>
			<a href="manage_students.php?student=<?php echo urlencode($student["id"]) ?>">Cancel</a>

		<?php } elseif($course) {  ?>

			<form action="enroll_student.php?course=<?php echo urlencode($course["id"]) ?>" method="post">
				<p>Course: <input type="text" disabled="true" value="<?php echo htmlentities($course["department"]) . " " . htmlentities($course["number"]) ?>"></p>
				<p>Student:
					<select name="student_select" required="true">
					<?php
						$student_set = find_all_students();
						while($student = mysqli_fetch_assoc($student_set)) {
							echo "<option value=" . htmlentities($student["id"]) . ">";
							echo htmlentities($student["last_name"]) . ", " . htmlentities($student["first_name"]);
							echo "</option>";
						}
						mysqli_free_result($student_set);
					 ?>
					</select>
				</p>
				<input type="submit" name="submit" value="Enroll">
			</form>
			<br>
			<a href="manage_courses.php?course=<?php echo urlencode($course["id"]) ?>">Cancel</a>
		<?php } ?>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
