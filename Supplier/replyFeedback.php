<html>

<head>
    <title>Reply Feedback</title>
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

    // Get feedback ID from URL
    $feedback_id = isset($_GET['feedback_id']) ? mysqli_real_escape_string($dbc, $_GET['feedback_id']) : '';

    if (empty($feedback_id)) {
        echo '<p class="error">Invalid feedback ID.</p>';
        echo '<p><a href="manageFeedback.php" style="color: blue; text-decoration: underline;">Back to Feedback List</a></p>';
        include('../includes/footer.html');
        exit();
    }

    // Handle form submission
    if (isset($_POST['submitReply'])) {
        $errors = array();
        
        // Check for response message
        if (empty($_POST['response']))
            $errors[] = 'You forgot to enter your response.';

        if (empty($errors)) {
            $response = mysqli_real_escape_string($dbc, $_POST['response']);
            
            // Update feedback with response
            $update_query = "UPDATE feedback 
                           SET supplier_response = '$response', 
                               response_date = NOW(), 
                               status = 'responded' 
                           WHERE feedback_id = '$feedback_id' AND supplier_id = '$supplier_id'";
            
            $update_result = mysqli_query($dbc, $update_query);
            
            if ($update_result && mysqli_affected_rows($dbc) == 1) {
                echo '<h1><br/>Thank you!</h1>
                      <p>Your response has been sent successfully!</p><p><br /></p>';
                echo '<p><a href="manageFeedback.php" style="color: blue; text-decoration: underline;">Back to Feedback List</a></p>';
                include('../includes/footer.html');
                exit();
            } else {
                echo '<h1>System Error</h1>
                      <p class="error">Error sending response. Please try again.</p>';
            }
        } else {
            echo '<br><h1>Error!</h1>
                  <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) {
                echo " - $msg<br />\n";
            }
            echo '</p><br><p>Please try again.</p><p><br /></p>';
        }
    }

    // Get feedback details
    $detail_query = "SELECT f.*, a.agentName 
                    FROM feedback f 
                    INNER JOIN agents a ON f.agent_id = a.agent_id 
                    WHERE f.feedback_id = '$feedback_id' AND f.supplier_id = '$supplier_id'";
    $detail_result = mysqli_query($dbc, $detail_query);

    if ($detail_result && mysqli_num_rows($detail_result) == 1) {
        $feedback = mysqli_fetch_assoc($detail_result);
        
        if ($feedback['status'] == 'responded') {
            echo '<p class="error">This feedback has already been responded to.</p>';
            echo '<p><a href="manageFeedback.php?view=' . $feedback_id . '" style="color: blue; text-decoration: underline;">View Details</a></p>';
            include('../includes/footer.html');
            exit();
        }
        ?>

        <br><h2>Reply to Feedback <br /><br /></h2>
        
        <p><a href="manageFeedback.php?view=<?php echo $feedback_id; ?>" style="color: blue; text-decoration: underline;">Back</a></p><br/>

        <!-- Display original feedback -->
        <h3>Original Feedback:</h3><br/>
        <table border="1" width="100%">
            <tr><td><b>From Agent:</b></td><td><?php echo htmlspecialchars($feedback['agentName']); ?></td></tr>
            <tr><td><b>Subject:</b></td><td><?php echo htmlspecialchars($feedback['feedback_subject']); ?></td></tr>
            <tr><td><b>Date:</b></td><td><?php echo date('M d, Y H:i', strtotime($feedback['feedback_date'])); ?></td></tr>
            <tr><td><b>Message:</b></td><td><?php echo nl2br(htmlspecialchars($feedback['feedback_message'])); ?></td></tr>
        </table>

        <br/><h3>Your Response:</h3><br/>

        <form action="replyFeedback.php?feedback_id=<?php echo $feedback_id; ?>" method="post">
            <textarea id="response" name="response" rows="8" cols="80" 
                     style="border: 1px solid #000; padding: 5px; font-family: Arial, sans-serif;"
                     placeholder="Enter your response to the agent..."><?php if (isset($_POST['response'])) echo htmlspecialchars($_POST['response']); ?></textarea>
            </p>
            
            <p><input type="submit" name="submit" value="Send Response" style="padding: 8px 16px; background: #007bff; color: white; border: none; cursor: pointer;">
            <input type="hidden" name="submitReply" value="TRUE" /></p>
        </form>

        <?php
    } else {
        echo '<p class="error"><br>Feedback not found or access denied.</p>';
        echo '<p><a href="manageFeedback.php" style="color: blue; text-decoration: underline;">Back to Feedback List</a></p>';
    }

    @mysqli_close($dbc); // Close the database connection.
    include('../includes/footer.html');
    ?>
</body>

</html>