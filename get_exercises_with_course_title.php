<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$servername = "localhost"; // Adresse de ton serveur
$username = "root"; // Ton nom d'utilisateur
$password = ""; // Ton mot de passe
$dbname = "momkadischool"; // Nom de la base de données

// Créer la connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$cours_id = $_GET['cours_id']; // Récupère l'ID du cours depuis la requête

// Vérifie si l'ID du cours est défini et est un entier valide
if (!isset($cours_id) || !is_numeric($cours_id)) {
    echo json_encode(['error' => 'ID de cours invalide']);
    exit();
}

// Query pour récupérer les exercices et le titre du cours
$sql = "
SELECT ec.id AS exercice_id, ec.description AS exercice_description, ec.fichier_url, c.titre AS cours_titre
FROM exercices_cours ec
INNER JOIN cours c ON ec.cours_id = c.id
WHERE ec.cours_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cours_id); // Lie le paramètre cours_id à la requête
$stmt->execute();

$result = $stmt->get_result();
$exercises = [];

while ($row = $result->fetch_assoc()) {
    $exercises[] = $row; // Ajoute chaque exercice avec son titre de cours
}

$stmt->close();
$conn->close();

// Vérifie si des exercices ont été trouvés
if (empty($exercises)) {
    echo json_encode(['message' => 'Aucun exercice trouvé pour ce cours']);
} else {
    echo json_encode($exercises); // Retourne les résultats en format JSON
}
?>
