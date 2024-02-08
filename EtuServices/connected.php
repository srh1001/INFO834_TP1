<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["login"])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: login.php");
    exit();
}

// Vérifier si un message a été passé dans l'URL
$message = isset($_GET["message"]) ? $_GET["message"] : "";

// Fonction de déconnexion
function logout() {
    // Détruire la session
    session_destroy();

    // Rediriger vers la page de connexion
    header("Location: login.php");
    exit();
}

// Vérifier si le formulaire de déconnexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["logout"])) {
    logout();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connecté</title>
</head>
<body>
    <h2>Connecté</h2>
    <?php if ($message === "created") { ?>
        <p>Votre compte a bien été créé.</p>
    <?php } ?>
    <p>Vous êtes connecté.</p>

    <form method="post" action="">
        <button type="submit" name="logout">Se déconnecter</button>
    </form>

</body>
</html>

