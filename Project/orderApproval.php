<?php
// Start the session.
session_start();

$page_title = 'Order Approval';
include('./includes/header.html');
require_once('mysqli.php'); // Connect to the db.
global $dbc;

// Fetch only pending orders
$query = "SELECT ps.*, p.product_name, p.product_price
          FROM product_sale ps
          JOIN product p ON ps.product_id = p.product_id
          WHERE ps.status = 'Pending Approval'";
$result = mysqli_query($dbc, $query); // Run the query.

if ($result) {
    $num = mysqli_num_rows($result);

    if ($num > 0) {
        // Table styling.
        echo '<table class="border-table" align="center" cellspacing="0" cellpadding="5">
                        <tr>
                            <th>Sale ID</th>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Order Quantity</th>
                            <th>Sale Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>';

        // Fetch and print all the records.
        while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>';
            echo '<td>' . $row['sale_id'] . '</td>';
            echo '<td>' . $row['product_id'] . '</td>';
            echo '<td>' . $row['product_name'] . '</td>';
            echo '<td>' . $row['order_quantity'] . '</td>';
            echo '<td>' . $row['date'] . '</td>';
            echo '<td>' . $row['status'] . '</td>';
            echo '<td>
            <form method="post" action="">
            <input type="hidden" name="sale_id" value="' . $row['sale_id'] . '">
            <button type="submit" name="approve" value="' . $row['sale_id'] . '">Approve</button>
        </form>
        <form method="post" action="">
            <input type="hidden" name="sale_id" value="' . $row['sale_id'] . '">
            <button type="submit" name="decline" value="' . $row['sale_id'] . '">Decline</button>
        </form>
        
                </td>';
            echo '</tr>';
        }

        echo '</table>';

        mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<p class="error">There are currently no pending orders.</p>';
    }
} else {
    // If there was an error in the query execution.
    echo '<p><font color="red">MySQL Error: ' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</font></p>';
}
?>


<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve']) && !empty($_POST['approve'])) {
        $approved = $_POST['approve'];

        // Retrieve the necessary information
        $quantity_query = "SELECT order_quantity, product_id FROM product_sale WHERE sale_id = '$approved'";
        $quantity_result = mysqli_query($dbc, $quantity_query);

        if ($quantity_result) {
            $quantity_row = mysqli_fetch_assoc($quantity_result);

            if ($quantity_row) {
                $order_quantity = $quantity_row['order_quantity'];
                $product_id = $quantity_row['product_id'];

                // Update the product table by subtracting the quantity
                $update_product_query = "UPDATE product SET product_quantity = CASE WHEN product_quantity - $order_quantity > 0 
                THEN product_quantity - $order_quantity ELSE 0 END WHERE product_id = '$product_id'";
                $update_product_result = mysqli_query($dbc, $update_product_query);

                if ($update_product_result) {
                    // Update the order status to 'Approved'
                    mysqli_query($dbc, "UPDATE product_sale SET status = 'Approved' WHERE sale_id = '$approved'");
                    echo '<p>Order approved successfully!</p>';
                } else {
                    echo '<p><font color="red">Error updating product quantity: ' . mysqli_error($dbc) . '</font></p>';
                }
            } else {
                echo '<p>Quantity information not found for the selected sale.</p>';
            }
        } else {
            echo '<p><font color="red">Error fetching quantity information: ' . mysqli_error($dbc) . '</font></p>';
        }
    } else {
        
    }

    if (isset($_POST['decline']) && !empty($_POST['decline'])) {
        $declined = $_POST['decline'];

        // Update the order status to 'Declined'
        mysqli_query($dbc, "UPDATE product_sale SET status = 'Declined' WHERE sale_id = '$declined'");
        echo '<p>Order declined successfully!</p>';
    }
}

include('./includes/footer.html');
?>
