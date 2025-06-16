<html>

<head>
    <title>Manage Feedback</title>
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
    // Retrieving supplier ID from the session
    $supplier_id = $_SESSION['supplier_id'];

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (f.feedback_subject LIKE '%$searchQuery%' OR a.agentName LIKE '%$searchQuery%')";
    }

    // Check if viewing specific feedback details
    $view_feedback_id = isset($_GET['view']) ? mysqli_real_escape_string($dbc, $_GET['view']) : null;

    if ($view_feedback_id) {
        // Display feedback details
        $detail_query = "SELECT f.*, a.agentName 
                        FROM feedback f 
                        INNER JOIN agents a ON f.agent_id = a.agent_id 
                        WHERE f.feedback_id = '$view_feedback_id' AND f.supplier_id = '$supplier_id'";
        $detail_result = mysqli_query($dbc, $detail_query);
        
        if ($detail_result && mysqli_num_rows($detail_result) == 1) {
            $feedback = mysqli_fetch_assoc($detail_result);
            
            // Mark as read if it's unread
            if ($feedback['status'] == 'unread') {
                $read_query = "UPDATE feedback SET status = 'read' WHERE feedback_id = '$view_feedback_id'";
                mysqli_query($dbc, $read_query);
                $feedback['status'] = 'read';
            }
            
            echo '<br><h2>Feedback Details <br /><br /></h2>';
            echo '<p><a href="manageFeedback.php" style="color: blue; text-decoration: underline;">Back</a></p><br/>';
            
            echo '<table border="1" width="100%">
                  <tr><td><b>From Agent:</b></td><td>' . htmlspecialchars($feedback['agentName']) . '</td></tr>
                  <tr><td><b>Subject:</b></td><td>' . htmlspecialchars($feedback['feedback_subject']) . '</td></tr>
                  <tr><td><b>Date Received:</b></td><td>' . date('M d, Y H:i', strtotime($feedback['feedback_date'])) . '</td></tr>
                  <tr><td><b>Status:</b></td><td>' . ucfirst($feedback['status']) . '</td></tr>
                  <tr><td><b>Agent Message:</b></td><td>' . nl2br(htmlspecialchars($feedback['feedback_message'])) . '</td></tr>';
            
            if ($feedback['supplier_response']) {
                echo '<tr><td><b>Your Response:</b></td><td>' . nl2br(htmlspecialchars($feedback['supplier_response'])) . '<br/><small><em>Replied on: ' . date('M d, Y H:i', strtotime($feedback['response_date'])) . '</em></small></td></tr>';
            } else {
                echo '<tr><td><b>Your Response:</b></td><td><em>No response sent yet</em></td></tr>';
            }
            
            echo '</table>';
            
            // Show reply link if not responded yet
            if ($feedback['status'] != 'responded') {
                echo '<br/><p><a href="replyFeedback.php?feedback_id=' . $feedback['feedback_id'] . '" style="color: blue; text-decoration: underline;">Reply to this Feedback</a></p>';
            }
            
        } else {
            echo '<p class="error"><br>Feedback not found.</p>';
        }
    } else {
        // Make the query for feedback list
        $query = "SELECT f.feedback_id, f.feedback_subject, f.feedback_date, f.status, a.agentName 
                  FROM feedback f 
                  INNER JOIN agents a ON f.agent_id = a.agent_id 
                  WHERE f.supplier_id = '$supplier_id'" . $searchCondition . " 
                  ORDER BY f.feedback_date DESC";
        
        echo '<br><h2>Manage Feedback <br /><br /></h2>';
        $result = @mysqli_query($dbc, $query); // Run the query.
        $num = @mysqli_num_rows($result);
        
        if ($num > 0) { // If it ran OK, display the records.
            // Search form
            echo '<form method="GET" action="manageFeedback.php">
                  <input type="text" name="search" placeholder="Search by Subject or Agent Name" value="' . $searchQuery . '" style="width: 230px;">
                  <input type="submit" value="Search">
                  </form><br/>
                  <table border="1" width="100%">
                  <tr>
                  <td><b>Feedback ID</b></td>
                  <td><b>Agent Name</b></td>
                  <td><b>Subject</b></td>
                  <td><b>Date</b></td>
                  <td><b>Status</b></td>
                  <td><b>Action</b></td>
                  </tr>';
            
            // Fetch and print all the records.
            while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                $status_color = '';
                if ($row['status'] == 'unread') $status_color = 'style="color: red; font-weight: bold;"';
                elseif ($row['status'] == 'read') $status_color = 'style="color: orange; font-weight: bold;"';
                elseif ($row['status'] == 'responded') $status_color = 'style="color: green; font-weight: bold;"';
                
                echo '<tr>'
                    . '<td>' . $row['feedback_id'] . '</td>'
                    . '<td>' . $row['agentName'] . '</td>'
                    . '<td>' . $row['feedback_subject'] . '</td>'
                    . '<td>' . date('M d, Y', strtotime($row['feedback_date'])) . '</td>'
                    . '<td><span ' . $status_color . '>' . ucfirst($row['status']) . '</span></td>'
                    . '<td><a href="manageFeedback.php?view=' . $row['feedback_id'] . '" style="color: blue; text-decoration: underline;">View Details</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            @mysqli_free_result($result); // Free up the resources.
        } else {
            echo '<form method="GET" action="manageFeedback.php">
                  <input type="text" name="search" placeholder="Search by Subject or Agent Name" value="' . $searchQuery . '" style="width: 230px;">
                  <input type="submit" value="Search">
                  </form><br/>';
            echo '<p class="error"><br>No feedback received yet.</p>';
        }
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>