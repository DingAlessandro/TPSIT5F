<?php
// Leggi il file JSON
$jsonData = file_get_contents('header-footer.json');

// Decodifica i dati JSON in array associativo
$data = json_decode($jsonData, true);

// Verifica se il JSON è stato letto e decodificato correttamente
if (!$data || !isset($data['footer'])) {
    echo "<!-- Errore nel caricamento del footer -->";
    return;
}

$footer = $data['footer'];
?>

<footer class="footer">
    <div class="footer-bottom">
        <p><?= $footer['text'] ?></p>
    </div>
</footer>
