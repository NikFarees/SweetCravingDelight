<html>

<head>
    <title>Manage Feedback</title>
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

    // Initialize variables for search
    $searchQuery = "";
    $searchCondition = "";
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (f.feedback_subject LIKE '%$searchQuery%' OR s.supplierName LIKE '%$searchQuery%')";
    }

    // Check if viewing specific feedback details
    $view_feedback_id = isset($_GET['view']) ? mysqli_real_escape_string($dbc, $_GET['view']) : null;

    if ($view_feedback_id) {
        // Display feedback details
        $detail_query = "SELECT f.*, s.supplierName 
                        FROM feedback f 
                        INNER JOIN suppliers s ON f.supplier_id = s.supplier_id 
                        WHERE f.feedback_id = '$view_feedback_id' AND f.agent_id = '$agent_id'";
        $detail_result = mysqli_query($dbc, $detail_query);
        
        if ($detail_result && mysqli_num_rows($detail_result) == 1) {
            $feedback = mysqli_fetch_assoc($detail_result);
            
            echo '<br><h2>Feedback Details <br /><br /></h2>';
            echo '<p><a href="manageFeedback.php" style="color: blue; text-decoration: underline;">Back</a></p><br/>';
            
            echo '<table border="1" width="100%">
                  <tr><td><b>To Supplier:</b></td><td>' . htmlspecialchars($feedback['supplierName']) . '</td></tr>
                  <tr><td><b>Subject:</b></td><td>' . htmlspecialchars($feedback['feedback_subject']) . '</td></tr>
                  <tr><td><b>Date Sent:</b></td><td>' . date('M d, Y H:i', strtotime($feedback['feedback_date'])) . '</td></tr>
                  <tr><td><b>Status:</b></td><td>' . ucfirst($feedback['status']) . '</td></tr>
                  <tr><td><b>Your Message:</b></td><td>' . nl2br(htmlspecialchars($feedback['feedback_message'])) . '</td></tr>';
            
            if ($feedback['supplier_response']) {
                echo '<tr><td><b>Supplier Response:</b></td><td>' . nl2br(htmlspecialchars($feedback['supplier_response'])) . '<br/><small><em>Replied on: ' . date('M d, Y H:i', strtotime($feedback['response_date'])) . '</em></small></td></tr>';
            } else {
                echo '<tr><td><b>Supplier Response:</b></td><td><em>No response yet</em></td></tr>';
            }
            
            echo '</table>';
        } else {
            echo '<p class="error"><br>Feedback not found.</p>';
        }
    } else {
        // Make the query for feedback list
        $query = "SELECT f.feedback_id, f.feedback_subject, f.feedback_date, f.status, s.supplierName 
                  FROM feedback f 
                  INNER JOIN suppliers s ON f.supplier_id = s.supplier_id 
                  WHERE f.agent_id = '$agent_id'" . $searchCondition . " 
                  ORDER BY f.feedback_date DESC";
        
        echo '<br><h2>Manage Feedback <br /><br /></h2>';
        $result = @mysqli_query($dbc, $query); // Run the query.
        $num = @mysqli_num_rows($result);
        
        if ($num > 0) { // If it ran OK, display the records.
            // Links and search form
            echo '<p><a href="submitFeedback.php" style="color: blue; text-decoration: underline;">Submit Feedback</a></p><br/>
                  <form method="GET" action="manageFeedback.php">
                  <input type="text" name="search" placeholder="Search by Subject or Supplier" value="' . $searchQuery . '" style="width: 230px;">
                  <input type="submit" value="Search">
                  </form><br/>
                  <table border="1" width="100%">
                  <tr>
                  <td><b>Feedback ID</b></td>
                  <td><b>Supplier</b></td>
                  <td><b>Subject</b></td>
                  <td><b>Date</b></td>
                  <td><b>Status</b></td>
                  <td><b>Action</b></td>
                  </tr>';
            
            // Fetch and print all the records.
            while ($row = @mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                echo '<tr>'
                    . '<td>' . $row['feedback_id'] . '</td>'
                    . '<td>' . $row['supplierName'] . '</td>'
                    . '<td>' . $row['feedback_subject'] . '</td>'
                    . '<td>' . date('M d, Y', strtotime($row['feedback_date'])) . '</td>'
                    . '<td>' . ucfirst($row['status']) . '</td>'
                    . '<td><a href="manageFeedback.php?view=' . $row['feedback_id'] . '" style="color: blue; text-decoration: underline;">View Details</a></td>';
                echo '</tr>';
            }
            echo '</table>';
            @mysqli_free_result($result); // Free up the resources.
        } else {
            echo '<p><a href="submitFeedback.php" style="color: blue; text-decoration: underline;">Submit Feedback</a></p><br/>';
            echo '<form method="GET" action="manageFeedback.php">
                  <input type="text" name="search" placeholder="Search by Subject or Supplier" value="' . $searchQuery . '" style="width: 230px;">
                  <input type="submit" value="Search">
                  </form><br/>';
            echo '<p class="error"><br>No feedback found.</p>';
        }
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>