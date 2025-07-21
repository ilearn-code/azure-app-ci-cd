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
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>PHP Web App - Azure CI/CD Demo</h1>
    <h2>Users from Database:</h2>

    <?php
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
        echo "<table>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['email']) . "</td>";
            echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
    ?>

    <p><strong>Deployment Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
</body>

</html>