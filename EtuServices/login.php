<?php
require_once("connexion_db.php");

// Démarrer la session
session_start();

// Vérifier si le formulaire de connexion a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $login = $_POST["login"];
    $password = $_POST["password"];

    // Récupérer le mot de passe haché de la base de données
    $query = "SELECT pw FROM Account WHERE login = '$login'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row["pw"];

        // Vérifier le mot de passe
        if (password_verify($password, $hashed_password)) {
            // Enregistrer le login de l'utilisateur dans la session
            $_SESSION["login"] = $login;
           
            // Exécuter le script Python avec le login comme argument
            $pythonBinPath = "C:\Users\srhmr\Downloads\Anaconda\python.exe";
            $scriptPath = "connexion_redis.py";
            $cmd = $pythonBinPath." ".$scriptPath." ".$login;
            $shelloutput = shell_exec($cmd);


            // Afficher le résultat du script python
            echo '<script>';
            echo 'console.log("Résultat du script Python : ' . addslashes($shelloutput) . '");';
            echo '</script>';            

            // Rediriger vers la page connectée
            header("Location: connected.php");
            exit();

        } else {
            // Mot de passe incorrect
            $_SESSION["error_message"] = "Login ou mot de passe incorrect.";

            // Ajouter la redirection après un délai avec JavaScript
            header("Refresh: 1; url=login.php");       
            exit();
        }
    } else {
        // Login incorrect
        $_SESSION["error_message"] = "Login ou mot de passe incorrect.";

        header("Refresh: 1; url=login.php");       
        exit();
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
    <title>Page de Connexion</title>
</head>
<body>
    <h2>Connexion</h2>
    <?php if (isset($_SESSION["error_message"])) { ?>
        <p style="color: red;"><?php echo $_SESSION["error_message"]; ?></p>
        <?php unset($_SESSION["error_message"]); ?>
    <?php } ?>
    <form method="post" action="">
        <label for="login">Login :</label>
        <input type="text" name="login" required><br>

        <label for="password">Mot de passe :</label>
        <input type="password" name="password" required><br>

        <button type="submit">Se connecter</button>

        <p><a href="register.php">Créer un compte</a></p>
    </form>
</body>
</html>


