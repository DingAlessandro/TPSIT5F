<?php
// Connessione al database
$conn = new mysqli("localhost", "root", "", "commerce");
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}

// Immagine hero (statica, puoi cambiarla dinamicamente se vuoi)
$hero_image = "images/hero.jpg";
$hero_alt = "Hero Image";
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZOZO</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class="page-container">
    <?php require 'header.php'; ?>



    <!-- HERO -->
    <div class="hero-image">
        <img src="<?= $hero_image ?>" alt="<?= $hero_alt ?>">
    </div>


    <!-- PRODOTTI -->
    <div class="products">
        <?php
        $sql = "SELECT name, price, mainImg FROM products";
        $result = $conn->query($sql);

        $num = 0;
        echo '<div class="divider"></div><div class="product-grid">';

        while ($row = $result->fetch_assoc()) {
            if ($num === 4) {
                echo '</div><div class="divider"></div><div class="product-grid">';
                $num = 0;
            }
            $name = htmlspecialchars($row['name']);
            $price = htmlspecialchars($row['price']);
            $image = htmlspecialchars($row['mainImg']);

            echo '
        <div class="product-item" data-name="' . $name . '" onclick="location.href=\'product.php?name=' . urlencode($name) . '\'">
            <img src="' . $image . '" alt="' . $name . '">
            <div class="product-info">
                <h3>' . $name . '</h3>
                <p>' . $price . '</p>
            </div>
        </div>';
            $num++;
        }

        echo '</div>'; // chiude ultimo .product-grid
        $conn->close();
        ?>
    </div>
</div>

<?php require 'footer.php'; ?>

</body>
</html>
