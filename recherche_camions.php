<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$types_camions = ['frigo', 'citerne', 'palette', 'plateau'];
$ville_options = [];
$searchPerformed = false;
$results = [];
$type = $ville = $ville_type = "";

// Vérifier si un type de camion a été sélectionné avant d'afficher les villes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["type"])) {
    $type = $_POST["type"];
    $ville_type = isset($_POST["ville_type"]) ? $_POST["ville_type"] : "arrivee";

    // Récupérer les villes en fonction du type de camion sélectionné
    $stmt = $conn->prepare("
        SELECT DISTINCT " . ($ville_type === 'depart' ? "ville_depart" : "ville_arrivee") . " 
        FROM Cargaisons c 
        JOIN Camions cam ON c.immat = cam.immat 
        WHERE cam.type_camion = ? 
        ORDER BY " . ($ville_type === 'depart' ? "ville_depart" : "ville_arrivee")
    );
    $stmt->bind_param("s", $type);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $ville_options[] = $row[$ville_type === 'depart' ? "ville_depart" : "ville_arrivee"];
    }
    $stmt->close();

    // Vérifier si une ville a été sélectionnée avant d'exécuter la recherche
    if (!empty($_POST["ville"])) {
        $ville = $_POST["ville"];
        $searchPerformed = true;

        // Rechercher les cargaisons correspondantes
        $stmt = $conn->prepare("
            SELECT cam.immat, cam.type_camion, c.id_cargaison, c.date_transport, c.ville_depart, c.ville_arrivee, c.numero_permis, ch.nom, ch.prenom 
            FROM Camions cam 
            JOIN Cargaisons c ON cam.immat = c.immat 
            JOIN Chauffeurs ch ON c.numero_permis = ch.numero_permis 
            WHERE cam.type_camion = ? AND " . ($ville_type === 'depart' ? "c.ville_depart" : "c.ville_arrivee") . " = ?"
        );
        $stmt->bind_param("ss", $type, $ville);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $results[] = $row;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechercher des Camions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script>
        function updateVilleOptions() {
            document.querySelector('form').submit();
        }
    </script>
</head>
<body class="bg-light">
    <a href="index.php" class="btn btn-secondary mb-3">🏠 Accueil</a>
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">🔍 Recherche de Camions</h2>
            <form method="post" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Type de camion</label>
                    <select name="type" class="form-control" required onchange="updateVilleOptions()">
                        <option value="" disabled selected>Sélectionner un type</option>
                        <?php foreach ($types_camions as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>" <?= $type == $t ? "selected" : "" ?>><?= htmlspecialchars($t) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Type de ville</label>
                    <select name="ville_type" class="form-control" required onchange="updateVilleOptions()">
                        <option value="depart" <?= $ville_type == 'depart' ? "selected" : "" ?>>Ville de départ</option>
                        <option value="arrivee" <?= $ville_type == 'arrivee' ? "selected" : "" ?>>Ville d'arrivée</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ville</label>
                    <select name="ville" class="form-control" required>
                        <option value="" disabled selected>Sélectionner une ville</option>
                        <?php foreach ($ville_options as $v): ?>
                            <option value="<?= htmlspecialchars($v) ?>" <?= $ville == $v ? "selected" : "" ?>><?= htmlspecialchars($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">🔎 Rechercher</button>
            </form>

            <?php if ($searchPerformed): ?>
                <?php if (!empty($results)): ?>
                    <div class="mt-4">
                        <h4>Résultats :</h4>
                        <ul class="list-group">
                            <?php foreach ($results as $row): ?>
                                <li class="list-group-item">
                                    🚛 Camion <?= htmlspecialchars($row["immat"]) ?> - Cargaison ID: <?= htmlspecialchars($row["id_cargaison"]) ?>
                                    <br><strong>Date:</strong> <?= htmlspecialchars($row["date_transport"]) ?>
                                    <br><strong>Départ:</strong> <?= htmlspecialchars($row["ville_depart"]) ?>
                                    <br><strong>Arrivée:</strong> <?= htmlspecialchars($row["ville_arrivee"]) ?>
                                    <br><strong>Chauffeur:</strong> <?= htmlspecialchars($row["nom"]) ?> <?= htmlspecialchars($row["prenom"]) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="mt-4 alert alert-warning text-center">
                        ⚠ Aucun résultat trouvé pour cette recherche !
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
