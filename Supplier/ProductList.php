<html>

<head>
    <title>List Of Products</title>
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
        $searchCondition = " AND (product_id LIKE '%$searchQuery%' OR productName LIKE
'%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT * FROM products WHERE supplier_id = '$supplier_id' " .
        $searchCondition;
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result); // OR die ('SQL Statement: ' . mysqli_error($dbc) );
    echo '<br><h2>List of Products<br /><br /></h2>
 <a href="AddProduct.php" style="color: blue; text-decoration: underline;">Add
Product</a><br />';

    if ($num > 0) { // If it ran OK, display the records.
        // Table header.
        echo '<br><a href="UpdateProduct.php" style="color: blue; text-decoration:
underline;">Update Product</a><br />
 <br><a href="DeleteProduct.php" style="color: blue; text-decoration:
underline;">Delete Product</a><br />
 <br><a href="AddStock.php" style="color: blue; text-decoration: underline;">Add
Stock</a>
 <br /><br />
 <form method="GET" action="ProductList.php">
 <input type="text" name="search" placeholder="Search by ID or Name" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>ID</b> </td>
 <td> <b>Name</b> </td>
 <td> <b>Price</b> </td>
 <td> <b>Stock Available</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['product_id'] . '</td>'
                . '<td>' . $row['productName'] . '</td>'
                . '<td>RM ' . $row['productPrice'] . '</td>'
                . '<td>' . $row['productQuantity'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>Registered product not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>