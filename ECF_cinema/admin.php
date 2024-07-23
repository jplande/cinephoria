<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'employe')) {
    header('Location: admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <main>
        <h1>Administration</h1>
        <p>Bienvenue dans la section administration, <?php echo htmlspecialchars($_SESSION['pseudo']); ?>.</p>
        <nav>
            <ul>
                <li><a href="admin.php?page=films">Gérer les films</a></li>
                <li><a href="admin.php?page=seances">Gérer les séances</a></li>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <li><a href="admin.php?page=salles">Gérer les salles</a></li>
                    <li><a href="admin.php?page=employes">Gérer les employés</a></li>
                    <li><a href="admin.php?page=dashboard">Voir le Dashboard</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <div class="admin-content">
            <?php
            if (isset($_GET['page'])) {
                $page = $_GET['page'];
                if ($page == 'films') {
                    include('admin_films.php');
                } elseif ($page == 'seances') {
                    include('admin_seances.php');
                } elseif ($page == 'salles' && $_SESSION['role'] === 'admin') {
                    include('admin_salles.php');
                } elseif ($page == 'employes' && $_SESSION['role'] === 'admin') {
                    include('admin_employes.php');
                } elseif ($page == 'dashboard' && $_SESSION['role'] === 'admin') {
                    include('admin_dashboard.php');
                }
            }
            ?>
        </div>
    </main>
    <?php include 'footer.php'; ?>
</body>
</html>
