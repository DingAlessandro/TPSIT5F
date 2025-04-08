<?php
session_start();

if (!isset($_SESSION['username'])) {
    echo "<script>
        alert('Devi effettuare il login per accedere al carrello');
        window.location.href = 'index.php';
    </script>";
    exit();
}

$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Carica contenuti JSON
$jsonContent = file_get_contents(__DIR__ . '/cart.json');
$textContent = json_decode($jsonContent, true)['textContent'] ?? [];

// Recupera ID carrello
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT cart_id FROM carts WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$cart = $stmt->get_result()->fetch_assoc();

$cart_id = $cart ? $cart['cart_id'] : null;

if (!$cart_id) {
    $stmt = $conn->prepare("INSERT INTO carts (username) VALUES (?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
}

// Recupera prodotti nel carrello
$cartItems = [];
$originalTotal = 0;

$stmt = $conn->prepare("
    SELECT ci.*, p.price, pc.img 
    FROM cart_items ci
    JOIN products p ON ci.product_name = p.name
    JOIN productsColors pc ON ci.product_name = pc.name AND ci.color = pc.color
    WHERE ci.cart_id = ?
");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $price = (float)str_replace(['$', ','], '', $row['price']);
    $quantity = (int)$row['quantity'];
    $itemTotal = $price * $quantity;
    $originalTotal += $itemTotal;

    $cartItems[] = [
        'name' => $row['product_name'],
        'price' => $price,
        'color' => $row['color'],
        'size' => $row['size'],
        'quantity' => $quantity,
        'image' => $row['img'],
        'item_total' => $itemTotal
    ];
}

$discountApplied = $_SESSION['discount_applied'] ?? false;
$total = $discountApplied ? $originalTotal * 0.9 : $originalTotal;

$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($textContent['cartTitle'] ?? 'Carrello') ?></title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/cart.css">
    <style>
        .checkout-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 10px 0;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .checkout-button:disabled {
            background-color: #e0e0e0;
            color: #a0a0a0;
            cursor: not-allowed;
            position: relative;
            opacity: 0.8;
        }

        .checkout-button:disabled::after {
            content: "";
            position: absolute;
            top: 50%;
            left: 10%;
            right: 10%;
            height: 2px;
            background-color: #ff3333;
            transform: rotate(-5deg);
            box-shadow: 0 0 3px rgba(0,0,0,0.2);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<?php require 'header.php'; ?>

<div class="cart-container">
    <div class="cart-items">
        <?php if (empty($cartItems)): ?>
            <p class="empty-cart-message"><?= htmlspecialchars($textContent['emptyCartMessage'] ?? 'Il tuo carrello è vuoto') ?></p>
        <?php else: ?>
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item"
                     data-product="<?= htmlspecialchars($item['name']) ?>"
                     data-color="<?= htmlspecialchars($item['color']) ?>"
                     data-size="<?= htmlspecialchars($item['size']) ?>">

                    <div class="item-image">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    </div>

                    <div class="item-details">
                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                        <p><?= htmlspecialchars($textContent['sizeLabel'] ?? 'Taglia') ?>: <?= htmlspecialchars($item['size']) ?></p>
                        <p><?= htmlspecialchars($textContent['colorLabel'] ?? 'Colore') ?>: <?= htmlspecialchars($item['color']) ?></p>
                        <p class="item-price">
                            <?= htmlspecialchars($textContent['priceLabel'] ?? 'Prezzo') ?>:
                            US$<?= number_format($item['price'], 2) ?>
                        </p>
                    </div>

                    <div class="item-actions">
                        <div class="quantity-control">
                            <button class="decrease">-</button>
                            <span class="quantity"><?= $item['quantity'] ?></span>
                            <button class="increase">+</button>
                        </div>
                        <button class="delete"><?= htmlspecialchars($textContent['deleteButton'] ?? 'Elimina') ?></button>
                    </div>
                </div>
                <div class="cart-divider"></div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="checkout-summary">
        <h3><?= htmlspecialchars($textContent['totalAmountLabel'] ?? 'Totale') ?></h3>
        <p class="total-amount">
            <span id="original-total" style="<?= $discountApplied ? '' : 'display:none;' ?>text-decoration: line-through;">
                US$<span><?= number_format($originalTotal, 2) ?></span>
            </span>
            <span id="discounted-total">
                US$<span><?= number_format($total, 2) ?></span>
            </span>
        </p>

        <div class="discount-code">
            <input type="text" id="discount-input"
                   placeholder="<?= htmlspecialchars($textContent['discountCodePlaceholder'] ?? 'Codice sconto') ?>">
            <button id="apply-discount-button">
                <?= htmlspecialchars($textContent['applyDiscountButton'] ?? 'Applica') ?>
            </button>
            <p class="discount-error" id="discount-error"></p>
        </div>

        <button class="checkout-button" id="checkout-button" <?= empty($cartItems) ? 'disabled' : '' ?>>
            <?= htmlspecialchars($textContent['checkoutButton'] ?? 'Proceed to Checkout') ?>
        </button>
    </div>
</div>

<div id="success-modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2><?= htmlspecialchars($textContent['purchaseSuccessTitle'] ?? 'Ordine completato!') ?></h2>
        <p><?= htmlspecialchars($textContent['purchaseSuccessMessage'] ?? 'Grazie per il tuo acquisto') ?></p>
        <button id="modal-close-btn"><?= htmlspecialchars($textContent['closeModalButton'] ?? 'Chiudi') ?></button>
    </div>
</div>

<script>
    $(document).ready(function() {
        let discountApplied = <?= $discountApplied ? 'true' : 'false' ?>;
        let originalTotal = <?= $originalTotal ?>;

        // Funzione per aggiornare lo stato del pulsante checkout
        function updateCheckoutButton() {
            const isCartEmpty = $('.cart-item').length === 0;
            $('#checkout-button').prop('disabled', isCartEmpty);
        }

        // Inizializza il pulsante checkout
        updateCheckoutButton();

        // Funzione per aggiornare i totali
        function updateTotals() {
            const discountedTotal = discountApplied ? originalTotal * 0.9 : originalTotal;

            if(discountApplied) {
                $('#original-total').show();
                $('#original-total span').text(originalTotal.toFixed(2));
                $('#discounted-total span').text(discountedTotal.toFixed(2));
            } else {
                $('#original-total').hide();
                $('#discounted-total span').text(originalTotal.toFixed(2));
            }
        }

        // Applicazione sconto
        $('#apply-discount-button').click(function() {
            const code = $('#discount-input').val().trim();

            if(!code) {
                $('#discount-error').text('Inserisci un codice sconto').show();
                return;
            }

            $.post('update_cart.php', {
                action: 'apply_discount',
                discount_code: code
            }, function(response) {
                if(response.success) {
                    discountApplied = true;
                    $('#discount-error').hide();

                    // Aggiorna i totali
                    originalTotal = response.original_total || originalTotal;
                    updateTotals();

                    // Animazione feedback
                    $('#discounted-total').css({color: 'green'}).animate({color: 'black'}, 1000);
                } else {
                    $('#discount-error').text(response.message).show();
                }
            }, 'json');
        });

        // Gestione quantità
        $('.quantity-control button').click(function() {
            const item = $(this).closest('.cart-item');
            const action = $(this).hasClass('increase') ? 'increment' : 'decrement';
            const currentQty = parseInt(item.find('.quantity').text());
            const newQty = action === 'increment' ? currentQty + 1 : currentQty - 1;

            if(newQty < 1) return;

            $.post('update_cart.php', {
                action: 'update_quantity',
                product_name: item.data('product'),
                color: item.data('color'),
                size: item.data('size'),
                quantity: newQty
            }, function(response) {
                if(response.success) {
                    location.reload();
                } else {
                    alert(response.message);
                }
            }, 'json');
        });

        // Rimozione prodotto
        $('.delete').click(function() {
            const item = $(this).closest('.cart-item');

            if(confirm('Sei sicuro di voler rimuovere questo articolo?')) {
                $.post('update_cart.php', {
                    action: 'remove_item',
                    product_name: item.data('product'),
                    color: item.data('color'),
                    size: item.data('size')
                }, function(response) {
                    if(response.success) {
                        item.next('.cart-divider').remove();
                        item.remove();
                        updateCheckoutButton(); // Aggiorna lo stato del pulsante
                        location.reload();
                    } else {
                        alert(response.message);
                    }
                }, 'json');
            }
        });

        //checkout
        $('#checkout-button').click(function() {
            if($('.cart-item').length === 0) {
                alert('Il tuo carrello è vuoto');
                return;
            }

            if(!confirm('Confermi di voler procedere all\'acquisto?')) {
                return;
            }

            $.post('checkout.php', function(response) {
                try {
                    const data = JSON.parse(response);
                    if(data.success) {
                        $('#success-modal').show();
                        $('.cart-items').html('<p class="empty-cart-message"><?= htmlspecialchars($textContent['emptyCartMessage'] ?? 'Il tuo carrello è vuoto') ?></p>');
                        $('#checkout-button').prop('disabled', true);
                        $('#original-total').hide();
                        $('#discounted-total span').text('0.00');
                    } else {
                        alert('Errore: ' + data.message);
                    }
                } catch(e) {
                    alert('Errore durante il checkout: ' + e.message);
                }
            });
        });

        // Chiusura modal
        $('.close-modal, #modal-close-btn').click(function() {
            $('#success-modal').hide();
            window.location.href = 'index.php';
        });
    });
</script>

</body>
</html>