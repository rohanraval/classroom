<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php $student_set = find_all_students(); ?>

<div id="main">
	<div id="navigation">
		<br><a href="admin.php">&laquo; Main menu</a> <br>
	</div>
	<div id="page">
		<?php echo message(); ?>
		<h2>Manage Students</h2>
		<table class="students">
			<tr>
				<th style="text-align: left; width: 100px;">Last Name</th>
				<th style="text-align: left; width: 100px;">First Name</th>
				<th style="text-align: left; width: 100px;">Username</th>
				<th style="text-align: left;">Actions</th>
			</tr>
			<?php while($student = mysqli_fetch_assoc($student_set)) { ?>
				<tr>
					<td><?php echo htmlentities($student["last_name"]); ?></td>
					<td><?php echo htmlentities($student["first_name"]); ?></td>
					<td><?php echo htmlentities($student["username"]); ?></td>
					<td><a href="enrollment.php?student=<?php echo urlencode($student["id"]); ?>">Manage Enrollment</a></td>
					<td><a href="edit_student.php?id=<?php echo urlencode($student["id"]); ?>">Edit</a></td>
					<td><a href="delete_student.php?id=<?php echo urlencode($student["id"]); ?>" onclick="return confirm('Are you sure?');">Delete</a></td>
				</tr>
			<?php } ?>
		</table>
		<br>
		+ <a href="new_student.php">Add new student</a>

	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
