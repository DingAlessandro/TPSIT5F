<?php
session_start();

// Verifica se l'utente è loggato
if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'Devi essere loggato per aggiungere al carrello.']);
    exit;
}

// Connessione al database
$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connessione al database fallita.']));
}

// Recupera i dati inviati via POST
$productName = isset($_POST['productName']) ? $conn->real_escape_string($_POST['productName']) : '';
$color = isset($_POST['color']) ? $conn->real_escape_string($_POST['color']) : '';
$size = isset($_POST['size']) ? $conn->real_escape_string($_POST['size']) : '';

// Verifica che i dati siano validi
if (empty($productName) || empty($color) || empty($size)) {
    echo json_encode(['success' => false, 'message' => 'Dati incompleti.']);
    exit;
}

// 1. Prima controlla la disponibilità in magazzino
// Determina la tabella magazzino corretta in base al tipo di prodotto
$productTypeQuery = "SELECT typeP FROM products WHERE name = ?";
$stmt = $conn->prepare($productTypeQuery);
$stmt->bind_param("s", $productName);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Prodotto non trovato.']);
    exit;
}

$warehouseTable = '';
switch ($product['typeP']) {
    case 'Slacks': $warehouseTable = 'Slacks'; break;
    case 'Shoes': $warehouseTable = 'Shoes'; break;
    case 'Shirts': $warehouseTable = 'Shirts'; break;
    case 'Bags': $warehouseTable = 'Bags'; break;
    default:
        echo json_encode(['success' => false, 'message' => 'Tipo prodotto non valido.']);
        exit;
}

// Controlla la quantità disponibile in magazzino
$stockQuery = "SELECT quantity FROM $warehouseTable WHERE name = ? AND color = ? AND typeS = ?";
$stmt = $conn->prepare($stockQuery);
$stmt->bind_param("sss", $productName, $color, $size);
$stmt->execute();
$stockResult = $stmt->get_result();
$stock = $stockResult->fetch_assoc();

if (!$stock) {
    echo json_encode(['success' => false, 'message' => 'Prodotto non disponibile in magazzino.']);
    exit;
}

// 2. Recupera l'ID del carrello dell'utente
$username = $_SESSION['username'];
$query = "SELECT cart_id FROM carts WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$cart = $result->fetch_assoc();

if (!$cart) {
    $query = "INSERT INTO carts (username) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
} else {
    $cart_id = $cart['cart_id'];
}

// 3. Verifica se il prodotto è già nel carrello
$checkQuery = "SELECT quantity FROM cart_items WHERE cart_id = ? AND product_name = ? AND color = ? AND size = ?";
$stmt = $conn->prepare($checkQuery);
$stmt->bind_param("isss", $cart_id, $productName, $color, $size);
$stmt->execute();
$checkResult = $stmt->get_result();
$existingItem = $checkResult->fetch_assoc();

if ($existingItem) {
    // Prodotto già nel carrello - verifica se possiamo aumentare la quantità
    $newQuantity = $existingItem['quantity'] + 1;

    if ($newQuantity > $stock['quantity']) {
        echo json_encode([
            'success' => false,
            'message' => 'Quantità massima disponibile raggiunta. Disponibilità: ' . $stock['quantity']
        ]);
        exit;
    }

    // Aggiorna la quantità
    $updateQuery = "UPDATE cart_items SET quantity = ? WHERE cart_id = ? AND product_name = ? AND color = ? AND size = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("issss", $newQuantity, $cart_id, $productName, $color, $size);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Quantità aggiornata nel carrello.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiornamento del carrello.']);
    }
} else {
    // Prodotto non nel carrello - aggiungi nuovo item
    if ($stock['quantity'] < 1) {
        echo json_encode(['success' => false, 'message' => 'Prodotto esaurito.']);
        exit;
    }

    $insertQuery = "INSERT INTO cart_items (cart_id, product_name, color, size, quantity) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("isss", $cart_id, $productName, $color, $size);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Prodotto aggiunto al carrello con successo.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Errore nell\'aggiunta al carrello.']);
    }
}

$stmt->close();
$conn->close();
?>