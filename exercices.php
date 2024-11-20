<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configurer la connexion à la base de données
$host = 'localhost';
$dbname = 'momkadischool';
$username = 'root';
$password = '';

header('Content-Type: application/json'); // Définir l'en-tête pour la réponse JSON

try {
    // Connexion à la base de données
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si le paramètre cours_id est présent et valide
    if (!isset($_GET['cours_id']) || empty($_GET['cours_id'])) {
        echo json_encode(["error" => "Le paramètre 'cours_id' est manquant ou vide."]);
        exit;
    }

    $cours_id = intval($_GET['cours_id']); // Sécuriser le paramètre reçu

    // Préparer et exécuter la requête SQL
    $stmt = $conn->prepare("SELECT id, description, fichier_url FROM exercices_cours WHERE cours_id = :cours_id");
    $stmt->bindParam(':cours_id', $cours_id, PDO::PARAM_INT);
    $stmt->execute();

    // Récupérer les résultats
    $exercises = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Retourner les résultats en JSON
    if (empty($exercises)) {
        echo json_encode([]); // Tableau vide si aucun exercice n'est trouvé
    } else {
        echo json_encode($exercises);
    }
} catch (PDOException $e) {
    // Retourner une erreur si la connexion ou la requête échoue
    echo json_encode(["error" => "Erreur de connexion ou de requête : " . $e->getMessage()]);
} catch (Exception $e) {
    // Gérer les autres erreurs possibles
    echo json_encode(["error" => "Une erreur est survenue : " . $e->getMessage()]);
}
