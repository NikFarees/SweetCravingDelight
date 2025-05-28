<html>

<head>
    <title>List Of Order Pending Approval</title>
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
        $searchCondition = " AND (orders.order_id LIKE '%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT orders.order_id, orders.agent_id, orders.product_id,
orders.orderQuantity, orders.custName, orders.custAddress, orders.custPhone, orders.orderDate,
orders.approval_date FROM orders
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE approval_status = 'approved' AND supplier_id = '$supplier_id'" .
        $searchCondition;
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);

    echo '<br><h2>History<br /><br /></h2>
 <a href="AgentApprovalHistory.php" style="color: blue; text-decoration:
underline;">Agent Approval History</a><br />
 <br><a href="OrderApprovalHistory.php" style="color: blue; text-decoration:
underline;">Order Approval History</a><br />
 <br><a href="StockHistory.php" style="color: blue; text-decoration: underline;">Stock
History</a><br />';
    if ($num > 0) { // If it ran OK, display the records.

        // Table header.
        echo '<br><h2>List of Approved Order <br /><br /></h2>

 <form method="GET" action="OrderApprovalHistory.php">
 <input type="text" name="search" placeholder="Search by Order ID" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>Order Id</b> </td>
 <td> <b>Agent Id</b> </td>
 <td> <b>Product Id</b> </td>
 <td> <b>Quantity</b> </td>
 <td> <b>Date Order</b> </td>
 <td> <b>Approval Date</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['order_id'] . '</td>'
                . '<td>' . $row['agent_id'] . '</td>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['orderQuantity'] . '</td>'
                . '<td>' . $row['orderDate'] . '</td>'
                . '<td>' . $row['approval_date'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>Approval order not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>