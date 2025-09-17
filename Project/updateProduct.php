<?php
// Set the page title and include the HTML header.
$page_title = 'Update Product Details';
include ('./includes/header.html');

// Check if the form has been submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require_once('mysqli.php'); // Connect to the db.

    global $dbc;

    $errors = array(); // Initialize error array.

    // Check for a product ID.
    if (empty($_POST['product_id'])) {
        $errors[] = 'Product ID is required.';
    } else {
        $product_id = $_POST['product_id'];
    }

    // Check for a product name.
    if (empty($_POST['product_name'])) {
        $errors[] = 'Product Name is required.';
    } else {
        $product_name = $_POST['product_name'];
    }

    // Check for a product description.
    if (empty($_POST['product_description'])) {
        $errors[] = 'Product Description is required.';
    } else {
        $product_description = $_POST['product_description'];
    }

    // Check for a product price.
    if (empty($_POST['product_price'])) {
        $errors[] = 'Product Price is required.';
    } else {
        $product_price = $_POST['product_price'];
    }

    // Check for a product quantity.
    if (empty($_POST['product_quantity'])) {
        $errors[] = 'Product Quantity is required.';
    } else {
        $product_quantity = $_POST['product_quantity'];
    }

    if (empty($errors)) { // If everything's OK.

        // Make the UPDATE query.
        $query = "UPDATE product 
                  SET 
                  product_name = '$product_name', 
                  product_description = '$product_description', 
                  product_price = '$product_price', 
                  product_quantity = '$product_quantity' 
                  WHERE product_id = '$product_id'";

        $result = @mysqli_query($dbc, $query); // Run the query.

        if ($result) { // If it ran OK.

            // Print a message.
            echo '<h1 id="mainhead">Thank you!</h1>
                <p>Product details have been updated.</p><p><br /></p>';

            // Include the footer and quit the script (to not show the form).
            include('./includes/footer.html');
            exit();

        } else { // If it did not run OK.
            echo '<h1 id="mainhead">System Error</h1>
                <p class="error">Product details could not be updated due to a system error. We apologize for any inconvenience.</p>'; // Public message.
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
?>

<h2>Update Product Details</h2>
<form action="updateProduct.php" method="post">
    <p>Product ID: <input type="text" name="product_id" size="10" maxlength="10" value="<?php if (isset($_POST['product_id'])) echo $_POST['product_id']; ?>" /></p>
    <p>Product Name: <input type="text" name="product_name" size="20" maxlength="35" value="<?php if (isset($_POST['product_name'])) echo $_POST['product_name']; ?>" /></p>
    <p>Product Description: <input type="text" name="product_description" size="20" maxlength="40" value="<?php if (isset($_POST['product_description'])) 
    echo $_POST['product_description']; ?>" /></p>
    <p>Product Price: <input type="text" name="product_price" size="10" maxlength="40" value="<?php if (isset($_POST['product_price'])) echo $_POST['product_price']; ?>" /></p>
    <p>Product Quantity: <input type="text" name="product_quantity" size="10" maxlength="40" value="<?php if (isset($_POST['product_quantity'])) echo $_POST['product_quantity']; ?>" /></p>
    <p><input type="submit" name="submit" value="Update Product" /></p>
</form>

<?php
include('./includes/footer.html');
?>
