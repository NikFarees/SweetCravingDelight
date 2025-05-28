<html>

<head>
    <title>Profile</title>
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

    // Make the query.
    $query = "SELECT agent_id, agentName, agentEmail FROM agents";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);
    echo '<br><h2>Profile</h2>';
    if ($num > 0) {
        // Mkae the query
        $query = "SELECT agent_id, agentName, agentEmail FROM agents WHERE agent_id
='$agent_id'";
        $result = @mysqli_query($dbc, $query); // Run the query.

        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<p><br />Agent ID : ' . $row['agent_id'] . '</p>';
            echo '<p><br />Name : ' . $row['agentName'] . '</p>';
            echo '<p><br />Email : ' . $row['agentEmail'] . '</p>';
        }

        echo '<br/><p> <a href="changePassword.php" style="color: blue; text-decoration:
underline;">Change Password</a> </p>';
        echo '<br/><p> <a href="logout.php" style="color: blue; text-decoration: underline;">Log
Out</a> </p>';

        @mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<p class="error"><br>There are currently no registered agent.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>
