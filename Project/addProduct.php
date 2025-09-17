<?php # Script 7.7 - register.php (3rd version after Scripts 7.3 & 7.5)

session_start();
$page_title = 'Register';
include ('./includes/header.html');

// Check if the form has been submitted.
if (isset($_POST['submitted'])) {

	require_once ('mysqli.php'); // Connect to the db.
		
	global $dbc;


	$errors = array(); // Initialize error array.
	
	// Check for a first name.
	if (empty($_POST['product_id'])) {
		$errors[] = 'You forgot to enter your product id.';
	} else {
		$pi = ($_POST['product_id']);
	}
	
	// Check for a last name.
	if (empty($_POST['product_name'])) {
		$errors[] = 'You forgot to enter your product name.';
	} else {
		$pn = ($_POST['product_name']);
	}
	
	// Check for an email address.
	if (empty($_POST['product_description'])) {
		$errors[] = 'You forgot to enter your product description.';
	} else {
		$pd = ($_POST['product_description']);
	}
	
    if (empty($_POST['product_price'])) {
		$errors[] = 'You forgot to enter your product price.';
	} else {
		$pp = ($_POST['product_price']);
	}

    if (empty($_POST['product_quantity'])) {
		$errors[] = 'You forgot to enter your product quantity.';
	} else {
		$pq = ($_POST['product_quantity']);
	}
	
	// Check for a password and match against the confirmed password.
	
	
	if (empty($errors)) { // If everything's okay.
	
		// Register the user in the database.
		
		// Check for previous registration.
		$query = "SELECT product_name FROM product WHERE product_id='$pi'";
		$result = @mysqli_query ($dbc,$query); // Run the query.
		if (mysqli_num_rows($result) == 0) {

			// Make the query.
			$query = "INSERT INTO product (product_id, product_name, product_description, product_price, product_quantity, registration_date) 
			VALUES ('$pi', '$pn', '$pd', '$pp', '$pq', NOW() )";		
			$result = @mysqli_query ($dbc,$query); // Run the query. // Run the query.
			if ($result) { // If it ran OK. == IF TRUE
			
				// Send an email, if desired.
				
				// Print a message.
				echo '<h1 id="mainhead">Thank you!</h1>
				<p>You are have add the product. </p><p><br /></p>';	
			
				// Include the footer and quit the script (to not show the form).
				include ('./includes/footer.html'); 
				exit();
				
			} else { // If it did not run OK.
				echo '<h1 id="mainhead">System Error</h1>
				<p class="error">Your product could not be added due to a system error. We apologize for any inconvenience.</p>'; // Public message.
				echo '<p>' . mysqli_error($dbc)  . '<br /><br />Query: ' . $query . '</p>'; // Debugging message.
				include ('./includes/footer.html'); 
				exit();
			}
				
		} else { // Already registered.
			echo '<h1 id="mainhead">Error!</h1>
			<p class="error">The product has already been added.</p>';
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
?>
<h2>Add</h2>
<form action="addProduct.php" method="post">
	<p>Product Id: <input type="text" name="product_id" size="15" maxlength="15" value="<?php if (isset($_POST['product_id'])) echo $_POST['product_id']; ?>" /></p>
	<p>Product Name: <input type="text" name="product_name" size="15" maxlength="30" value="<?php if (isset($_POST['product_name'])) echo $_POST['product_name']; ?>" /></p>
	<p>Description:  <textarea name="product_description" rows="4" cols="50" maxlength="250"><?php if (isset($_POST['product_description']))
	 echo htmlspecialchars($_POST['product_description']); ?></textarea>
    <p>Price: <input type="text" name="product_price" size="20" maxlength="40" value="<?php if (isset($_POST['product_price'])) echo $_POST['product_price']; ?>"  /> </p>
    <p>Quantity: <input type="text" name="product_quantity" size="20" maxlength="40" value="<?php if (isset($_POST['product_quantity'])) echo $_POST['product_quantity']; ?>"  /> </p>
	
	<p><input type="submit" name="submit" value="Add" /> &nbsp;&nbsp;
    <button type="button" onclick="history.back()">Cancel</button>
	<input type="hidden" name="submitted" value="TRUE" />
</form>
<?php
include ('./includes/footer.html');
?>