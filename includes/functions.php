<?php
	//redirects to given location using http location header
	function redirect_to($new_location) {
		header("Location: " . $new_location);
		exit;
	}

	// prepares the string for mysql processing
	function mysql_prep($string) {
		global $connection;
		return mysqli_real_escape_string($connection, $string);
	}

	//confirms whether sql query is in result set
	function confirm_query($result_set) {
		if (!$result_set) {
	 	   die("Database query failed.");
	    }
	}

	//error handling
	function form_errors($errors=array()) {
		$output = "";
		if (!empty($errors)) {
		  $output .= "<div class=\"error\">";
		  $output .= "Please fix the following errors:";
		  $output .= "<ul>";
		  foreach ($errors as $key => $error) {
		    $output .= "<li>";
			$output .= htmlentities($error);
			$output .= "</li>";
		  }
		  $output .= "</ul>";
		  $output .= "</div>";
		}
		return $output;
	}

	//finds all courses
	function find_all_courses() {
		global $connection;

		$query = "SELECT * ";
		$query .= "FROM courses ";
		$query .= "ORDER BY department ASC, number ASC";
		$course_set = mysqli_query($connection, $query);
		confirm_query($course_set);
		return $course_set;
	}

	function find_students_in_course($course_id) {
		global $connection;

		$query = "SELECT s.*, c.* ";
		$query .= "FROM students AS s ";
		$query .= "LEFT JOIN students_courses AS sc ON s.id = sc.student_id ";
		$query .= "LEFT JOIN courses AS c ON sc.course_id = c.id ";
		$query .= "WHERE c.id = {$course_id} ";
		$query .= "ORDER BY s.last_name ASC, s.first_name ASC ";
		$student_set = mysqli_query($connection, $query);
		confirm_query($student_set);
		return $student_set;
	}

	function find_courses_for_student($student_id) {
		global $connection;

		$query = "SELECT s.*, c.* ";
		$query .= "FROM courses AS c ";
		$query .= "LEFT JOIN students_courses AS sc ON sc.course_id = c.id ";
		$query .= "LEFT JOIN students AS s ON sc.student_id = s.id ";
		$query .= "WHERE s.id = {$student_id} ";
		$query .= "ORDER BY c.department ASC, c.number ASC ";
		$course_set = mysqli_query($connection, $query);
		confirm_query($course_set);
		return $course_set;
	}

	function find_course_by_id($course_id) {
		global $connection;

		$query = "SELECT * ";
		$query .= "FROM courses ";
		$query .= "WHERE courses.id = {$course_id} ";
		$query .= "LIMIT 1";
		$course_set = mysqli_query($connection, $query);
		confirm_query($course_set);
		if ($course = mysqli_fetch_assoc($course_set))
			return $course;
		else
			return null;
		}

	//finds all the subjects for course
	function find_subjects_for_course($course_id, $public=false) {
		global $connection;

		$query = "SELECT * ";
		$query .= "FROM subjects WHERE course_id = {$course_id} ";
		if($public)
			$query .= "AND visible = 1 ";
		$query .= "ORDER BY position ASC";
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set);
		return $subject_set;
	}

	//finds the set of pages for the given subject
	function find_pages_for_subject($subject_id, $public=false) {
		global $connection;
		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id);

		$query = "SELECT * ";
		$query .= "FROM pages ";
		$query .= "WHERE subject_id = {$safe_subject_id} ";
		if($public)
			$query .= "AND visible = 1 ";
		$query .= "ORDER BY position ASC";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);
		return $page_set;
	}

	//finds the subject in the database given its id
	function find_subject_by_id ($subject_id, $public=true) {
		global $connection;

		$safe_subject_id = mysqli_real_escape_string($connection, $subject_id);

		$query = "SELECT * ";
		$query .= "FROM subjects ";
		$query .= "WHERE id = {$safe_subject_id} ";
		if($public)
			$query .= "AND visible = 1 ";
		$query .= "LIMIT 1";
		$subject_set = mysqli_query($connection, $query);
		confirm_query($subject_set);

		if ($subject = mysqli_fetch_assoc($subject_set))
			return $subject;
		else
			return null;
	}

	//finds the page in the database given its id
	function find_page_by_id ($page_id, $public=true) {
		global $connection;

		$safe_page_id = mysqli_real_escape_string($connection, $page_id);

		$query = "SELECT * ";
		$query .= "FROM pages ";
		$query .= "WHERE id = {$safe_page_id} ";
		if($public)
			$query .= "AND visible = 1 ";
		$query .= "LIMIT 1";
		$page_set = mysqli_query($connection, $query);
		confirm_query($page_set);

		if ($page = mysqli_fetch_assoc($page_set))
			return $page;
		else
			return null;
	}

	function find_default_page_for_subject($subject_id) {
		$page_set = find_pages_for_subject($subject_id, true);
		if ($first_page = mysqli_fetch_assoc($page_set))
			return $first_page;
		else
			return null;
	}

	//finds the page or subject that is selected by user
	function find_selected_page($public=false) {
		global $current_course;
		global $current_subject;
		global $current_page;

		if(isset($_GET["course"])) {
			$current_course = find_course_by_id($_GET["course"]);
			$current_subject = isset($_GET["subject"]) ? find_subject_by_id($_GET["subject"], $public) : null;
			$current_page = isset($_GET["page"]) ? find_page_by_id($_GET["page"], $public) : null;
		} elseif (isset($_GET["subject"])) {
			$current_subject = find_subject_by_id($_GET["subject"], $public);
			if($current_subject && $public)
				$current_page = find_default_page_for_subject($current_subject["id"]);
			else
				$current_page = null;
		} elseif (isset($_GET["page"])) {
			$current_subject = null;
			$current_page = find_page_by_id($_GET["page"], $public);
		} else {
			$selected_subject_id = null;
			$current_subject = null;
			$selected_page_id = null;
			$current_page = null;
		}
	}

	/* handles the navigation menu
	 	* when user selects a subject/page, then it will
		* put that info in the URL for later access
	 * navigation takes 2 arguments
	 	* the current subject array or null
		* the current page array or null
	*/
	function navigation($course_id, $subject_array, $page_array) {
		$output = "<ul class=\"subjects\">";
		$subject_set = find_subjects_for_course($course_id);
		while($subject = mysqli_fetch_assoc($subject_set)) {
			$output .= "<li";
			if ($subject_array && $subject["id"] == $subject_array["id"]) {
				$output .= " class=\"selected\"";
			}
			$output .= ">";
			$output .= "<a href=\"manage_content.php?course=";
			$output .= $course_id;
			$output .= "&subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]);
			$output .= "</a>";

			$page_set = find_pages_for_subject($subject["id"]);
			$output .= "<ul class=\"pages\">";
			while($page = mysqli_fetch_assoc($page_set)) {
				$output .= "<li";
				if ($page_array && $page["id"] == $page_array["id"]) {
					$output .= " class=\"selected\"";
				}
				$output .= ">";
				$output .= "<a href=\"manage_content.php?course=";
				$output .= urlencode($course_id);
				$output .= "&page=";
				$output .= urlencode($page["id"]);
				$output .= "\">";
				$output .= htmlentities($page["menu_name"]);
				$output .= "</a></li>";
			}
			mysqli_free_result($page_set);
			$output .= "</ul></li>";
		}
		mysqli_free_result($subject_set);
		$output .= "</ul>";
		return $output;
	}

	/* handles the PUBLIC site navigation menu
	 	* when user selects a subject/page, then it will
		* put that info in the URL for later access
	 * navigation takes 2 arguments
	 	* the current subject array or null
		* the current page array or null
	*/
	function public_navigation($course_array, $subject_array, $page_array) {
		echo "<p><a href=\"login.php\">Admin Sign In</a></p>";
		echo "<hr>";
		$output = "<ul class=\"subjects\">";
		$subject_set = find_subjects_for_course($course_array["id"], true);
		while($subject = mysqli_fetch_assoc($subject_set)) {
			$output .= "<li";
			if ($subject_array && $subject["id"] == $subject_array["id"]) {
				$output .= " class=\"selected\"";
			}
			$output .= ">";
			$output .= "<a href=\"index.php?subject=";
			$output .= urlencode($subject["id"]);
			$output .= "\">";
			$output .= htmlentities($subject["menu_name"]);
			if ($subject["id"] != $subject_array["id"])
				$output .= " &raquo;";
			$output .= "</a>";

			if($subject_array["id"] == $subject["id"] || $page_array["subject_id"] == $subject["id"]) {
				$page_set = find_pages_for_subject($subject["id"], true);
				$output .= "<ul class=\"pages\">";
				while($page = mysqli_fetch_assoc($page_set)) {
					$output .= "<li";
					if ($page_array && $page["id"] == $page_array["id"]) {
						$output .= " class=\"selected\"";
					}
					$output .= ">";
					$output .= "<a href=\"index.php?page=";
					$output .= urlencode($page["id"]);
					$output .= "\">";
					$output .= htmlentities($page["menu_name"]);
					$output .= "</a></li>";
				}
				$output .= "</ul>";
				mysqli_free_result($page_set);
			}
			$output .= "</li>";
		}
		mysqli_free_result($subject_set);
		$output .= "</ul>";
		return $output;
	}

	function find_all_admins() {
		global $connection;

		$query  = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "ORDER BY username ASC";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);
		return $admin_set;
	}

	function find_admin_by_id($admin_id) {
		global $connection;

		$safe_admin_id = mysqli_real_escape_string($connection, $admin_id);

		$query = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "WHERE id = {$safe_admin_id} ";
		$query .= "LIMIT 1";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);

		if ($admin = mysqli_fetch_assoc($admin_set))
			return $admin;
		else
			return null;
	}

	function find_admin_by_username($username) {
		global $connection;

		$safe_username = mysqli_real_escape_string($connection, $username);

		$query = "SELECT * ";
		$query .= "FROM admins ";
		$query .= "WHERE username = '{$safe_username}' ";
		$query .= "LIMIT 1";
		$admin_set = mysqli_query($connection, $query);
		confirm_query($admin_set);

		if ($admin = mysqli_fetch_assoc($admin_set))
			return $admin;
		else
			return null;
	}

	function find_all_students() {
		global $connection;

		$query  = "SELECT * ";
		$query .= "FROM students ";
		$query .= "ORDER BY last_name ASC, first_name ASC";
		$student_set = mysqli_query($connection, $query);
		confirm_query($student_set);
		return $student_set;
	}

	function find_student_by_id($student_id) {
		global $connection;

		$safe_student_id = mysqli_real_escape_string($connection, $student_id);

		$query = "SELECT * ";
		$query .= "FROM students ";
		$query .= "WHERE id = {$safe_student_id} ";
		$query .= "LIMIT 1";
		$student_set = mysqli_query($connection, $query);
		confirm_query($student_set);

		if ($student = mysqli_fetch_assoc($student_set))
			return $student;
		else
			return null;
	}

	function find_student_by_username($username) {
		global $connection;

		$safe_username = mysqli_real_escape_string($connection, $username);

		$query = "SELECT * ";
		$query .= "FROM students ";
		$query .= "WHERE username = '{$safe_username}' ";
		$query .= "LIMIT 1";
		$student_set = mysqli_query($connection, $query);
		confirm_query($student_set);

		if ($student = mysqli_fetch_assoc($student_set))
			return $student;
		else
			return null;
	}

	function password_encrypt($password) {
		$hash_format = "$2y$10$"; //Uses Blowfish ciper with a cost of 10
		$salt_length = 22; //Blowfish salts must be 22 characters or more
		$salt = generate_salt($salt_length); // generates random salt of length 22
		$format_and_salt = $hash_format . $salt; //appends format info to salt

		$hash = crypt($password, $format_and_salt);
		return $hash;
	}

	function generate_salt($length) {
		//32-char unique, random string using mt_rand, and securing with an MD5 hash
		$unique_random_string = md5(uniqid(mt_rand(), true));

		//make this string valid for a salt
		$base64_string = base64_encode($unique_random_string);
		$base64_string = str_replace('+', '.', $base64_string); //+ is not valid so replace + with .

		//Truncate to correct length argument
		$salt = substr($base64_string, 0, $length);
		return $salt;
	}

	function password_check($password, $existing_hash) {
		//existing hash contains format and salt at the start
		$hash = crypt($password, $existing_hash);
		return $hash === $existing_hash;
	}

	function attempt_admin_login($username, $password) {
		$admin = find_admin_by_username($username);
		if($admin) {
			//username valid, now check password
			if(password_check($password, $admin["hashed_password"])) {
				//password also valid
				return $admin;
			}
		}
		//username or password INVALID
			return false;
	}

	function attempt_student_login($username, $password) {
		$student = find_student_by_username($username);
		if($student) {
			//username valid, now check password
			if(password_check($password, $student["hashed_password"])) {
				//password also valid
				return $student;
			}
		}
		//username or password INVALID
			return false;
	}

	function confirm_logged_in($admin=1) {
		if($admin == 1) {
			if(!isset($_SESSION["admin_id"]))
				redirect_to("login.php?admin=1");
		}
		else {
			if(!isset($_SESSION["student_id"]))
				redirect_to("login.php?admin=0");
		}

	}

	function find_comments_for_page($page_id) {
		global $connection;
		$safe_page_id = mysqli_real_escape_string($connection, $page_id);

		$query  = "SELECT * ";
		$query .= "FROM comments ";
		$query .= "WHERE page_id = {$safe_page_id} ";
		$query .= "ORDER BY date DESC";
		$comment_set = mysqli_query($connection, $query);
		confirm_query($comment_set);
		return $comment_set;
	}

	//finds the comment in the database given its id
	function find_comment_by_id ($comment_id) {
		global $connection;

		$safe_comment_id = mysqli_real_escape_string($connection, $comment_id);

		$query = "SELECT * ";
		$query .= "FROM comments ";
		$query .= "WHERE id = {$safe_comment_id} ";
		$query .= "LIMIT 1";
		$comment_set = mysqli_query($connection, $query);
		confirm_query($comment_set);

		if ($comment = mysqli_fetch_assoc($comment_set))
			return $comment;
		else
			return null;
	}

	//insert into new position

 ?>
