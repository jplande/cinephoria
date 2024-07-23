<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connection.php';

$user_id = $_SESSION['user_id'];

// Récupérer les réservations de l'utilisateur
$stmt = $conn->prepare("SELECT r.id, r.seance_id, r.num_places, r.prix_total, s.h_debut, s.h_fin, f.titre FROM reservation r JOIN seance s ON r.seance_id = s.id JOIN film f ON s.film_id = f.id WHERE r.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon espace</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <h1>Mon espace</h1>
        <h2>Mes réservations</h2>

        <?php if (count($reservations) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID Réservation</th>
                        <th>Film</th>
                        <th>Date et heure</th>
                        <th>Nombre de places</th>
                        <th>Prix total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['id']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['titre']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['h_debut']) . ' - ' . htmlspecialchars($reservation['h_fin']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['num_places']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['prix_total']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez aucune réservation.</p>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>
