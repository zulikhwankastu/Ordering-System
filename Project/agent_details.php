<?php
session_start();

// Set the page title and include the HTML header.
$page_title = 'Agent Details';
include('./includes/header.html');

// Check if the form has been submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once('mysqli.php'); // Connect to the db.

    global $dbc;

    $errors = array(); // Initialize error array.

    // Check for an agent ID.
    if (empty($_POST['agent_id'])) {
        $errors[] = 'Agent ID is required.';
    } else {
        $agent_id = $_POST['agent_id'];
    }

    // Check for first name.
    if (empty($_POST['first_name'])) {
        $errors[] = 'First Name is required.';
    } else {
        $first_name = $_POST['first_name'];
    }

    // Check for last name.
    if (empty($_POST['last_name'])) {
        $errors[] = 'Last name is required.';
    } else {
        $last_name = $_POST['last_name'];
    }

    // Validate email.
    if (empty($_POST['email'])) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email format.';
    } else {
        $email = $_POST['email'];
    }

    // Check for password.
    if (empty($_POST['password'])) {
        $errors[] = 'Password is required.';
    } else {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password.
    }

    if (empty($errors)) { // If everything's OK.

        // Make the UPDATE query.
        $query = "UPDATE agent 
                  SET 
                  first_name = '$first_name', 
                  last_name = '$last_name', 
                  email = '$email', 
                  password = '$password' 
                  WHERE agent_id = '$agent_id'";

        $result = @mysqli_query($dbc, $query); // Run the query.

        if ($result) { // If it ran OK.

            // Print a message.
            echo '<h1 id="mainhead">Thank you!</h1>
                <p>Agent details have been updated.</p><p><br /></p>';

            // Include the footer and quit the script (to not show the form).
            include('./includes/footer.html');
            exit();

        } else { // If it did not run OK.
            echo '<h1 id="mainhead">System Error</h1>
                <p class="error">Agent details could not be updated due to a system error. We apologize for any inconvenience.</p>'; // Public message.
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; // Debugging message.
            include('./includes/footer.html');
            exit();
        }
    } else { // Report the errors.

        echo '<h1 id="mainhead">Error!</h1>
            <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) { // Print each error.
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    } // End of if (empty($errors)) IF.

    mysqli_close($dbc); // Close the database connection.
} // End of the main Submit conditional.

// Fetch existing agent details for display.
require_once('mysqli.php'); // Connect to the db.
global $dbc;



$query = "SELECT agent_id, first_name, last_name, email, password FROM agent";
$result = @mysqli_query($dbc, $query);

if ($result) {
    // Display existing agent details in a table.
    echo '<h2>Existing Agent Details</h2>';
    echo '<table border="1">
            <tr>
                <th>Agent ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Password</th>
            </tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>
                <td>' . $row['agent_id'] . '</td>
                <td>' . $row['first_name'] . '</td>
                <td>' . $row['last_name'] . '</td>
                <td>' . $row['email'] . '</td>
                <td>' . $row['password'] . '</td>
              </tr>';
    }

    echo '</table>';
} else {
    echo '<p>No existing agent details found.</p>';
}

?>

<h2>Update Agent Details</h2>
<form action="agent_details.php" method="post">
    <p>Agent ID: <input type="text" name="agent_id" size="10" maxlength="10" value="<?php if (isset($_POST['agent_id'])) echo $_POST['agent_id']; ?>" /></p>
    <p>First Name: <input type="text" name="first_name" size="20" maxlength="35" value="<?php if (isset($_POST['first_name'])) echo $_POST['first_name']; ?>" /></p>
    <p>Last Name: <input type="text" name="last_name" size="20" maxlength="40" value="<?php if (isset($_POST['last_name'])) echo $_POST['last_name']; ?>" /></p>
    <p>Email: <input type="text" name="email" size="30" maxlength="50" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>" /></p>
    <p>Password: <input type="password" name="password" size="20" maxlength="40" value="<?php if (isset($_POST['password'])) echo $_POST['password']; ?>" /></p>
    <p><input type="submit" name="submit" value="Update Agent" /></p>
</form>

<?php
include('./includes/footer.html');
?>
