<html>

<head>
    <title>Bulk Order Products</title>
    <style>
        .product-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .product-table th,
        .product-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        .product-table th {
            background-color: #f2f2f2;
        }

        .quantity-input {
            width: 60px;
        }

        .total-section {
            background-color: #f9f9f9;
            padding: 15px;
            margin: 20px 0;
            border: 1px solid #ddd;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }

        .bulk-controls {
            margin: 10px 0;
        }
    </style>
    <script>
        function selectAllProducts() {
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]');
            const selectAllBtn = document.getElementById('selectAllBtn');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);

            checkboxes.forEach(cb => cb.checked = !allChecked);
            selectAllBtn.textContent = allChecked ? 'Select All' : 'Deselect All';
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            const checkboxes = document.querySelectorAll('input[name="selected_products[]"]:checked');

            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                const price = parseFloat(row.querySelector('.product-price').textContent);
                const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
                total += price * quantity;
            });

            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        function loadProducts() {
            const supplierId = document.getElementById('supplier_id').value;
            if (supplierId) {
                window.location.href = 'bulkOrder.php?supplier_id=' + supplierId;
            }
        }
    </script>
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
        header("Location: login.php");
        exit();
    }

    // Retrieving agent ID from the session
    $agent_id = $_SESSION['agent_id'];
    $selected_supplier_id = isset($_GET['supplier_id']) ? $_GET['supplier_id'] : '';

    // Process bulk order submission
    if (isset($_POST['submitBulkOrder'])) {
        $errors = array();

        // Check for customer information
        if (empty($_POST['custName']))
            $errors[] = 'You forgot to enter customer name.';
        if (empty($_POST['custAddress']))
            $errors[] = 'You forgot to enter customer address.';
        if (empty($_POST['custPhone']))
            $errors[] = 'You forgot to enter customer phone.';
        if (empty($_POST['supplier_id']))
            $errors[] = 'You forgot to select a supplier.';
        if (empty($_POST['selected_products']))
            $errors[] = 'You must select at least one product.';

        if (empty($errors)) {
            $supplier_id = $_POST['supplier_id'];
            $custName = $_POST['custName'];
            $custAddress = $_POST['custAddress'];
            $custPhone = $_POST['custPhone'];
            $selected_products = $_POST['selected_products'];
            $quantities = $_POST['quantities'];

            // Verify agent is approved for this supplier
            $approval_query = "SELECT supplier_id FROM agents_approval 
                              WHERE agent_id = '$agent_id' AND supplier_id = '$supplier_id' 
                              AND approval_status = 'approved'";
            $approval_result = mysqli_query($dbc, $approval_query);

            if (mysqli_num_rows($approval_result) == 1) {
                $order_success = true;
                $insufficient_stock = array();

                // Check stock availability for all selected products
                foreach ($selected_products as $product_id) {
                    $quantity = isset($quantities[$product_id]) ? (int)$quantities[$product_id] : 0;

                    if ($quantity > 0) {
                        $stock_query = "SELECT productQuantity, productName FROM products 
                                       WHERE product_id = '$product_id' AND supplier_id = '$supplier_id'";
                        $stock_result = mysqli_query($dbc, $stock_query);

                        if ($stock_result && mysqli_num_rows($stock_result) == 1) {
                            $stock_row = mysqli_fetch_assoc($stock_result);
                            $available_quantity = $stock_row['productQuantity'];

                            if ($available_quantity < $quantity) {
                                $insufficient_stock[] = $stock_row['productName'] . " (Available: $available_quantity, Requested: $quantity)";
                                $order_success = false;
                            }
                        } else {
                            $order_success = false;
                        }
                    }
                }

                if ($order_success && empty($insufficient_stock)) {
                    // Insert all orders
                    $orders_inserted = 0;

                    foreach ($selected_products as $product_id) {
                        $quantity = isset($quantities[$product_id]) ? (int)$quantities[$product_id] : 0;

                        if ($quantity > 0) {
                            $insert_query = "INSERT INTO orders (agent_id, product_id, orderQuantity, 
                                           custName, custAddress, custPhone, orderDate) 
                                           VALUES ('$agent_id', '$product_id', $quantity, '$custName', 
                                           '$custAddress', '$custPhone', NOW())";

                            if (mysqli_query($dbc, $insert_query)) {
                                $orders_inserted++;
                            }
                        }
                    }

                    if ($orders_inserted > 0) {
                        echo '<h1 id="mainhead"><br/>Thank you!</h1>
                              <p class="success">Your bulk order has been successfully placed!</p>
                              <p>Total orders placed: ' . $orders_inserted . '</p>
                              <p><a href="listOfProducts.php" style="color: blue; text-decoration: underline;">Continue</a></p>';
                        include('../includes/footer.html');
                        exit();
                    }
                } else {
                    echo '<br><h1 id="mainhead">Error!</h1>
                          <p class="error">Insufficient stock for the following products:</p><ul>';
                    foreach ($insufficient_stock as $stock_error) {
                        echo '<li>' . $stock_error . '</li>';
                    }
                    echo '</ul>';
                }
            } else {
                echo '<br><h1 id="mainhead">Error!</h1>
                      <p class="error">You are not approved to order from this supplier.</p>';
            }
        } else {
            echo '<br><h1 id="mainhead">Error!</h1>
                  <p class="error">The following error(s) occurred:<br />';
            foreach ($errors as $msg) {
                echo " - $msg<br />\n";
            }
            echo '</p><br><p>Please try again.</p>';
        }
    }
    ?>

    <br />
    <p><a href="listofProducts.php" style="color: blue; text-decoration: underline;">Back</a></p><br />
    <h2>Bulk Order Products</h2>

    <form action="bulkOrder.php" method="post">
        <!-- Supplier Selection -->
        <p>
            <label for="supplier_id">Select Supplier:</label>
            <select id="supplier_id" name="supplier_id" onchange="loadProducts()">
                <option value="" selected disabled>Please choose a supplier</option>
                <?php
                // Fetch approved suppliers for this agent
                $supplier_query = "SELECT DISTINCT s.supplier_id, s.supplierName 
                                  FROM suppliers s
                                  INNER JOIN agents_approval aa ON s.supplier_id = aa.supplier_id
                                  WHERE aa.agent_id = '$agent_id' AND aa.approval_status = 'approved'
                                  ORDER BY s.supplierName";
                $supplier_result = mysqli_query($dbc, $supplier_query);

                if ($supplier_result) {
                    while ($supplier_row = mysqli_fetch_assoc($supplier_result)) {
                        $selected = ($supplier_row['supplier_id'] == $selected_supplier_id) ? 'selected' : '';
                        echo '<option value="' . $supplier_row['supplier_id'] . '" ' . $selected . '>' .
                            $supplier_row['supplierName'] . '</option>';
                    }
                }
                ?>
            </select>
        </p>

        <?php if ($selected_supplier_id): ?>
            <!-- Product Selection Table -->
            <div class="bulk-controls">
                <button type="button" id="selectAllBtn" onclick="selectAllProducts()">Select All</button>
            </div>

            <table class="product-table">
                <thead>
                    <tr>
                        <th>Select</th>
                        <th>Product ID</th>
                        <th>Product Name</th>
                        <th>Available Quantity</th>
                        <th>Price ($)</th>
                        <th>Order Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fetch products for the selected supplier
                    $product_query = "SELECT p.product_id, p.productName, p.productQuantity, p.productPrice
                                 FROM products p
                                 WHERE p.supplier_id = '$selected_supplier_id'
                                 ORDER BY p.productName";
                    $product_result = mysqli_query($dbc, $product_query);

                    if ($product_result && mysqli_num_rows($product_result) > 0) {
                        while ($product_row = mysqli_fetch_assoc($product_result)) {
                            echo '<tr>';
                            echo '<td><input type="checkbox" name="selected_products[]" value="' . $product_row['product_id'] . '" onchange="calculateTotal()"></td>';
                            echo '<td>' . $product_row['product_id'] . '</td>';
                            echo '<td>' . $product_row['productName'] . '</td>';
                            echo '<td>' . $product_row['productQuantity'] . '</td>';
                            echo '<td class="product-price">' . number_format($product_row['productPrice'], 2) . '</td>';
                            echo '<td><input type="number" class="quantity-input" name="quantities[' . $product_row['product_id'] . ']" min="1" max="' . $product_row['productQuantity'] . '" onchange="calculateTotal()"></td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="6">No products found for this supplier.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>

            <!-- Total Amount Display -->
            <div class="total-section">
                <h3>Estimated Total: $<span id="totalAmount">0.00</span></h3>
            </div>

            <!-- Customer Information -->
            <h3>Customer Information</h3>
            <p><label for="custName">Customer Name</label>:
                <input type="text" id="custName" name="custName" size="30" maxlength="100"
                    value="<?php if (isset($_POST['custName'])) echo $_POST['custName']; ?>" required />
            </p>

            <p><label for="custAddress">Customer Address</label>:
                <input type="text" id="custAddress" name="custAddress" size="50" maxlength="200"
                    value="<?php if (isset($_POST['custAddress'])) echo $_POST['custAddress']; ?>" required />
            </p>

            <p><label for="custPhone">Customer Phone</label>:
                <input type="text" id="custPhone" name="custPhone" size="20" maxlength="20"
                    value="<?php if (isset($_POST['custPhone'])) echo $_POST['custPhone']; ?>" required />
            </p>

            <p><input type="submit" name="submit" value="Place Bulk Order" /></p>
            <input type="hidden" name="submitBulkOrder" value="TRUE" />

        <?php endif; ?>
    </form>

    <?php
    mysqli_close($dbc);
    include('../includes/footer.html');
    ?>
</body>

</html>