<?php
// Include your database connection.
session_start();

$page_title = 'Discount Management';
include('./includes/header.html');

require_once('mysqli.php');
global $dbc;

// Check if the form is submitted to set discounts
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $discount_id = mysqli_real_escape_string($dbc, $_POST['discount_id']);
    $product_id = mysqli_real_escape_string($dbc, $_POST['product_id']);

    // Loop through the submitted ranges
    foreach ($_POST['min_quantity'] as $index => $min_quantity) {
        $max_quantity = intval($_POST['max_quantity'][$index]);
        $discount_percentage = floatval($_POST['discount_percentage'][$index]);
        $start_date = mysqli_real_escape_string($dbc, $_POST['start_date'][$index]);
        $end_date = mysqli_real_escape_string($dbc, $_POST['end_date'][$index]);

        // Insert discount details into the database.
        $insert_discount_query = "INSERT INTO discounts (discount_id, product_id, min_quantity, max_quantity, discount_percentage, start_date, end_date) 
                                  VALUES ('$discount_id', '$product_id', '$min_quantity', '$max_quantity', '$discount_percentage', '$start_date', '$end_date')";
        $insert_discount_result = mysqli_query($dbc, $insert_discount_query);

        if (!$insert_discount_result) {
            echo '<p class="error">Error setting discount: ' . mysqli_error($dbc) . '</p>';
        }
    }

    echo '<p>Discounts set successfully!</p>';
}

// Display form to set discounts
echo '<h2>Set Discounts</h2>';
echo '<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
        <label for="discount_id">Discount ID:</label>
        <input type="text" name="discount_id" required>
        <br>
        <label for="product_id">Product ID:</label>
        <input type="text" name="product_id" required>
        <br>';

// Add fields for multiple discount ranges dynamically
echo '<div id="discountRanges">';
echo '<div class="discountRange">';
echo '<label>Range 1:</label> <br>';
echo '<label for="min_quantity[]">Minimum Quantity:</label><input type="number" name="min_quantity[]" required> <br>';
echo '<label for="max_quantity[]">Maximum Quantity:</label><input type="number" name="max_quantity[]" required> <br>';
echo '<label for="discount_percentage[]">Discount Percentage:</label><input type="number" name="discount_percentage[]" step="0.01" required> <br>';
echo '<label for="start_date[]">Start Date:</label><input type="date" name="start_date[]" required> <br>';
echo '<label for="end_date[]">End Date:</label><input type="date" name="end_date[]" required> <br>';
echo '</div>';
echo '</div>';


echo '<br>';
echo '<button type="submit">Set Discounts</button>';
echo '</form>';

$select_discounts_query = "SELECT * FROM discounts";
$select_discounts_result = mysqli_query($dbc, $select_discounts_query);

if ($select_discounts_result) {
    echo '<h2>Discounts Information</h2>';
    echo '<table border="1">
            <tr>
                <th>Discount ID</th>
                <th>Product ID</th>
                <th>Min Quantity</th>
                <th>Max Quantity</th>
                <th>Discount Percentage</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Delete Discount</th>
            </tr>';

    while ($row = mysqli_fetch_assoc($select_discounts_result)) {
        echo '<tr>
                <td>' . $row['discount_id'] . '</td>
                <td>' . $row['product_id'] . '</td>
                <td>' . $row['min_quantity'] . '</td>
                <td>' . $row['max_quantity'] . '</td>
                <td>' . $row['discount_percentage'] . '</td>
                <td>' . $row['start_date'] . '</td>
                <td>' . $row['end_date'] . '</td>
                <td><form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
                    <input type="hidden" name="delete_discount_id" value="' . $row['discount_id'] . '">
                    <button type="submit">Delete</button>
                </form></td>
              </tr>';
    }

    echo '</table>';
} else {
    echo '<p class="error">Error fetching discounts information: ' . mysqli_error($dbc) . '</p>';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_discount_id'])) {
    $delete_discount_id = mysqli_real_escape_string($dbc, $_POST['delete_discount_id']);

    // Delete discount from the database
    $delete_discount_query = "DELETE FROM discounts WHERE discount_id = '$delete_discount_id'";
    $delete_discount_result = mysqli_query($dbc, $delete_discount_query);

    if (!$delete_discount_result) {
        echo '<p class="error">Error deleting discount: ' . mysqli_error($dbc) . '</p>';
    } else {
        echo '<p>Discount deleted successfully!</p>';
    }
}
// Close the database connection.
mysqli_close($dbc);
?>
