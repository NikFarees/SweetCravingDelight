<html>

<head>
    <title>Agent Approval</title>
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

    // Retrieving supplier ID from the session
    $supplier_id = $_SESSION['supplier_id'];

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (agent_id LIKE '%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT * FROM agents_approval WHERE approval_status = 'pending' AND
supplier_id = '$supplier_id' " . $searchCondition;
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);

    echo '<br><h2>List of Pending Agents <br /><br /></h2>';
    if ($num > 0) { // If it ran OK, display the records.
        // Table header.
        echo '<a href="ApproveAgent.php" style="color: blue; text-decoration:
underline;">Approve Pending Agent</a><br />
 <br><a href="RejectAgent.php" style="color: blue; text-decoration: underline;">Reject
Pending Agent</a><br /><br />

 <form method="GET" action="AgentApprovalList.php">
 <input type="text" name="search" placeholder="Search by Agent ID" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>ID</b> </td>
 <td> <b>Agent Id</b> </td>
 <td> <b>Apply Date</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['agent_approval_id'] . '</td>'
                . '<td>' . $row['agent_id'] . '</td>'
                . '<td>' . $row['applyDate'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error">Pending agent approval not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>