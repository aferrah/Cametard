<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

// Récupérer la liste des villes disponibles
$ville_options = [];
$result = $conn->query("SELECT DISTINCT ville_arrivee FROM Cargaisons ORDER BY ville_arrivee");
while ($row = $result->fetch_assoc()) {
    $ville_options[] = $row['ville_arrivee'];
}

// Traitement de la recherche
$results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST["type"];
    $ville = $_POST["ville"];

    $stmt = $conn->prepare("SELECT cam.immat, cam.type_camion, c.id_cargaison, c.ville_arrivee 
                            FROM Camions cam 
                            JOIN Cargaisons c ON cam.immat = c.immat
                            WHERE cam.type_camion = ? AND c.ville_arrivee = ?");
    $stmt->bind_param("ss", $type, $ville);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher des Camions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">🔍 Recherche de Camions</h2>
            <form method="post" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Type de camion</label>
                    <select name="type" class="form-control" required>
                        <option value="frigo">Frigo</option>
                        <option value="citerne">Citerne</option>
                        <option value="palette">Palette</option>
                        <option value="plateau">Plateau</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ville d'arrivée</label>
                    <select name="ville" class="form-control" required>
                        <option value="" disabled selected>Sélectionner une ville</option>
                        <?php foreach ($ville_options as $ville): ?>
                            <option value="<?= htmlspecialchars($ville) ?>"><?= htmlspecialchars($ville) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">🔎 Rechercher</button>
            </form>

            <?php if (!empty($results)): ?>
                <div class="mt-4">
                    <h4>Résultats :</h4>
                    <ul class="list-group">
                        <?php foreach ($results as $row): ?>
                            <li class="list-group-item">
                                🚛 Camion <?= htmlspecialchars($row["immat"]) ?> - Cargaison ID: <?= htmlspecialchars($row["id_cargaison"]) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
