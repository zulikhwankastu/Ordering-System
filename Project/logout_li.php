<?php # Script 9.9 - logout.php (2nd version after Script 9.4)
// This page lets the user logout.

session_start(); // Access the existing session.

// If no session variable exists, redirect the user.
if (!($_SESSION['user_id'])) {

	// Start defining the URL.
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	// Check for a trailing slash.
	if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
		$url = substr ($url, 0, -1); // Chop off the slash.
	}
	$url .= '/index.php'; // Add the page.
	header("Location: $url");
	exit(); // Quit the script.

} else { // Cancel the session.
	$_SESSION = array(); // Destroy the variables.
	session_destroy(); // Destroy the session itself.
	setcookie ('PHPSESSID', '', time()-300, '/', '', 0); // Destroy the cookie.
}

// Set the page title and include the HTML header.
$page_title = 'Logged Out!';
include ('./includes/header.html');

// Print a customized message.
echo "<h1>Logged Out!</h1>
<p>You are now logged out!</p>
<p><br /><br /></p>";

include ('./includes/footer.html');
?>