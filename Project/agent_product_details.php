<?php # Script 7.6 - view_users.php (2nd version after Script 7.4)
// This script retrieves all the records from the users table.

$page_title = 'View the Current Product';
include ('./includes/header_agent.html');

// Page header.
echo '<h1 id="mainhead">Product Details</h1>';

require_once ('mysqli.php'); // Connect to the db.
global $dbc;
		
// Make the query.
$query = "SELECT product_id, product_name, product_description, product_price, product_quantity, 
                  DATE_FORMAT(registration_date, '%M %d, %Y') AS registration_date 
          FROM product
          ORDER BY registration_date DESC";
$result = mysqli_query($dbc, $query);

if ($result) {
    $num = mysqli_num_rows($result);

    echo "<p>There are currently $num products.</p>\n";

    // Table header.
    echo '<table align="center" cellspacing="0" cellpadding="5">
            <tr>
                <td align="left"><b>Product ID</b></td>
                <td align="left"><b>Product Name</b></td>
                <td align="left"><b>Product Description</b></td>
                <td align="left"><b>Product Price</b></td>
                <td align="left"><b>Product Quantity</b></td>
                <td align="left"><b>Registration Date</b></td>
            </tr>';

    // Fetch and print all the records.
    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
        echo '<tr>
                <td align="left">' . $row['product_id'] . '</td>
                <td align="left">' . $row['product_name'] . '</td>
                <td align="left">' . $row['product_description'] . '</td>
                <td align="left">' . $row['product_price'] . '</td>
                <td align="left">' . $row['product_quantity'] . '</td>
                <td align="left">' . $row['registration_date'] . '</td>
              </tr>';
    }

    echo '</table>';

    mysqli_free_result($result); // Free up the resources.

} else {
    echo '<p class="error">There are currently no products.</p>';
}

@mysqli_close($dbc); // Close the database connection.


?>

<?php
include ('./includes/footer.html');
?>