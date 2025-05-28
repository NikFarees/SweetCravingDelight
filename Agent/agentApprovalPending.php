<html>

<head>
    <title>Agent Approval Pending</title>
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

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";

    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND supplier_id LIKE '%$searchQuery%'";
    }


    // Make the query.
    $query = "SELECT supplier_id, applyDate FROM agents_approval
 WHERE approval_status = 'pending' AND agent_id = $agent_id" . $searchCondition .
        ";";
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);
    if ($num > 0) {
        echo '<br/><p> <a href="listOfSuppliers.php" style="color: blue; text-decoration:
underline;">Back</a> </p>';

        // Table header.
        echo '<br><h2>Agent Approval (Pending) <br /><br /></h2>';

        // Search form
        echo '<form method="GET" action="agentApprovalPending.php">
 <input type="text" name="search" placeholder="Search by Supplier ID" value="' .
            $searchQuery . '" style="width: 250px;">
 <input type="submit" value="Search">
 </form><br/>';
        // Table header
        echo '<table border="1" width="100%">
 <tr>
 <td> <b>Supplier ID</b> </td>
 <td> <b>Apply Date</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['supplier_id'] . '</td>'
                . '<td>' . $row['applyDate'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<br/><p> <a href="listOfSuppliers.php" style="color: blue; text-decoration:
underline;">Back</a> </p>';
        echo '<p class="error"><br>Agent approval not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>