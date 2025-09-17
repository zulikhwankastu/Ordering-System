<?php
// Start the session.
session_start();

$page_title = 'Items to Restock';
include ('./includes/header.html');

echo "<h1 id='mainhead'>Items to Restock</h1>";

// Check if user_id is set in the session.
if (isset($_SESSION['user_id'])) {
    echo "<p>Welcome, {$_SESSION['first_name']}!<br/> Your user ID is {$_SESSION['user_id']}.</p>";
} else {
    echo "<p>Welcome";
}

require_once('mysqli.php'); // Connect to the database.
global $dbc;

// Check if the form is submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Loop through the posted data to update the product quantities.
    foreach ($_POST['restock'] as $product_id => $restock_quantity) {
        // Validate the restock quantity (you might want to add more validation).
        $restock_quantity = (int)$restock_quantity;

        // Update the product quantity in the database.
        $update_query = "UPDATE product SET product_quantity = product_quantity + $restock_quantity WHERE product_id = '$product_id'";
        $update_result = mysqli_query($dbc, $update_query);

        if (!$update_result) {
            echo '<p class="error">Error updating product quantity: ' . mysqli_error($dbc) . '</p>';
        }
    }
}

// Make the query to get items to restock.
$query = "SELECT product_id, product_name, product_quantity FROM product WHERE product_quantity < 10 ORDER BY product_quantity ASC";
$result = mysqli_query($dbc, $query);

if ($result) {
    $num = mysqli_num_rows($result);

    if ($num > 0) {
        // Display the restock form.
        echo '<form method="post" action="Restock.php">';
        echo "<p>There are currently $num items to restock.</p>\n";

        // Table header.
        echo '<table align="center" cellspacing="0" cellpadding="5">
                <tr>
                    <td align="left"><b>Product ID</b></td>
                    <td align="left"><b>Product Name</b></td>
                    <td align="left"><b>Current Quantity</b></td>
                    <td align="left"><b>Restock Quantity</b></td>
                </tr>';

        // Fetch and print all the records.
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>
                    <td align="left">' . $row['product_id'] . '</td>
                    <td align="left">' . $row['product_name'] . '</td>
                    <td align="left">' . $row['product_quantity'] . '</td>
                    <td align="left"><input type="number" name="restock[' . $row['product_id'] . ']" value="0" min="0"></td>
                  </tr>';
        }

        echo '</table>';
        echo '<p><input type="submit" value="Restock"></p>';
        echo '</form>';
    } else {
        echo '<p>No items currently need restocking.</p>';
    }

    mysqli_free_result($result); // Free up the resources.
} else {
    echo '<p class="error">Error fetching items to restock: ' . mysqli_error($dbc) . '</p>';
}

mysqli_close($dbc); // Close the database connection.

include ('./includes/footer.html'); // Include the HTML footer.
?>
