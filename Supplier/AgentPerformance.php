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

    // Initialize variables for search
    $searchQuery = "";
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (agents.agent_id LIKE '%$searchQuery%' OR
agents.agentName LIKE '%$searchQuery%')";
    } else {
        $searchCondition = "";
    }

    // Make the query.
    $query = "SELECT agents.agent_id, agents.agentName, COUNT(orders.order_id) AS
totalOrders, SUM(products.productPrice * orders.orderQuantity) AS totalPrice FROM agents
 INNER JOIN relationships ON agents.agent_id = relationships.agent_id
 INNER JOIN orders ON agents.agent_id = orders.agent_id
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE relationships.supplier_id = '$supplier_id' AND products.supplier_id =
'$supplier_id' AND orders.approval_status = 'approved'
 $searchCondition
 GROUP BY agents.agent_id, agents.agentName;";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);

    echo '<br><p><a href="AgentList.php" style="color: blue; text-decoration:
underline;">Back</a></p>
 <br><h2>List of Agents Performance<br /><br /></h2>';

    if ($num > 0) { // If it ran OK, display the records.

        // Display search form
        echo '<form method="GET" action="AgentPerformance.php">
 <input type="text" name="search" placeholder="Search by Agent ID or Name"
value="' . $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form><br/>';
        // Table header.
        echo '<table border="1" width="100%">
 <tr>
 <td> <b>Agent Id</b> </td>
 <td> <b>Agent Name</b> </td>
 <td> <b>Total Orders</b></td>
 <td> <b>Total Price</b></td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['agent_id'] . '</td>'
                . '<td>' . $row['agentName'] . '</td>'
                . '<td>' . $row['totalOrders'] . '</td>'
                . '<td>RM ' . $row['totalPrice'] . '</td>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error">Agent performance not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>