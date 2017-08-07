<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(0); ?>
<?php include("../includes/layouts/header.php"); ?>

<?php
	if(!isset($_GET["student"])){
		redirect_to("login.php?admin=0");
	}
	$student = find_student_by_id($_GET["student"]);
	if (!$student) {
      redirect_to("login.php?admin=0");
    }
	$course_set = find_courses_for_student($student["id"]);
?>

<div id="main">
	<div id="navigation">
		&nbsp;
	</div>
	<div id="page">
		<h2>Student: <?php echo htmlentities($student["first_name"]) . " " . htmlentities($student["last_name"]) ?></h2>
		<?php if(mysqli_num_rows($course_set) == 0) { ?>
			<p>You are not enrolled in any courses.</p>
		<?php } else { ?>
			<p style="text-align:center;">COURSES</p>
			<div class="course-list">
				<?php
					while($course = mysqli_fetch_assoc($course_set)) {
						echo "<a href=\"class_main.php?course=";
						echo urlencode($course["id"]);
						echo "\"><div>" . $course["department"] . " " . $course["number"] . ": " . $course["course_name"];
						echo "</div></a>";
					}
				 ?>
			</div>
		<?php } ?>
		<br><br>
		<a href="logout.php">Logout</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
