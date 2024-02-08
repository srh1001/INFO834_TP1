<?php
$servername = "localhost";
$username = "root"; // Utilisateur par défaut de XAMPP
$password = "";     // Mot de passe par défaut de XAMPP (laissez vide par défaut)
$dbname = "INFO834_TP1_DB";

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}
?>
