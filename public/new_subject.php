<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(); ?>

<?php if(!$current_course)
		redirect_to("manage_courses.php");
?>

<?php
	if(isset($_POST["submit"])) {
		//Process this form

		$course_id = $current_course["id"];
		$menu_name = mysql_prep($_POST["menu_name"]);
		$position = (int) $_POST["position"];
		$visible = (int) $_POST["visible"];

		//validations
		$required_fields = ["menu_name", "position", "visible"];
		validate_presences($required_fields);
		$fields_with_max_lengths = ["menu_name" => 30];
		validate_max_lengths($fields_with_max_lengths);

		if(empty($errors)) {

			//position adjustments
			$query_position_update = "UPDATE subjects SET position = position + 1 WHERE position >= {$position} AND course_id = {$course_id} ";
			$result_position_update = mysqli_query($connection, $query_position_update);

			$query  = "INSERT INTO subjects (";
			$query .= "  course_id, menu_name, position, visible )";
			$query .= " VALUES (";
			$query .= " {$course_id}, '{$menu_name}', {$position}, {$visible}";
			$query .= ")";
			$result = mysqli_query($connection, $query);

			if($result && $result_position_update && mysqli_affected_rows($connection) >= 1) { //Success!
				$_SESSION["message"] = "Subject created.";
				redirect_to("manage_content.php?course=" . urlencode($course_id));
			}
			else { //Failure!
				$_SESSION["message"] = "Subject creation failed.";
				redirect_to("new_subject.php?course=" . urlencode($course_id));
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
		<h2>Create Subject</h2>
		<form action="new_subject.php?course=<?php echo urlencode($current_course["id"])?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="">
			</p>
			<p>Position:
				<select name="position">
					<?php
						$subject_set = find_subjects_for_course($current_course["id"]);
						$subject_count = mysqli_num_rows($subject_set);
						for($count=1; $count <= $subject_count + 1; $count++) {
							echo "<option value=\"{$count}\">{$count}</option>";
						}
					 ?>
				</select>
			</p>
			<p>Visible:
				<input type="radio" name="visible" value="0"> No
				&nbsp;
				<input type="radio" name="visible" value="1"> Yes
			</p>
			<input type="submit" name="submit" value="Create Subject">
		</form>
		<br>
		<a href="manage_content.php?course=<?php echo urlencode($current_course["id"])?>">Cancel</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
