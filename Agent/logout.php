<html>

<head>
    <title>Log out</title>
</head>

<body>
    <?php
    include('../includes/headerAgent.html');
    require_once('../mysqli.php'); // Connect to the db.
    global $dbc;
    // Start the session.
    session_start();
    // Check if supplier is logged in
    if (!isset($_SESSION['agent_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    // Retrieving agent ID from the session
    $agent_id = $_SESSION['agent_id'];

    if (isset($_POST['submit'])) {
        if ($_POST['submit'] == "Yes") {
            // Unset all of the session variables
            $_SESSION = array();
            // Destroy the session.
            session_destroy();

            // Close the database connection.
            mysqli_close($dbc);
            // Redirect to login page
            header("location: login.php");
            exit;
        } else {
            header("location: profileAgent.php");
        }
    }
    ?>
    <br />
    <form action="logout.php" method="post">
        <p>Are you sure you want to log out?</p><br>
        <p><input type="submit" name="submit" value="Yes" />
            <input type="submit" name="submit" value="No" />
        </p>
        <input type="hidden" name="submitLogOut" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>