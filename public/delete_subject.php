<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
	$current_subject = find_subject_by_id($_GET["subject"], false);
	if(!$current_subject)
		//subject ID was missing, invalid, or not in DB
		redirect_to("manage_content.php");

	$pages_set = find_pages_for_subject($current_subject["id"]);
	if(mysqli_num_rows($pages_set) > 0) {
		$_SESSION["message"] = "Cannot delete a subject with pages. Delete pages first.";
		redirect_to("manage_content.php?course= " . urlencode($current_subject["course_id"]) . "&subject=" . urlencode($current_subject["id"]));
	}

	//position adjustments (moving up or down the table)
	$query_position_update = "UPDATE subjects SET position = position - 1 WHERE position > {$current_subject["position"]} AND course_id = {$current_subject["course_id"]} ";
	$result_position_update = mysqli_query($connection, $query_position_update);

	$id = $current_subject["id"];
	$query  = "DELETE FROM subjects ";
	$query .= "WHERE id = {$id} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && $result_position_update && mysqli_affected_rows($connection) >= 1) {
		//Success!
		$_SESSION["message"] = "Subject deleted.";
	} else {
		//Failure!
		$_SESSION["message"] = "Subject deletion failed.";
	}
	redirect_to("manage_content.php?course= " . urlencode($current_subject["course_id"]) . "&subject=" . urlencode($current_subject["id"]));
 ?>
