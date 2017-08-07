<?php require_once("../includes/session.php"); ?>
<?php require_once("../includes/db_connection.php"); ?>
<?php require_once("../includes/functions.php"); ?>
<?php confirm_logged_in(); ?>
<?php $layout_context = "admin"; ?>
<?php include("../includes/layouts/header.php"); ?>

<?php find_selected_page(); ?>

<?php if(!isset($_GET["course"]))
		redirect_to("manage_courses.php");
?>

<div id="main">
	<div id="navigation">
		<br>
		<a href="manage_courses.php?course=<?php echo $current_course["id"];?>">&laquo; Manage Course</a> <br>
		<?php echo navigation($current_course["id"], $current_subject, $current_page); ?>
		<br>
		<a href="new_subject.php?course=<?php echo $current_course["id"]; ?>">+ Add a subject</a>
	</div>

	<div id="page">

		<?php echo message(); ?>

		<?php
		if($current_subject) { ?>
			<h2>Manage Subject</h2>
			Menu name: <?php echo htmlentities($current_subject["menu_name"]); ?><br>
			Position: <?php echo $current_subject["position"]; ?><br>
			Visible: <?php echo $current_subject["visible"] == 1 ? 'yes' : 'no'; ?><br>
			<br>
			<a href="edit_subject.php?subject=<?php echo urlencode($current_subject["id"]); ?>">Edit Subject</a>
			<br><br>
			<hr>

			<h3>Pages in this subject:</h3>
			<?php
			$page_set = find_pages_for_subject($current_subject["id"]);
			echo "<ul>";
			while($page = mysqli_fetch_assoc($page_set)) {
				$output  = "<li>";
				$output .= "<a href=\"manage_content.php?course=";
				$output .= urlencode($current_course["id"]);
				$output .= "&page=";
				$output .= urlencode($page["id"]);
				$output .= "\">";
				$output .= htmlentities($page["menu_name"]);
				$output .= "</a></li>";
				echo $output;
			}
			mysqli_free_result($page_set);
			echo "</ul>";
			?>
			<br>
			+ <a href="new_page.php?subject=<?php echo urlencode($current_subject["id"]); ?>">Add a new page to this subject</a>
		<?php }
		elseif ($current_page) { ?>
			<h2>Manage Page</h2>
			<strong>Menu name:</strong> <?php echo htmlentities($current_page["menu_name"]); ?><br><br>
			<strong>Position:</strong> <?php echo $current_page["position"]; ?><br><br>
			<strong>Visible:</strong> <?php echo $current_page["visible"] == 1 ? 'yes' : 'no'; ?><br><br>
			<strong>Content:</strong> <br>
			<div class="view-content">
				<?php echo nl2br(htmlentities($current_page["content"])); ?>
			</div>
			<br>
			<a href="edit_page.php?page=<?php echo urlencode($current_page["id"]); ?>">Edit Page</a>

		<?php }
		else { ?>
			<br>
			Please select a subject or a page from the navigation.
		<?php } ?>
	</div>

	<?php if ($current_page) { ?>
	<div id="comments">
		<h3>Comments</h3>
		<?php
		$comment_set = find_comments_for_page($current_page["id"]);
		if(mysqli_num_rows($comment_set) == 0)
			echo "<p>Be the first to comment.</p>";
		while($comment = mysqli_fetch_assoc($comment_set)) {
			$output  = "<div class=\"comment\">";
			$output .=  "<strong>" . htmlentities($comment["name"]) . "</strong><br>";
			$output .=  date('M j, Y g:i a', strtotime($comment["date"]) );
			$output .= "<p>" . htmlentities($comment["comment_text"]) . "</p>";
			$output .= "<a href=\"delete_comment.php?page=";
			$output .= urlencode($current_page["id"]);
			$output .= "&comment=";
			$output .= urlencode($comment["id"]);
			$output .= "\" >Delete Comment</a><br><br>";
			$output .= "</div>";
			echo $output;
		}
		mysqli_free_result($comment_set);
	 	?>
	</div>
	<?php } ?>
</div>

<?php include("../includes/layouts/footer.php"); ?>
