<?php
// ============================================
// FIXED CORS HANDLER - Place this at the top
// ============================================
// Allow from any origin
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

// Your existing code continues...
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

// Helper functions
function sendResponse($data, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function sendError($message, $status = 400)
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['error' => $message]);
    exit;
}

function getRequestData()
{
    $data = json_decode(file_get_contents('php://input'), true);
    return $data ?: [];
}

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
    }

    sendResponse($result);
}

// POST: Handle compensation actions
if ($method === 'POST') {
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

            $db->query(
                "UPDATE compensation_reviews SET status = 'approved', finance_approved_at = NOW() WHERE id = ?",
                [$id]
            );

            if (!empty($review['proposed_hourly_rate'])) {
                $db->query(
                    "UPDATE employees SET hourly_rate = ? WHERE id = ?",
                    [$review['proposed_hourly_rate'], $review['employee_id']]
                );
            }

            $db->commit();

            $newRate = $review['proposed_hourly_rate'] ?? number_format($review['proposed_salary'] / 160, 2);
            notifyEmployee(
                $review['employee_id'],
                'Compensation Approved',
                "Your compensation has been approved! New hourly rate: ₱" . $newRate
            );

            sendResponse([
                'success' => true,
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

        sendResponse(['success' => true, 'message' => 'Compensation rejected']);
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

        sendResponse(['success' => true, 'id' => $db->lastInsertId(), 'message' => 'Compensation review created'], 201);
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

    sendResponse(['success' => true, 'message' => 'Compensation updated']);
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
    sendResponse(['success' => true, 'message' => 'Compensation deleted']);
}
?>