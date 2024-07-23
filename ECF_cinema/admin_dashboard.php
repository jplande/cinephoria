<?php
include 'db_connection.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté et s'il a le rôle d'admin ou d'employé
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'employe')) {
    header("Location: login.php");
    exit();
}

// Récupérer les données de réservation pour les 7 derniers jours
$query = "
    SELECT film.titre, COUNT(reservation.id) AS nombre_reservations
    FROM reservation
    JOIN seance ON reservation.seance_id = seance.id
    JOIN film ON seance.film_id = film.id
    WHERE seance.h_debut >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY film.titre
    ORDER BY nombre_reservations DESC
";
$result = $conn->query($query);

if (!$result) {
    die("Erreur dans la requête SQL : " . $conn->error);
}

$films = [];
$reservations = [];

while ($row = $result->fetch_assoc()) {
    $films[] = $row['titre'];
    $reservations[] = $row['nombre_reservations'];
}

// Requête pour récupérer les détails des réservations des 7 derniers jours
$detailsQuery = "
    SELECT seance.h_debut, film.titre, COUNT(reservation.id) AS nombre_reservations, seance.num_salle
    FROM reservation
    JOIN seance ON reservation.seance_id = seance.id
    JOIN film ON seance.film_id = film.id
    WHERE seance.h_debut >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY seance.h_debut, film.titre, seance.num_salle
    ORDER BY seance.h_debut DESC
";
$detailsResult = $conn->query($detailsQuery);

if (!$detailsResult) {
    die("Erreur dans la requête SQL pour les détails : " . $conn->error);
}

$details = [];
while ($row = $detailsResult->fetch_assoc()) {
    $details[] = $row;
}

// Convertir les données en JSON pour les utiliser dans le graphique
$films_json = json_encode($films);
$reservations_json = json_encode($reservations);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard des Réservations</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .chart-container {
            width: 60%;
        }
        .table-container {
            width: 80%;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <main class="dashboard-container">
        <h1>Dashboard des Réservations</h1>
        <div class="chart-container">
            <canvas id="reservationsChart"></canvas>
        </div>
        <div class="table-container">
            <h2>Détails des Réservations des 7 Derniers Jours</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Film</th>
                        <th>Nombre de Réservations</th>
                        <th>Numéro de Salle</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($details as $detail) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($detail['h_debut']); ?></td>
                            <td><?php echo htmlspecialchars($detail['titre']); ?></td>
                            <td><?php echo htmlspecialchars($detail['nombre_reservations']); ?></td>
                            <td><?php echo htmlspecialchars($detail['num_salle']); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </main>
    <?php include 'footer.php'; ?>

    <script>
        const ctx = document.getElementById('reservationsChart').getContext('2d');
        const films = <?php echo $films_json; ?>;
        const reservations = <?php echo $reservations_json; ?>;
        
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: films,
                datasets: [{
                    label: 'Nombre de réservations',
                    data: reservations,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
