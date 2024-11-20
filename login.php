<?php
// Inclure le fichier de configuration de la base de données
include('db_config.php');



// Récupérer et sécuriser les données de l'utilisateur
$phone = $_POST['phone'];
$password = $_POST['password'];

if (empty($phone) || empty($password)) {
    echo json_encode([
        'status' => 'failed',
        'message' => 'Veuillez remplir tous les champs requis.'
    ]);
    exit();
}

// Utiliser une requête préparée pour éviter les injections SQL
$sql = "SELECT * FROM utilisateurs WHERE numero_telephone = :phone AND statut = 'actif'";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
$stmt->execute();

// Vérifier si l'utilisateur existe
if ($stmt->rowCount() > 0) {
    // Récupérer les données de l'utilisateur
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le mot de passe
    if (password_verify($password, $user['mot_de_passe'])) {
        // Si le mot de passe est correct, récupérer le rôle et autres informations de l'utilisateur
        $role = $user['role'];
        $classe = isset($user['classe']) ? $user['classe'] : null;
        $etablissement = isset($user['etablissement']) ? $user['etablissement'] : null;
        $matiere = isset($user['matiere']) ? $user['matiere'] : null;

        // Réponse JSON avec les informations de l'utilisateur
        echo json_encode([
            'status' => 'success',
            'is_active' => '1',
            'role' => $role,
            'classe' => $classe,
            'etablissement' => $etablissement,
            'matiere' => $matiere
        ]);
    } else {
        // Mot de passe incorrect
        echo json_encode([
            'status' => 'failed',
            'is_active' => '0',
            'message' => 'Nom d\'utilisateur ou mot de passe incorrect.'
        ]);
    }
} else {
    // Utilisateur non trouvé ou compte inactif
    echo json_encode([
        'status' => 'failed',
        'is_active' => '0',
        'message' => 'Nom d\'utilisateur ou mot de passe incorrect, ou compte inactif.'
    ]);
}

// Fermer la connexion
$conn = null;
?>
