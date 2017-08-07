<?php
	if(!isset($layout_context))
		$layout_context = "public";
 ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
	<head>
		<!-- Latest compiled and minified CSS
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
		jQuery library
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
		Latest compiled JavaScript
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
		<meta name="viewport" content="width=device-width, initial-scale=1" >-->

		<link rel="stylesheet" href="stylesheets/public.css" media="all" type="text/css">
		<title>Classroom <?php if($layout_context == "admin") { echo "Admin"; } ?></title>
	</head>
	<body>
		<div class="container">
		<div id="header" class="row">
			<a href="index.php"><h1>CLASSROOM <?php if($layout_context == "admin") { echo "Admin"; } ?></h1></a>
		</div>
