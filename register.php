<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Content-Type: application/json");

// Inclure le fichier de configuration de la base de données
include('db_config.php');

// Récupérer les données de l'utilisateur envoyées en POST
$phone = $_POST['phone'];
$prenom = $_POST['prenom'];
$nom = $_POST['nom'];
$genre = isset($_POST['genre']) ? $_POST['genre'] : null;  // Ajout du genre
$birth_date = $_POST['birth_date'];
$role = $_POST['role'];

// Si la classe, l'établissement, la matière, ou le parrain sont non définis, on les met à null
$classe = isset($_POST['classe']) ? $_POST['classe'] : null;
$etablissement = isset($_POST['etablissement']) ? $_POST['etablissement'] : null;
$matiere = isset($_POST['matiere']) ? $_POST['matiere'] : null;
$parrain = isset($_POST['parrain']) ? $_POST['parrain'] : null;
$password = $_POST['password'];

// Convertir le nom de l'établissement en majuscules si disponible
if ($etablissement) {
    $etablissement = strtoupper($etablissement);
}

// Validation des champs obligatoires
if (empty($phone) || empty($prenom) || empty($nom) || empty($birth_date) || empty($role) || empty($password)) {
    echo json_encode(["status" => "error", "message" => "Tous les champs obligatoires doivent être remplis."]);
    exit;
}

// Validation du numéro de téléphone (8 chiffres)
if (!preg_match("/^\d{8}$/", $phone)) {
    echo json_encode(["status" => "error", "message" => "Le numéro de téléphone doit contenir exactement 8 chiffres."]);
    exit;
}

// Validation de l'âge (au moins 13 ans)
$age = date_diff(date_create($birth_date), date_create('today'))->y;
if ($age < 13) {
    echo json_encode(["status" => "error", "message" => "L'âge doit être supérieur ou égal à 13 ans."]);
    exit;
}

// Sécuriser le mot de passe
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Préparation de la requête pour l'insertion des données dans la base de données
try {
    if ($role === 'eleve') {
        // Vérification que la classe et l'établissement sont remplis pour un élève
        if (empty($classe) || empty($etablissement)) {
            echo json_encode(["status" => "error", "message" => "La classe et l'établissement sont obligatoires pour un élève."]);
            exit;
        }

        // Requête d'insertion pour un élève avec le champ genre
        $stmt = $conn->prepare("INSERT INTO utilisateurs (numero_telephone, prenom, nom, genre, date_naissance, role, classe, etablissement, mot_de_passe, statut) 
                                VALUES (:phone, :prenom, :nom, :genre, :birth_date, :role, :classe, :etablissement, :password, 'actif')");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':classe', $classe);
        $stmt->bindParam(':etablissement', $etablissement);
        $stmt->bindParam(':password', $hashed_password);
    } elseif ($role === 'enseignant') {
        // Vérification que la matière et le parrain sont remplis pour un enseignant
        if (empty($matiere) || empty($parrain)) {
            echo json_encode(["status" => "error", "message" => "La matière et le parrain sont obligatoires pour un enseignant."]);
            exit;
        }

        // Requête d'insertion pour un enseignant avec le champ genre
        $stmt = $conn->prepare("INSERT INTO utilisateurs (numero_telephone, prenom, nom, genre, date_naissance, role, matiere, parrain_marein, mot_de_passe, statut) 
                                VALUES (:phone, :prenom, :nom, :genre, :birth_date, :role, :matiere, :parrain, :password, 'inactif')");
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':genre', $genre);
        $stmt->bindParam(':birth_date', $birth_date);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':matiere', $matiere);
        $stmt->bindParam(':parrain', $parrain);
        $stmt->bindParam(':password', $hashed_password);
    } else {
        echo json_encode(["status" => "error", "message" => "Rôle invalide."]);
        exit;
    }

    // Exécuter la requête
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Inscription réussie"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Erreur lors de l'insertion des données: " . $stmt->errorInfo()]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Erreur SQL: " . $e->getMessage()]);
}

// Fermer la connexion
$conn = null;
?>
