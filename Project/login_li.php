<?php # Script 9.6 - login.php (3rd version after Scripts 9.1 & 9.3)
// Send NOTHING to the Web browser prior to the session_start() line!

// Check if the form has been submitted.
if (isset($_POST['submitted'])) {

	require_once ('mysqli.php'); // Connect to the db.
	global $dbc;
	
			
	$errors = array(); // Initialize error array.
	
	// Check for an email address.
	if (empty($_POST['email'])) {
		$errors[] = 'You forgot to enter your email address.';
	} else {
		$e = ($_POST['email']);
	}
	
	// Check for a password.
	if (empty($_POST['password'])) {
		$errors[] = 'You forgot to enter your password.';
	} else {
		$p = ($_POST['password']);
	}
	
	if (empty($errors)) { // If everything's OK.

		/* Retrieve the user_id and first_name for 
		that email/password combination. */
		$query = "SELECT user_id, first_name FROM users WHERE email='$e' AND password=SHA('$p')";		
		$result = @mysqli_query ($dbc,$query); // Run the query.
		$row = mysqli_fetch_array ($result, MYSQLI_NUM); // Return a record, if applicable.
		
		if ($row) { // A record was pulled from the database.
				
			// Set the session data & redirect.
			session_start();
			$_SESSION['user_id'] = $row[0];
			$_SESSION['first_name'] = $row[1];

			// Redirect the user to the loggedin.php page.
			// Start defining the URL.
			$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
			// Check for a trailing slash.
			if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
				$url = substr ($url, 0, -1); // Chop off the slash.
			}
			// Add the page.
			$url .= '/loggedin_li.php';
			
			header("Location: $url");
			exit(); // Quit the script.
				
		} else { // No record matched the query.
			$errors[] = 'The email address and password entered do not match those on file.'; // Public message.
			$errors[] = mysqli_error($dbc)  . '<br /><br />Query: ' . $query; // Debugging message.
		}
		
	} // End of if (empty($errors)) IF.
		
	mysqli_close($dbc); // Close the database connection.

} else { // Form has not been submitted.

	$errors = NULL;

} // End of the main Submit conditional.

// Begin the page now.
$page_title = 'Login';
include ('./includes/header.html');

if (!empty($errors)) { // Print any error messages.
	echo '<h1 id="mainhead">Error!</h1>
	<p class="error">The following error(s) occurred:<br />';
	foreach ($errors as $msg) { // Print each error.
		echo " - $msg<br />\n";
	}
	echo '</p><p>Please try again.</p>';
}

// Create the form.
?>
<h2>Login</h2>
<form action="login_li.php" method="post">
	<p>Email Address: <input type="text" name="email" size="20" maxlength="40" /> </p>
	<p>Password: <input type="password" name="password" size="20" maxlength="20" /></p>
	<p><input type="submit" name="submit" value="Login" /></p>
	<input type="hidden" name="submitted" value="TRUE" />
</form>
<?php
include ('./includes/footer.html');
?>