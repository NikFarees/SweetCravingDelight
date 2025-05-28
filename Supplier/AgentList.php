<html>

<head>
    <title>List Of Agents</title>
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
        $searchCondition = " AND (agents.agent_id LIKE '%$searchQuery%' OR
agents.agentName LIKE '%$searchQuery%')";
    }

    // Make the query.
    $query = "SELECT DISTINCT agents.agent_id, agents.agentName FROM agents
 INNER JOIN relationships ON agents.agent_id = relationships.agent_id
 INNER JOIN agents_approval ON agents.agent_id = agents_approval.agent_id
 WHERE relationships.supplier_id = '$supplier_id' AND
agents_approval.approval_status = 'approved'" . $searchCondition;
    $result = @mysqli_query($dbc, $query); // Run the query.
    $num = @mysqli_num_rows($result);

    echo '<br><h2>List of Agents<br /></h2>';
    if ($num > 0) { // If it ran OK, display the records.
        // Table header.
        echo '<br><a href="AgentPerformance.php" style="color: blue; text-decoration:
underline;">Agent Performance</a><br />
 <br><a href="TotalPerformance.php" style="color: blue; text-decoration:
underline;">Total Performance</a><br /><br />

 <form method="GET" action="AgentList.php">
 <input type="text" name="search" placeholder="Search by ID or Name" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>Agent Id</b> </td>
 <td> <b>Agent Name</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['agent_id'] . '</td>'
                . '<td>' . $row['agentName'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else { // If it did not run OK.
        echo '<p class="error"><br>Agent not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>