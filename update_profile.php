<?php
// Inclure votre fichier de configuration pour la connexion à la base de données
include('db_config.php');

// Vérifiez si les données sont envoyées par méthode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les paramètres envoyés via POST
    $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
    $oldPhone = isset($_POST['oldPhone']) ? $_POST['oldPhone'] : '';
    $nom = isset($_POST['nom']) ? $_POST['nom'] : '';
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : '';
    $classe = isset($_POST['classe']) ? $_POST['classe'] : '';
    $etablissement = isset($_POST['etablissement']) ? $_POST['etablissement'] : '';
    $matiere = isset($_POST['matiere']) ? $_POST['matiere'] : '';
    $date_naissance = isset($_POST['date_naissance']) ? $_POST['date_naissance'] : ''; // Date de naissance

    // Validation des données
    if (empty($phone) || empty($nom) || empty($prenom) || empty($date_naissance)) {
        echo json_encode(['status' => 'error', 'message' => 'Tous les champs obligatoires doivent être remplis.']);
        exit();
    }

    // Convertir le nom de l'établissement en majuscules avant de l'insérer
    $etablissement = strtoupper($etablissement);

    // Vérifier si le numéro de téléphone existe déjà dans la base de données
    $sqlCheckPhone = "SELECT COUNT(*) FROM utilisateurs WHERE numero_telephone = :phone AND numero_telephone != :oldPhone";
    $stmtCheckPhone = $conn->prepare($sqlCheckPhone);
    $stmtCheckPhone->bindParam(':phone', $phone);
    $stmtCheckPhone->bindParam(':oldPhone', $oldPhone);
    $stmtCheckPhone->execute();

    $phoneExists = $stmtCheckPhone->fetchColumn();

    if ($phoneExists > 0) {
        // Si le numéro de téléphone existe déjà, retournez un message d'erreur
        echo json_encode(['status' => 'error', 'message' => 'Le numéro de téléphone existe déjà.']);
        exit();
    }

    // Définir le chemin de l'image de profil
    $oldImagePath = "Upload/profile/{$oldPhone}.png"; // Ancien chemin de l'image
    $newImagePath = "Upload/profile/{$phone}.png"; // Nouveau chemin avec le nouveau numéro de téléphone

    // Vérifier si le fichier d'image avec l'ancien numéro existe
    if (file_exists($oldImagePath)) {
        // Si l'image existe, renommer l'image avec le nouveau numéro de téléphone
        if (rename($oldImagePath, $newImagePath)) {
            $photo_profil = $newImagePath; // Nouveau chemin de l'image
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors du renommage de l\'image.']);
            exit();
        }
    } else {
        // Si l'image avec l'ancien numéro n'existe pas, vous pouvez soit définir un chemin par défaut,
        // soit gérer cela autrement. Par exemple, utiliser un chemin par défaut :
        $photo_profil = $newImagePath; // Vous pouvez également uploader une nouvelle image si nécessaire.
    }

    // Requête SQL pour mettre à jour le profil
    $sql = "UPDATE utilisateurs SET 
            numero_telephone = :phone, 
            nom = :nom, 
            prenom = :prenom, 
            classe = :classe, 
            etablissement = :etablissement, 
            matiere = :matiere, 
            date_naissance = :date_naissance,
            photo_profil = :photo_profil 
            WHERE numero_telephone = :oldPhone";

    try {
        // Préparer la requête SQL
        $stmt = $conn->prepare($sql);

        // Lier les paramètres
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':oldPhone', $oldPhone);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':classe', $classe);
        $stmt->bindParam(':etablissement', $etablissement);
        $stmt->bindParam(':matiere', $matiere);
        $stmt->bindParam(':date_naissance', $date_naissance);
        $stmt->bindParam(':photo_profil', $photo_profil);

        // Exécuter la requête
        $stmt->execute();

        // Si la mise à jour a réussi, retourner un message de succès
        echo json_encode(['status' => 'success', 'message' => 'Profil mis à jour avec succès']);
    } catch (PDOException $e) {
        // Si une erreur se produit, retourner un message d'erreur
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du profil: ' . $e->getMessage()]);
    }
} else {
    // Si la méthode n'est pas POST, renvoyer une erreur
    echo json_encode(['status' => 'error', 'message' => 'Requête invalide.']);
}
?>
