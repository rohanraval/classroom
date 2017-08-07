<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php require_once("../includes/validation_functions.php"); ?>
<?php $layout_context = "public"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(true); ?>

 <?php
 	if(isset($_POST["submit"])) {
 		//Process this form

 		//validations
 		$required_fields = ["name", "comment_text"];
 		validate_presences($required_fields);

 		$fields_with_max_lengths = ["name" => 50];
 		validate_max_lengths($fields_with_max_lengths);

 		if(empty($errors)) {
 			//Perform creation

			$page_id = $current_page["id"];
			$name = mysql_prep($_POST["name"]);
			$comment_text = mysql_prep($_POST["comment_text"]);

			$query  = "INSERT INTO comments (";
			$query .= "page_id, name, comment_text) ";
			$query .= "VALUES (";
			$query .= "{$page_id}, '{$name}', '{$comment_text}'";
			$query .= ")";
			$result = mysqli_query($connection, $query);

			if($result && mysqli_affected_rows($connection) == 1) { //Success!
				$_SESSION["message"] = "Comment created.";
				redirect_to("index.php?page=" . urlencode($page_id));
			}
			else { //Failure!
				$_SESSION["message"] = "Comment creation failed.";
			}
 		}
 	} else {
 		//This is probably a GET request
 	}
  ?>

<div id="main">
	<div id="navigation">
		<?php echo public_navigation($current_course, $current_subject, $current_page); ?>
	</div>

	<div id="page">
		<?php echo message(); ?>
		<?php
		if ($current_page) { ?>
			<h2><?php echo htmlentities($current_page["menu_name"]); ?></h2>
			<div class="content">
				<?php echo nl2br(htmlentities($current_page["content"])); ?>
			</div>
		<?php }
		else { ?>
			<br>
			<div id="intro_message">
				<h3>Welcome to this class!</h3>
			</div>
		<?php } ?>
	</div>

	<?php if ($current_page) { ?>
	<div id="comments">
		<?php echo form_errors($errors); ?>
		<h3>Comments</h3>
		<?php
		$comment_set = find_comments_for_page($current_page["id"]);
		if(mysqli_num_rows($comment_set) == 0)
			echo "<p>Be the first to comment.</p>";
		while($comment = mysqli_fetch_assoc($comment_set)) {
			$output  = "<div class=\"comment\">";
			$output .= "<strong>" . htmlentities($comment["name"]) . "</strong><br>";
			$output .= "<span id=\"date\">" . date('M j, Y g:i a', strtotime($comment["date"]) ) . "</span>";
			$output .= "<p>" . htmlentities($comment["comment_text"]) . "</p>";
			$output .= "</div>";
			echo $output;
		}
		mysqli_free_result($comment_set);
		?>
		<hr>
		<h3>New Comment</h3>
		<form class="" action="index.php?page=<?php echo urlencode($current_page["id"]); ?>" method="post">
			<p>Name:
				<input type="text" name="name" placeholder="Enter your name">
			</p>
			<p>Comment:</p>
				<textarea name="comment_text" rows="3" cols="30" placeholder="Enter your comment"></textarea>
			<br>
			<input type="submit" name="submit" value="Submit">
		</form>
	</div>
	<?php } ?>
</div>

<?php include("../includes/layouts/footer.php"); ?>
