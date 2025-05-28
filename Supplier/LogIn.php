<html>

<head>
    <title>Login</title>
    <link href="../includes/styleloginRegister.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="Form">
        <h1>Welcome to Sweet Craving Delight</h1>
        <form action="LogIn.php" method="post">
            <fieldset>
                <legend>
                    <h2>Login (Supplier)</h2>
                </legend>
                <p><label for="email">Email Address</label>: <input type="text" id="supplierEmail"
                        name="supplierEmail" size="20" maxlength="40" value="<?php if (isset($_POST['supplierEmail']))
                                                                                    echo $_POST['supplierEmail']; ?>" /></p>
                <p><label for="password">Password</label>: <input type="password"
                        id="supplierPassword" name="supplierPassword" size="10" maxlength="20" /></p>
                <a href="Register.php">Create a new account</a><br /><br />
            </fieldset>
            <p><input type="submit" name="submit" value="Login" /></p>
            <input type="hidden" name="submittedLogin" value="TRUE" />

        </form>
    </div>

    <?php
    // Check if the form has been submitted.
    if (isset($_POST['submittedLogin'])) {
        require_once('../mysqli.php'); // Connect to the db.
        global $dbc;

        echo '<div id="result">';
        $errors = array(); // Initialize error array.
        // Check for an email address.
        if (empty($_POST['supplierEmail'])) {
            $errors[] = 'You forgot to enter your email address.';
        } else {
            $e = $_POST['supplierEmail'];
        }
        // Check for a password.
        if (empty($_POST['supplierPassword'])) {
            $errors[] = 'You forgot to enter your password.';
        } else {
            $p = $_POST['supplierPassword'];
        }

        if (empty($errors)) { // If everything's okay.

            // Check for previous registration.
            $query = "SELECT supplier_id, supplierName FROM suppliers WHERE
supplierEmail='$e' AND supplierPassword=SHA('$p')";
            $result = @mysqli_query($dbc, $query); // Run the query.

            if (mysqli_num_rows($result) == 1) {

                // Start the session.
                session_start();
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                // Set session variables.
                $_SESSION['supplier_id'] = $row['supplier_id'];
                $_SESSION['product_id'] = $row['product_id'];
                // redirect the user to list of product page
                header('Location: ProductList.php');
                exit();
            } else {
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The email address and password do not match those on
file..</p>';
            }
        } else { // Report the errors.
            echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p><p><br /></p>';
        } // End of if (empty($errors)) IF.
        mysqli_close($dbc); // Close the database connection.
    }
    echo '</div>';
    ?>
</body>

</html>