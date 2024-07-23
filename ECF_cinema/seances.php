<?php
// Inclure le fichier de connexion à la base de données
include 'db_connection.php';

// Assurez-vous que la connexion utilise UTF-8
$conn->set_charset("utf8");

// Démarrer la session
session_start();

// Vérifier si les paramètres nécessaires sont passés
if (isset($_GET['film_id'])) {
    $film_id = intval($_GET['film_id']);

    // Préparer et exécuter la requête pour obtenir les détails du film
    $film_sql = "SELECT titre FROM film WHERE id = ?";
    $stmt = $conn->prepare($film_sql);
    if (!$stmt) {
        echo "Échec de la préparation de la requête (film): (" . $conn->errno . ") " . $conn->error;
        exit();
    }
    $stmt->bind_param("i", $film_id);
    $stmt->execute();
    $film_result = $stmt->get_result();

    if ($film_result->num_rows > 0) {
        $film_row = $film_result->fetch_assoc();
        $film_title = htmlspecialchars($film_row['titre'], ENT_QUOTES, 'UTF-8');

        // Préparer et exécuter la requête pour obtenir les séances disponibles
        $seances_sql = "SELECT id, h_debut, h_fin, num_salle, qualite, places_disponibles, prix FROM seance WHERE film_id = ? AND places_disponibles > 0";
        $stmt = $conn->prepare($seances_sql);
        if (!$stmt) {
            echo "Échec de la préparation de la requête (séance): (" . $conn->errno . ") " . $conn->error;
            exit();
        }
        $stmt->bind_param("i", $film_id);
        $stmt->execute();
        $seances_result = $stmt->get_result();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservations pour <?php echo $film_title; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Séances disponibles pour <?php echo $film_title; ?></h1>

        <?php if (isset($seances_result) && $seances_result->num_rows > 0): ?>
        <form method="POST" action="reservation_details.php">
            <table>
                <thead>
                    <tr>
                        <th>Heure de début</th>
                        <th>Heure de fin</th>
                        <th>Numéro de salle</th>
                        <th>Qualité</th>
                        <th>Places disponibles</th>
                        <th>Prix par place</th>
                        <th>Réserver</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $seances_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars(date("H:i", strtotime($row['h_debut']))); ?></td>
                        <td><?php echo htmlspecialchars(date("H:i", strtotime($row['h_fin']))); ?></td>
                        <td><?php echo htmlspecialchars($row['num_salle']); ?></td>
                        <td><?php echo htmlspecialchars($row['qualite']); ?></td>
                        <td><?php echo htmlspecialchars($row['places_disponibles']); ?></td>
                        <td><?php echo htmlspecialchars($row['prix']); ?> €</td>
                        <td>
                            <input type="radio" name="seance_id" value="<?php echo $row['id']; ?>" required>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <button type="submit">Réserver</button>
        </form>
        <?php else: ?>
        <p>Aucune séance disponible pour ce film.</p>
        <?php endif; ?>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>

<?php
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();
?>
