<html>

<head>
    <title>Reject Order</title>
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
        $errors = array(); // Initialize error array.
        // Check for an order id.
        if (empty($_POST['order_id'])) {
            $errors[] = 'You forgot to choose order id.';
        } else {
            $a = $_POST['order_id'];
        }
        if (empty($errors)) { // If everything's OK.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            // Make the UPDATE query.
            $query = "DELETE FROM orders WHERE order_id='$a'";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if ($result) { // If it ran OK.
                // Print a message.
                echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Order has been rejected. </p><p><br /></p>';
                echo '<p> <a href="OrderApprovalList.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                // Include the footer and quit the script (to not show the form).
                include('../includes/footer.html');
                exit();
            } else { // If it did not run OK.
                echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your order could not be rejected due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; // Debugging message.
                include('../includes/footer.html');
                exit();
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
    <h2>Reject Order</h2>
    <form action="RejectOrder.php" method="post">
        <p>Order Id: <select name="order_id">
                <!-- Add default option -->
                <option value="" selected disabled>Please choose order id</option>
                <!-- Populate genre options from database -->
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Retrieving supplier ID from the session
                $supplier_id = $_SESSION['supplier_id'];
                // Fetch genres from the database
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
        <p><input type="submit" name="submit" value="Reject Order" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>