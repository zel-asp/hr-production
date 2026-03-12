<?php

// Add this at the VERY TOP of api/job-requisition.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Then your existing code...
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

// ... rest of your code
require_once __DIR__ . '/../cors-handler.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/keys.php';

$apiInfo = validateApiKey();
$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];

// GET: Get job requisitions
if ($method === 'GET') {
    $id = $_GET['id'] ?? null;
    $status = $_GET['status'] ?? null;
    
    if ($id) {
        $result = $db->query(
            "SELECT * FROM job_requisitions WHERE id = ?",
            [$id]
        )->fetch_one();
    } elseif ($status) {
        $result = $db->query(
            "SELECT * FROM job_requisitions WHERE status = ? ORDER BY created_at DESC",
            [$status]
        )->find();
    } else {
        $result = $db->query("SELECT * FROM job_requisitions ORDER BY created_at DESC")->find();
    }
    
    sendResponse($result);
}

// POST: Create job requisition
elseif ($method === 'POST') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }
    
    $data = getRequestData();
    
    $required = ['job_title', 'department', 'requested_by', 'positions', 'needed_by'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            sendError("$field is required", 400);
        }
    }
    
    $db->query(
        "INSERT INTO job_requisitions 
         (job_title, department, requested_by, positions, needed_by, priority, justification, status, created_at) 
         VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', NOW())",
        [
            $data['job_title'],
            $data['department'],
            $data['requested_by'],
            $data['positions'],
            $data['needed_by'],
            $data['priority'] ?? 'medium',
            $data['justification'] ?? null
        ]
    );
    
    sendResponse(['id' => $db->lastInsertId(), 'message' => 'Job requisition created'], 201);
}

// PUT: Update job requisition
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
        "UPDATE job_requisitions 
         SET job_title = ?, department = ?, requested_by = ?, 
             positions = ?, needed_by = ?, priority = ?, justification = ?
         WHERE id = ?",
        [
            $data['job_title'] ?? null,
            $data['department'] ?? null,
            $data['requested_by'] ?? null,
            $data['positions'] ?? null,
            $data['needed_by'] ?? null,
            $data['priority'] ?? null,
            $data['justification'] ?? null,
            $id
        ]
    );
    
    sendResponse(['message' => 'Job requisition updated']);
}

// DELETE: Delete job requisition
elseif ($method === 'DELETE') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }
    
    $id = $_GET['id'] ?? '';
    if (!$id) {
        sendError('ID is required', 400);
    }
    
    $db->query("DELETE FROM job_requisitions WHERE id = ?", [$id]);
    sendResponse(['message' => 'Job requisition deleted']);
}

// PATCH: Update status
elseif ($method === 'PATCH') {
    if (!canWrite($apiInfo)) {
        sendError('Write permission required', 403);
    }
    
    $id = $_GET['id'] ?? '';
    $data = getRequestData();
    
    if (!$id || empty($data['status'])) {
        sendError('ID and status are required', 400);
    }
    
    $db->query(
        "UPDATE job_requisitions SET status = ? WHERE id = ?",
        [$data['status'], $id]
    );
    
    sendResponse(['message' => 'Status updated']);
}