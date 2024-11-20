<?php
$host = 'localhost';
$dbname = 'momkadischool';
$username = 'root';
$password = '';

// Créer une connexion à la base de données
$conn = new mysqli($host, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Définir l'en-tête pour indiquer que la réponse est du JSON
header('Content-Type: application/json');

// Requête SQL pour récupérer toutes les images, triées par 'position'
$sql = "SELECT image_url FROM carousel ORDER BY position";
$result = $conn->query($sql);

$images = [];

// Vérifier s'il y a des résultats
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ajouter uniquement l'URL en tant que chaîne de caractères, sans clé associée
        $images[] = $row['image_url'];
    }
} else {
    $images = [];
}

// Fermer la connexion
$conn->close();

// Retourner le tableau des images sous forme de JSON
echo json_encode($images);
?>
