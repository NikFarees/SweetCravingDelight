<html>

<head>
    <title>Login</title>
    <link href="../includes/styleloginRegister.css" rel="stylesheet" type="text/css" />
</head>

<body>
    <div class="Form">
        <form action="register.php" method="post">
            <a href="login.php">Back</a>
            <fieldset>
                <legend>
                    <h2>Register</h2>
                </legend>
                <p><label for="name">Name</label>: <input type="text" id="name" name="name" size="20" maxlength="40" value="<?php if (isset($_POST['name'])) echo $_POST['name']; ?>" /></p>
                <p><label for="email">Email Address</label>: <input type="text" id="email" name="email" size="20" maxlength="40" value="<?php if (isset($_POST['email'])) echo
                                                                                                                                        $_POST['email']; ?>" /></p>
                <p><label for="password1">Password</label>: <input type="password" id="password1" name="password1" size="20" maxlength="40" /></p>
                <p><label for="password2">Confirm Password</label>: <input type="password" id="password2" name="password2" size="20" maxlength="40" /></p><br />
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
        if (empty($_POST['name']))
            $errors[] = 'You forgot to enter your name.';

        // Check for an email address.
        if (empty($_POST['email']))
            $errors[] = 'You forgot to enter your email address.';

        // Check for a password and match against the confirmed password.
        if (!empty($_POST['password1'])) {
            if ($_POST['password1'] != $_POST['password2'])
                $errors[] = 'Your password did not match the confirmed password.';
        } else {
            $errors[] = 'You forgot to enter your password.';
        }

        if (empty($errors)) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $password = $_POST['password1'];

            // Check for previous registration.
            $query = "SELECT agent_id FROM agents WHERE agentEmail='$email'";
            $result = @mysqli_query($dbc, $query); // Run the query.
            if (mysqli_num_rows($result) == 0) {
                // Make the query.
                $query = "INSERT INTO agents (agentName, agentEmail, agentPassword) VALUES
('$name', '$email', SHA('$password'))";
                $result = @mysqli_query($dbc, $query); // Run the query.
                if ($result) {
                    echo '<h1 id="mainhead">Thank you!</h1>
 <p>You are now registered. </p><p><br /></p>';
                    exit();
                } else {
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">You could not be registered due to a system error. We
apologize for any inconvenience.</p>'; // Public message.
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>'; //Debugging message.
                    include('./includes/footer.html');

                    exit();
                }
            } else {
                echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The email address has already been registered.</p>';
            }
        } else {
            echo '<h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) {
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