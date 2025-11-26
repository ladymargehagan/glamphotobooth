<?php
/**
 * Admin Migration Script
 * Migrates admin users from pb_admin to pb_service_providers
 * admin/migrate_admin.php
 */

require_once __DIR__ . '/../settings/core.php';

// Only allow access from localhost or admin
$is_localhost = in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1', 'localhost']);
$is_admin = isset($_SESSION['user_role']) && $_SESSION['user_role'] == 1;

if (!$is_localhost && !$is_admin) {
    http_response_code(403);
    die('Access denied');
}

$db_conn = new db_connection();
$db_conn->db_connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'migrate') {
    try {
        // Get all admins from pb_admin
        $sql = "SELECT * FROM pb_admin";
        $admins = $db_conn->db_fetch_all($sql);

        if (!$admins) {
            echo json_encode(['success' => false, 'message' => 'No admins found in pb_admin table']);
            exit;
        }

        $migrated = 0;
        $errors = [];

        foreach ($admins as $admin) {
            // Check if customer already exists
            $check_sql = "SELECT id FROM pb_customer WHERE email = '" . mysqli_real_escape_string($db_conn->db, $admin['email']) . "' LIMIT 1";
            $existing_customer = $db_conn->db_fetch_one($check_sql);

            $customer_id = null;

            if (!$existing_customer) {
                // Create customer record with role 1 (admin)
                $insert_customer_sql = "INSERT INTO pb_customer (name, email, password, user_role, created_at)
                                        VALUES ('" . mysqli_real_escape_string($db_conn->db, $admin['name']) . "',
                                                '" . mysqli_real_escape_string($db_conn->db, $admin['email']) . "',
                                                '" . mysqli_real_escape_string($db_conn->db, $admin['password']) . "',
                                                1, NOW())";
                if ($db_conn->db_write_query($insert_customer_sql)) {
                    $customer_id = $db_conn->last_insert_id();
                } else {
                    $errors[] = "Failed to create customer for admin: {$admin['email']}";
                    continue;
                }
            } else {
                $customer_id = $existing_customer['id'];
            }

            // Check if service provider already exists for this customer
            $check_provider_sql = "SELECT provider_id FROM pb_service_providers WHERE customer_id = $customer_id LIMIT 1";
            $existing_provider = $db_conn->db_fetch_one($check_provider_sql);

            if (!$existing_provider) {
                // Create service provider profile for admin
                $insert_provider_sql = "INSERT INTO pb_service_providers (customer_id, business_name, description, hourly_rate, created_at)
                                        VALUES ($customer_id,
                                                'Admin - " . mysqli_real_escape_string($db_conn->db, $admin['name']) . "',
                                                'Platform Administrator',
                                                0, NOW())";
                if ($db_conn->db_write_query($insert_provider_sql)) {
                    $migrated++;
                } else {
                    $errors[] = "Failed to create service provider for admin: {$admin['email']}";
                }
            } else {
                $migrated++;
            }
        }

        $result = [
            'success' => true,
            'message' => "Migration complete. $migrated admin(s) migrated.",
            'errors' => $errors
        ];

        if (!empty($errors)) {
            $result['success'] = false;
        }

        echo json_encode($result);
        exit;
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Migration error: ' . $e->getMessage()]);
        exit;
    }
}

// Get current admins
$sql = "SELECT COUNT(*) as count FROM pb_admin";
$result = $db_conn->db_fetch_one($sql);
$admin_count = $result['count'];

// Get migrated admins
$sql = "SELECT COUNT(*) as count FROM pb_customer WHERE user_role = 1";
$result = $db_conn->db_fetch_one($sql);
$migrated_count = $result['count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Migration Tool</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        h1 {
            color: #102152;
            margin-bottom: 20px;
        }
        .status {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .status-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .status-item:last-child {
            margin-bottom: 0;
        }
        .status-label {
            font-weight: 600;
            color: #333;
        }
        .status-value {
            color: #102152;
            font-weight: bold;
        }
        button {
            background: #102152;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s;
        }
        button:hover {
            background: #0d1838;
        }
        button:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 6px;
            margin-top: 20px;
            border: 1px solid #f5c6cb;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Admin Migration Tool</h1>

        <div class="warning">
            <strong>⚠️ Before proceeding:</strong> This migration will move admin users from pb_admin to pb_service_providers with role 1 (admin) in pb_customer. Make a database backup first!
        </div>

        <div class="status">
            <div class="status-item">
                <span class="status-label">Admins in pb_admin:</span>
                <span class="status-value"><?php echo $admin_count; ?></span>
            </div>
            <div class="status-item">
                <span class="status-label">Admins migrated:</span>
                <span class="status-value"><?php echo $migrated_count; ?></span>
            </div>
        </div>

        <?php if ($admin_count > 0 && $admin_count !== $migrated_count): ?>
            <form id="migrationForm">
                <input type="hidden" name="action" value="migrate">
                <button type="submit">Start Migration</button>
            </form>
        <?php elseif ($admin_count === $migrated_count && $admin_count > 0): ?>
            <div class="success">
                ✓ All admins have been migrated successfully.
            </div>
            <p style="text-align: center; margin-top: 20px; color: #666;">
                You can now safely delete the pb_admin table if desired.
            </p>
        <?php else: ?>
            <div class="warning">
                No admins found to migrate.
            </div>
        <?php endif; ?>

        <div id="result" style="margin-top: 20px;"></div>
    </div>

    <script>
        document.getElementById('migrationForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            const button = this.querySelector('button');
            button.disabled = true;
            button.textContent = 'Migrating...';

            try {
                const response = await fetch('', {
                    method: 'POST',
                    body: new FormData(this)
                });

                const data = await response.json();
                const resultDiv = document.getElementById('result');

                if (data.success) {
                    resultDiv.innerHTML = `<div class="success"><strong>✓ Success:</strong> ${data.message}</div>`;
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    resultDiv.innerHTML = `<div class="error"><strong>✗ Error:</strong> ${data.message}</div>`;
                    if (data.errors && data.errors.length > 0) {
                        resultDiv.innerHTML += '<div style="margin-top: 10px;">' + data.errors.map(e => `<div>• ${e}</div>`).join('') + '</div>';
                    }
                    button.disabled = false;
                    button.textContent = 'Start Migration';
                }
            } catch (error) {
                document.getElementById('result').innerHTML = `<div class="error"><strong>✗ Error:</strong> ${error.message}</div>`;
                button.disabled = false;
                button.textContent = 'Start Migration';
            }
        });
    </script>
</body>
</html>
