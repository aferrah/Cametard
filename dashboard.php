<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "config.php";

// Récupérer les informations de l'utilisateur connecté
$user_id = $_SESSION["user_id"];
$stmt = $conn->prepare("SELECT username, role FROM Logins WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $role);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow p-4">
            <h2 class="text-center">Bienvenue, <?= htmlspecialchars($username) ?> !</h2>
            <p class="text-center">Vous êtes connecté en tant que <strong><?= htmlspecialchars($role) ?></strong>.</p>

            <div class="d-flex justify-content-center mt-4">
                <a href="recherche_camions.php" class="btn btn-primary mx-2">🔍 Rechercher Camions</a>
                <a href="ajouter_camion.php" class="btn btn-success mx-2">🚛 Ajouter un Camion</a>
                <a href="modifier_chauffeur.php" class="btn btn-warning mx-2">✏️ Modifier Chauffeurs</a>
                <a href="logout.php" class="btn btn-danger mx-2">🚪 Déconnexion</a>
            </div>
        </div>
    </div>
</body>
</html>
