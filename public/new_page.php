<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php find_selected_page(); ?>

<?php
	if(!$current_subject)
		redirect_to("manage_content.php");
 ?>
 <?php
 	if(isset($_POST["submit"])) {
 		//Process this form

 		//validations
 		$required_fields = ["menu_name", "position", "visible", "content"];
 		validate_presences($required_fields);

 		$fields_with_max_lengths = ["menu_name" => 30];
 		validate_max_lengths($fields_with_max_lengths);

 		if(empty($errors)) {
 			//Perform creation

			$subject_id = $current_subject["id"];
			$menu_name = mysql_prep($_POST["menu_name"]);
	 		$position = (int) $_POST["position"];
	 		$visible = (int) $_POST["visible"];
			$content = mysql_prep($_POST["content"]);

			$query_position_update = "UPDATE pages SET position = position + 1 WHERE subject_id = {$subject_id} AND position >= {$position}";
			$result_position_update = mysqli_query($connection, $query_position_update);

			$query  = "INSERT INTO pages (";
			$query .= "subject_id, menu_name, position, visible, content ) ";
			$query .= "VALUES (";
			$query .= "{$subject_id}, '{$menu_name}', {$position}, {$visible}, '{$content}'";
			$query .= ")";
			$result = mysqli_query($connection, $query);

			if($result && $result_position_update && mysqli_affected_rows($connection) >= 1) { //Success!
				$_SESSION["message"] = "Page created.";
				redirect_to("manage_content.php?course=" . urlencode($current_subject["course_id"]) . "&subject=" . urlencode($subject_id));
			}
			else { //Failure!
				$_SESSION["message"] = "Page creation failed.";
			}
 		}
 	} else {
 		//This is probably a GET request
 	}
  ?>

<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<div id="main">
	<div id="navigation">
	</div>
	<div id="page">
		<?php echo message(); ?>
		<?php echo form_errors($errors); ?>
		<h2>Create Page</h2>
		<form action="new_page.php?subject=<?php echo urlencode($current_subject["id"]); ?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="">
			</p>
			<p>Position:
				<select name="position">
					<?php
						$page_set = find_pages_for_subject($current_subject["id"]);
						$page_count = mysqli_num_rows($page_set);
						for($count=1; $count <= $page_count + 1; $count++) {
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
			<p>Content:
				<br><br>
				<textarea name="content" rows="10" cols="50"></textarea>
			</p>
			<input type="submit" name="submit" value="Create Page">
		</form>
		<br>
		<a href="manage_content.php?course=<?php echo urlencode($current_subject["course_id"])?>&subject=<?php echo urlencode($current_subject["id"])?>">Cancel</a>
	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
