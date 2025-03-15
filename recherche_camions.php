<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$ville_options = [];
$result = $conn->query("SELECT DISTINCT ville_arrivee FROM Cargaisons ORDER BY ville_arrivee");
while ($row = $result->fetch_assoc()) {
    $ville_options[] = $row['ville_arrivee'];
}

$results = [];
$searchPerformed = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchPerformed = true;
    $type = $_POST["type"];
    $ville = $_POST["ville"];

    $stmt = $conn->prepare("SELECT cam.immat, cam.type_camion, c.id_cargaison, c.date_transport, c.ville_depart, c.ville_arrivee, c.numero_permis, ch.nom, ch.prenom FROM Camions cam JOIN Cargaisons c ON cam.immat = c.immat JOIN Chauffeurs ch ON c.numero_permis = ch.numero_permis WHERE cam.type_camion = ? AND c.ville_arrivee = ?");
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
    <script>
        function toggleDetails(id) {
            let details = document.getElementById("details-" + id);
            details.style.display = details.style.display === "none" ? "block" : "none";
        }
    </script>
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

            <?php if ($searchPerformed): ?>
                <?php if (!empty($results)): ?>
                    <div class="mt-4">
                        <h4>Résultats :</h4>
                        <ul class="list-group">
                            <?php foreach ($results as $row): ?>
                                <li class="list-group-item">
                                    <button class="btn btn-link" onclick="toggleDetails(<?= $row['id_cargaison'] ?>)">
                                        🚛 Camion <?= htmlspecialchars($row["immat"]) ?> - Cargaison ID: <?= htmlspecialchars($row["id_cargaison"]) ?>
                                    </button>
                                    <div id="details-<?= $row['id_cargaison'] ?>" style="display:none; margin-top:10px;" class="border p-2 bg-light">
                                        <p><strong>Date:</strong> <?= htmlspecialchars($row["date_transport"]) ?></p>
                                        <p><strong>Ville Départ:</strong> <?= htmlspecialchars($row["ville_depart"]) ?></p>
                                        <p><strong>Ville Arrivée:</strong> <?= htmlspecialchars($row["ville_arrivee"]) ?></p>
                                        <p><strong>Chauffeur:</strong> <?= htmlspecialchars($row["nom"]) ?> <?= htmlspecialchars($row["prenom"]) ?></p>
                                    </div>
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
