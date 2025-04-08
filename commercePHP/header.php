<?php
// Inizia la sessione per accedere ai dati dell'utente loggato
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Avvia la sessione solo se non è già avviata
}

// Leggi il file JSON per l'header
$jsonData = file_get_contents('header-footer.json');

// Decodifica i dati JSON in array associativo
$data = json_decode($jsonData, true);

// Verifica se il JSON è stato letto e decodificato correttamente
if (!$data || !isset($data['header'])) {
    echo "<!-- Errore nel caricamento dell'header -->";
    return;
}

$header = $data['header'];
?>

<header>
    <nav class="navbar">
        <!-- Logo e link Home -->
        <div class="navbar-brand">
            <img src="<?= htmlspecialchars($header['logo']) ?>" alt="Logo visvim">
            <a href="<?= htmlspecialchars($header['homeLink']) ?>" class="home-link">
                <?= htmlspecialchars($header['homeText']) ?>
            </a>
        </div>

        <div class="navbar-left">
            <!-- Se l'utente è loggato, mostra il messaggio di benvenuto accanto al carrello -->
            <?php if (isset($_SESSION['username'])): ?>
                <span class="welcome-message">Benvenuto, <?= htmlspecialchars($_SESSION['username']); ?>!</span>
            <?php endif; ?>
        </div>

        <div class="navbar-right">
            <!-- Mostra Logout se l'utente è loggato, altrimenti mostra Login -->
            <?php if (isset($_SESSION['username'])): ?>
                <a href="logout.php" class="home-link logout-link">Logout</a>
            <?php else: ?>
                <a href="<?= htmlspecialchars($header['loginLink'] ?? 'login.php') ?>" class="home-link">
                    <?= htmlspecialchars($header['loginText'] ?? 'Login') ?>
                </a>
            <?php endif; ?>

            <div class="navbar-cart">
                <!-- Link al carrello con controllo accesso -->
                <a href="<?= htmlspecialchars($header['cartLink']) ?>" id="cart-link">
                    <img src="<?= htmlspecialchars($header['cartIcon']) ?>" alt="Carrello">
                </a>
            </div>
        </div>
    </nav>
</header>

<script>
    // Funzione che controlla se l'utente è loggato prima di permettere l'accesso al carrello
    document.getElementById('cart-link').addEventListener('click', function(event) {
        // Controlla se la variabile di sessione è definita
        <?php if (!isset($_SESSION['username'])): ?>
        event.preventDefault(); // Impedisce il redirect
        alert("Questa funzionalità non è disponibile se non ti accedi.");
        <?php endif; ?>
    });
</script>