<html>

<head>
    <title>Login</title>
    <link href="../includes/styleloginRegister.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="Form">
        <form action="Register.php" method="post">
            <a href="login.php">Back</a>
            <fieldset>
                <legend>
                    <h2>Register</h2>
                </legend>
                <p><label for="name">Name</label>: <input type="text" id="supplierName"
                        name="supplierName" size="23" maxlength="100" value="<?php if (isset($_POST['supplierName'])) echo $_POST['supplierName']; ?>" /></p>
                <p><label for="email">Email Address</label>: <input type="text" id="supplierEmail"
                        name="supplierEmail" size="23" maxlength="100" value="<?php if (isset($_POST['supplierEmail']))
                                                                                    echo $_POST['supplierEmail']; ?>" /></p>
                <p>
                    <label for="supplierCategory">Category</label>:
                    <select id="supplierCategory" name="supplierCategory">
                        <option value="">Please choose a category</option>
                        <option value="brownies">Brownies</option>
                        <option value="chocojars">Chocojars</option>
                        <option value="cookies">Cookies</option>
                    </select>
                </p>
                <p><label for="password1">Password</label>: <input type="password"
                        id="supplierPassword1" name="supplierPassword1" size="23" maxlength="100" /></p>
                <p><label for="password2">Confirm Password</label>: <input type="password"
                        id="supplierPassword2" name="supplierPassword2" size="23" maxlength="100" /></p><br />
            </fieldset>
            <p><input type="submit" name="submit" value="Register" /></p>
            <input type="hidden" name="submittedRegister" value="TRUE" />
        </form>
    </div>

    <?php
    // Check if the form has been submitted.
    if (isset($_POST['submittedRegister'])) {
        require_once('../mysqli.php'); // Connect to the db.
        global $dbc;

        echo '<div id="result">';
        $errors = array(); // Initialize error array.
        // Check for a name.
        if (empty($_POST['supplierName']))
            $errors[] = 'You forgot to enter your name.';

        // Check for an email address.
        if (empty($_POST['supplierEmail']))
            $errors[] = 'You forgot to enter your email address.';

        // Check for a category.
        if (empty($_POST['supplierCategory']))
            $errors[] = 'You forgot to choose your category.';

        // Check for a password and match against the confirmed password.
        if (!empty($_POST['supplierPassword1'])) {
            if ($_POST['supplierPassword1'] != $_POST['supplierPassword2'])
                $errors[] = 'Your password did not match the confirmed password.';
        } else {
            $errors[] = 'You forgot to enter your password.';
        }

        if (empty($errors)) { // If everything's okay.
            $name = $_POST['supplierName'];
            $email = $_POST['supplierEmail'];
            $category = $_POST['supplierCategory'];
            $password = $_POST['supplierPassword1'];

            // Check for previous registration.
            $query = "SELECT supplier_id FROM suppliers WHERE supplierEmail='$email'";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if (mysqli_num_rows($result) == 0) {
                // Make the query.
                $query = "INSERT INTO suppliers (supplierName, supplierEmail,
supplierCategory, supplierPassword) VALUES ('$name', '$email', '$category', SHA('$password'))";
                $result = @mysqli_query($dbc, $query); // Run the query. // Run the query.
                if ($result) { // If it ran OK. == IF TRUE
                    // Print a message.
                    echo '<h1 id="mainhead">Thank you!</h1>
 <p>You are now registered. </p><p><br /></p>';
                    // Include the footer and quit the script (to not show the form).
                    exit();
                } else { // If it did not run OK.
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">You could not be registered due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; //Debugging message.
                    include('./includes/footer.html');
                    exit();
                }
            } else { // Already registered.
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The email address has already been registered.</p>';
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