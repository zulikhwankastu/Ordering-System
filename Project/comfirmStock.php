<?php
// Start the session.
session_start();

$page_title = 'Confirm Stock Availability';
include('./includes/header_agent.html');

// Check if the form is submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h2>Product Availability Confirmation</h2>";
    echo '<table align="center" cellspacing="0" cellpadding="5">
            <tr>
                <td align="left"><b>Product ID</b></td>
                <td align="left"><b>Product Name</b></td>
                <td align="left"><b>Available Quantity</b></td>
                <td align="left"><b>Order Quantity</b></td>
                <td align="left"><b>Status</b></td>
            </tr>';

    // Check if product_id and order_quantity are set and are arrays.
    if (isset($_POST['product_id']) && isset($_POST['order_quantity']) &&
        is_array($_POST['product_id']) && is_array($_POST['order_quantity'])) {

        // Loop through each product entry.
        for ($i = 0; $i < count($_POST['product_id']); $i++) {
            // Assuming you have received product_id and quantity from the order form.
            $product_id = $_POST['product_id'][$i];
            $order_quantity = $_POST['order_quantity'][$i];

            require_once('mysqli.php'); // Include your database connection.
            global $dbc;

            // Check stock availability.
            $query = "SELECT product_name, product_quantity FROM product WHERE product_id = '$product_id'";
            $result = mysqli_query($dbc, $query);

            if ($result) {
                $row = mysqli_fetch_assoc($result);
                $product_name = $row['product_name'];
                $available_quantity = $row['product_quantity'];

                $status = "Insufficient Stock";
                $statusClass = "error";

                if ($order_quantity <= $available_quantity) {
                    // Stock is available, proceed with the order.
                    $status = "Available stock";
                    $statusClass = "success";
                    
                }

                // Display confirmation details in the table.
                echo '<tr>
                        <td align="left">' . $product_id . '</td>
                        <td align="left">' . $product_name . '</td>
                        <td align="left">' . $available_quantity . '</td>
                        <td align="left">' . $order_quantity . '</td>
                        <td align="left" class="' . $statusClass . '">' . $status . '</td>
                      </tr>';
            } else {
                // Error fetching stock information.
                echo '<tr>
                        <td colspan="5" class="error">Error fetching stock information: ' . mysqli_error($dbc) . '</td>
                      </tr>';
            }

            mysqli_close($dbc); // Close the database connection.
        }
    } else {
        // No product entries submitted.
        echo '<tr>
                <td colspan="5" class="error">No product entries submitted.</td>
              </tr>';
    }

    echo '</table>';

    // Show "Place Order" button if the confirmation form has been submitted.
    
   
}

// Display the form for entering product details.
?>

<h2>Confirm Stock for Product</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <?php
    // Display product entry fields based on the number of products the user wants to check.
    $numProducts = isset($_POST['num_products']) ? $_POST['num_products'] : 1;
    for ($i = 0; $i < $numProducts; $i++) {
        echo '<div class="product-entry">
                <label for="product_id[]">Product ID:</label>
                <input type="text" name="product_id[]" required>
                <br>
                <label for="order_quantity[]">Order Quantity:</label>
                <input type="number" name="order_quantity[]" required>
            </div>';
    }
    ?>
    <button type="submit">Confirm available stock</button>
</form>

<?php
include('./includes/footer.html');
?>
