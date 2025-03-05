<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $immat = $_POST["immat"];
    $type = $_POST["type"];
    $poids = $_POST["poids"];

    $stmt = $conn->prepare("INSERT INTO Camions (immat, type_camion, poids_transport) VALUES (?, ?, ?)");
    $stmt->bind_param("ssd", $immat, $type, $poids);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>🚛 Camion ajouté avec succès.</div>";
    } else {
        $message = "<div class='alert alert-danger'>⚠️ Erreur lors de l'ajout.</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Camion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">🚛 Ajouter un Camion</h2>
            <?= $message ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Immatriculation</label>
                    <input type="text" name="immat" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type</label>
                    <select name="type" class="form-control" required>
                        <option value="frigo">Frigo</option>
                        <option value="citerne">Citerne</option>
                        <option value="palette">Palette</option>
                        <option value="plateau">Plateau</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Poids Transporté (kg)</label>
                    <input type="number" step="0.01" name="poids" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">➕ Ajouter</button>
            </form>
        </div>
    </div>
</body>
</html>
