<html>

<head>
    <title>List Of Agents Performance</title>
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

    // Make the query.
    $query = "SELECT COUNT(orders.order_id) AS totalOrders, SUM(products.productPrice *
orders.orderQuantity) AS totalPrice FROM agents
 INNER JOIN relationships ON agents.agent_id = relationships.agent_id
 INNER JOIN orders ON agents.agent_id = orders.agent_id
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE relationships.supplier_id = '$supplier_id' AND products.supplier_id =
'$supplier_id' AND orders.approval_status = 'approved';";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);
    if ($num > 0) { // If it ran OK, display the records.
        // Fetch the row from the result set
        $row = mysqli_fetch_assoc($result);

        // Header.
        echo '<br><p><a href="AgentList.php" style="color: blue; text-decoration:
underline;">Back</a></p>'
            . '<br><h2>Total Performance<br /><br /></h2>'
            . '<p>'
            . '<b>Total Orders: </b>' . $row['totalOrders'] . '</br>'
            . '<b>Total Price: </b> RM ' . $row['totalPrice'] . '</br>'
            . '</p>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>There are currently no agents.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>