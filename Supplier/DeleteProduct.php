<html>

<head>
    <title>Delete Product</title>
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
        // Check for a product id.
        if (empty($_POST['product_id'])) {
            $errors[] = 'You forgot to choose product id.';
        } else {
            $i = $_POST['product_id'];
        }
        if (empty($errors)) { // If everything's OK.
            // Check if the product ID exists.
            $query = "SELECT product_id FROM products WHERE product_id='$i'";
            $result = @mysqli_query($dbc, $query);
            if (mysqli_num_rows($result) == 1) { // Match was found.
                // Delete the product from stocks table
                $query_delete_stock = "DELETE FROM stocks WHERE product_id='$i'";
                $result_delete_stock = @mysqli_query($dbc, $query_delete_stock);
                // Delete the product from orders table
                $query_delete_orders = "DELETE FROM orders WHERE product_id='$i'";
                $result_delete_orders = @mysqli_query($dbc, $query_delete_orders);
                // Delete the product from products table
                $query_delete_product = "DELETE FROM products WHERE product_id='$i'";
                $result_delete_product = @mysqli_query($dbc, $query_delete_product);
                if ($result_delete_product) { // If it ran OK.
                    // Print a message.
                    echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>The product has been deleted. </p><p><br /></p>';
                    echo '<br/><p> <a href="ProductList.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                    // Include the footer and quit the script (to not show the form).
                    include('../includes/footer.html');
                    exit();
                } else { // If it did not run OK.
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">The product could not be deleted due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query_delete_product .
                        '</p>'; // Debugging message.
                    include('../includes/footer.html');
                    exit();
                }
            } else { // Product ID does not exist.
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The product ID does not exist.</p>';
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
    <p><a href="ProductList.php" style="color: blue; text-decoration:
underline;">Back</a></p><br>
    <h2>Delete Product</h2>
    <form action="DeleteProduct.php" method="post">
        <p>Product Id:
            <select name="product_id">
                <option value="" selected disabled>Please choose product id</option>
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Retrieving supplier ID from the session
                $supplier_id = $_SESSION['supplier_id'];
                // Fetch product IDs from the database
                $query = mysqli_query($connection, "SELECT product_id FROM products WHERE
supplier_id='$supplier_id'");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $product_id = $row['product_id'];
                        echo '<option value="' . $product_id . '"> ' . $product_id . '</option>';
                    }
                } else {
                    echo "Error fetching product IDs: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>
        <p><input type="submit" name="submit" value="Delete Product" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>