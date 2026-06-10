<?php
session_start();
header('Content-Type: application/json');

define('ADMIN_PASSWORD_HASH', '$2y$10$bkqBNqLkTEuLKp02UZKSBuF9T9Zg.W6QmSEFX8hoIzE0LpnQ.zBi2');

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'login') {
        $password = $data['password'] ?? '';
        if (password_verify($password, ADMIN_PASSWORD_HASH)) {
            $_SESSION['admin_logged_in'] = true;
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Incorrect admin password.']);
        }
    } elseif ($action === 'logout') {
        session_destroy();
        echo json_encode(['success' => true]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['check'])) {
    $isLoggedIn = (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) || (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
    echo json_encode(['loggedIn' => $isLoggedIn]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request action.']);
}
?>