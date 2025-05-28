<html>

<head>
    <title>Order Product</title>
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
    if (isset($_POST['submitOrder'])) {
        $errors = array();
        // Check for a product id
        if (empty($_POST['product_id']))
            $errors[] = 'You forgot to enter product ID.';
        // Check for a quantity
        if (empty($_POST['quantity']))
            $errors[] = 'You forgot to enter product quantity.';
        // Check for a customer name
        if (empty($_POST['custName']))
            $errors[] = 'You forgot to enter customer name.';
        // Check for a customer address
        if (empty($_POST['custAddress']))
            $errors[] = 'You forgot to enter customer address.';
        // Check for a customer phone
        if (empty($_POST['custPhone']))
            $errors[] = 'You forgot to enter customer phone.';
        if (empty($errors)) {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $custName = $_POST['custName'];
            $custAddress = $_POST['custAddress'];
            $custPhone = $_POST['custPhone'];
            // make a query
            $query = "SELECT products.product_id FROM products
 INNER JOIN suppliers ON products.supplier_id = suppliers.supplier_id
 INNER JOIN agents_approval ON agents_approval.supplier_id =
suppliers.supplier_id
 WHERE agents_approval.agent_id = '$agent_id' AND products.product_id =
'$product_id' AND agents_approval.approval_status = 'approved';";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if (mysqli_num_rows($result) == 1) {
                // Check if the ordered quantity is available for the product
                $check_query = "SELECT productQuantity FROM products WHERE product_id =
'$product_id'";
                $check_result = mysqli_query($dbc, $check_query);
                if ($check_result) {
                    $row = mysqli_fetch_assoc($check_result);
                    $available_quantity = $row['productQuantity'];
                    if ($available_quantity >= $quantity) { // Quantity available
                        // make a query
                        $query = "INSERT INTO orders (agent_id, product_id, orderQuantity, custName,
custAddress, custPhone, orderDate) VALUES
 ('$agent_id', '$product_id', $quantity, '$custName', '$custAddress',
'$custPhone', NOW())";
                        $insert_result = mysqli_query($dbc, $query); // Run the query.
                        echo '<h1 id="mainhead"><br/>Thank you!</h1>
 <p>Your order has been requested. </p><p><br /></p>';
                        echo '<p> <a href="listOfProducts.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                        include('../includes/footer.html');
                        exit();
                    } else { // Quantity not available
                        echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The ordered quantity exceeds the available quantity.</p>';
                    }
                } else {
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Error fetching product information.</p>';
                    include('../includes/footer.html');
                    exit();
                }
            } else {
                echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">Registered product not found or approval is pending.</p>';
            }
        } else {
            echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><br><p>Please try again.</p><p><br /></p>';
        }
        mysqli_close($dbc); // Close the database connection.
    }
    ?>
    <br />
    <p> <a href="listofProducts.php" style="color: blue; text-decoration:
underline;">Back</a></p><br />
    <h2>Order Product</h2>
    <form action="orderProduct.php" method="post">
        <p>
            <label for="product_id">Product:</label>
            <select id="product_id" name="product_id">
                <option value="" selected disabled>Please choose a product ID</option>
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Fetch products from the database
                $query = mysqli_query($connection, "SELECT products.product_id,
suppliers.supplier_id, products.productName, suppliers.supplierCategory, products.productPrice,
products.productQuantity FROM products
 INNER JOIN suppliers ON products.supplier_id = suppliers.supplier_id
 INNER JOIN agents_approval ON agents_approval.supplier_id =
suppliers.supplier_id
 WHERE agents_approval.agent_id = '$agent_id' AND
agents_approval.approval_status = 'approved';");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $product_id = $row['product_id'];
                        echo '<option value="' . $product_id . '"> ' . $product_id . '</option>';
                    }
                } else {
                    echo "Error fetching products: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>
        <p><label for="quantity">Quantity</label>: <input type="number" min="1" id="quantity"
                name="quantity" size="20" maxlength="40" value="<?php if (isset($_POST['quantity'])) echo
                                                                $_POST['quantity']; ?>" /></p>
        <p><label for="custName">Customer Name</label>: <input type="text" id="custName"
                name="custName" size="20" maxlength="40" value="<?php if (isset($_POST['custName'])) echo
                                                                $_POST['custName']; ?>" /></p>
        <p><label for="custAddress">Customer Address</label>: <input type="text"
                id="custAddress" name="custAddress" size="20" maxlength="40" value="<?php if (isset($_POST['custAddress'])) echo $_POST['custAddress']; ?>" /></p>
        <p><label for="custPhone">Customer Phone</label>: <input type="text" id="custPhone"
                name="custPhone" size="20" maxlength="40" value="<?php if (isset($_POST['custPhone'])) echo
                                                                    $_POST['custPhone']; ?>" /></p>
        <p><input type="submit" name="submit" value="Order" /></p>
        <input type="hidden" name="submitOrder" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>