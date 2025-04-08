<?php
// Avvia la sessione
session_start();

// Distrugge tutte le variabili di sessione
$_SESSION = array();

// Se si desidera distruggere completamente la sessione, cancella anche il cookie di sessione
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Infine, distrugge la sessione
session_destroy();

// Reindirizza alla pagina di login o alla home
header("Location: login.php");
exit;
?>