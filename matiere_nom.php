<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "momkadischool";

$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Forcer l'encodage UTF-8
$conn->set_charset("utf8");

// Récupérer l'ID de la matière passé en paramètre
$id = $_GET['id'];

// Préparer et exécuter la requête pour récupérer le nom de la matière
$sql = "SELECT nom FROM matieres WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nom);
$stmt->fetch();

// Retourner le nom de la matière sous forme de JSON
echo json_encode(['nom' => $nom]);

// Fermer la connexion
$stmt->close();
$conn->close();
?>
