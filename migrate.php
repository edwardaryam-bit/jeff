<?php
require_once 'db.php';

echo "Running migrations and seeding initial database records...\n";

function getOrCreateUser($pdo, $name, $email, $passwordHash, $role) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return $id;
    }
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $passwordHash, $role]);
    return $pdo->lastInsertId();
}

function seedDriver($pdo, $userId, $vehicleType) {
    $stmt = $pdo->prepare("SELECT id FROM drivers WHERE user_id = ?");
    $stmt->execute([$userId]);
    $id = $stmt->fetchColumn();
    if ($id) {
        return $id;
    }
    $stmt = $pdo->prepare("INSERT INTO drivers (user_id, vehicle_type, status) VALUES (?, ?, 'inactive')");
    $stmt->execute([$userId, $vehicleType]);
    return $pdo->lastInsertId();
}

try {
    $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $userPassword = password_hash('password123', PASSWORD_DEFAULT);

    getOrCreateUser($pdo, 'System Admin', 'admin@ewaste.com', $adminPassword, 'admin');
    echo "Admin user verified/created: admin@ewaste.com / admin123\n";

    $driver1Id = getOrCreateUser($pdo, 'John Boda', 'john@boda.com', $userPassword, 'driver');
    seedDriver($pdo, $driver1Id, 'Boda boda');
    echo "Boda Boda driver verified/created: john@boda.com / password123\n";

    $driver3Id = getOrCreateUser($pdo, 'Timothy Tuku', 'tim@tuku.com', $userPassword, 'driver');
    seedDriver($pdo, $driver3Id, 'Tuku Tuku');
    echo "Tuku Tuku driver verified/created: tim@tuku.com / password123\n";

    $driver4Id = getOrCreateUser($pdo, 'Paul Pickup', 'paul@pickup.com', $userPassword, 'driver');
    seedDriver($pdo, $driver4Id, 'Pickup');
    echo "Pickup driver verified/created: paul@pickup.com / password123\n";

    $driver2Id = getOrCreateUser($pdo, 'Fred Truck', 'fred@truck.com', $userPassword, 'driver');
    seedDriver($pdo, $driver2Id, 'Truck');
    echo "Truck driver verified/created: fred@truck.com / password123\n";

    getOrCreateUser($pdo, 'Alice Customer', 'alice@customer.com', $userPassword, 'user');
    echo "Customer user verified/created: alice@customer.com / password123\n";

    echo "Seeding completed successfully!\n";

} catch (PDOException $e) {
    die("Seeding failed: " . $e->getMessage() . "\n");
}
?>