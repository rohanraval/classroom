<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
	$current_page = find_page_by_id($_GET["page"], false);
	if(!$current_page)
		//page ID was missing, invalid, or not in DB
		redirect_to("manage_content.php");

	$id = $current_page["id"];
	$subject_id = $current_page["subject_id"];

	//position adjustments (moving up or down the table)
	$query_position_update = "UPDATE pages SET position = position - 1 WHERE subject_id = {$subject_id} AND position > {$current_subject["position"]}";
	$result_position_update = mysqli_query($connection, $query_position_update);

	$query  = "DELETE FROM pages ";
	$query .= "WHERE id = {$id} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && mysqli_affected_rows($connection) >= 1) {
		//Success!
		$_SESSION["message"] = "Page deleted.";
	} else {
		//Failure!
		$_SESSION["message"] = "Page deletion failed.";
	}
	$subject = find_subject_by_id($subject_id);
	redirect_to("manage_content.php?course= " . urlencode($subject["course_id"]) . "&page=" . urlencode($current_page["id"]));
 ?>
