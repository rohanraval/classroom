<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php //confirm_logged_in(); ?>

<?php
	$admin = find_admin_by_id($_GET["id"]);
	if(!$admin)
		//admin ID was missing, invalid, or not in DB
		redirect_to("manage_admins.php");

	$query  = "DELETE FROM admins ";
	$query .= "WHERE id = {$admin["id"]} ";
	$query .= "LIMIT 1";
	$result = mysqli_query($connection, $query);

	if($result && mysqli_affected_rows($connection) == 1) {
		//Success!
		$_SESSION["message"] = "Admin deleted.";
		redirect_to("manage_admins.php");
	} else {
		//Failure!
		$_SESSION["message"] = "Admin deletion failed.";
		redirect_to("manage_admins.php?id=" . urlencode($id));
	}
 ?>
