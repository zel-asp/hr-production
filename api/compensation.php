<?php
// ============================================
// COMPENSATION REVIEWS API - FIXED (No duplicate functions)
// ============================================

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Include required files (functions.php already has sendResponse, getRequestData, etc.)
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

$apiInfo = validateApiKey();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET: Get compensation reviews
if ($method === 'GET') {
    $status = $_GET['status'] ?? null;
    $id = $_GET['id'] ?? null;

    if ($id) {
        $result = $db->query(
            "SELECT cr.*, e.full_name, e.department, e.position, e.email 
             FROM compensation_reviews cr
             JOIN employees e ON cr.employee_id = e.id
             WHERE cr.id = ?",
            [$id]
        )->fetch_one();

        sendResponse($result ?: []);
    } else {
        $sql = "SELECT cr.*, e.full_name, e.department, e.position, e.email 
                FROM compensation_reviews cr
                JOIN employees e ON cr.employee_id = e.id";

        if ($status) {
            $dbStatus = $status;
            if ($status === 'pending') {
                $dbStatus = 'pending_finance';
            } elseif ($status === 'pending_finance') {
                $dbStatus = 'pending_finance';
            }

            $sql .= " WHERE cr.status = ? ORDER BY cr.created_at DESC";
            $result = $db->query($sql, [$dbStatus])->find();
        } else {
            $sql .= " ORDER BY cr.created_at DESC";
            $result = $db->query($sql)->find();
        }

        sendResponse($result ?: []);
    }
}

// POST: Handle compensation actions
elseif ($method === 'POST') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }

    $data = getRequestData();
    $action = $_GET['action'] ?? '';
    $id = $_GET['id'] ?? '';

    // APPROVE COMPENSATION (Finance only)
    if ($action === 'approve' && $id) {
        if ($apiInfo['name'] !== 'Finance System') {
            sendError('Only finance can approve compensation', 403);
        }

        $db->beginTransaction();

        try {
            // Get compensation review
            $review = $db->query(
                "SELECT cr.*, e.id as employee_id, e.full_name, e.email 
                 FROM compensation_reviews cr
                 JOIN employees e ON cr.employee_id = e.id
                 WHERE cr.id = ?",
                [$id]
            )->fetch_one();

            if (!$review) {
                throw new Exception('Compensation review not found');
            }

            // Update status to 'approved'
            $db->query(
                "UPDATE compensation_reviews SET status = 'approved', finance_approved_at = NOW() WHERE id = ?",
                [$id]
            );

            // UPDATE HOURLY RATE using proposed_hourly_rate
            if (!empty($review['proposed_hourly_rate'])) {
                $db->query(
                    "UPDATE employees SET hourly_rate = ? WHERE id = ?",
                    [$review['proposed_hourly_rate'], $review['employee_id']]
                );
            }

            $db->commit();

            // NOTIFY EMPLOYEE
            $newRate = $review['proposed_hourly_rate'] ?? number_format($review['proposed_salary'] / 160, 2);

            try {
                notifyEmployee(
                    $review['employee_id'],
                    'Compensation Approved',
                    "Your compensation has been approved! New hourly rate: ₱" . $newRate
                );
            } catch (Exception $e) {
                error_log("Notification failed: " . $e->getMessage());
            }

            sendResponse([
                'message' => 'Compensation approved successfully',
                'employee' => $review['full_name'],
                'new_rate' => $newRate
            ]);

        } catch (Exception $e) {
            $db->rollBack();
            sendError($e->getMessage(), 500);
        }
    }

    // REJECT COMPENSATION
    elseif ($action === 'reject' && $id) {
        if (empty($data['reason'])) {
            sendError('Rejection reason is required', 400);
        }

        $db->query(
            "UPDATE compensation_reviews SET status = 'rejected', finance_notes = ? WHERE id = ?",
            [$data['reason'], $id]
        );

        sendResponse(['message' => 'Compensation rejected']);
    }

    // CREATE COMPENSATION REVIEW
    elseif ($action === 'create') {
        $required = ['employee_id', 'current_salary', 'proposed_salary', 'proposed_hourly_rate'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                sendError("$field is required", 400);
            }
        }

        $db->query(
            "INSERT INTO compensation_reviews 
             (employee_id, current_salary, proposed_salary, proposed_hourly_rate, 
              review_type, review_date, effective_date, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending_finance', NOW())",
            [
                $data['employee_id'],
                $data['current_salary'],
                $data['proposed_salary'],
                $data['proposed_hourly_rate'],
                $data['review_type'] ?? 'annual',
                date('Y-m-d'),
                $data['effective_date'] ?? date('Y-m-d', strtotime('+30 days'))
            ]
        );

        sendResponse(['id' => $db->lastInsertId(), 'message' => 'Compensation review created'], 201);
    } else {
        sendError('Invalid action', 400);
    }
}

// PUT: Update compensation
elseif ($method === 'PUT') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }

    $id = $_GET['id'] ?? '';
    if (!$id) {
        sendError('ID is required', 400);
    }

    $data = getRequestData();

    $db->query(
        "UPDATE compensation_reviews 
         SET proposed_salary = ?, proposed_hourly_rate = ?, effective_date = ?
         WHERE id = ? AND status = 'pending_finance'",
        [
            $data['proposed_salary'] ?? null,
            $data['proposed_hourly_rate'] ?? null,
            $data['effective_date'] ?? null,
            $id
        ]
    );

    sendResponse(['message' => 'Compensation updated']);
}

// DELETE: Delete compensation
elseif ($method === 'DELETE') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }

    $id = $_GET['id'] ?? '';
    if (!$id) {
        sendError('ID is required', 400);
    }

    $db->query("DELETE FROM compensation_reviews WHERE id = ? AND status = 'pending_finance'", [$id]);
    sendResponse(['message' => 'Compensation deleted']);
}

// Handle unsupported methods
else {
    sendError('Method not allowed', 405);
}
?>