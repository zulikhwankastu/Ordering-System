<?php # Script 7.6 - view_users.php (2nd version after Script 7.4)
// This script retrieves all the records from the users table.

session_start(); // Start the session.

// If no session value is present, redirect the user.
if (!isset($_SESSION['user_id'])) {

	// Start defining the URL.
	$url = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
	// Check for a trailing slash.
	if ((substr($url, -1) == '/') OR (substr($url, -1) == '\\') ) {
		$url = substr ($url, 0, -1); // Chop off the slash.
	}
	$url .= '/index.php'; // Add the page.
	header("Location: $url");
	exit(); // Quit the script.
}
$page_title = 'View the Current Users';
include ('./includes/header.html');
// Page header.
echo '<h1 id="mainhead">Registered Users</h1>';

echo "<p>You are = {$_SESSION['first_name']}<br/> Id = {$_SESSION['user_id']}!</p>";

require_once ('mysqli.php'); // Connect to the db.
global $dbc;
		
// Make the query.
$query = "SELECT CONCAT(last_name, ', ', first_name) AS name, DATE_FORMAT(registration_date, '%M %d, %Y') 
		AS dr FROM users ORDER BY registration_date DESC";		
$result = @mysqli_query ($dbc,$query);// Run the query.
$num = @mysqli_num_rows($result);// OR die ('SQL Statement: ' . mysqli_error($dbc) );

if ($num > 0) { // If it ran OK, display the records.

	echo "<p>There are currently $num registered users.</p>\n";

	// Table header.
	echo '<table align="center" cellspacing="0" cellpadding="5">
	<tr><td align="left"><b>Name</b></td><td align="left"><b>Date Registered</b></td></tr>';
	
	// Fetch and print all the records.
	while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
		echo '<tr><td align="left">' . $row['name'] . '</td><td align="left">' . $row['dr'] . '</td></tr>';
	}

	echo '</table>';
	
	@mysqli_free_result($result); // Free up the resources.	

} else { // If it did not run OK.
	echo '<p class="error">There are currently no registered users.</p>';
}

@mysqli_close($dbc); // Close the database connection.

include ('./includes/footer.html'); // Include the HTML footer.
?>