<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php $layout_context = "public"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(true); ?>

<div id="main">
	<div id="navigation">
		<a href="login.php?admin=1"><div>Admin</div></a>
		<a href="login.php?admin=0"><div>Student</div></a>
	</div>

	<div id="page">
		<br>
		<div id="intro_message">
			<h3>Welcome to Classroom!</h3>
			<p>Classroom is a wiki and forum designed for school or university courses.</p>
			<p>Classroom employs a content management system that allows instructors to create
			course pages, and allows students to view published course content. Classroom also
			features a forum for each topic where students can engage in discussions.</p>
		</div>
	</div>
</div>
<?php include("../includes/layouts/footer.php"); ?>
