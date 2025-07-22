<?php
require_once 'config.php';

// Handle form submission for inserting data
$message = '';
if ($_POST && isset($_POST['action'])) {
    if ($_POST['action'] === 'insert' && isset($_POST['name'], $_POST['email'])) {
        try {
            // Create table if it doesn't exist
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
            $stmt->execute([trim($_POST['name']), trim($_POST['email'])]);
            $message = "<div class='success'>✅ User added successfully!</div>";
        } catch (PDOException $e) {
            $message = "<div class='error'>❌ Error inserting data: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}
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
            margin-bottom: 20px;
        }

        .error {
            color: #dc3545;
            background-color: #f8d7da;
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #f5c6cb;
            margin-bottom: 20px;
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

        .form-container {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .btn-secondary {
            background-color: #6c757d;
        }

        .btn-secondary:hover {
            background-color: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>PHP Web App - Azure CI/CD Demo</h1>

        <?php echo $message; ?>

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
              

            <!-- New Section: Add User Form -->
            <div class="section">
                <h2>➕ Add New User</h2>
                <div class="form-container">
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="insert">
                        
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" id="name" name="name" required maxlength="100" 
                                   placeholder="Enter user name">
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" required maxlength="100" 
                                   placeholder="Enter user email">
                        </div>
                        
                        <button type="submit">Add User</button>
                        <button type="reset" class="btn-secondary">Clear Form</button>
                    </form>
                </div>
            </div>

            <!-- New Section: Display Users Data -->
            <div class="section">
                <h2>👥 Users Data</h2>
                <?php
                try {
                    // Create table if it doesn't exist
                    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        name VARCHAR(100) NOT NULL,
                        email VARCHAR(100) NOT NULL,
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                    )");

                    // Get all users
                    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
                    $users = $stmt->fetchAll();

                    if (empty($users)) {
                        echo "<div class='info'>No users found. Add some users using the form above!</div>";
                    } else {
                        echo "<table>";
                        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";
                        
                        foreach ($users as $user) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($user['id']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                            echo "<td>" . htmlspecialchars($user['created_at']) . "</td>";
                            echo "</tr>";
                        }
                        echo "</table>";
                        
                        echo "<p><strong>Total Users:</strong> " . count($users) . "</p>";
                    }

                } catch (PDOException $e) {
                    echo "<div class='error'>Error fetching users: " . htmlspecialchars($e->getMessage()) . "</div>";
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
                        echo "<div class='info'>No tables found in the database.</div>";
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