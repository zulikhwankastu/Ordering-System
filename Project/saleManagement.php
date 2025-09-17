<?php
// Include your database connection.
session_start();

$page_title = 'Sale Management';
include('./includes/header.html');

require_once('mysqli.php');
global $dbc;

// View sales performance by agent
$view_sales_by_agent_query = "SELECT agent_id, COUNT(s.sale_id) AS total_sales, SUM(ps.order_quantity) AS 
total_products_sold, SUM(p.product_price * ps.order_quantity) AS total_price_sold, SUM(ps.total_price) AS total_price_after_discount
                             FROM sale s
                             JOIN product_sale ps ON s.sale_id = ps.sale_id
                             JOIN product p ON ps.product_id = p.product_id
                             WHERE ps.status = 'Approved'
                             GROUP BY agent_id";
$view_sales_by_agent_result = mysqli_query($dbc, $view_sales_by_agent_query);

if ($view_sales_by_agent_result) {
    // Display sales performance by agent
    echo '<h2>Sales Performance by Agent</h2>';
    echo '<table border="1">
            <tr>
                <th>Agent ID</th>
                <th>Total Sales</th>
                <th>Total Products Sold</th>
                <th>Total Price Sold</th>
                <th>Total Price After Discount</th>
            </tr>';
    while ($row = mysqli_fetch_assoc($view_sales_by_agent_result)) {
        echo '<tr>
                <td>' . $row['agent_id'] . '</td>
                <td>' . $row['total_sales'] . '</td>
                <td>' . $row['total_products_sold'] . '</td>
                <td>' . $row['total_price_sold'] . '</td>
                <td>' . $row['total_price_after_discount'] . '</td>
              </tr>';
    }
    echo '</table>';
} else {
    echo '<p class="error">Error fetching sales performance by agent: ' . mysqli_error($dbc) . '</p>';
}

// View total sales performance
$view_total_sales_query = "SELECT COUNT(s.sale_id) AS total_sales, SUM(ps.order_quantity) AS total_products_sold,
 SUM(p.product_price * ps.order_quantity) AS total_price_sold, SUM(ps.total_price) AS total_price_after_discount
                          FROM sale s
                          JOIN product_sale ps ON s.sale_id = ps.sale_id
                          JOIN product p ON ps.product_id = p.product_id
                          WHERE ps.status = 'Approved'
                          ";
$view_total_sales_result = mysqli_query($dbc, $view_total_sales_query);

if ($view_total_sales_result) {
    // Display total sales performance
    echo '<h2>Total Sales Performance</h2>';
    echo '<table border="1">
            <tr>
                <th>Total Sales</th>
                <th>Total Products Sold</th>
                <th>Total Price Sold</th>
                <th>Total Price After Discount</th>
            </tr>';
    $total_sales_row = mysqli_fetch_assoc($view_total_sales_result);
    echo '<tr>
            <td>' . $total_sales_row['total_sales'] . '</td>
            <td>' . $total_sales_row['total_products_sold'] . '</td>
            <td>' . $total_sales_row['total_price_sold'] . '</td>
            <td>' . $total_sales_row['total_price_after_discount'] . '</td>
          </tr>';
    echo '</table>';
} else {
    echo '<p class="error">Error fetching total sales performance: ' . mysqli_error($dbc) . '</p>';
}

// Close the database connection.
mysqli_close($dbc);
?>
