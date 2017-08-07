<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(); ?>
<?php $_SESSION["caller"] = "manage_courses"; ?>

<div id="main">
	<div id="navigation">
		<br>
		<a href="admin.php">&laquo; Main menu</a> <br><br><hr>
		Courses:
		<ul class="courses">
			<?php
				$course_set = find_all_courses();
				while($course = mysqli_fetch_assoc($course_set)) {
					echo "<li";
					if ($course["id"] == $current_course["id"])
						echo " class=\"selected\"";
					echo "><a href=\"manage_courses.php?course=";
					echo urlencode($course["id"]);
					echo "\">" . htmlentities($course["department"]) . " " . htmlentities($course["number"]);
					echo "</a>";
					echo "</li>";
				}
				mysqli_free_result($course_set);
			 ?>
		</ul>
		<br><hr><br>
		+ <a href="new_course.php" style="text-decoration: none">Add a new course</a>
	</div>

	<div id="page">

		<?php echo message(); ?>

		<?php if($current_course) { ?>
			<h2>Manage Course</h2>
			<strong>Course Number:</strong> <?php echo htmlentities($current_course["department"]) . " " . htmlentities($current_course["number"]); ?><br><br>
			<strong>Course Name:</strong> <?php echo htmlentities($current_course["course_name"]); ?><br><br>
			<strong>Instructor:</strong> <?php echo htmlentities($current_course["instructor_name"]); ?><br><br>
			<strong>Course Description:</strong><br>
				<div class="view-content">
					<?php echo htmlentities($current_course["description"]); ?>
				</div>
			<br>
			<a href="edit_course.php?course=<?php echo urlencode($current_course["id"]); ?>">Edit Course Information</a>
			<br><br>
			<a href="manage_content.php?course=<?php echo urlencode($current_course["id"]); ?>">Manage Course Content</a>
			<br><br>
			<a href="delete_course.php?course=<?php echo urlencode($current_course["id"]); ?>" onclick="return confirm('Are you sure?');">Delete Course</a>
		<?php } else { ?>
			<br>Please select a course from the navigation.
		<?php } ?>
	</div>
	<?php if($current_course) { ?>
		<div class="view-students">
			<h3>Students in this course:</h3>
			<table>
				<?php $student_set = find_students_in_course($current_course["id"]); ?>
				<tr>
					<th style="text-align: left; width: 150px;">Name</th>
					<th style="text-align: left; width: 100px;">Username</th>
					<th style="text-align: left;">Actions</th>
				</tr>
				<?php while($student = mysqli_fetch_assoc($student_set)) { ?>
					<tr>
						<td><?php echo $student["first_name"] . " " . $student["last_name"] ?></td>
						<td><?php echo $student["username"] ?></td>
						<td><a href="manage_students.php?student=<?php echo urlencode($student["id"]); ?>">Edit</a></td>
						<td><a href="unenroll_student.php?student=<?php echo urlencode($student["id"])?>&course=<?php echo urlencode($current_course["id"])?>" onclick="return confirm('Are you sure?');">Unenroll</a></td>
					</tr>
				<?php }
					mysqli_free_result($student_set);
				?>
			</table>
			<br>
			+ <a href="enroll_student.php?course=<?php echo urlencode($current_course["id"])?>">Enroll a new student</a>
		</div>
		<?php } ?>
</div>

<?php include("../includes/layouts/footer.php"); ?>
