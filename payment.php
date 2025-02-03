<?php
session_start();
include 'dbconnect.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$sql_user = "SELECT name FROM users WHERE user_id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_details = $stmt_user->get_result()->fetch_assoc();

// Fetch hiring records for the user
$sql = "
    SELECT 
        hr.hiring_id, 
        hr.total_hours, 
        hr.hourly_rate, 
        (hr.total_hours * hr.hourly_rate) AS total_fee, 
        (hr.total_hours * hr.hourly_rate * 0.10) AS platform_fee, 
        (hr.total_hours * hr.hourly_rate) - (hr.total_hours * hr.hourly_rate * 0.10) AS payable_amount,
        p.amount AS paid,
        p.status AS payment_status,
        hr.status AS hire_status
    FROM hiring_records hr
    LEFT JOIN payments p ON hr.hiring_id = p.hiring_id
    WHERE hr.user_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$hiring_records = $result->fetch_all(MYSQLI_ASSOC);

// Fetch aggregated data for the summary
$summary_sql = "
    SELECT 
        SUM(hr.total_hours * hr.hourly_rate) AS total_spent,
        SUM(hr.total_hours * hr.hourly_rate * 0.10) AS total_platform_fee,
        SUM((hr.total_hours * hr.hourly_rate) - (hr.total_hours * hr.hourly_rate * 0.10)) AS total_to_helpers
    FROM hiring_records hr
    WHERE hr.user_id = ?
";
$stmt_summary = $conn->prepare($summary_sql);
$stmt_summary->bind_param("i", $user_id);
$stmt_summary->execute();
$summary_data = $stmt_summary->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - TogetherA+</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .payment-summary {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f9f9f9;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item h3 {
            font-size: 16px;
            margin-bottom: 5px;
            color: #333;
        }
        .summary-item p {
            font-size: 14px;
            color: #666;
        }
        .dummy-pay-container {
            text-align: center;
        }
        .dummy-pay-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .dummy-pay-btn:hover {
            background-color: #0056b3;
        }
        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: left;
            background-color: #f9f9f9;
        }
        .payment-table th, .payment-table td {
            padding: 12px;
            border: 1px solid #ddd;
        }
        .payment-table th {
            background-color: #333;
            color: white;
        }
        .no-records {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .payment-insights-container {
            text-align: center;
            margin-top: 20px;
        }
        .view-btn {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .view-btn:hover {
            background-color: #3e8e41;
        }
    </style>
</head>

<body>
    <?php include 'header_user.php'; ?>
    <main>
        <div class="payment-wrapper">
            <!-- Payment Summary -->
            <div class="payment-summary">
                <div class="summary-item">
                    <h3>User ID</h3>
                    <p><?php echo htmlspecialchars($user_id); ?></p>
                </div>
                <div class="summary-item">
                    <h3>User Name</h3>
                    <p><?php echo htmlspecialchars($user_details['name']); ?></p>
                </div>
                <div class="summary-item">
                    <h3>Total Spent</h3>
                    <p>$<?php echo number_format($summary_data['total_spent'], 2); ?></p>
                </div>
                <div class="summary-item">
                    <h3>Total Platform Fee</h3>
                    <p>$<?php echo number_format($summary_data['total_platform_fee'], 2); ?></p>
                </div>
                <div class="summary-item">
                    <h3>Total to Helpers</h3>
                    <p>$<?php echo number_format($summary_data['total_to_helpers'], 2); ?></p>
                </div>
                <div class="dummy-pay-container">
                    <form action="dummy_payment.php" method="POST">
                        <button type="submit" class="dummy-pay-btn">Dummy Payment</button>
                    </form>
                </div>
            </div>

            <!-- View Payment Insights -->
            <div class="payment-insights-container">
                <a href="payment_insights.php" class="view-btn">View Payment Insights</a>
            </div>

            <!-- Payment Table -->
            <table class="payment-table">
                <thead>
                    <tr>
                        <th>Hiring ID</th>
                        <th>Hours</th>
                        <th>Hourly Rate</th>
                        <th>Total Fee</th>
                        <th>Platform Fee</th>
                        <th>Payable Amount</th>
                        <th>Paid</th>
                        <th>Payment Status</th>
                        <th>Hire Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($hiring_records) > 0): ?>
                        <?php foreach ($hiring_records as $record): ?>
                            <tr>
                                <td><?php echo $record['hiring_id']; ?></td>
                                <td><?php echo $record['total_hours']; ?></td>
                                <td><?php echo number_format($record['hourly_rate'], 2); ?></td>
                                <td><?php echo number_format($record['total_fee'], 2); ?></td>
                                <td><?php echo number_format($record['platform_fee'], 2); ?></td>
                                <td><?php echo number_format($record['payable_amount'], 2); ?></td>
                                <td><?php echo number_format($record['paid'], 2); ?></td>
                                <td><?php echo ucfirst($record['payment_status']); ?></td>
                                <td><?php echo ucfirst($record['hire_status']); ?></td>
                                <td><a href="view_hiring.php?hiring_id=<?php echo $record['hiring_id']; ?>" class="dummy-pay-btn">View</a></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="no-records">No payment records found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
