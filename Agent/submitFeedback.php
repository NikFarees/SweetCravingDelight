<html>

<head>
    <title>Submit Feedback</title>
    <style>
        textarea {
            border: 1px solid #ccc;
            background-color: #fff;
            padding: 5px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            resize: vertical;
        }
        input[type="text"], select {
            border: 1px solid #ccc;
            padding: 5px;
            font-size: 14px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <?php
    include('../includes/headerAgent.html');
    require_once('../mysqli.php'); // Connect to the db.
    global $dbc;
    // Start the session.
    session_start();
    // Check if agent is logged in
    if (!isset($_SESSION['agent_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }
    // Retrieving agent ID from the session
    $agent_id = $_SESSION['agent_id'];

    if (isset($_POST['submitFeedback'])) {
        $errors = array();
        // Check for a supplier id
        if (empty($_POST['supplier_id']))
            $errors[] = 'You forgot to select a supplier.';
        // Check for a subject
        if (empty($_POST['subject']))
            $errors[] = 'You forgot to enter feedback subject.';
        // Check for a message
        if (empty($_POST['message']))
            $errors[] = 'You forgot to enter feedback message.';

        if (empty($errors)) {
            $supplier_id = mysqli_real_escape_string($dbc, $_POST['supplier_id']);
            $subject = mysqli_real_escape_string($dbc, $_POST['subject']);
            $message = mysqli_real_escape_string($dbc, $_POST['message']);

            // Verify agent is approved for this supplier
            $approval_query = "SELECT s.supplier_id, s.supplierName, s.supplierCategory 
                              FROM suppliers s 
                              INNER JOIN agents_approval aa ON s.supplier_id = aa.supplier_id 
                              WHERE aa.agent_id = '$agent_id' AND s.supplier_id = '$supplier_id' 
                              AND aa.approval_status = 'approved'";
            $approval_result = @mysqli_query($dbc, $approval_query);

            if (mysqli_num_rows($approval_result) == 1) {
                // Insert feedback into database
                $insert_query = "INSERT INTO feedback (agent_id, supplier_id, feedback_subject, 
                               feedback_message, feedback_date, status) 
                               VALUES ('$agent_id', '$supplier_id', '$subject', '$message', NOW(), 'unread')";
                $insert_result = mysqli_query($dbc, $insert_query);

                if ($insert_result) {
                    echo '<h1 id="mainhead"><br/>Thank you!</h1>
                          <p>Your feedback has been submitted successfully!</p><p><br /></p>';
                    echo '<p> <a href="manageFeedback.php" style="color: blue; text-decoration:
                          underline;">Ok</a> </p>';
                    include('../includes/footer.html');
                    exit();
                } else {
                    echo '<h1 id="mainhead">System Error</h1>
                          <p class="error">Error submitting feedback. Please try again.</p>';
                }
            } else {
                echo '<br><h1 id="mainhead">Error!</h1>
                      <p class="error">You are not approved to send feedback to this supplier.</p>';
            }
        } else {
            echo '<br><h1 id="mainhead">Error!</h1>
                  <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) { // Print each error.
                echo " - $msg<br />\n";
            }
            echo '</p><br><p>Please try again.</p><p><br /></p>';
        }
        mysqli_close($dbc); // Close the database connection.
    }
    ?>
    <br />
    <p> <a href="manageFeedback.php" style="color: blue; text-decoration:
underline;">Back</a></p><br />
    <h2>Submit Feedback</h2>
    <form action="submitFeedback.php" method="post">
        <p>
            <label for="supplier_id">Supplier:</label>
            <select id="supplier_id" name="supplier_id">
                <option value="" selected disabled>Please choose a supplier</option>
                <?php
                // Database connection
                $connection = mysqli_connect("localhost", "root", "root", "project_ip_dropship");
                if (!$connection) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                // Debug: Let's see what we get from the query
                $debug_query = "SELECT s.supplier_id, s.supplierName, s.supplierCategory, aa.approval_status
                               FROM suppliers s 
                               INNER JOIN agents_approval aa ON s.supplier_id = aa.supplier_id 
                               WHERE aa.agent_id = '$agent_id'";
                $debug_result = mysqli_query($connection, $debug_query);

                echo "<!-- Debug: Agent ID = $agent_id -->";
                if ($debug_result) {
                    while ($debug_row = mysqli_fetch_assoc($debug_result)) {
                        echo "<!-- Debug: Supplier " . $debug_row['supplier_id'] . " - " . $debug_row['supplierName'] . " - Status: " . $debug_row['approval_status'] . " -->";
                    }
                }

                // Fetch approved suppliers from the database
                $query = mysqli_query($connection, "SELECT s.supplier_id, s.supplierName, s.supplierCategory 
                                                   FROM suppliers s 
                                                   INNER JOIN agents_approval aa ON s.supplier_id = aa.supplier_id 
                                                   WHERE aa.agent_id = '$agent_id' AND aa.approval_status = 'approved'
                                                   ORDER BY s.supplierName");
                if ($query) {
                    $supplier_count = 0;
                    // dropdown list
                    while ($row = mysqli_fetch_assoc($query)) {
                        $supplier_count++;
                        $supplier_id = $row['supplier_id'];
                        $supplier_name = $row['supplierName'];
                        $supplier_category = $row['supplierCategory'];
                        $selected = (isset($_POST['supplier_id']) && $_POST['supplier_id'] == $supplier_id) ? 'selected' : '';
                        echo '<option value="' . $supplier_id . '" ' . $selected . '>' .
                            htmlspecialchars($supplier_name) . ' (' . htmlspecialchars($supplier_category) . ')</option>';
                    }

                    if ($supplier_count == 0) {
                        echo '<option value="" disabled>No approved suppliers found</option>';
                    }
                    echo "<!-- Debug: Found $supplier_count approved suppliers -->";
                } else {
                    echo "Error fetching suppliers: " . mysqli_error($connection);
                }
                // Close the database connection
                mysqli_close($connection);
                ?>
            </select>
        </p>

        <p><label for="subject">Subject</label>:
            <input type="text" id="subject" name="subject" size="50" maxlength="100"
                value="<?php if (isset($_POST['subject'])) echo htmlspecialchars($_POST['subject']); ?>" />
        </p>

        <p><label for="message">Message</label>:<br />
            <textarea id="message" name="message" rows="8" cols="60"
                placeholder="Enter your detailed feedback here..."><?php if (isset($_POST['message'])) echo htmlspecialchars($_POST['message']); ?></textarea>
        </p>

        <p><input type="submit" name="submit" value="Submit Feedback" /></p>
        <input type="hidden" name="submitFeedback" value="TRUE" />
    </form>

    <?php
    include('../includes/footer.html');
    ?>
</body>

</html>