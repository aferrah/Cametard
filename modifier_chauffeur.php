<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}
include "config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["delete"])) {
        $numero_permis = $_POST["numero_permis"];
        $stmt = $conn->prepare("DELETE FROM Chauffeurs WHERE numero_permis = ?");
        $stmt->bind_param("s", $numero_permis);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Chauffeur supprimé.</div>";
        } else {
            $message = "<div class='alert alert-danger'>⚠️ Erreur lors de la suppression.</div>";
        }
        $stmt->close();
        echo "<script>window.location.href='modifier_chauffeur.php';</script>";
        exit();
    } else {
        $numero_permis = $_POST["numero_permis"];
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $stmt = $conn->prepare("UPDATE Chauffeurs SET nom = ?, prenom = ? WHERE numero_permis = ?");
        $stmt->bind_param("sss", $nom, $prenom, $numero_permis);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success'>✅ Chauffeur mis à jour.</div>";
        } else {
            $message = "<div class='alert alert-danger'>⚠️ Erreur lors de la mise à jour.</div>";
        }
        $stmt->close();
        echo "<script>window.location.href='modifier_chauffeur.php';</script>";
        exit();
    }
}

// Rafraîchir la liste des chauffeurs après modification/suppression
$chauffeurs = [];
$result = $conn->query("SELECT numero_permis, nom, prenom FROM Chauffeurs ORDER BY nom");
while ($row = $result->fetch_assoc()) {
    $chauffeurs[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Chauffeur</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <a href="index.php" class="btn btn-secondary m-3">🏠 Accueil</a>
    <div class="container mt-5">
        <div class="card p-4 shadow-lg">
            <h2 class="text-center">✏️ Modifier un Chauffeur</h2>
            <?= $message ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Sélectionner un Chauffeur</label>
                    <select name="numero_permis" class="form-control" required onchange="updateFields(this)">
                        <option value="" disabled selected>Sélectionner un chauffeur</option>
                        <?php foreach ($chauffeurs as $chauffeur): ?>
                            <option value="<?= htmlspecialchars($chauffeur["numero_permis"]) ?>"
                                    data-nom="<?= htmlspecialchars($chauffeur["nom"]) ?>"
                                    data-prenom="<?= htmlspecialchars($chauffeur["prenom"]) ?>">
                                <?= htmlspecialchars($chauffeur["nom"]) ?> <?= htmlspecialchars($chauffeur["prenom"]) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="chauffeur-info" style="display:none;">
                    <p id="chauffeur-selected" class="text-center text-primary"></p>
                    <div class="mb-3">
                        <label class="form-label">Nom</label>
                        <input type="text" id="nom" name="nom" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Prénom</label>
                        <input type="text" id="prenom" name="prenom" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning w-100">🔄 Modifier</button>
                    <button type="submit" name="delete" class="btn btn-danger w-100 mt-2">🗑 Supprimer</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        function updateFields(select) {
            let selectedOption = select.options[select.selectedIndex];
            let nom = selectedOption.getAttribute("data-nom");
            let prenom = selectedOption.getAttribute("data-prenom");
            
            document.getElementById("nom").value = nom;
            document.getElementById("prenom").value = prenom;
            document.getElementById("chauffeur-info").style.display = "block";
            document.getElementById("chauffeur-selected").textContent = `Chauffeur ${nom} ${prenom} sélectionné`;
        }
    </script>
</body>
</html>
