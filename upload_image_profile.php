<?php
include('db_config.php'); // Inclure le fichier de connexion à la base de données

if (isset($_POST['phone']) && isset($_POST['image'])) {
    // Récupérer les données
    $phone = $_POST['phone'];
    $base64Image = $_POST['image'];

    // Décoder l'image
    $imageData = base64_decode($base64Image);
    
    // Utiliser le numéro de téléphone comme nom de fichier
    $fileName = $phone . '.png'; // Le nom du fichier sera le numéro de téléphone
    $filePath = 'Upload/profile/' . $fileName; // Chemin où l'image sera sauvegardée

    // Vérifier si le répertoire 'Upload/profile/' existe, sinon le créer
    $directoryPath = 'Upload/profile/';
    if (!is_dir($directoryPath)) {
        // Si le répertoire n'existe pas, le créer
        if (!mkdir($directoryPath, 0777, true)) {
            echo 'Erreur lors de la création du répertoire';
            exit;
        }
    }

    // Vérifier si une ancienne image existe et la supprimer
    if (file_exists($filePath)) {
        if (!unlink($filePath)) {
            echo 'Erreur lors de la suppression de l\'ancienne image';
            exit;
        }
    }

    // Sauvegarder la nouvelle image sur le serveur
    if (file_put_contents($filePath, $imageData)) {
        // Mise à jour de l'URL de l'image dans la base de données avec PDO
        try {
            // Préparation de la requête SQL
            $query = "UPDATE utilisateurs SET photo_profil = :photo_profil WHERE numero_telephone = :phone";
            $stmt = $conn->prepare($query);
            
            // Lier les paramètres
            $stmt->bindParam(':photo_profil', $filePath, PDO::PARAM_STR);
            $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
            
            // Exécuter la requête
            if ($stmt->execute()) {
                echo 'Image de profil mise à jour avec succès';
            } else {
                echo 'Erreur lors de la mise à jour de l\'image de profil';
            }
        } catch (PDOException $e) {
            echo 'Erreur de base de données: ' . $e->getMessage();
        }
    } else {
        echo 'Erreur lors de l\'enregistrement de l\'image';
    }
} else {
    echo 'Données manquantes';
}
?>
