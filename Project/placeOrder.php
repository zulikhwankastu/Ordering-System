<?php
// Start the session.
session_start();

$page_title = 'Order Management';
include('./includes/header_agent.html');

// Include your database connection.
require_once('mysqli.php');
global $dbc;

// Function to calculate total price with discounts
function calculateTotalPrice($product_id, $order_quantity, $dbc) {
    // Fetch the unit price of the product
    $query = "SELECT product_price FROM product WHERE product_id = '$product_id'";
    $result = mysqli_query($dbc, $query);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $unit_price = floatval($row['product_price']);

        // Check if there are any applicable discounts
        $discount_percentage = 0.00; // Default value
        $discount_query = "SELECT discount_percentage FROM discounts 
                           WHERE product_id = '$product_id' 
                           AND $order_quantity BETWEEN min_quantity AND max_quantity 
                           AND CURDATE() BETWEEN start_date AND end_date";
        $discount_result = mysqli_query($dbc, $discount_query);

        if ($discount_result && mysqli_num_rows($discount_result) > 0) {
            $discount_row = mysqli_fetch_assoc($discount_result);
            $discount_percentage = floatval($discount_row['discount_percentage']);
        }

        // Calculate total price before discount
        $total_price = ($unit_price * $order_quantity);
        $total_price_after_discount = $total_price * (1 - $discount_percentage);
       
       

        
       
        return $total_price_after_discount;
    } else {
        return false;
    }
}

// Check if the form is submitted.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Assuming you have received sale details.
    $sale_id = mysqli_real_escape_string($dbc, $_POST['sale_id']);
    $agent_id = mysqli_real_escape_string($dbc, $_POST['agent_id']);
    $customer_name = mysqli_real_escape_string($dbc, $_POST['customer_name']);
    $address = mysqli_real_escape_string($dbc, $_POST['address']);
    $contact_number = mysqli_real_escape_string($dbc, $_POST['contact_number']);

    // Insert order details into the database.
    $insert_sale_query = "INSERT INTO sale (sale_id, agent_id, customer_name, customer_address, customer_contact, sale_date, status) 
                         VALUES ('$sale_id', '$agent_id', '$customer_name', '$address', '$contact_number', NOW(), 'Pending Approval')";
    $insert_sale_result = mysqli_query($dbc, $insert_sale_query);

    if ($insert_sale_result) {
        // Assuming there's only one product input field now.
        $product_id = mysqli_real_escape_string($dbc, $_POST['product_id']);
        $order_quantity = intval($_POST['order_quantity']);

        // Check stock availability for the product.
        $query = "SELECT product_quantity FROM product WHERE product_id = '$product_id'";
        $result = mysqli_query($dbc, $query);

        if ($result) {
            $row = mysqli_fetch_assoc($result);
            $available_quantity = intval($row['product_quantity']);

            if ($order_quantity <= $available_quantity) {
                // Stock is available, proceed with the order.

                // Calculate total price with discounts
                $total_price = calculateTotalPrice($product_id, $order_quantity, $dbc);

                if ($total_price !== false) {
                    // Insert product details into the database.
                    $insert_product_query = "INSERT INTO product_sale (sale_id, product_id, order_quantity, date, status,  total_price) 
                                            VALUES ('$sale_id', '$product_id', '$order_quantity', NOW(), 'Pending Approval',  '$total_price')";
                    $insert_product_result = mysqli_query($dbc, $insert_product_query);

                    if (!$insert_product_result) {
                        echo '<p class="error">Error recording product details: ' . mysqli_error($dbc) . '</p>';
                    } else {
                        echo "<p>Order confirmed and details recorded successfully! Total Price: $total_price</p>";
                    }
                } else {
                    echo '<p class="error">Error calculating total price: ' . mysqli_error($dbc) . '</p>';
                }
            } else {
                // Insufficient stock for the product.
                echo "<p>Insufficient stock for Product ID: $product_id. Please choose a lower quantity.</p>";
            }
        } else {
            // Error fetching stock information.
            echo '<p class="error">Error fetching stock information: ' . mysqli_error($dbc) . '</p>';
        }
    } else {
        echo '<p class="error">Error recording sale details: ' . mysqli_error($dbc) . '</p>';
    }
}

// Close the database connection.
mysqli_close($dbc);
?>

<h2>Order Confirmation</h2>
<form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
    <label for="sale_id">Sale ID:</label>
    <input type="text" name="sale_id" required>
    <br>
    <label for="agent_id">Agent ID:</label>
    <input type="text" name="agent_id" required>
    <br>
    <label for="product_id">Product ID:</label>
    <input type="text" name="product_id" required>
    <br>
    <label for="order_quantity">Order Quantity:</label>
    <input type="number" name="order_quantity" required>
    <br>
    <label for="customer_name">Customer Name:</label>
    <input type="text" name="customer_name" required>
    <br>
    <label for="address">Address:</label>
    <textarea name="address" required></textarea>
    <br>
    <label for="contact_number">Contact Number:</label>
    <input type="text" name="contact_number" required>
    <br>
    <button type="submit">Confirm Order</button>
</form>

<?php
include('./includes/footer.html');
?>
