<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html>

<head>
    <title>PHP Azure Demo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .success {
            color: #28a745;
            background-color: #d4edda;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
        }

        .info {
            color: #17a2b8;
            background-color: #d1ecf1;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #bee5eb;
        }

        .section {
            margin-bottom: 30px;
        }

        h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-connected {
            background-color: #28a745;
            color: white;
        }

        .status-disconnected {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>PHP Web App - Azure CI/CD Demo</h1>

        <?php
        // Database connection status
        $dbConnected = false;
        $dbError = '';

        try {
            // Test database connection
            $stmt = $pdo->query("SELECT 1");
            $dbConnected = true;
            echo "<div class='success'><strong>✅ Database Connected Successfully!</strong></div>";
        } catch (PDOException $e) {
            $dbError = $e->getMessage();
            echo "<div class='error'><strong>❌ Database Connection Failed:</strong> " . htmlspecialchars($dbError) . "</div>";
        }
        ?>

        <div class="section">
            <h2>🏗️ Application Configuration</h2>
            <?php
            // Application settings with environment variables
            $settings = [
                'App Name' => 'PHP Azure Demo',
                'Environment' => getenv('ENVIRONMENT') ?: 'Development',
                'Version' => '1.0.0',
                'PHP Version' => phpversion(),
                'Server Host' => $_SERVER['HTTP_HOST'] ?? 'localhost',
                'Database Host' => getenv('DB_HOST') ?: 'localhost',
                'Database Name' => getenv('DB_NAME') ?: 'Not configured',
                'Database User' => getenv('DB_USER') ?: 'Not configured',
                'Max Users' => 100,
                'Feature Flag' => true,
                'SSL Enabled' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
            ];

            // Display settings in a table
            echo "<table>";
            echo "<tr><th>Setting</th><th>Value</th></tr>";
            foreach ($settings as $key => $value) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($key) . "</td>";

                // Format different value types
                if (is_bool($value)) {
                    $displayValue = $value ? '<span class="status-badge status-connected">TRUE</span>' : '<span class="status-badge status-disconnected">FALSE</span>';
                } elseif ($key === 'Database Host' || $key === 'Database Name' || $key === 'Database User') {
                    $displayValue = htmlspecialchars($value);
                    if ($value === 'Not configured') {
                        $displayValue = '<span class="status-badge status-disconnected">' . $displayValue . '</span>';
                    }
                } else {
                    $displayValue = htmlspecialchars($value);
                }

                echo "<td>" . $displayValue . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            ?>
        </div>

        <?php if ($dbConnected): ?>
            <div class="section">
                <h2>🗄️ Database Information</h2>
                <?php
                try {
                    // Get MySQL version
                    $stmt = $pdo->query("SELECT VERSION() as version");
                    $version = $stmt->fetch()['version'];

                    // Get current database
                    $stmt = $pdo->query("SELECT DATABASE() as current_db");
                    $currentDb = $stmt->fetch()['current_db'];

                    // Get current user
                    $stmt = $pdo->query("SELECT USER() as current_user");
                    $currentUser = $stmt->fetch()['current_user'];

                    // Get database size
                    $stmt = $pdo->prepare("
                    SELECT 
                        ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size_mb
                    FROM information_schema.tables 
                    WHERE table_schema = ?
                ");
                    $stmt->execute([$currentDb]);
                    $dbSize = $stmt->fetch()['size_mb'] ?? 0;

                    // Database info array
                    $dbInfo = [
                        'MySQL Version' => $version,
                        'Current Database' => $currentDb,
                        'Current User' => $currentUser,
                        'Database Size' => $dbSize . ' MB',
                        'Connection Time' => date('Y-m-d H:i:s')
                    ];

                    echo "<table>";
                    echo "<tr><th>Database Property</th><th>Value</th></tr>";
                    foreach ($dbInfo as $key => $value) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($key) . "</td>";
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                        echo "</tr>";
                    }
                    echo "</table>";

                } catch (PDOException $e) {
                    echo "<div class='error'>Error fetching database info: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                ?>
            </div>

            <div class="section">
                <h2>📋 Database Tables</h2>
                <?php
                try {
                    // Show tables
                    $stmt = $pdo->query("SHOW TABLES");
                    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

                    if (empty($tables)) {
                        echo "<div class='info'>No tables found in the database. Consider importing your database schema.</div>";
                    } else {
                        echo "<table>";
                        echo "<tr><th>Table Name</th><th>Actions</th></tr>";
                        foreach ($tables as $table) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($table) . "</td>";
                            echo "<td><small>Table exists ✅</small></td>";
                            echo "</tr>";
                        }
                        echo "</table>";

                        // If we have tables, try to show sample data from the first table
                        if (!empty($tables)) {
                            $firstTable = $tables[0];
                            echo "<h3>📊 Sample Data from '{$firstTable}' Table</h3>";

                            try {
                                // Get table structure first
                                $stmt = $pdo->query("DESCRIBE `{$firstTable}`");
                                $columns = $stmt->fetchAll();

                                // Get sample data (limit to 5 rows)
                                $stmt = $pdo->query("SELECT * FROM `{$firstTable}` LIMIT 5");
                                $sampleData = $stmt->fetchAll();

                                if (empty($sampleData)) {
                                    echo "<div class='info'>Table '{$firstTable}' exists but contains no data.</div>";
                                } else {
                                    echo "<table>";
                                    echo "<tr>";
                                    foreach ($columns as $column) {
                                        echo "<th>" . htmlspecialchars($column['Field']) . "</th>";
                                    }
                                    echo "</tr>";

                                    foreach ($sampleData as $row) {
                                        echo "<tr>";
                                        foreach ($row as $value) {
                                            echo "<td>" . htmlspecialchars($value ?? 'NULL') . "</td>";
                                        }
                                        echo "</tr>";
                                    }
                                    echo "</table>";

                                    if (count($sampleData) >= 5) {
                                        echo "<small>Showing first 5 records only...</small>";
                                    }
                                }

                            } catch (PDOException $e) {
                                echo "<div class='error'>Error fetching sample data: " . htmlspecialchars($e->getMessage()) . "</div>";
                            }
                        }
                    }

                } catch (PDOException $e) {
                    echo "<div class='error'>Error fetching tables: " . htmlspecialchars($e->getMessage()) . "</div>";
                }
                ?>
            </div>
        <?php else: ?>
            <div class="section">
                <h2>🔧 Database Setup Required</h2>
                <div class="info">
                    <strong>Database connection is not working.</strong><br>
                    Please check:
                    <ul>
                        <li>Database environment variables are set in Azure App Service</li>
                        <li>Database server is running and accessible</li>
                        <li>Firewall rules allow connection from Azure</li>
                        <li>Database credentials are correct</li>
                    </ul>
                </div>
            </div>
        <?php endif; ?>

        <div class="section">
            <h2>🚀 Deployment Information</h2>
            <table>
                <tr>
                    <th>Property</th>
                    <th>Value</th>
                </tr>
                <tr>
                    <td>Last Deployment</td>
                    <td><?= date('Y-m-d H:i:s') ?></td>
                </tr>
                <tr>
                    <td>Server Time</td>
                    <td><?= date('Y-m-d H:i:s') ?></td>
                </tr>
                <tr>
                    <td>User Agent</td>
                    <td><?= htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown') ?></td>
                </tr>
                <tr>
                    <td>Request Method</td>
                    <td><?= htmlspecialchars($_SERVER['REQUEST_METHOD'] ?? 'Unknown') ?></td>
                </tr>
                <tr>
                    <td>Request URI</td>
                    <td><?= htmlspecialchars($_SERVER['REQUEST_URI'] ?? 'Unknown') ?></td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>