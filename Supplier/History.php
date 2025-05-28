<html>

<head>
    <title>History</title>
</head>

<body>
    <?php
    include('../includes/headerSupplier.html');

    session_start(); // Start the session

    // Check if supplier is logged in
    if (!isset($_SESSION['supplier_id'])) {
        // Redirect to the login page if not logged in
        header("Location: login.php");
        exit();
    }

    echo '<br><h2>History<br /><br /></h2>
 <a href="AgentApprovalHistory.php" style="color: blue; text-decoration:
underline;">Agent Approval History</a><br />
 <br><a href="OrderApprovalHistory.php" style="color: blue; text-decoration:
underline;">Order Approval History</a><br />
 <br><a href="StockHistory.php" style="color: blue; text-decoration: underline;">Stock
History</a>';

    include('../includes/footer.html');
    ?>
</body>

</html>