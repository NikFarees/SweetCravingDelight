<html>

<head>
    <title>Order Approval</title>
</head>

<body>
    <?php
    include('../includes/headerSupplier.html');
    require_once('../mysqli.php'); // Connect to the db.
    global $dbc;

    session_start(); // Start the session

    // Check if supplier is logged in
    if (!isset($_SESSION['supplier_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }

    // Retrieving supplier ID from the session
    $supplier_id = $_SESSION['supplier_id'];

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (order_id LIKE '%$searchQuery%' OR orders.custName LIKE
'%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT orders.order_id, orders.agent_id, orders.product_id,
orders.orderQuantity, orders.custName, orders.custAddress, orders.custPhone, orders.orderDate
FROM orders
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE approval_status = 'pending' AND supplier_id = '$supplier_id'" . $searchCondition;
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);

    echo '<br><h2>List of Pending Orders <br /><br /></h2>';
    if ($num > 0) { // If it ran OK, display the records.
        // Table header.
        echo '<a href="ApproveOrder.php" style="color: blue; text-decoration:
underline;">Approve Pending Order</a><br />
 <br><a href="RejectOrder.php" style="color: blue; text-decoration: underline;">Reject
Pending Order</a><br /><br />

 <form method="GET" action="OrderApprovalList.php">
 <input type="text" name="search" placeholder="Search by Order ID or Customer
Name" value="' . $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>Order Id</b> </td>
 <td> <b>Agent Id</b> </td>
 <td> <b>Product Id</b> </td>
 <td> <b>Customer Name</b> </td>
 <td> <b>Customer Address</b> </td>
 <td> <b>Customer Phone</b> </td>
 <td> <b>Quantity</b> </td>
 <td> <b>Date Order</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['order_id'] . '</td>'
                . '<td>' . $row['agent_id'] . '</td>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['custName'] . '</td>'
                . '<td>' . $row['custAddress'] . '</td>'
                . '<td>' . $row['custPhone'] . '</td>'
                . '<td>' . $row['orderQuantity'] . '</td>'
                . '<td>' . $row['orderDate'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error">Pending order not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>