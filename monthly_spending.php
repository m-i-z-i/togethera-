<?php
session_start();
include 'dbconnect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch monthly spending data
$sql = "
    SELECT 
        MONTH(hr.created_at) AS month,
        YEAR(hr.created_at) AS year,
        SUM(hr.total_hours * hr.hourly_rate) AS monthly_spent,
        SUM(hr.total_hours * hr.hourly_rate * 0.10) AS monthly_platform_fee,
        SUM((hr.total_hours * hr.hourly_rate) - (hr.total_hours * hr.hourly_rate * 0.10)) AS monthly_to_helpers
    FROM hiring_records hr
    WHERE hr.user_id = ?
    GROUP BY YEAR(hr.created_at), MONTH(hr.created_at)
    ORDER BY YEAR(hr.created_at), MONTH(hr.created_at);
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Spending Breakdown</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .details-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #333;
            color: white;
        }
    </style>
</head>
<body>
    <?php include 'header_user.php'; ?>
    <div class="details-container">
        <h2>Monthly Spending Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Spent</th>
                    <th>Platform Fee</th>
                    <th>Paid to Helpers</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data as $row): ?>
                    <tr>
                        <td><?php echo "{$row['month']}/{$row['year']}"; ?></td>
                        <td>$<?php echo number_format($row['monthly_spent'], 2); ?></td>
                        <td>$<?php echo number_format($row['monthly_platform_fee'], 2); ?></td>
                        <td>$<?php echo number_format($row['monthly_to_helpers'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
