<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Accesso non autorizzato']);
    exit();
}

$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Errore di connessione al database']));
}

$username = $_SESSION['username'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'update_quantity':
        handleQuantityUpdate($conn, $username);
        break;

    case 'remove_item':
        handleItemRemoval($conn, $username);
        break;

    case 'apply_discount':
        handleDiscount();
        break;

    case 'checkout':
        handleCheckout($conn, $username);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Azione non valida']);
}

$conn->close();

function handleQuantityUpdate($conn, $username) {
    $required = ['product_name', 'color', 'size', 'quantity'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
            return;
        }
    }

    // Controllo disponibilità
    $product = $conn->real_escape_string($_POST['product_name']);
    $color = $conn->real_escape_string($_POST['color']);
    $size = $conn->real_escape_string($_POST['size']);
    $quantity = (int)$_POST['quantity'];

    // Recupera tipo prodotto
    $type = $conn->query("SELECT typeP FROM products WHERE name = '$product'")->fetch_assoc()['typeP'];
    $table = match($type) {
        'Slacks' => 'Slacks',
        'Shoes' => 'Shoes',
        'Shirts' => 'Shirts',
        'Bags' => 'Bags',
        default => null
    };

    if (!$table) {
        echo json_encode(['success' => false, 'message' => 'Prodotto non valido']);
        return;
    }

    // Verifica stock
    $stock = $conn->query("
        SELECT quantity FROM $table 
        WHERE name = '$product' AND color = '$color' AND typeS = '$size'
    ")->fetch_assoc();

    if (!$stock || $stock['quantity'] < $quantity) {
        echo json_encode(['success' => false, 'message' => 'Quantità non disponibile']);
        return;
    }

    // Aggiorna quantità
    $stmt = $conn->prepare("
        UPDATE cart_items ci
        JOIN carts c ON ci.cart_id = c.cart_id
        SET ci.quantity = ?
        WHERE c.username = ?
        AND ci.product_name = ?
        AND ci.color = ?
        AND ci.size = ?
    ");
    $stmt->bind_param("issss", $quantity, $username, $product, $color, $size);
    executeStatement($stmt);
}

function handleItemRemoval($conn, $username) {
    $required = ['product_name', 'color', 'size'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['success' => false, 'message' => 'Dati mancanti']);
            return;
        }
    }

    $product = $conn->real_escape_string($_POST['product_name']);
    $color = $conn->real_escape_string($_POST['color']);
    $size = $conn->real_escape_string($_POST['size']);

    $stmt = $conn->prepare("
        DELETE ci FROM cart_items ci
        JOIN carts c ON ci.cart_id = c.cart_id
        WHERE c.username = ?
        AND ci.product_name = ?
        AND ci.color = ?
        AND ci.size = ?
    ");
    $stmt->bind_param("ssss", $username, $product, $color, $size);
    executeStatement($stmt);
}

function handleDiscount() {
    $code = $_POST['discount_code'] ?? '';
    $_SESSION['discount_applied'] = ($code === 'sconto');
    echo json_encode([
        'success' => $_SESSION['discount_applied'],
        'message' => $_SESSION['discount_applied'] ? '' : 'Codice sconto non valido'
    ]);
}

function handleCheckout($conn, $username) {
    // Inizia una transazione
    $conn->begin_transaction();

    try {
        // 1. Recupera tutti gli item nel carrello
        $stmt = $conn->prepare("
            SELECT ci.product_name, ci.color, ci.size, ci.quantity, p.typeP 
            FROM cart_items ci
            JOIN products p ON ci.product_name = p.name
            JOIN carts c ON ci.cart_id = c.cart_id
            WHERE c.username = ?
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $items = $result->fetch_all(MYSQLI_ASSOC);

        // 2. Per ogni item, aggiorna il magazzino
        foreach ($items as $item) {
            $warehouseTable = '';
            switch ($item['typeP']) {
                case 'Slacks': $warehouseTable = 'Slacks'; break;
                case 'Shoes': $warehouseTable = 'Shoes'; break;
                case 'Shirts': $warehouseTable = 'Shirts'; break;
                case 'Bags': $warehouseTable = 'Bags'; break;
                default: throw new Exception("Tipo prodotto non valido");
            }

            // Aggiorna la quantità nel magazzino
            $updateStmt = $conn->prepare("
                UPDATE $warehouseTable 
                SET quantity = quantity - ?
                WHERE name = ? 
                AND color = ? 
                AND typeS = ?
            ");
            $updateStmt->bind_param(
                "isss",
                $item['quantity'],
                $item['product_name'],
                $item['color'],
                $item['size']
            );
            $updateStmt->execute();
        }

        // 3. Svuota il carrello
        $deleteStmt = $conn->prepare("
            DELETE ci FROM cart_items ci
            JOIN carts c ON ci.cart_id = c.cart_id
            WHERE c.username = ?
        ");
        $deleteStmt->bind_param("s", $username);
        $deleteStmt->execute();

        // 4. Conferma la transazione
        $conn->commit();

    } catch (Exception $e) {
        // Rollback in caso di errore
        $conn->rollback();
        throw new Exception("Checkout fallito: " . $e->getMessage());
    }
}

function executeStatement($stmt) {
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nell\'operazione']);
    }
}
?>