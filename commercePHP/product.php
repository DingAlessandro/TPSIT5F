<?php
// Connessione al database usando mysqli
$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Inizia la sessione per gestire l'utente loggato
session_start();

// Recupera il parametro 'name' dalla query string dell'URL
$productName = isset($_GET['name']) ? $_GET['name'] : null;

// Carica il file JSON per testo personalizzato
$jsonFile = file_get_contents(__DIR__ . '/products.json');
if ($jsonFile === false) {
    echo "Errore nel caricamento del file JSON.";
    exit;
}

// Decodifica il file JSON
$textContent = json_decode($jsonFile, true)['textContent'] ?? null;
if ($textContent === null) {
    echo "Errore nel formato del file JSON.";
    exit;
}

if ($productName) {
    // Query per ottenere i dati del prodotto
    $query = "SELECT * FROM products WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Se il prodotto esiste, recupera anche i colori
    if ($product) {
        // Query per recuperare i colori del prodotto dalla tabella 'productsColors'
        $query_colors = "SELECT * FROM productsColors WHERE name = ?";
        $stmt_colors = $conn->prepare($query_colors);
        $stmt_colors->bind_param("s", $product['name']);
        $stmt_colors->execute();
        $result_colors = $stmt_colors->get_result();
        $colors = $result_colors->fetch_all(MYSQLI_ASSOC);

        // Recupera le taglie del prodotto in base al tipo
        $sizes = [];
        switch ($product['typeP']) {
            case 'Slacks': // pantaloni
                $query_sizes = "SELECT typeS, lengthS, inseam, waist, thigh_width, hem FROM Slacksize";
                break;
            case 'Shoes': // scarpe
                $query_sizes = "SELECT typeS, lengthS, widthS FROM ShoesSize";
                break;
            case 'Shirts': // camicie
                $query_sizes = "SELECT typeS, chest, shoulder, sleeve FROM ShirtSize";
                break;
            case 'Bags': // borse
                $query_sizes = "SELECT typeS, height, width, depth FROM BagSize";
                break;
            default:
                $query_sizes = null;
        }

        if ($query_sizes) {
            $stmt_sizes = $conn->prepare($query_sizes);
            $stmt_sizes->execute();
            $result_sizes = $stmt_sizes->get_result();
            $sizes = $result_sizes->fetch_all(MYSQLI_ASSOC);
        }

        // Imposta l'immagine iniziale come quella del primo colore
        $selectedColorImage = $colors[0]['img'];
    } else {
        echo "<script> alert('Prodotto non trovato'); </script>";
    }
} else {
    // Se manca il parametro 'name'
    echo "<script> alert('Prodotto non trovato'); </script>";
}

// Chiudi la connessione al database
$conn->close();
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZOZO-P</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/product-detail.css">
</head>
<body>
<?php require 'header.php'; ?>

<!-- Sezione dettagli prodotto -->
<main class="product-detail">
    <?php if (isset($product)): ?>
        <div class="product-images">
            <div class="main-image">
                <img id="main-image" src="<?php echo $selectedColorImage; ?>" alt="Immagine del prodotto">
            </div>
        </div>
        <div class="product-info">
            <div class="product-header">
                <h1 id="product-name"><?php echo $product['name']; ?></h1>
                <h2 id="product-price" class="product-price"><?php echo $product['price']; ?> €</h2>
            </div>
            <div id="product-options" class="product-options">
                <?php foreach ($colors as $color): ?>
                    <div class="option">
                        <div class="thumbnail-item">
                            <img src="<?php echo $color['img']; ?>" alt="<?php echo $color['color']; ?>" class="color-thumbnail">
                        </div>
                        <h3><?php echo $color['color']; ?></h3>

                        <!-- Seleziona la taglia per ogni colore -->
                        <?php if (!empty($sizes)): ?>
                            <select class="size-select">
                                <?php foreach ($sizes as $size): ?>
                                    <option value="<?php echo $size['typeS']; ?>"><?php echo $size['typeS']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>

                        <button class="add-to-cart"><?php echo $textContent['addToCartButton']; ?></button>
                    </div>
                    <div class="thumbnail-divider"></div>
                <?php endforeach; ?>
            </div>
            <div id="product-description" class="product-description">
                <h2><?php echo $textContent['productDetailsHeader']; ?></h2>
                <?php foreach (explode("\n", $product['description']) as $line): ?>
                    <p><?php echo $line; ?></p>
                <?php endforeach; ?>
            </div>
            <div id="product-specs" class="product-specs">
                <h2><?php echo $textContent['productSpecsHeader']; ?></h2>
                <a id="view-specs-link" class="specs" href="product2.php?name=<?php echo urlencode($product['name']); ?>"><?php echo $textContent['viewSpecsLink']; ?></a>
            </div>
        </div>
    <?php else: ?>
        <p><?php echo isset($error_message) ? $error_message : $textContent['productLoadError']; ?></p>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ottieni tutte le miniature
        const thumbnails = document.querySelectorAll('.color-thumbnail');

        // Ottieni l'immagine principale
        const mainImage = document.getElementById('main-image');

        // Aggiungi evento click su ogni miniatura
        thumbnails.forEach(function(thumbnail) {
            thumbnail.addEventListener('click', function() {
                // Cambia l'immagine principale
                mainImage.src = thumbnail.src;
                mainImage.alt = thumbnail.alt;
            });
        });

        const addToCartButtons = document.querySelectorAll('.add-to-cart');
        addToCartButtons.forEach(function(button) {
            button.addEventListener('click', function(event) {
                <?php if (!isset($_SESSION['username'])): ?>
                alert('Devi essere loggato per aggiungere il prodotto al carrello.');
                event.preventDefault();
                <?php else: ?>
                // Prepara i dati da inviare
                const color = event.target.closest('.option').querySelector('h3').textContent.trim();
                const size = event.target.closest('.option').querySelector('.size-select').value;
                const productName = '<?php echo addslashes($product['name']); ?>';

                // Mostra feedback di caricamento
                const originalText = event.target.textContent;
                event.target.textContent = 'Caricamento...';
                event.target.disabled = true;

                // Invia la richiesta POST
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `color=${encodeURIComponent(color)}&size=${encodeURIComponent(size)}&productName=${encodeURIComponent(productName)}`
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Mostra un messaggio di successo più elegante
                            const notification = document.createElement('div');
                            notification.className = 'cart-notification';
                            notification.textContent = data.message;
                            document.body.appendChild(notification);

                            // Rimuovi il messaggio dopo 3 secondi
                            setTimeout(() => {
                                notification.remove();
                            }, 3000);
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        alert('Si è verificato un errore: ' + error.message);
                    })
                    .finally(() => {
                        event.target.textContent = originalText;
                        event.target.disabled = false;
                    });
                <?php endif; ?>
            });
        });
    });
</script>

</body>
</html>
