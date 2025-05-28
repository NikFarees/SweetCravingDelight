<html>

<head>
    <title>Request Agent Work</title>
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
    if (isset($_POST['submitReqAgentWork'])) {
        $errors = array(); // Initialize error array.
        // Check for a supplier id
        if (empty($_POST['supplier_id']))
            $errors[] = 'You forgot to choose supplier id.';
        if (empty($errors)) {
            $supplier_id = $_POST['supplier_id'];
            // Check if the supplier ID exists in the database.
            $query = "SELECT supplier_id FROM suppliers WHERE supplier_id = '$supplier_id'";
            $result = @mysqli_query($dbc, $query);
            $num = mysqli_num_rows($result);
            if ($num == 1) { // Supplier ID found.
                // Check if the agent has already made a request for this supplier ID.
                $query = "SELECT * FROM agents_approval WHERE agent_id='$agent_id' AND
supplier_id='$supplier_id'";
                $result = @mysqli_query($dbc, $query);
                $num_requests = mysqli_num_rows($result);
                if ($num_requests == 0) { // No previous request found.
                    // Make the INSERT query.
                    $query = "INSERT INTO agents_approval (agent_id, supplier_id, applyDate) VALUES
('$agent_id', '$supplier_id', NOW())";
                    $result = @mysqli_query($dbc, $query);
                    if ($result) { // If the query ran OK.
                        echo '<h1 id="mainhead"><br/>Thank you!</h1>
 <p>Your agent work request has been submitted. </p><p><br /></p>';
                        echo '<br/><p> <a href="listOfSuppliers.php" style="color: blue; text-decoration:
underline;">Ok</a> </p>';
                        // Include the footer and quit the script (to not show the form).
                        include('../includes/footer.html');
                        exit();
                    } else { // If it did not run OK.
                        echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your request has failed due to a system error. We apologize for
any inconvenience.</p>';
                        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>';
                        include('../includes/footer.html');
                        exit();
                    }
                } else {
                    echo '<h1 id="mainhead"><br>Error!</h1>
 <p class="error">You have already requested agent work for this supplier.</p>';
                }
            } else {
                echo '<h1 id="mainhead"><br>Error!</h1>
 <p class="error">Registered supplier not found.</p>';
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
    ?>
    <br />
    <p> <a href="listOfSuppliers.php" style="color: blue; text-decoration:
underline;">Back</a></p><br />
    <h2>Request for agent work</h2>
    <form action="reqAgentWork.php" method="post">
        <p>
            <label for="supplier_id">Supplier:</label>
            <select id="supplier_id" name="supplier_id">
                <option value="" selected disabled>Please choose a supplier id</option>
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Retrieving supplier ID from the session
                $supplier_id = $_SESSION['supplier_id'];
                // Fetch suppliers from the database
                $query = mysqli_query($connection, "SELECT suppliers.supplier_id,
suppliers.supplierName FROM suppliers");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $supplier_id = $row['supplier_id'];
                        $supplier_name = $row['supplierName'];
                        echo '<option value="' . $supplier_id . '"> ' . $supplier_id . '</option>';
                    }
                } else {
                    echo "Error fetching suppliers: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>
        <p><input type="submit" name="submit" value="Request" /></p>
        <input type="hidden" name="submitReqAgentWork" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>