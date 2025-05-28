<html>

<head>
    <title>Approve Order</title>
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
    // Check if the form has been submitted.
    if (isset($_POST['submitted'])) {
        $errors = array();
        // Check for an order id.
        if (empty($_POST['order_id'])) {
            $errors[] = 'You forgot to choose an order id.';
        } else {
            $order_id = $_POST['order_id'];
        }
        if (empty($errors)) { // If everything's OK.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            // Get the order details
            $order_query = "SELECT orders.product_id, orders.orderQuantity,
products.productQuantity FROM orders
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE orders.order_id='$order_id' AND products.supplier_id='$supplier_id'";
            $order_result = mysqli_query($dbc, $order_query);
            if ($order_result) {
                $order_data = mysqli_fetch_assoc($order_result);
                $product_quantity_available = $order_data['productQuantity'];
                $order_quantity = $order_data['orderQuantity'];
                if ($order_quantity > $product_quantity_available) {
                    // The order quantity exceeds the available product quantity
                    echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The order quantity exceeds the available product
quantity.</p>';
                    echo '<br><p><a href="OrderApprovalList.php" style="color: blue; text-decoration:
underline;">Back</a></p>';
                    include('../includes/footer.html');
                    exit();
                }
                // Update the order approval status
                $update_order_query = "UPDATE orders SET approval_status='approved',
approval_date=NOW() WHERE order_id='$order_id'";
                $update_order_result = mysqli_query($dbc, $update_order_query);
                if ($update_order_result) {
                    // Update the product quantity
                    $update_product_query = "UPDATE products SET productQuantity =
productQuantity - $order_quantity WHERE product_id='{$order_data['product_id']}'";
                    $update_product_result = mysqli_query($dbc, $update_product_query);
                    if ($update_product_result) {
                        // Print a success message.
                        echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Order has been approved. Product quantity updated.</p><p><br /></p>';
                        echo '<p><a href="OrderApprovalList.php" style="color: blue; text-decoration:
underline;">Ok</a></p>';
                        include('../includes/footer.html');
                        exit();
                    } else {
                        echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Failed to update product quantity. We apologize for any
inconvenience.</p>';
                        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $update_product_query
                            . '</p>';
                    }
                } else {
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your order could not be approved due to a system error. We
apologize for any inconvenience.</p>';
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $update_order_query .
                        '</p>';
                }
            } else {
                echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Failed to fetch order details. We apologize for any
inconvenience.</p>';
                echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $order_query . '</p>';
            }
        } else { // Report the errors.
            echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p><p><br /></p>';
        } // End of if (empty($errors)) IF.
        mysqli_close($dbc); // Close the database connection.
    } // End of the main Submit conditional.
    ?>
    <br>
    <p><a href="OrderApprovalList.php" style="color: blue; text-decoration:
underline;">Back</a></p></br>
    <h2>Approve Order</h2>
    <form action="ApproveOrder.php" method="post">
        <p>Order Id: <select name="order_id">
                <!-- Default option -->
                <option value="" selected disabled>Please choose order id</option>
                <!-- Populate order options from the database -->
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Retrieving supplier ID from the session
                $supplier_id = $_SESSION['supplier_id'];
                // Fetch orders from the database
                $query = mysqli_query($connection, "SELECT orders.order_id, orders.agent_id,
orders.product_id, orders.orderQuantity, orders.custName, orders.custAddress,
orders.custPhone, orders.orderDate FROM orders
 INNER JOIN products ON orders.product_id = products.product_id
 WHERE approval_status = 'pending' AND supplier_id =
'$supplier_id';");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $order_id = $row['order_id'];
                        echo '<option value="' . $order_id . '"> ' . $order_id . '</option>';
                    }
                } else {
                    echo "Error fetching orders: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select></p>
        <p><input type="submit" name="submit" value="Approve Order" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>