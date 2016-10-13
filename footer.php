</div>
	<div id="footer">
		<?php
		 if (isset($_SESSION['username'])) {
			echo '<a href="home.php" title="home">Home</a>|  
            <a href="addpatient.php" title="addpatient">Add Patient</a>|
			<a href="logout.php" title="logout">Logout</a>';
		} else {
             echo '<a href="home.php" title="home">Login</a>';
         } ?>
	</div>
</body>
</html>