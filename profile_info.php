<?php
// Inclure le fichier de configuration de la base de données
include('db_config.php');

// Vérifiez si le numéro de téléphone est fourni via GET
if (isset($_GET['phone'])) {
    $phone = $_GET['phone'];

    // Préparer la requête pour récupérer les données de l'utilisateur
    $sql = "SELECT 
                numero_telephone, 
                prenom, 
                nom, 
                date_naissance, 
                role, 
                classe, 
                etablissement, 
                matiere, 
                parrain_marein, 
                statut, 
                date_creation, 
                photo_profil 
            FROM utilisateurs 
            WHERE numero_telephone = :phone";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Récupérer les données de l'utilisateur
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($userData);  // Retourner les données en format JSON
    } else {
        echo json_encode(["error" => "Utilisateur non trouvé"]);
    }
} else {
    echo json_encode(["error" => "Numéro de téléphone non fourni"]);
}

// Fermer la connexion PDO
$conn = null;
?>
