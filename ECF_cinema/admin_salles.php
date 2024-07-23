<?php
include 'db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Gestion des salles
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $num_places = $_POST['num_places'];
                $qualite = $_POST['qualite'];

                $stmt = $conn->prepare("INSERT INTO salle (num_places, qualite) VALUES (?, ?)");
                $stmt->bind_param("is", $num_places, $qualite);
                $stmt->execute();
                $stmt->close();
                break;
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM salle WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                break;
        }
    }
}

$salles = $conn->query("SELECT * FROM salle");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les salles</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Gérer les salles</h1>
        <form method="post">
            <input type="hidden" name="action" value="add">
            <label>Nombre de places: <input type="number" name="num_places" required></label>
            <label>Qualité: <input type="text" name="qualite" required></label>
            <button type="submit">Ajouter</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Nombre de places</th>
                    <th>Qualité</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($salle = $salles->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($salle['num_places']); ?></td>
                        <td><?php echo htmlspecialchars($salle['qualite']); ?></td>
                        <td>
                            <form method="post" action="admin_salles.php" style="display:inline;">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $salle['id']; ?>">
                                <button type="submit">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
