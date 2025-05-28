<html>

<head>
    <title>Update Product</title>
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
        // Check for a product id.
        if (empty($_POST['product_id'])) {
            $errors[] = 'You forgot to choose product id.';
        } else {
            $i = $_POST['product_id'];
        }
        // Check for a product name.
        if (empty($_POST['productName'])) {
            $errors[] = 'You forgot to enter product name.';
        } else {
            $n = $_POST['productName'];
        }
        // Check for a product price.
        if (empty($_POST['productPrice'])) {
            $errors[] = 'You forgot to enter product price.';
        } else {
            $p = $_POST['productPrice'];
        }
        if (empty($errors)) { // If everything's OK.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            // Make the UPDATE query.
            $query = "UPDATE products SET productName='$n', productPrice='$p' WHERE
product_id='$i'";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if ($result) { // If it ran OK.
                // Print a message.
                echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Product has been updated. </p><p><br /></p>';
                echo '<br/><p> <a href="ProductList.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                // Include the footer and quit the script (to not show the form).
                include('../includes/footer.html');
                exit();
            } else { // If it did not run OK.
                echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your product detail could not be changed due to a system error. We
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
    <p><a href="ProductList.php" style="color: blue; text-decoration:
underline;">Back</a></p>
    <br>
    <h2>Update Product Detail</h2>
    <form action="UpdateProduct.php" method="post">
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
        <p>Product Name: <input type="text" name="productName" size="25" maxlength="100"
                value="<?php if (isset($_POST['productName'])) echo $_POST['productName']; ?>" /></p>
        <p>Price: <input type="text" name="productPrice" size="15" maxlength="30"
                value="<?php if (isset($_POST['productPrice'])) echo $_POST['productPrice']; ?>" /></p>
        <p><input type="submit" name="submit" value="Update Product" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>