<html>

<head>
    <title>Login</title>
    <link href="../includes/styleloginRegister.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="Form">
        <h1>Welcome to Sweet Craving Delight</h1>
        <form action="login.php" method="post">
            <fieldset>
                <legend>
                    <h2>Login (Agent)</h2>
                </legend>
                <p><label for="email">Email Address</label>: <input type="text" id="email" name="email" size="20" maxlength="40" value="<?php if (isset($_POST['email'])) echo
                                                                                                                                        $_POST['email']; ?>" /></p>
                <p><label for="password">Password</label>: <input type="password" id="password" name="password" size="10" maxlength="20" /></p>
                <a href="register.php">Create a new account</a><br /><br />
            </fieldset>
            <p><input type="submit" name="submit" value="Login" /></p>
            <input type="hidden" name="submittedLogin" value="TRUE" />
        </form>
    </div>

    <?php
    if (isset($_POST['submittedLogin'])) {
        // Start the session.
        session_start();

        require_once('../mysqli.php'); // Connect to the db.
        global $dbc;

        echo '<div id="result">';
        $errors = array(); // Initialize error array.
        // Check for an email address.
        if (empty($_POST['email']))
            $errors[] = 'You forgot to enter your email address.';

        // Check for password
        if (empty($_POST['password']))
            $errors[] = 'You forgot to enter your password.';

        if (empty($errors)) {
            $email = $_POST['email'];
            $password = $_POST['password'];

            // Check for previous registration.
            $query = "SELECT agent_id FROM agents WHERE (agentEmail='$email' AND
agentPassword=SHA('$password') )";
            $result = @mysqli_query($dbc, $query); // Run the query.

            if (mysqli_num_rows($result) == 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                // Set session variables.
                $_SESSION['agent_id'] = $row['agent_id'];

                // redirect the user to list of supplier page
                header('Location: listOfSuppliers.php');
                exit();
            } else {
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The email address and password do not match those on
file..</p>';
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
    echo '</div>';
    ?>
</body>

</html>