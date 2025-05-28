<html>

<head>
    <title>Add Stock</title>
</head>

<body>
    <?php
    include('../includes/headerSupplier.html');
    require_once('../mysqli.php');
    global $dbc;
    session_start(); // Start the session
    // Check if supplier is logged in
    if (!isset($_SESSION['supplier_id'])) {
        // Redirect to the login page if not logged in
        header("Location: LogIn.php");
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
        // Check for a stock quantity.
        if (empty($_POST['stockquantity'])) {
            $errors[] = 'You forgot to enter stock quantity.';
        } else {
            $s = $_POST['stockquantity'];
        }
        if (empty($errors)) { // If everything's okay.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            $query = "INSERT INTO stocks (product_id, stockquantity, dateChange) VALUES ('$i',
'$s', NOW())";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if ($result) { // If it ran OK.
                $query = "UPDATE products SET productQuantity=('$s' + productQuantity) WHERE
product_id='$i'";
                $result = @mysqli_query($dbc, $query); // Run the query.
                // Print a message.
                echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Stock has been changed. </p><p><br /></p>';
                echo '<br/><p> <a href="ProductList.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                // Include the footer and quit the script (to not show the form).
                include('../includes/footer.html');
                exit();
            } else { // If it did not run OK.
                echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Stock could not be changed due to a system error. We apologize for
any inconvenience.</p>'; // Public message.
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
    <p><a href="ProductList.php" style="color: blue; text-decoration:
underline;">Back</a></p><br>
    <h2>Add Stock</h2>
    <form action="AddStock.php" method="post">
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
                // Fetch stocks from the database
                $query = mysqli_query($connection, "SELECT stocks.change_id, stocks.product_id,
stocks.stockquantity, stocks.dateChange FROM stocks
 INNER JOIN products ON stocks.product_id = products.product_id
 WHERE products.supplier_id = '$supplier_id'");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $product_id = $row['product_id'];
                        echo '<option value="' . $product_id . '"> ' . $product_id . '</option>';
                    }
                } else {
                    echo "Error fetching stocks: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>
        <p>Stock Quantity: <input type="text" name="stockquantity" size="15" maxlength="15"
                value="<?php if (isset($_POST['stockquantity'])) echo $_POST['stockquantity']; ?>" /></p>
        <p><input type="submit" name="submit" value="Add Stock" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>