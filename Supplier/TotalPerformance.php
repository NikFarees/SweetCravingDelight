<html>

<head>
    <title>Total Performance Dashboard</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            color: #666;
            margin-top: 5px;
            font-size: 0.9em;
        }

        .charts-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .chart-container {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            height: 500px;
        }

        table {
            border-collapse: collapse;
        }

        table td {
            padding: 8px;
        }

        .performance-summary {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php
    session_start();

    include('../includes/headerSupplier.html');
    require_once('../mysqli.php');
    global $dbc;

    if (!isset($_SESSION['supplier_id'])) {
        header("Location: login.php");
        exit();
    }

    $supplier_id = $_SESSION['supplier_id'];

    // Get overall performance metrics
    $total_query = "SELECT 
        COUNT(orders.order_id) AS totalOrders,
        SUM(products.productPrice * orders.orderQuantity) AS totalRevenue,
        AVG(products.productPrice * orders.orderQuantity) AS avgOrderValue,
        COUNT(DISTINCT agents.agent_id) AS totalAgents,
        COUNT(DISTINCT products.product_id) AS totalProducts
    FROM agents
    INNER JOIN relationships ON agents.agent_id = relationships.agent_id
    INNER JOIN orders ON agents.agent_id = orders.agent_id
    INNER JOIN products ON orders.product_id = products.product_id
    WHERE relationships.supplier_id = '$supplier_id' 
    AND products.supplier_id = '$supplier_id' 
    AND orders.approval_status = 'approved'";

    $total_result = mysqli_query($dbc, $total_query);
    $total_data = mysqli_fetch_assoc($total_result);

    // Get monthly performance for trend analysis
    $monthly_query = "SELECT 
        DATE_FORMAT(orders.orderDate, '%Y-%m') as month,
        COUNT(orders.order_id) AS monthlyOrders,
        SUM(products.productPrice * orders.orderQuantity) AS monthlyRevenue
    FROM agents
    INNER JOIN relationships ON agents.agent_id = relationships.agent_id
    INNER JOIN orders ON agents.agent_id = orders.agent_id
    INNER JOIN products ON orders.product_id = products.product_id
    WHERE relationships.supplier_id = '$supplier_id' 
    AND products.supplier_id = '$supplier_id' 
    AND orders.approval_status = 'approved'
    AND orders.orderDate >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(orders.orderDate, '%Y-%m')
    ORDER BY month";

    $monthly_result = mysqli_query($dbc, $monthly_query);
    $monthly_data = [];
    while ($row = mysqli_fetch_assoc($monthly_result)) {
        $monthly_data[] = $row;
    }

    // Get top performing products
    $products_query = "SELECT 
        products.productName,
        COUNT(orders.order_id) AS productOrders,
        SUM(products.productPrice * orders.orderQuantity) AS productRevenue
    FROM products
    INNER JOIN orders ON products.product_id = orders.product_id
    WHERE products.supplier_id = '$supplier_id' 
    AND orders.approval_status = 'approved'
    GROUP BY products.product_id, products.productName
    ORDER BY productRevenue DESC
    LIMIT 5";

    $products_result = mysqli_query($dbc, $products_query);
    $top_products = [];
    while ($row = mysqli_fetch_assoc($products_result)) {
        $top_products[] = $row;
    }
    ?>

    <div class="dashboard-container">
        <br>
        <p><a href="AgentList.php" style="color: blue; text-decoration: underline;">Back</a></p><br />

        <h2>Total Performance Dashboard</h2>
        <br />
        <?php if ($total_data && $total_data['totalOrders'] > 0): ?>

            <!-- Summary Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">RM <?php echo number_format($total_data['totalRevenue'], 2); ?></div>
                    <div class="stat-label">Total Revenue</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($total_data['totalOrders']); ?></div>
                    <div class="stat-label">Total Orders</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_data['totalAgents']; ?></div>
                    <div class="stat-label">Active Agents</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">RM <?php echo number_format($total_data['avgOrderValue'], 2); ?></div>
                    <div class="stat-label">Avg Order Value</div>
                </div>
            </div>

            <!-- Performance Charts -->
            <div class="charts-row">
                <div class="chart-container">
                    <h3>Revenue Trend (Last 6 Months)</h3>
                    <canvas id="revenueChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Top Performing Products</h3>
                    <canvas id="productsChart"></canvas>
                </div>
            </div>

            <!-- Monthly Performance Trends -->
            <?php if (!empty($monthly_data)): ?>
                <br />
                <h3>Monthly Performance Trends</h3>
                <table border="1" width="100%">
                    <tr>
                        <td><b>Month</b></td>
                        <td><b>Orders</b></td>
                        <td><b>Revenue</b></td>
                        <td><b>Avg Order Value</b></td>
                    </tr>
                    <?php foreach ($monthly_data as $month):
                        $current_avg = $month['monthlyOrders'] > 0 ? $month['monthlyRevenue'] / $month['monthlyOrders'] : 0;
                    ?>
                        <tr>
                            <td><?php echo date('M Y', strtotime($month['month'] . '-01')); ?></td>
                            <td><?php echo $month['monthlyOrders']; ?></td>
                            <td>RM <?php echo number_format($month['monthlyRevenue'], 2); ?></td>
                            <td>RM <?php echo number_format($current_avg, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>

            <!-- Performance Insights -->
            <div class="performance-summary">
                <h3>Performance Insights</h3>
                <p><strong>🏆 Revenue per Agent:</strong> RM <?php echo number_format($total_data['totalRevenue'] / $total_data['totalAgents'], 2); ?></p>
                <p><strong>📊 Order Fulfillment Rate:</strong> 100% (Only approved orders counted)</p>
                <p><strong>📈 Product Diversity:</strong> <?php echo $total_data['totalProducts']; ?> products generating revenue</p>
                <?php if (!empty($top_products)): ?>
                    <p><strong>💡 Top Product:</strong> <?php echo htmlspecialchars($top_products[0]['productName']); ?> with RM <?php echo number_format($top_products[0]['productRevenue'], 2); ?> in revenue</p>
                <?php endif; ?>
            </div>

        <?php else: ?>
            <p class="error" style="text-align: center; padding: 40px;">
                No performance data available. There are currently no approved orders to display performance metrics.
            </p>
        <?php endif; ?>
    </div>

    <script>
        <?php if (!empty($monthly_data)): ?>
            // Revenue Trend Chart (Bar Chart)
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_map(function ($m) {
                                    return date('M Y', strtotime($m['month'] . '-01'));
                                }, $monthly_data)) . "'"; ?>],
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: [<?php echo implode(', ', array_map(function ($m) {
                                    return $m['monthlyRevenue'];
                                }, $monthly_data)); ?>],
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#f39c12', '#27ae60',
                            '#9b59b6', '#e67e22', '#1abc9c', '#34495e',
                            '#95a5a6', '#16a085', '#2ecc71', '#f1c40f'
                        ],
                        borderColor: [
                            '#2980b9', '#c0392b', '#f1c40f', '#229954',
                            '#8e44ad', '#d35400', '#16a085', '#2c3e50',
                            '#7f8c8d', '#138d75', '#27ae60', '#d68910'
                        ],
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 15
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (RM)',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value.toFixed(0);
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Month',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'RM ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        <?php endif; ?>

        // Top Products Chart
        const productsCtx = document.getElementById('productsChart').getContext('2d');
        <?php if (!empty($top_products)): ?>
            new Chart(productsCtx, {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_map(function ($p) {
                                    return htmlspecialchars($p['productName']);
                                }, $top_products)) . "'"; ?>],
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: [<?php echo implode(', ', array_map(function ($p) {
                                    return $p['productRevenue'];
                                }, $top_products)); ?>],
                        backgroundColor: [
                            '#3498db', '#e74c3c', '#f39c12', '#27ae60', '#9b59b6'
                        ],
                        borderColor: [
                            '#2980b9', '#c0392b', '#f1c40f', '#229954', '#8e44ad'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 15
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (RM)',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            },
                            ticks: {
                                callback: function(value) {
                                    return 'RM ' + value.toFixed(0);
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Products',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            },
                            ticks: {
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return 'RM ' + context.parsed.y.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        <?php else: ?>
            // No data chart placeholder
            new Chart(productsCtx, {
                type: 'bar',
                data: {
                    labels: ['No Data'],
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: [0],
                        backgroundColor: ['#bdc3c7'],
                        borderColor: ['#95a5a6'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 15
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Revenue (RM)',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Products',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        <?php endif; ?>
    </script>

    <?php
    mysqli_close($dbc);
    include('../includes/footer.html');
    ?>
</body>

</html>