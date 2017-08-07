<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php $_SESSION["caller"] = "enrollment"; ?>

<?php
  $student = find_student_by_id($_GET["student"]);
  if (!$student) {
    redirect_to("manage_students.php");
  }
  $course_set = find_courses_for_student($student["id"]);
?>

<div id="main">
	<div id="navigation">
		<br><a href="manage_students.php">&laquo; Manage Students</a> <br>
	</div>
	<div id="page">
		<?php echo message(); ?>
		<h2>Enrollment: <?php echo htmlentities($student["first_name"]) . " " . htmlentities($student["last_name"])?></h2>
		<table class="courses">
			<tr>
				<th style="text-align: left; width: 50px;">Dept</th>
				<th style="text-align: left; width: 50px; padding-right: 30px;">Number</th>
				<th style="text-align: left; width: 250px;">Course Name</th>
				<th style="text-align: left; width: 150px;">Instructor Name</th>
				<th style="text-align: left;">Actions</th>
			</tr>
			<?php while($course = mysqli_fetch_assoc($course_set)) { ?>
				<tr>
					<td><?php echo htmlentities($course["department"]); ?></td>
					<td><?php echo htmlentities($course["number"]); ?></td>
					<td><?php echo htmlentities($course["course_name"]); ?></td>
					<td><?php echo htmlentities($course["instructor_name"]); ?></td>
					<td><a href="unenroll_student.php?student=<?php echo urlencode($student["id"]); ?>&course=<?php echo urlencode($course["id"]);?>" onclick="return confirm('Are you sure?');">Unenroll</a></td>
				</tr>
			<?php } ?>
		</table>
		<br>
		+ <a href="enroll_student.php?student=<?php echo urlencode($student["id"]); ?>">Enroll student in new course</a>

	</div>
</div>
