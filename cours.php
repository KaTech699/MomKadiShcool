<?php
// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "momkadischool";

// Créer la connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifiez la connexion
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Définir l'encodage UTF-8
$conn->set_charset("utf8");

// Récupérer l'ID de la matière depuis la requête GET
$matiere_id = isset($_GET['matiere_id']) ? intval($_GET['matiere_id']) : null;

// Vérifier si `matiere_id` est fourni
if ($matiere_id !== null) {
    // Étape 1: Récupérer les `id` des cours liés à la `matiere_id`
    $sql = "SELECT id, titre, description, fichier_url FROM cours WHERE matiere_id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("i", $matiere_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $cours = [];
        while ($row = $result->fetch_assoc()) {
            $cours[] = [
                'id' => $row['id'],
                'titre' => $row['titre'],
                'description' => isset($row['description']) ? $row['description'] : '',
                'fichier_url' => isset($row['fichier_url']) ? $row['fichier_url'] : ''
            ];
        }

        // Retourner les cours en JSON
        echo json_encode($cours, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["error" => "Erreur dans la préparation de la requête"]);
    }
} else {
    echo json_encode(["error" => "matiere_id non fourni"]);
}

// Fermer la connexion
$conn->close();
?>
