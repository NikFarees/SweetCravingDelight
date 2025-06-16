<html>

<head>
    <title>Agent Performance Dashboard</title>
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

        .chart-controls {
            background: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .control-group {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .control-group label {
            font-weight: bold;
            color: #2c3e50;
        }

        .control-group select, .control-group input {
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }

        .control-group button {
            padding: 8px 15px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .control-group button:hover {
            background: #2980b9;
        }

        .performance-bar {
            background: #ecf0f1;
            height: 20px;
            border-radius: 10px;
            margin: 5px 0;
            overflow: hidden;
        }

        .performance-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 0.5s ease;
        }

        .rank-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .rank-1 {
            background: #f39c12;
            color: white;
        }

        .rank-2 {
            background: #95a5a6;
            color: white;
        }

        .rank-3 {
            background: #e67e22;
            color: white;
        }

        .rank-other {
            background: #bdc3c7;
            color: #2c3e50;
        }

        table {
            border-collapse: collapse;
        }

        table td {
            padding: 8px;
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

    // Initialize variables for search and chart display
    $searchQuery = "";
    $chartLimit = isset($_GET['chart_limit']) ? (int)$_GET['chart_limit'] : 3; // Default to top 3
    
    if (isset($_GET['search'])) {
        $searchQuery = $_GET['search'];
        $searchCondition = " AND (agents.agent_id LIKE '%$searchQuery%' OR agents.agentName LIKE '%$searchQuery%')";
    } else {
        $searchCondition = "";
    }

    // Enhanced query with more metrics
    $query = "SELECT 
        agents.agent_id, 
        agents.agentName, 
        COUNT(orders.order_id) AS totalOrders,
        SUM(products.productPrice * orders.orderQuantity) AS totalPrice,
        AVG(products.productPrice * orders.orderQuantity) AS avgOrderValue,
        MAX(orders.orderDate) AS lastOrderDate,
        MIN(orders.orderDate) AS firstOrderDate
    FROM agents
    INNER JOIN relationships ON agents.agent_id = relationships.agent_id
    INNER JOIN orders ON agents.agent_id = orders.agent_id
    INNER JOIN products ON orders.product_id = products.product_id
    WHERE relationships.supplier_id = '$supplier_id' 
    AND products.supplier_id = '$supplier_id' 
    AND orders.approval_status = 'approved'
    $searchCondition
    GROUP BY agents.agent_id, agents.agentName
    ORDER BY totalPrice DESC";

    $result = mysqli_query($dbc, $query);
    $agents_data = [];
    $total_agents = 0;
    $total_orders = 0;
    $total_revenue = 0;

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $agents_data[] = $row;
            $total_agents++;
            $total_orders += $row['totalOrders'];
            $total_revenue += $row['totalPrice'];
        }
    }

    // Calculate additional metrics
    $avg_revenue_per_agent = $total_agents > 0 ? $total_revenue / $total_agents : 0;
    $top_performer = !empty($agents_data) ? $agents_data[0] : null;

    // Prepare chart data with limit
    $chart_agents = array_slice($agents_data, 0, $chartLimit);
    ?>

    <div class="dashboard-container">
        <br>
        <p><a href="AgentList.php" style="color: blue; text-decoration: underline;">Back</a></p><br/>

        <h2>Agent Performance Dashboard</h2>

        <!-- Search Form -->
        <form method="GET" action="AgentPerformance.php" style="margin: 20px 0;">
            <input type="text" name="search" placeholder="Search by Agent ID or Name"
                value="<?php echo htmlspecialchars($searchQuery); ?>" style="width: 250px; padding: 8px;">
            <input type="hidden" name="chart_limit" value="<?php echo $chartLimit; ?>">
            <input type="submit" value="Search" style="padding: 8px 15px;">
            <?php if ($searchQuery): ?>
                <a href="AgentPerformance.php?chart_limit=<?php echo $chartLimit; ?>" style="margin-left: 10px; color: blue;">Clear Search</a>
            <?php endif; ?>
        </form>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_agents; ?></div>
                <div class="stat-label">Active Agents</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_orders; ?></div>
                <div class="stat-label">Total Orders</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">RM <?php echo number_format($total_revenue, 2); ?></div>
                <div class="stat-label">Total Revenue</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">RM <?php echo number_format($avg_revenue_per_agent, 2); ?></div>
                <div class="stat-label">Avg Revenue/Agent</div>
            </div>
        </div>

        <?php if (!empty($agents_data)): ?>
            <!-- Chart Controls -->
            <div class="chart-controls">
                <form method="GET" action="AgentPerformance.php" class="control-group">
                    <label for="chart_limit">📊 Display Agents in Charts:</label>
                    <select name="chart_limit" id="chart_limit">
                        <option value="1" <?php echo $chartLimit == 1 ? 'selected' : ''; ?>>Top 1 Agent</option>
                        <option value="2" <?php echo $chartLimit == 2 ? 'selected' : ''; ?>>Top 2 Agents</option>
                        <option value="3" <?php echo $chartLimit == 3 ? 'selected' : ''; ?>>Top 3 Agents</option>
                        <option value="5" <?php echo $chartLimit == 5 ? 'selected' : ''; ?>>Top 5 Agents</option>
                        <option value="10" <?php echo $chartLimit == 10 ? 'selected' : ''; ?>>Top 10 Agents</option>
                        <option value="<?php echo count($agents_data); ?>" <?php echo $chartLimit >= count($agents_data) ? 'selected' : ''; ?>>All Agents (<?php echo count($agents_data); ?>)</option>
                    </select>
                    <?php if ($searchQuery): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($searchQuery); ?>">
                    <?php endif; ?>
                    <button type="submit">Update Charts</button>
                    <span style="color: #666; font-size: 14px;">
                        Currently showing: <strong><?php echo min($chartLimit, count($agents_data)); ?></strong> out of <strong><?php echo count($agents_data); ?></strong> agents
                    </span>
                </form>
            </div>

            <!-- Performance Charts -->
            <div class="charts-row">
                <div class="chart-container">
                    <h3>Orders Analysis (Top <?php echo min($chartLimit, count($agents_data)); ?>)</h3>
                    <canvas id="ordersChart"></canvas>
                </div>

                <div class="chart-container">
                    <h3>Revenue Analysis (Top <?php echo min($chartLimit, count($agents_data)); ?>)</h3>
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

            <!-- Detailed Performance Table -->
            <br />
            <h3>Complete Agent Performance Table</h3>
            <table border="1" width="100%">
                <tr>
                    <td><b>Rank</b></td>
                    <td><b>Agent</b></td>
                    <td><b>Orders</b></td>
                    <td><b>Revenue</b></td>
                    <td><b>Avg Order Value</b></td>
                    <td><b>Performance</b></td>
                    <td><b>Period</b></td>
                </tr>
                <?php
                $max_revenue = !empty($agents_data) ? $agents_data[0]['totalPrice'] : 1;
                foreach ($agents_data as $index => $agent):
                    $rank = $index + 1;
                    $performance_percentage = ($agent['totalPrice'] / $max_revenue) * 100;

                    // Determine rank badge class
                    $rank_class = 'rank-other';
                    if ($rank == 1) $rank_class = 'rank-1';
                    elseif ($rank == 2) $rank_class = 'rank-2';
                    elseif ($rank == 3) $rank_class = 'rank-3';

                    // Determine performance bar color
                    $bar_color = '#3498db';
                    if ($performance_percentage >= 80) $bar_color = '#27ae60';
                    elseif ($performance_percentage >= 60) $bar_color = '#f39c12';
                    elseif ($performance_percentage >= 40) $bar_color = '#e67e22';
                    else $bar_color = '#e74c3c';
                    
                    // Highlight if agent is shown in charts
                    $row_style = $index < $chartLimit ? 'background-color: #f0f8ff;' : '';
                ?>
                    <tr style="<?php echo $row_style; ?>">
                        <td>
                            <span class="rank-badge <?php echo $rank_class; ?>">
                                #<?php echo $rank; ?>
                            </span>
                            <?php if ($index < $chartLimit): ?>
                                <small style="color: #3498db;">📊 In Charts</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($agent['agentName']); ?></strong><br>
                            <small>ID: <?php echo $agent['agent_id']; ?></small>
                        </td>
                        <td><?php echo $agent['totalOrders']; ?></td>
                        <td>
                            <strong>RM <?php echo number_format($agent['totalPrice'], 2); ?></strong>
                        </td>
                        <td>RM <?php echo number_format($agent['avgOrderValue'], 2); ?></td>
                        <td>
                            <div class="performance-bar">
                                <div class="performance-fill"
                                    style="width: <?php echo $performance_percentage; ?>%; background: <?php echo $bar_color; ?>;">
                                </div>
                            </div>
                            <small><?php echo number_format($performance_percentage, 1); ?>% of top performer</small>
                        </td>
                        <td>
                            <small>
                                From: <?php echo date('M d, Y', strtotime($agent['firstOrderDate'])); ?><br>
                                Last: <?php echo date('M d, Y', strtotime($agent['lastOrderDate'])); ?>
                            </small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- Performance Insights -->
            <div style="margin-top: 30px; padding: 20px; background: #ecf0f1; border-radius: 8px;">
                <h3>Performance Insights</h3>
                <?php if ($top_performer): ?>
                    <p><strong>🏆 Top Performer:</strong> <?php echo htmlspecialchars($top_performer['agentName']); ?>
                        with RM <?php echo number_format($top_performer['totalPrice'], 2); ?> in revenue</p>
                <?php endif; ?>
                <p><strong>📊 Average Order Value:</strong> RM <?php echo number_format($total_revenue / $total_orders, 2); ?></p>
                <p><strong>📈 Growth Opportunity:</strong>
                    <?php
                    $bottom_performers = array_slice($agents_data, -2);
                    if (!empty($bottom_performers)) {
                        echo "Focus on supporting " . htmlspecialchars($bottom_performers[0]['agentName']);
                        if (count($bottom_performers) > 1) {
                            echo " and " . htmlspecialchars($bottom_performers[1]['agentName']);
                        }
                        echo " to improve overall performance";
                    }
                    ?>
                </p>
            </div>

        <?php else: ?>
            <p class="error" style="text-align: center; padding: 40px;">
                <?php echo $searchQuery ? "No agents found matching your search." : "No agent performance data available."; ?>
            </p>
        <?php endif; ?>
    </div>

    <script>
        // Orders Analysis Chart
        <?php if (!empty($chart_agents)): ?>
            const ordersCtx = document.getElementById('ordersChart').getContext('2d');
            new Chart(ordersCtx, {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_map(function ($agent) {
                                    return htmlspecialchars($agent['agentName']);
                                }, $chart_agents)) . "'"; ?>],
                    datasets: [{
                        label: 'Total Orders',
                        data: [<?php echo implode(', ', array_map(function ($agent) {
                                    return $agent['totalOrders'];
                                }, $chart_agents)); ?>],
                        backgroundColor: [
                            '#3498db', '#2ecc71', '#f39c12', '#e74c3c',
                            '#9b59b6', '#e67e22', '#1abc9c', '#34495e',
                            '#95a5a6', '#16a085'
                        ],
                        borderColor: [
                            '#2980b9', '#27ae60', '#f1c40f', '#c0392b',
                            '#8e44ad', '#d35400', '#16a085', '#2c3e50',
                            '#7f8c8d', '#138d75'
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
                                text: 'Number of Orders',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                }
                            },
                            ticks: {
                                stepSize: 1
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Agents',
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
                                    return context.parsed.y + ' orders';
                                }
                            }
                        }
                    }
                }
            });

            // Revenue Analysis Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: [<?php echo "'" . implode("', '", array_map(function ($agent) {
                                    return htmlspecialchars($agent['agentName']);
                                }, $chart_agents)) . "'"; ?>],
                    datasets: [{
                        label: 'Revenue (RM)',
                        data: [<?php echo implode(', ', array_map(function ($agent) {
                                    return $agent['totalPrice'];
                                }, $chart_agents)); ?>],
                        backgroundColor: [
                            '#e74c3c', '#27ae60', '#f39c12', '#3498db',
                            '#9b59b6', '#e67e22', '#1abc9c', '#34495e',
                            '#95a5a6', '#16a085'
                        ],
                        borderColor: [
                            '#c0392b', '#229954', '#d68910', '#2980b9',
                            '#8e44ad', '#d35400', '#16a085', '#2c3e50',
                            '#7f8c8d', '#138d75'
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
                                text: 'Agents',
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
    </script>

    <?php
    mysqli_close($dbc);
    include('../includes/footer.html');
    ?>
</body>

</html>