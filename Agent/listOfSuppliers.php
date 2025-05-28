<html>

<head>
    <title>List Of Suppliers</title>
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

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";

    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " WHERE supplier_id LIKE '%$searchQuery%' OR supplierName LIKE
'%$searchQuery%'";
    }
    // Make the query.
    $query = "SELECT supplier_id, supplierName, supplierCategory FROM suppliers" .
        $searchCondition;
    $result = @mysqli_query($dbc, $query);
    $num = @mysqli_num_rows($result);
    echo '<br><h2>List of Suppliers <br /><br /></h2>';
    if ($num > 0) {
        // Table header.
        echo '<p> <a href="reqAgentWork.php" style="color: blue; text-decoration:
underline;">Request Agent Work</a></p>
 <br/><p> <a href="agentApprovalPending.php" style="color: blue; text-decoration:
underline;">Agent Approval (Pending)</a></p><br />

 <form method="GET" action="listOfSuppliers.php">
 <input type="text" name="search" placeholder="Search by ID or Name" value="' .
            $searchQuery . '" style="width: 230px;">
 <input type="submit" value="Search">
 </form> <br/>
 <table border="1" width="100%">
 <tr>
 <td> <b>ID</b> </td>
 <td> <b>Name</b> </td>
 <td> <b>Category</b> </td>
 </tr>';
        // Fetch and print all the records.
        while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            echo '<tr>'
                . '<td>' . $row['supplier_id'] . '</td>'
                . '<td>' . $row['supplierName'] . '</td>'
                . '<td>' . $row['supplierCategory'] . '</td>';
            echo '</tr>';
        }
        echo '</table>';
        @mysqli_free_result($result); // Free up the resources.
    } else {
        echo '<p class="error">Registered supplier not found.</p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>