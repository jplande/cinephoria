<?php
include 'db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Gestion des employés
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nom = $_POST['nom'];
                $prenom = $_POST['prenom'];
                $pseudo = $_POST['pseudo'];
                $mail = $_POST['mail'];
                $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

                $stmt = $conn->prepare("INSERT INTO user (nom, prenom, pseudo, mail, mdp, role) VALUES (?, ?, ?, ?, ?, 'employe')");
                $stmt->bind_param("sssss", $nom, $prenom, $pseudo, $mail, $mdp);
                $stmt->execute();
                $stmt->close();
                break;
            case 'edit':
                $id = $_POST['id'];
                $nom = $_POST['nom'];
                $prenom = $_POST['prenom'];
                $pseudo = $_POST['pseudo'];
                $mail = $_POST['mail'];
                $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

                $stmt = $conn->prepare("UPDATE user SET nom = ?, prenom = ?, pseudo = ?, mail = ?, mdp = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $nom, $prenom, $pseudo, $mail, $mdp, $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'delete':
                $id = $_POST['id'];
                $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();
                break;
            case 'reset_password':
                $id = $_POST['id'];
                $mdp = password_hash($_POST['mdp'], PASSWORD_BCRYPT);

                $stmt = $conn->prepare("UPDATE user SET mdp = ? WHERE id = ?");
                $stmt->bind_param("si", $mdp, $id);
                $stmt->execute();
                $stmt->close();
                break;
        }
    }
}

$employes = $conn->query("SELECT * FROM user WHERE role = 'employe'");
?>

<h1>Gérer les employés</h1>
<form method="post">
    <input type="hidden" name="action" value="add">
    <label>Nom: <input type="text" name="nom" required></label>
    <label>Prénom: <input type="text" name="prenom" required></label>
    <label>Pseudo: <input type="text" name="pseudo" required></label>
    <label>Mail: <input type="email" name="mail" required></label>
    <label>Mot de passe: <input type="password" name="mdp" required></label>
    <button type="submit">Ajouter</button>
</form>
<table>
    <thead>
        <tr>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Pseudo</th>
            <th>Mail</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($employe = $employes->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($employe['nom']); ?></td>
                <td><?php echo htmlspecialchars($employe['prenom']); ?></td>
                <td><?php echo htmlspecialchars($employe['pseudo']); ?></td>
                <td><?php echo htmlspecialchars($employe['mail']); ?></td>
                <td>
                    <form method="post" action="admin_employes.php" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?php echo $employe['id']; ?>">
                        <button type="submit">Supprimer</button>
                    </form>
                    <form method="post" action="admin_employes.php" style="display:inline;">
                        <input type="hidden" name="action" value="reset_password">
                        <input type="hidden" name="id" value="<?php echo $employe['id']; ?>">
                        <input type="password" name="mdp" placeholder="Nouveau mot de passe" required>
                        <button type="submit">Réinitialiser le mot de passe</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
