<?php
require_once("connexion_db.php");

// Vérifier si le formulaire d'inscription a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    // Vérifier si le login est déjà utilisé
    $check_query = "SELECT * FROM Account WHERE login = '$login'";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        // Le login est déjà utilisé
        $error_message = "Ce login est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        // Hasher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer les données dans la base de données
        $insert_query = "INSERT INTO Account (login, pw) VALUES ('$login', '$hashed_password')";
        
        if ($conn->query($insert_query) === TRUE) {
            // Démarrer la session
            session_start();

            // Enregistrer le login de l'utilisateur dans la session
            $_SESSION["login"] = $login;

            // Rediriger vers la page connectée avec un message dans l'URL
            header("Location: connected.php?message=created");
            exit();
        } else {
            // Erreur lors de l'inscription
            $error_message = "Erreur d'inscription : " . $conn->error;
        }
    }
}

// Fermer la connexion à la base de données
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <h2>Inscription</h2>
    <?php if (isset($error_message)) { ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
        <?php header("Refresh: 1; url=register.php"); // Rediriger vers register.php après un délai ?>
    <?php } ?>
    <form method="post" action="">
        <label for="login">Login :</label>
        <input type="text" name="login" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br>

        <button type="submit">Créer un compte</button>
    </form>
</body>
</html>


