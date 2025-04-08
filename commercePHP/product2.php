<?php
// Connessione al database usando mysqli
$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Recupera il parametro 'name' dalla query string dell'URL
$productName = isset($_GET['name']) ? $_GET['name'] : null;

if ($productName) {
    // Query per ottenere i dati del prodotto
    $query = "SELECT * FROM products WHERE name = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $productName);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Se il prodotto esiste, recupera anche i colori e le taglie
    if ($product) {
        // Recupera i colori del prodotto dalla tabella 'productsColors'
        $query_colors = "SELECT * FROM productsColors WHERE name = ?";
        $stmt_colors = $conn->prepare($query_colors);
        $stmt_colors->bind_param("s", $product['name']);
        $stmt_colors->execute();
        $result_colors = $stmt_colors->get_result();
        $colors = $result_colors->fetch_all(MYSQLI_ASSOC);

        // Recupera le taglie e le quantità disponibili per ogni colore,
        // utilizzando if / elseif (senza switch)
        $sizes = [];
        if ($product['typeP'] === 'Slacks') {
            foreach ($colors as $color) {
                $query_sizes = "SELECT s.typeS, s.quantity, ss.lengthS, ss.inseam, ss.waist, ss.thigh_width, ss.hem 
                                FROM Slacks s
                                JOIN Slacksize ss ON s.typeS = ss.typeS
                                WHERE s.color = ? AND s.name = ?";
                $stmt_sizes = $conn->prepare($query_sizes);
                $stmt_sizes->bind_param("ss", $color['color'], $product['name']);
                $stmt_sizes->execute();
                $result_sizes = $stmt_sizes->get_result();
                $sizes[$color['color']] = $result_sizes->fetch_all(MYSQLI_ASSOC);
            }
        } elseif ($product['typeP'] === 'Shoes') {
            foreach ($colors as $color) {
                $query_sizes = "SELECT s.typeS, s.quantity, ss.lengthS, ss.widthS 
                                FROM Shoes s
                                JOIN ShoesSize ss ON s.typeS = ss.typeS
                                WHERE s.color = ? AND s.name = ?";
                $stmt_sizes = $conn->prepare($query_sizes);
                $stmt_sizes->bind_param("ss", $color['color'], $product['name']);
                $stmt_sizes->execute();
                $result_sizes = $stmt_sizes->get_result();
                $sizes[$color['color']] = $result_sizes->fetch_all(MYSQLI_ASSOC);
            }
        } elseif ($product['typeP'] === 'Shirts') {
            foreach ($colors as $color) {
                $query_sizes = "SELECT s.typeS, s.quantity, ss.chest, ss.shoulder, ss.sleeve 
                                FROM Shirts s
                                JOIN ShirtSize ss ON s.typeS = ss.typeS
                                WHERE s.color = ? AND s.name = ?";
                $stmt_sizes = $conn->prepare($query_sizes);
                $stmt_sizes->bind_param("ss", $color['color'], $product['name']);
                $stmt_sizes->execute();
                $result_sizes = $stmt_sizes->get_result();
                $sizes[$color['color']] = $result_sizes->fetch_all(MYSQLI_ASSOC);
            }
        } elseif ($product['typeP'] === 'Bags') {
            foreach ($colors as $color) {
                $query_sizes = "SELECT s.typeS, s.quantity, ss.height, ss.width, ss.depth 
                                FROM Bags s
                                JOIN BagSize ss ON s.typeS = ss.typeS
                                WHERE s.color = ? AND s.name = ?";
                $stmt_sizes = $conn->prepare($query_sizes);
                $stmt_sizes->bind_param("ss", $color['color'], $product['name']);
                $stmt_sizes->execute();
                $result_sizes = $stmt_sizes->get_result();
                $sizes[$color['color']] = $result_sizes->fetch_all(MYSQLI_ASSOC);
            }
        }

        // Recupera le specifiche generali del prodotto in base al tipo.
        // Poiché per i tipi diversi (diversi da Slacks) non abbiamo un filtro diretto,
        // selezioniamo tutte le righe della rispettiva tabella.
        $specs = [];
        if ($product['typeP'] == 'Slacks') {
            $query_specs = "SELECT typeS, lengthS, inseam, waist, thigh_width, hem FROM Slacksize 
                            WHERE typeS IN (SELECT typeS FROM Slacks WHERE name = ?)";
            $stmt_specs = $conn->prepare($query_specs);
            $stmt_specs->bind_param("s", $product['name']);
        } elseif ($product['typeP'] == 'Shirts') {
            $query_specs = "SELECT typeS, chest, shoulder, sleeve FROM ShirtSize";
            $stmt_specs = $conn->prepare($query_specs);
        } elseif ($product['typeP'] == 'Shoes') {
            $query_specs = "SELECT typeS, lengthS, widthS FROM ShoesSize";
            $stmt_specs = $conn->prepare($query_specs);
        } elseif ($product['typeP'] == 'Bags') {
            $query_specs = "SELECT typeS, height, width, depth FROM BagSize";
            $stmt_specs = $conn->prepare($query_specs);
        }
        if (isset($stmt_specs)) {
            $stmt_specs->execute();
            $result_specs = $stmt_specs->get_result();
            $specs = $result_specs->fetch_all(MYSQLI_ASSOC);
        }

        // Dati di testo
        $textContent = [
            'productDetailsHeader' => 'Dettagli del prodotto',
            'addToCartButton' => 'Aggiungi al carrello',
            'productSpecsHeader' => 'Specifiche',
            'viewSpecsLink' => 'Visualizza specifiche e colori',
            'productNotFound' => 'Prodotto non trovato',
            'missingNameParam' => "Parametro 'name' mancante nell'URL",
            'productLoadError' => 'Errore nel caricamento dei dati del prodotto'
        ];
    } else {
        echo "<script> alert('Prodotto non trovato'); </script>";
    }
} else {
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
    <title>Specifiche e Colori del Prodotto</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/product2.css">
</head>
<body>

<?php require 'header.php'; ?>

<!-- Sezione specifiche e colori -->
<main class="product-specs-colors">
    <button id="back-button" class="back-button" onclick="window.history.back();">Indietro</button>
    <?php if (isset($product)): ?>
        <h1 id="product-name"><?php echo $product['name']; ?></h1>

        <!-- Tabella delle specifiche -->
        <div class="specs-table">
            <h2 id="specs-header"><?php echo $textContent['productSpecsHeader']; ?></h2>
            <table id="specs-table">
                <?php if ($product['typeP'] == 'Slacks'): ?>
                    <tr>
                        <th>Taglia</th>
                        <th>Length</th>
                        <th>Inseam</th>
                        <th>Waist</th>
                        <th>Thigh Width</th>
                        <th>Hem</th>
                    </tr>
                    <?php foreach ($specs as $spec): ?>
                        <tr>
                            <td><?php echo $spec['typeS']; ?></td>
                            <td><?php echo $spec['lengthS']; ?></td>
                            <td><?php echo $spec['inseam']; ?></td>
                            <td><?php echo $spec['waist']; ?></td>
                            <td><?php echo $spec['thigh_width']; ?></td>
                            <td><?php echo $spec['hem']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif ($product['typeP'] == 'Shirts'): ?>
                    <tr>
                        <th>Taglia</th>
                        <th>Chest</th>
                        <th>Shoulder</th>
                        <th>Sleeve</th>
                    </tr>
                    <?php foreach ($specs as $spec): ?>
                        <tr>
                            <td><?php echo $spec['typeS']; ?></td>
                            <td><?php echo $spec['chest']; ?></td>
                            <td><?php echo $spec['shoulder']; ?></td>
                            <td><?php echo $spec['sleeve']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif ($product['typeP'] == 'Shoes'): ?>
                    <tr>
                        <th>Taglia</th>
                        <th>Length</th>
                        <th>Width</th>
                    </tr>
                    <?php foreach ($specs as $spec): ?>
                        <tr>
                            <td><?php echo $spec['typeS']; ?></td>
                            <td><?php echo $spec['lengthS']; ?></td>
                            <td><?php echo $spec['widthS']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php elseif ($product['typeP'] == 'Bags'): ?>
                    <tr>
                        <th>Taglia</th>
                        <th>Height</th>
                        <th>Width</th>
                        <th>Depth</th>
                    </tr>
                    <?php foreach ($specs as $spec): ?>
                        <tr>
                            <td><?php echo $spec['typeS']; ?></td>
                            <td><?php echo $spec['height']; ?></td>
                            <td><?php echo $spec['width']; ?></td>
                            <td><?php echo $spec['depth']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6">Nessuna specifica disponibile</td></tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Tabella dei colori -->
        <div class="colors-table">
            <h2 id="colors-header">Colori Disponibili</h2>
            <table id="colors-table">
                <tr>
                    <th>Colore</th>
                    <th>Immagine</th>
                    <th>Taglie Disponibili (con quantità)</th>
                </tr>
                <?php foreach ($colors as $color): ?>
                    <tr>
                        <td><?php echo $color['color']; ?></td>
                        <td><img src="<?php echo $color['img']; ?>" alt="<?php echo $color['color']; ?>" width="50"></td>
                        <td>
                            <?php
                            if (isset($sizes[$color['color']]) && !empty($sizes[$color['color']])) {
                                $available_sizes = [];
                                foreach ($sizes[$color['color']] as $size) {
                                    $available_sizes[] = "Taglia {$size['typeS']} ({$size['quantity']} disponibili)";
                                }
                                echo implode(', ', $available_sizes);
                            } else {
                                echo "Nessuna taglia disponibile";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>

    <?php else: ?>
        <p><?php echo isset($error_message) ? $error_message : $textContent['productLoadError']; ?></p>
    <?php endif; ?>
</main>

<?php require 'footer.php'; ?>

</body>
</html>
