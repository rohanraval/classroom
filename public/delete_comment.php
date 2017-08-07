<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>

<?php
	$current_page = find_page_by_id($_GET["page"], false);
	$current_comment = find_comment_by_id($_GET["comment"]);
	if(!$current_page)
		//page ID was missing, invalid, or not in DB
		redirect_to("manage_content.php");
	if(!$current_comment)
		//comment ID missing, redirect to page
		redirect_to("manage_content.php?page=" . urlencode($current_page["id"]));

	$id = $current_comment["id"];
	$query  = "DELETE FROM comments ";
	$query .= "WHERE id = {$id} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && mysqli_affected_rows($connection) == 1) {
		//Success!
		$_SESSION["message"] = "Comment deleted.";
	} else {
		//Failure!
		$_SESSION["message"] = "Comment deletion failed.";
	}
	redirect_to("manage_content.php?page=" . urlencode($current_page["id"]));
 ?>
