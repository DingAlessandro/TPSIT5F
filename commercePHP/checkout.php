<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Utente non autenticato']);
    exit;
}

$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connessione al database fallita']));
}

$username = $_SESSION['username'];
$conn->begin_transaction();

try {
    // 1. Recupera gli item del carrello con quantità e tipo prodotto
    $stmt = $conn->prepare("
        SELECT 
            ci.product_name, 
            ci.color, 
            ci.size, 
            ci.quantity AS cart_quantity,
            p.typeP,
            w.quantity AS warehouse_quantity
        FROM cart_items ci
        JOIN products p ON ci.product_name = p.name
        JOIN carts c ON ci.cart_id = c.cart_id
        JOIN (
            SELECT name, color, typeS, quantity 
            FROM Slacks UNION ALL
            SELECT name, color, typeS, quantity 
            FROM Shoes UNION ALL
            SELECT name, color, typeS, quantity 
            FROM Shirts UNION ALL
            SELECT name, color, typeS, quantity 
            FROM Bags
        ) w ON ci.product_name = w.name 
            AND ci.color = w.color 
            AND ci.size = w.typeS
        WHERE c.username = ?
    ");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    // 2. Verifica disponibilità e aggiorna magazzino
    foreach ($items as $item) {
        if ($item['warehouse_quantity'] < $item['cart_quantity']) {
            throw new Exception("Quantità insufficiente per {$item['product_name']} ({$item['color']}/{$item['size']})");
        }

        $warehouseTable = match ($item['typeP']) {
            'Slacks' => 'Slacks',
            'Shoes' => 'Shoes',
            'Shirts' => 'Shirts',
            'Bags' => 'Bags',
            default => throw new Exception("Tipo prodotto non valido")
        };

        $updateStmt = $conn->prepare("
            UPDATE $warehouseTable 
            SET quantity = quantity - ? 
            WHERE name = ? 
            AND color = ? 
            AND typeS = ?
        ");
        $updateStmt->bind_param("isss",
            $item['cart_quantity'],
            $item['product_name'],
            $item['color'],
            $item['size']
        );
        $updateStmt->execute();
    }

    // 3. Svuota carrello
    $deleteStmt = $conn->prepare("
        DELETE ci 
        FROM cart_items ci
        JOIN carts c ON ci.cart_id = c.cart_id
        WHERE c.username = ?
    ");
    $deleteStmt->bind_param("s", $username);
    $deleteStmt->execute();

    // 4. Rimuovi sconto applicato
    unset($_SESSION['discount_applied']);
    unset($_SESSION['discount_code']);

    $conn->commit();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Checkout fallito: ' . $e->getMessage()
    ]);
}

$conn->close();
?>