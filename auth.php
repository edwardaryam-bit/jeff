<?php
session_start();
require_once 'db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'No request data received.']);
    exit;
}

$action = $data['action'] ?? '';

if ($action === 'signup') {
    $name = trim($data['name'] ?? '');
    $email = trim($data['email'] ?? '');
    $password_raw = $data['password'] ?? '';
    $role = trim($data['role'] ?? 'user');
    $vehicle_type = trim($data['vehicle_type'] ?? '');

    if (empty($name) || empty($email) || empty($password_raw)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    if ($role === 'driver' && empty($vehicle_type)) {
        echo json_encode(['success' => false, 'message' => 'Vehicle type is required for drivers.']);
        exit;
    }

    $password = password_hash($password_raw, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?) RETURNING id");
        $stmt->execute([$name, $email, $password, $role]);
        $userId = $stmt->fetchColumn();

        if ($role === 'driver') {
            $stmtDriver = $pdo->prepare("INSERT INTO drivers (user_id, vehicle_type, status) VALUES (?, ?, 'inactive')");
            $stmtDriver->execute([$userId, $vehicle_type]);
        }

        $pdo->commit();
        
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = $role;

        echo json_encode([
            'success' => true, 
            'user' => [
                'id' => $userId,
                'name' => $name, 
                'email' => $email,
                'role' => $role,
                'vehicle_type' => $vehicle_type
            ]
        ]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23505 || strpos($e->getMessage(), 'unique constraint') !== false) {
            echo json_encode(['success' => false, 'message' => 'Email already registered!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
} elseif ($action === 'login') {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
            }

            if ($user['role'] === 'driver') {
                $stmtDriver = $pdo->prepare("SELECT id as driver_id, vehicle_type, status as driver_status FROM drivers WHERE user_id = ?");
                $stmtDriver->execute([$user['id']]);
                $driverDetails = $stmtDriver->fetch();
                if ($driverDetails) {
                    $user = array_merge($user, $driverDetails);
                }
            }

            unset($user['password']);
            echo json_encode(['success' => true, 'user' => $user]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password!']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Login failed: ' . $e->getMessage()]);
    }
} elseif ($action === 'forgot') {
    $email = trim($data['email'] ?? '');
    $password_raw = $data['password'] ?? '';

    if (empty($email) || empty($password_raw)) {
        echo json_encode(['success' => false, 'message' => 'Email and new password are required.']);
        exit;
    }

    try {
        $stmtCheck = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmtCheck->execute([$email]);
        $exists = $stmtCheck->fetchColumn();

        if (!$exists) {
            echo json_encode(['success' => false, 'message' => 'No account associated with that email address.']);
            exit;
        }

        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
        $stmtUpdate = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $success = $stmtUpdate->execute([$password_hash, $email]);

        echo json_encode(['success' => $success]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Password reset failed: ' . $e->getMessage()]);
    }
} elseif ($action === 'logout') {
    session_destroy();
    echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
}
?>