<div id="footer">Copyright <?php echo date("Y"); ?>, Rohan S Raval</div>
</div>
</body>
</html>

<?php
	// 5. Close the database connection
	if(isset($connection))
		mysqli_close($connection);
 ?>
