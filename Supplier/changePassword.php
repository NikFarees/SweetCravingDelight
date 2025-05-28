<html>

<head>
    <title>Change Password</title>
</head>

<body>
    <?php
    include('../includes/headerSupplier.html');
    require_once('../mysqli.php'); // Connect to the db.
    global $dbc;

    // Start the session.
    session_start();
    // Check if supplier is logged in
    if (!isset($_SESSION['supplier_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    // Retrieving agent ID from the session
    $supplier_id = $_SESSION['supplier_id'];

    if (isset($_POST['submitChangePassword'])) {
        $errors = array(); // Initialize error array.
        // Check for an existing password.
        if (empty($_POST['supplierPassword']))
            $errors[] = 'You forgot to enter your existing password.';
        // Check for a password and match against the confirmed password.
        if (!empty($_POST['supplierPassword1'])) {
            if ($_POST['supplierPassword1'] != $_POST['supplierPassword2'])
                $errors[] = 'Your new password did not match the confirmed new password.';
        } else {
            $errors[] = 'You forgot to enter your new password.';
        }
        if (empty($errors)) { // If everything's OK.
            $password = $_POST['supplierPassword'];
            $newPassword = $_POST['supplierPassword1'];
            // Check that they've entered the right agent_id/password combination.
            $query = "SELECT supplier_id FROM suppliers WHERE (supplier_id='$supplier_id' AND
supplierPassword=SHA('$password') )";
            $result = @mysqli_query($dbc, $query); // Run the query.
            $num = mysqli_num_rows($result);
            if (mysqli_num_rows($result) == 1) { // Match was made.
                // Get the user_id.
                $row = mysqli_fetch_array($result, MYSQLI_NUM);
                // Make the UPDATE query.
                $query = "UPDATE suppliers SET supplierPassword=SHA('$newPassword') WHERE
supplier_id=$row[0]";
                $result = @mysqli_query($dbc, $query); // Run the query.
                if ($result) { // If it ran OK.
                    // Send an email, if desired.
                    // Print a message.
                    echo '<h1 id="mainhead"><br/>Thank you!</h1>
 <p>Your password has been updated. </p><p><br /></p>';

                    echo '<br/><p> <a href="profileSupplier.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                    // Include the footer and quit the script (to not show the form).
                    include('../includes/footer.html');
                    exit();
                } else { // If it did not run OK.
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your password could not be changed due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; //Debugging message.
                    include('../includes/footer.html');
                    exit();
                }
            } else { // Invalid email address/password combination.
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The email address and password do not match those on file.</p>';
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

    ?>
    <br />
    <p> <a href="profileSupplier.php" style="color: blue; text-decoration:
underline;">Back</a></p><br />
    <h2>Change Your Password</h2>
    <form action="changePassword.php" method="post">
        <p>Current Password: <input type="password" name="supplierPassword" size="20"
                maxlength="40" /></p>
        <p>New Password: <input type="password" name="supplierPassword1" size="20"
                maxlength="40" /></p>
        <p>Confirm New Password: <input type="password" name="supplierPassword2" size="20"
                maxlength="40" /></p>
        <p><input type="submit" name="submit" value="Register" /></p>
        <input type="hidden" name="submitChangePassword" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>