<html>

<head>
    <title>List Of Products</title>
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
        $searchCondition = " AND (products.product_id LIKE '%$searchQuery%' OR
products.productName LIKE '%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT products.product_id, suppliers.supplier_id, products.productName,
suppliers.supplierCategory, products.productPrice, products.productQuantity FROM products
 INNER JOIN suppliers ON products.supplier_id = suppliers.supplier_id
 INNER JOIN agents_approval ON agents_approval.supplier_id = suppliers.supplier_id
 WHERE agents_approval.agent_id = '$agent_id' AND
agents_approval.approval_status = 'approved'" . $searchCondition;
    echo '<br><h2>List of Products <br /><br /></h2>';
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result); // OR die ('SQL Statement: ' . mysqli_error($dbc) );
    if ($num > 0) { // If it ran OK, display the records.
        // Table header.
        echo '<p> <a href="orderProduct.php" style="color: blue; text-decoration:
underline;">Order Product</a></p>
 <br/><p> <a href="orderPending.php" style="color: blue; text-decoration:
underline;">Order Approval (Pending)</a></p><br />

 <form method="GET" action="listOfProducts.php" >
 <input type="text" name="search" placeholder="Search by Product ID or Product
Name" value="' . $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form><br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>Product ID</b> </td>
 <td> <b>Supplier ID</b> </td>
 <td> <b>Product Name</b> </td>
 <td> <b>Product Category</b> </td>
 <td> <b>Product Price</b> </td>
 <td> <b>Product Quantity</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['supplier_id'] . '</td>'
                . '<td>' . $row['productName'] . '</td>'
                . '<td>' . $row['supplierCategory'] . '</td>'
                . '<td>' . $row['productPrice'] . '</td>'
                . '<td>' . $row['productQuantity'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<p class="error"><br>Registered product not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>