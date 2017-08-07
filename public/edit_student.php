<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php //confirm_logged_in(); ?>
<?php require_once("../includes/validation_functions.php"); ?>

<?php
  $student = find_student_by_id($_GET["id"]);
  if (!$student) {
    redirect_to("manage_students.php");
  }
?>

<?php
if (isset($_POST['submit'])) {
  // Process the form

  // validations
  $required_fields = ["first_name", "last_name", "username", "password"];
  validate_presences($required_fields);
  $fields_with_max_lengths = ["username" => 50];
  validate_max_lengths($fields_with_max_lengths);

  if (empty($errors)) {
    // Perform Update

	$first_name = mysql_prep($_POST["first_name"]);
	$last_name = mysql_prep($_POST["last_name"]);
	$username = mysql_prep($_POST["username"]);
	$hashed_password = password_encrypt($_POST["password"]);


    $query  = "UPDATE admins SET ";
	$query .= "first_name = '{$first_name}', ";
	$query .= "username = '{$last_name}', ";
    $query .= "username = '{$username}', ";
    $query .= "hashed_password = '{$hashed_password}' ";
    $query .= "WHERE id = {$student["id"]} ";
    $query .= "LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_affected_rows($connection) >= 0) {
      // Success
      $_SESSION["message"] = "Student updated.";
      redirect_to("manage_students.php");
    } else {
      // Failure
      $_SESSION["message"] = "Student update failed.";
    }

  }
} else {
  // This is probably a GET request
}

?>

<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<div id="main">
  <div id="navigation">
    &nbsp;
  </div>
  <div id="page">
    <?php echo message(); ?>
    <?php echo form_errors($errors); ?>

    <h2>Edit Student: <?php echo htmlentities($student["first_name"] . " " . $student["last_name"]); ?></h2>
    <form action="edit_student.php?id=<?php echo urlencode($student["id"]); ?>" method="post">
		<p>First Name: <input type="text" name="first_name" value="<?php echo htmlentities($student["first_name"]); ?>" /></p>
		<p>Last Name: <input type="text" name="last_name" value="<?php echo htmlentities($student["last_name"]); ?>" /></p>
      	<p>Username: <input type="text" name="username" value="<?php echo htmlentities($student["username"]); ?>" /></p>
      	<p>Password: <input type="password" name="password" value="" /></p>
      	<input type="submit" name="submit" value="Edit Student" />
    </form>
    <br />
    <a href="manage_students.php">Cancel</a>
  </div>
</div>

<?php include("../includes/layouts/footer.php"); ?>
