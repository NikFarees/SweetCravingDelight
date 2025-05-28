<!DOCTYPE html>
<html>

<head>
    <title>History List Of Stock Changed</title>
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
        $searchCondition = " AND (stocks.product_id LIKE '%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT stocks.change_id, stocks.product_id, stocks.stockquantity,
stocks.dateChange FROM stocks
 INNER JOIN products ON stocks.product_id = products.product_id
 WHERE products.supplier_id = '$supplier_id'" . $searchCondition;
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
        echo '<br><h2>List of Stock Changed <br /><br /></h2>

 <form method="GET" action="StockHistory.php">
 <input type="text" name="search" placeholder="Search by Product ID" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>Change Id</b> </td>
 <td> <b>Product Id</b> </td>
 <td> <b>Stock Added</b> </td>
 <td> <b>Change Date</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['change_id'] . '</td>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['stockquantity'] . '</td>'
                . '<td>' . $row['dateChange'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>Stock not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>