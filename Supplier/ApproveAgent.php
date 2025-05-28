<html>

<head>
    <title>Approve Agent</title>
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
    // Check if the form has been submitted.
    if (isset($_POST['submitted'])) {
        $errors = array();
        // Check for an agent id.
        if (empty($_POST['agent_id'])) {
            $errors[] = 'You forgot to choose agent id.';
        } else {
            $agent_id = $_POST['agent_id'];
        }
        if (empty($errors)) { // If everything's OK.
            // Retrieving supplier ID from the session
            $supplier_id = $_SESSION['supplier_id'];
            // Check if there is a relationship between the agent and supplier
            $relationship_query = "SELECT * FROM relationships WHERE agent_id='$agent_id' AND
supplier_id='$supplier_id'";
            $relationship_result = mysqli_query($dbc, $relationship_query);
            if (!$relationship_result) {
                echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Error checking relationship. We apologize for any
inconvenience.</p>';
                echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $relationship_query . '</p>';
                include('../includes/footer.html');
                exit();
            }
            $relationship_count = mysqli_num_rows($relationship_result);
            if ($relationship_count > 0) {
                // Relationship exists, inform the user
                echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The selected agent already has a relationship with your
supplier.</p>';
                echo '<p>Please check the relationship before approving the agent.</p><p><br
/></p>';
            } else { // Relationship does not exist, update agent approval
                //
                // Make the UPDATE query.
                $approve_query = "UPDATE agents_approval SET approval_status='approved',
approval_date=NOW() WHERE agent_id='$agent_id' AND supplier_id='$supplier_id'";
                $approve_result = mysqli_query($dbc, $approve_query);
                if ($approve_result) { // If it ran OK.
                    // Add data into the relationships table
                    $relationship_insert_query = "INSERT INTO relationships (supplier_id, agent_id)
VALUES ('$supplier_id', '$agent_id')";
                    $relationship_insert_result = mysqli_query($dbc, $relationship_insert_query);
                    if (!$relationship_insert_result) {
                        echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Error adding data into the relationships table. We apologize
for any inconvenience.</p>';
                        echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' .
                            $relationship_insert_query . '</p>';
                        include('../includes/footer.html');
                        exit();
                    }
                    echo '<br><h1 id="mainhead">Thank you!</h1>
 <p>Agent has been approved. </p><p><br /></p>';
                    echo '<br/><p> <a href="AgentApprovalList.php" style="color: blue; textdecoration: underline;">Ok</a> </p>';
                    include('../includes/footer.html');
                    exit();
                } else { // If it did not run OK.
                    echo '<h1 id="mainhead">System Error</h1>
 <p class="error">Your agent could not be approved due to a system error. We
apologize for any inconvenience.</p>';
                    echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $approve_query . '</p>';
                    include('../includes/footer.html');
                    exit();
                }
            }
        } else { // Report the errors.
            echo '<br><h1 id="mainhead">Error!</h1>
 <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><p>Please try again.</p><p><br /></p>';
        } // End of if (empty($errors)) IF.
        mysqli_close($dbc); // Close the database connection.
    } // End of the main Submit conditional.
    ?>
    <br>
    <p><a href="AgentApprovalList.php" style="color: blue; text-decoration:
underline;">Back</a></p></br>
    <h2>Approve Agent</h2>
    <form action="ApproveAgent.php" method="post">
        <p>Agent Id:
            <select name="agent_id">
                <option value="" selected disabled>Please choose agent id</option>
                <!-- Populate genre options from database -->
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }
                // Retrieving supplier ID from the session
                $supplier_id = $_SESSION['supplier_id'];
                // Fetch genres from the database
                $query = mysqli_query($connection, "SELECT * FROM agents_approval WHERE
supplier_id='$supplier_id' AND approval_status='pending'");
                if ($query) {
                    // drop down list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $agent_id = $row['agent_id'];
                        echo '<option value="' . $agent_id . '"> ' . $agent_id . '</option>';
                    }
                } else {
                    echo "Error fetching agents_approval: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>
        <p><input type="submit" name="submit" value="Approve Agent" /></p>
        <input type="hidden" name="submitted" value="TRUE" />
    </form>
    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>