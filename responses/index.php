<?php
require_once '../database.php';
$pdo = getDBConnection();

$submissions = [];
$total_submissions = 0;

if ($pdo) {
    $stmt = $pdo->query("SELECT * FROM submissions ORDER BY timestamp DESC");
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_submissions = count($submissions);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Responses Dashboard - Ashwini Reloaded</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <style>
        .dashboard-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        .summary-cards {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }
        .card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 25px;
            flex: 1;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .card h3 {
            font-size: 1.1rem;
            color: var(--text-secondary);
            margin-bottom: 10px;
            font-family: 'Inter', sans-serif;
        }
        .card .value {
            font-size: 3rem;
            font-weight: 700;
            color: var(--text-primary);
            font-family: 'Playfair Display', serif;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            overflow-x: auto;
            padding: 1px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }
        th, td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        th {
            background: rgba(255, 255, 255, 0.05);
            font-weight: 600;
            color: var(--text-secondary);
            white-space: nowrap;
        }
        tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        td {
            color: var(--text-primary);
        }
        .message-cell {
            max-width: 250px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        @media (max-width: 768px) {
            .summary-cards { flex-direction: column; }
            
            /* Responsive Table Cards for Mobile */
            table, thead, tbody, th, td, tr {
                display: block;
            }
            thead tr {
                display: none;
            }
            tr {
                margin-bottom: 20px;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                padding: 10px;
            }
            td {
                border-bottom: 1px solid rgba(255, 255, 255, 0.05);
                padding: 10px 15px;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            td:last-child {
                border-bottom: none;
            }
            td::before {
                content: attr(data-label);
                font-size: 0.8rem;
                text-transform: uppercase;
                color: var(--text-secondary);
                font-weight: 600;
            }
            .message-cell {
                white-space: normal;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Logo -->
        <div class="logo-container" style="margin-bottom: 40px;">
            <img src="../assets/images/logo_Black.png" alt="Logo" class="main-logo" style="height: 60px;">
        </div>

        <h1 class="serif-heading" style="text-align: left; margin-bottom: 30px;">Submissions Dashboard</h1>
        
        <div class="summary-cards">
            <div class="card">
                <h3>Total Submissions</h3>
                <div class="value"><?= $total_submissions ?></div>
            </div>
            <!-- Additional metrics could go here -->
            <div class="card">
                <h3>Latest Submission</h3>
                <div class="value" style="font-size: 1.5rem; line-height: 2rem; margin-top: 15px;">
                    <?= $total_submissions > 0 ? htmlspecialchars(explode(" ", $submissions[0]['timestamp'])[0]) : 'None yet' ?>
                </div>
            </div>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h2 class="serif-heading" style="font-size: 2rem; margin: 0;">All Responses</h2>
            <a href="export.php" class="btn" style="background: var(--btn-primary-bg); color: var(--btn-primary-text); padding: 10px 20px; border-radius: 30px; font-weight: 600; text-decoration: none; font-family: 'Inter', sans-serif;">Download CSV</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Full Name</th>
                        <th>Instagram ID</th>
                        <th>Location</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total_submissions > 0): ?>
                        <?php foreach ($submissions as $sub): ?>
                            <tr>
                                <td data-label="Date & Time" style="white-space: nowrap;"><?= htmlspecialchars($sub['timestamp']) ?></td>
                                <td data-label="Full Name" style="font-weight: 600;"><?= htmlspecialchars($sub['full_name']) ?></td>
                                <td data-label="Instagram ID">
                                    <?php if(!empty($sub['insta_id'])): ?>
                                        <a href="https://instagram.com/<?= htmlspecialchars(ltrim($sub['insta_id'], '@')) ?>" target="_blank" style="color: var(--btn-primary-bg); text-decoration: none;">
                                            <?= htmlspecialchars($sub['insta_id']) ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td data-label="Location"><?= htmlspecialchars($sub['location']) ?></td>
                                <td data-label="Email"><a href="mailto:<?= htmlspecialchars($sub['email']) ?>" style="color: var(--text-primary);"><?= htmlspecialchars($sub['email']) ?></a></td>
                                <td data-label="Phone"><?= htmlspecialchars($sub['contact_number']) ?></td>
                                <td data-label="Message" class="message-cell" title="<?= htmlspecialchars($sub['message']) ?>">
                                    <?= htmlspecialchars($sub['message']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">No submissions yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
