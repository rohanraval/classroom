<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php find_selected_page(); ?>
<?php
	if(!$current_page)
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

			$id = $current_page["id"];
			$menu_name = mysql_prep($_POST["menu_name"]);
			$position = (int) $_POST["position"];
			$visible = (int) $_POST["visible"];
			$content = mysql_prep($_POST["content"]);

			//position adjustments (moving up or down the table)
			if($current_page["position"] < $position) {
				$old_position = $current_page["position"];
				$query_position_update = "UPDATE pages SET position = position - 1 WHERE position > {$old_position} AND position <= {$position} ";
				$result_position_update = mysqli_query($connection, $query_position_update);
			} elseif($current_page["position"] > $position) {
				$old_position = $current_page["position"];
				$query_position_update = "UPDATE pages SET position = position + 1 WHERE position >= {$position} AND position < {$old_position} ";
				$result_position_update = mysqli_query($connection, $query_position_update);
			}

			//Perform update
	 		$query  = "UPDATE pages SET ";
	 		$query .= "menu_name = '{$menu_name}', ";
	 		$query .= "position = {$position}, ";
	 		$query .= "visible = {$visible}, ";
			$query .= "content = '{$content}' ";
	 		$query .= "WHERE id = {$id} ";
			$query .= "LIMIT 1";
	 		$result = mysqli_query($connection, $query);

	 		if($result && mysqli_affected_rows($connection) >= 0) {
				//Success!
	 			$_SESSION["message"] = "Page updated.";
				$current_subject = find_subject_by_id($current_page["subject_id"]);
				redirect_to("manage_content.php?course=" . urlencode($current_subject["course_id"]) . "&page=" . urlencode($current_page["id"]));
	 		}
	 		else {
				//Failure!
	 			$_SESSION["message"] = "Page update failed.";
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
		<?php $current_subject = find_subject_by_id($current_page["subject_id"]); ?>
		<?php echo navigation($current_subject["course_id"], $current_subject, $current_page); ?>
	</div>
	<div id="page">
		<?php echo message(); ?>
		<?php echo form_errors($errors); ?>
		<h2>Edit Page: <?php echo htmlentities($current_page["menu_name"]); ?></h2>
		<form action="edit_page.php?page=<?php echo urlencode($current_page["id"]); ?>" method="post">
			<p>Menu name:
				<input type="text" name="menu_name" value="<?php echo htmlentities($current_page["menu_name"]); ?>">
			</p>
			<p>Position:
				<select name="position">
					<?php
						$page_set = find_pages_for_subject($current_page["subject_id"]);
						$page_count = mysqli_num_rows($page_set);
						for($count=1; $count <= $page_count; $count++) {
							echo "<option value=\"{$count}\"";
							if($current_page["position"] == $count)
								echo " selected";
							echo ">{$count}</option>";
						}
					 ?>
				</select>
			</p>
			<p>Visible:
				<input type="radio" name="visible" value="0"
				<?php
					if($current_page["visible"] == 0)
						echo "checked";
				 ?>
				> No
				&nbsp;
				<input type="radio" name="visible" value="1"
				<?php
					if($current_page["visible"] == 1)
						echo "checked";
				 ?>
				 > Yes
			</p>
			<p>Content:
				<br><br>
				<textarea name="content" rows="10" cols="50"><?php echo $current_page["content"]; ?></textarea>
			</p>
			<input type="submit" name="submit" value="Edit Page">
		</form>
		<br>
		<a href="manage_content.php?course=<?php echo urlencode($current_subject["course_id"]) ?>&page=<?php echo urlencode($current_page["id"])?>">Cancel</a>
		&nbsp;
		&nbsp;
		<a href="delete_page.php?page=<?php echo urlencode($current_page["id"]); ?>" onclick="return confirm('Are you sure?');">Delete page</a>

	</div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
