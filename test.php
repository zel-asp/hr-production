<?php
// Save this as phpmyadmin.php in your project root

// Your Railway database credentials
$host = 'mysql.railway.internal';
$port = '3306';
$database = 'railway';
$username = 'root';
$password = 'PFehwhnPqYpMrSswkAQYgWmWJQHPrQIp';

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['sql_query'])) {
        $stmt = $pdo->query($_POST['sql_query']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $message = "Query executed successfully!";
    }
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>

    <head>
        <title>Simple Database Manager</title>
        <style>
            body {
                font-family: Arial;
                padding: 20px;
            }

            textarea {
                width: 100%;
                height: 100px;
            }

            .error {
                color: red;
            }

            .success {
                color: green;
            }

            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 20px;
            }

            th,
            td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
            }
        </style>
    </head>

    <body>
        <h1>Simple Database Manager</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?= $error ?></div>
            <?php endif; ?>

        <?php if (isset($message)): ?>
            <div class="success"><?= $message ?></div>
            <?php endif; ?>

        <form method="POST">
            <label>Enter SQL Query:</label><br>
            <textarea name="sql_query"
                placeholder="e.g., ALTER TABLE job_postings DROP COLUMN column_name"></textarea><br>
            <button type="submit">Execute</button>
        </form>

        <?php if (isset($result) && !empty($result)): ?>
            <h3>Results:</h3>
            <table>
                <thead>
                    <tr>
                            <?php foreach (array_keys($result[0]) as $column): ?>
                            <th><?= htmlspecialchars($column) ?></th>
                            <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach ($result as $row): ?>
                        <tr>
                                <?php foreach ($row as $value): ?>
                                <td><?= htmlspecialchars($value ?? 'NULL') ?></td>
                                <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>

        <h3>Quick Commands:</h3>
        <ul>
            <li><strong>Show tables:</strong> SHOW TABLES;</li>
            <li><strong>Describe table:</strong> DESCRIBE job_postings;</li>
            <li><strong>Drop column:</strong> ALTER TABLE job_postings DROP COLUMN column_name;</li>
        </ul>
    </body>

</html>