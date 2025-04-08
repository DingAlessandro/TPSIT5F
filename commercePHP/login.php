<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Avvia la sessione solo se non è già avviata
}

$errorMessage = ""; // Messaggio d'errore

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connessione al database usando mysqli
    $conn = new mysqli("localhost", "root", "", "commerce");
    if ($conn->connect_error) {
        die("Connessione fallita: " . $conn->connect_error);
    }

    // Recupera i dati inviati dal form
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $action = isset($_POST['action']) ? $_POST['action'] : "";

    if ($action === "login") {
        // Caso LOGIN
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            // Verifica la password (assumendo che le password siano hashate)
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['username'] = $user['username'];
                echo "<script>alert('Accesso riuscito'); window.location.href='index.php';</script>";
                exit();
            } else {
                $errorMessage = "Password errata.";
            }
        } else {
            $errorMessage = "Utente non trovato.";
        }
        $stmt->close();

    } elseif ($action === "register") {
        // Caso REGISTRAZIONE
        // Verifica se l'utente esiste già
        $query = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->fetch_assoc()) {
            $errorMessage = "Username già in uso. Scegli un altro username.";
        } else {
            // Registra l'utente
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insertQuery = "INSERT INTO users (username, password_hash) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param("ss", $username, $hashedPassword);
            if ($stmtInsert->execute()) {
                // Registrazione riuscita: esegui automaticamente anche il login
                $_SESSION['username'] = $username;

                // Crea un carrello vuoto per il nuovo utente
                $createCartQuery = "INSERT INTO carts (username) VALUES (?)";
                $cartStmt = $conn->prepare($createCartQuery);
                $cartStmt->bind_param("s", $username);
                $cartStmt->execute();

                echo "<script>alert('Registrazione riuscita. Accesso eseguito e carrello creato.'); window.location.href='index.php';</script>";
                exit();
            } else {
                $errorMessage = "Errore durante la registrazione. Riprova.";
            }
            $stmtInsert->close();
        }
        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZOZO-P - Login / Registrazione</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<?php require 'header.php'; ?>

<main class="login-page">
    <h1>Accedi / Registrati</h1>
    <?php if (!empty($errorMessage)): ?>
        <p style="color: red;"><?php echo $errorMessage; ?></p>
    <?php endif; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" class="login-form">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required placeholder="Inserisci il tuo username">
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required placeholder="Inserisci la tua password">
        </div>
        <div class="form-buttons">
            <button type="submit" name="action" value="login" class="btn btn-primary">Accedi</button>
            <button type="submit" name="action" value="register" class="btn btn-secondary">Registrati</button>
        </div>
    </form>
</main>

</body>
</html>
