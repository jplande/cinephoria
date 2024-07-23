<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinéma</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <nav>
            <div class="nav-wrapper">
                <div class="logo-container">
                    <img src="images/logo.png" alt="Logo" class="logo">
                    <span class="site-name">Mon Cinéma</span>
                </div>
                <div class="menu-icon" onclick="toggleMenu()">☰</div>
                <ul class="nav-center" id="nav-center">
                    <li><a href="index.php">Accueil</a></li>
                    <li><a href="reservation.php">Réservation</a></li>
                    <li><a href="film.php">Films</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'employe')): ?>
                        <li><a href="admin.php">Administration</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="mon_espace.php">Mon espace</a></li>
                    <?php endif; ?>
                </ul>
                <div class="nav-right">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-name"><?php echo htmlspecialchars($_SESSION['pseudo']); ?></span>
                        <a href="logout.php">Se déconnecter</a>
                    <?php else: ?>
                        <a href="login.php">Se connecter <img src="images/login_icon.png" alt="Login Icon" class="login-icon"></a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>
</body>
</html>
