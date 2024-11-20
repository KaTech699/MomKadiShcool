<?php
header('Content-Type: application/json');

// Récupération du paramètre 'classe_nom' passé dans l'URL
$classe_nom = isset($_GET['classe_nom']) ? $_GET['classe_nom'] : '';

// Vérifier si le paramètre 'classe_nom' est fourni
if (empty($classe_nom)) {
    echo json_encode(["error" => "Paramètre 'classe_nom' manquant"]);
    exit;
}

// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'momkadischool');
if ($conn->connect_error) {
    echo json_encode(["error" => "Échec de la connexion à la base de données : " . $conn->connect_error]);
    exit;
}

// Préparer la requête pour récupérer les IDs des matières de la classe
$stmt = $conn->prepare("SELECT id FROM matieres WHERE classe_nom = ?");
if (!$stmt) {
    echo json_encode(["error" => "Erreur de préparation de la requête : " . $conn->error]);
    $conn->close();
    exit;
}

$stmt->bind_param("s", $classe_nom);
$stmt->execute();
$result = $stmt->get_result();

// Initialiser un tableau pour stocker les IDs des matières
$matieresIds = [];

if ($result && $result->num_rows > 0) {
    // Récupérer chaque ligne de résultat et l'ajouter au tableau des IDs
    while ($row = $result->fetch_assoc()) {
        $matieresIds[] = $row['id'];  // Ajouter l'ID de la matière au tableau
    }
}

// Vérifier si des matières ont été trouvées et envoyer la réponse
if (empty($matieresIds)) {
    echo json_encode(["error" => "Aucune matière trouvée pour la classe '$classe_nom'"]);
} else {
    // Retourner les IDs des matières sous forme de tableau JSON
    echo json_encode($matieresIds);
}

// Fermer la connexion à la base de données
$stmt->close();
$conn->close();
?>
