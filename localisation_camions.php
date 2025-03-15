<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$types_camions = ['frigo', 'citerne', 'palette', 'plateau'];
$results = [];
$searchPerformed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $searchPerformed = true;
    $type = $_POST["type"];
    $date = $_POST["date"];

    $stmt = $conn->prepare("SELECT c.immat, c.type_camion, l.ville_matin, l.ville_soir FROM Camions c JOIN Localisation l ON c.immat = l.immat WHERE c.type_camion = ? AND l.date_localisation = ?");
    $stmt->bind_param("ss", $type, $date);
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
    <title>Localisation des Camions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">📍 Localisation des Camions</h2>
            <form method="post" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Type de camion</label>
                    <select name="type" class="form-control" required>
                        <option value="" disabled selected>Sélectionner un type</option>
                        <?php foreach ($types_camions as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>"><?= htmlspecialchars($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">🔍 Rechercher</button>
            </form>
            
            <?php if ($searchPerformed): ?>
                <?php if (!empty($results)): ?>
                    <div class="mt-4">
                        <h4>Résultats :</h4>
                        <ul class="list-group">
                            <?php foreach ($results as $row): ?>
                                <li class="list-group-item">
                                    🚛 Camion <?= htmlspecialchars($row["immat"]) ?>
                                    <br><strong>Départ :</strong> <?= htmlspecialchars($row["ville_matin"]) ?>
                                    <br><strong>Arrivée :</strong> <?= htmlspecialchars($row["ville_soir"]) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="mt-4 alert alert-warning text-center">
                        ⚠ Aucun camion trouvé pour cette recherche !
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
