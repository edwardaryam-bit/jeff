<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $action = $_GET['action'] ?? '';

    if ($action === 'get_drivers') {
        $stmt = $pdo->prepare("
            SELECT d.id as driver_id, u.name, d.vehicle_type, d.status
            FROM drivers d
            JOIN users u ON d.user_id = u.id
        ");
        $stmt->execute();
        $drivers = $stmt->fetchAll();
        echo json_encode($drivers);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>