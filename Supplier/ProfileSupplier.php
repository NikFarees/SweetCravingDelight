<html>

<head>
    <title>Profile</title>
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

    // Make the query.
    $query = "SELECT supplier_id, supplierName, supplierEmail FROM suppliers";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);
    if ($num > 0) { // If it ran OK, display the records.
        $query = "SELECT supplier_id, supplierName, supplierEmail FROM suppliers WHERE
supplier_id ='$supplier_id'";
        $result = @mysqli_query($dbc, $query); // Run the query.

        echo '<br><h2>Profile</h2>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<p><br />ID : ' . $row['supplier_id'] . '</p>';
            echo '<p><br />Name : ' . $row['supplierName'] . '</p>';
            echo '<p><br />Email : ' . $row['supplierEmail'] . '</p>';
        }

        echo '<br/><p> <a href="changePassword.php" style="color: blue; text-decoration:
underline;">Change Password</a> </p>';
        echo '<br/><p> <a href="logout.php" style="color: blue; text-decoration: underline;">Log
Out</a> </p>';

        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>There are currently no registered agent.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>