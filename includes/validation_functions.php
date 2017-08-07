<?php

$errors = [];

function fieldname_as_text($fieldname) {
	$fieldname = str_replace("_", " ", $fieldname);
	$fieldname = ucfirst($fieldname);
	return $fieldname;
}

// * presence
// use trim() so empty spaces don't count
// use === to avoid false positives
// empty() would consider "0" to be empty
function has_presence($value) {
	return isset($value) && $value !== "";
}

function validate_presences($required_fields) {
	global $errors;
	foreach($required_fields as $field) {
		$value = trim($_POST[$field]);
		if (!has_presence($value)) {
		  $errors[$field] = fieldname_as_text($field) . " can't be blank";
		}
	}
}

// * string length
// max length
function has_max_length($value, $max) {
	return strlen($value) <= $max;
}

function validate_max_lengths($fields_with_max_lengths) {
	global $errors;
	// Expects an assoc. array
	foreach($fields_with_max_lengths as $field => $max) {
		$value = trim($_POST[$field]);
	  if (!has_max_length($value, $max)) {
	    $errors[$field] = fieldname_as_text($field) . " is too long";
	  }
	}
}

function has_min_length($value, $min) {
	return strlen($value) >= $min;
}

function validate_min_lengths($fields_with_min_lengths) {
	global $errors;
	// Expects an assoc. array
	foreach($fields_with_min_lengths as $field => $min) {
		$value = trim($_POST[$field]);
	  if (!has_min_length($value, $min)) {
	    $errors[$field] = fieldname_as_text($field) . " is too short";
	  }
	}
}

// * inclusion in a set
function has_inclusion_in($value, $set) {
	return in_array($value, $set);
}

function confirm_password($p1, $p2) {
	global $errors;
	if($p1 != $p2)
		$errors["password_mismatch"] = "Passwords do not match.";
}

function is_enrolled($student_id, $course_id) {
	global $errors;
	$course_set = find_courses_for_student($student_id);
	while($course = mysqli_fetch_assoc($course_set)) {
		if($course["id"] == $course_id) {
			$errors["is_enrolled"] = "The student is already enrolled in this course.";
		}
	}
}

?>
