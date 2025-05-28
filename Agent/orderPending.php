<html>

<head>
    <title>Agent Approval Pending</title>
</head>

<body>
    <?php
    include('../includes/headerAgent.html');
    require_once('../mysqli.php'); // Connect to the db.
    global $dbc;

    // Start the session.
    session_start();

    // Check if supplier is logged in
    if (!isset($_SESSION['agent_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    // Retrieving agent ID from the session
    $agent_id = $_SESSION['agent_id'];

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";

    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (custName LIKE '%$searchQuery%' OR product_id LIKE
'%$searchQuery%' )";
    }

    // Make the query.
    $query = "SELECT order_id, product_id, orderQuantity, custName, custAddress, custPhone,
orderDate FROM orders
 WHERE approval_status = 'pending' AND agent_id = '$agent_id'" . $searchCondition .
        ";";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);
    if ($num > 0) {
        echo '<br/><p> <a href="listOfProducts.php" style="color: blue; text-decoration:
underline;">Back</a> </p>';
        // Table header.
        echo '<br><h2>Order Approval (Pending) <br /><br /></h2>';

        // Search form
        echo '<form method="GET" action="orderPending.php">
 <input type="text" name="search" placeholder="Search by Customer Name and
Product ID" value="' . $searchQuery . '" style="width: 250px;">
 <input type="submit" value="Search">
 </form><br/>';

        // Table header
        echo '<table border="1" width="100%">
 <tr>
 <td> <b>Order ID</b> </td>
 <td> <b>Product ID</b> </td>
 <td> <b>Quantity</b> </td>
 <td> <b>Customer Name</b> </td>
 <td> <b>Customer Address</b> </td>
 <td> <b>Customer Phone</b> </td>
 <td> <b>Order Date</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['order_id'] . '</td>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['orderQuantity'] . '</td>'
                . '<td>' . $row['custName'] . '</td>'
                . '<td>' . $row['custAddress'] . '</td>'
                . '<td>' . $row['custPhone'] . '</td>'
                . '<td>' . $row['orderDate'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<br/><p> <a href="listOfProducts.php" style="color: blue; text-decoration:
underline;">Back</a> </p>';
        echo '<p class="error"><br>Order not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>