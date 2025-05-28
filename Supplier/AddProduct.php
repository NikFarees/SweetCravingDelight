<html>

<head>
    <title>Add Product</title>
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
        // Check for a product quantity.
        if (empty($_POST['productQuantity'])) {
            $errors[] = 'You forgot to enter product quantity.';
        } else {
            $q = $_POST['productQuantity'];
        }
        if (empty($errors)) { // If everything's okay.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            // Check for previous registration.
            $query = "SELECT product_id FROM products WHERE productName='$n'";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if (mysqli_num_rows($result) == 0) {
                // Make the query.
                $query = "INSERT INTO products (supplier_id, productName, productPrice,
productQuantity) VALUES ('$supplier_id', '$n', '$p', '$q')";
                $result = @mysqli_query($dbc, $query); // Run the query.
                if ($result) {
                    // Retrieve the product_id of the newly inserted product
                    $product_id = mysqli_insert_id($dbc);
                    // Insert the product into the stocks table
                    $query_stock = "INSERT INTO stocks (product_id, stockquantity, dateChange)
VALUES ('$product_id', '$q', NOW())";
                    $result_stock = @mysqli_query($dbc, $query_stock);
                    if ($result_stock) {
                        // Print a message.
                        echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Product are now registered. </p><p><br /></p>';
                        echo '<br/><p> <a href="ProductList.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                        // Include the footer and quit the script (to not show the form).
                        include('../includes/footer.html');
                        exit();
                    } else {
                        echo '<h1 id="mainhead">System Error</h1>
 <p class="error">You could not be registered due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query_stock . '</p>'; //Debugging message.
                        include('../includes/footer.html');
                        exit();
                    }
                } else {
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">You could not be registered due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; //Debugging message.
                    include('../includes/footer.html');
                    exit();
                }
            } else {
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The Product has already been registered.</p>';
            }
        } else {
            echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p><p><br /></p>';
        }
        mysqli_close($dbc); // Close the database connection.
    }
    ?>
    <br>
    <p><a href="ProductList.php" style="color: blue; text-decoration:
underline;">Back</a></p>
    <br />
    <h2>Add Product</h2>
    <form action="AddProduct.php" method="post">
        <p>Product Name: <input type="text" name="productName" size="15" maxlength="100"
                value="<?php if (isset($_POST['productName'])) echo $_POST['productName']; ?>" /></p>
        <p>Price: <input type="text" name="productPrice" size="15" maxlength="30"
                value="<?php if (isset($_POST['productPrice'])) echo $_POST['productPrice']; ?>" /></p>
        <p>Quantity: <input type="text" name="productQuantity" size="20" maxlength="40"
                value="<?php if (isset($_POST['productQuantity'])) echo $_POST['productQuantity']; ?>" /> </p>
        <p><input type="submit" name="submit" value="Add Product" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>