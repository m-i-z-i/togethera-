<?php include 'header_user.php'; ?> <!-- Include the header -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Insights</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .insights-container {
            max-width: 1200px;
            margin: 20px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .insight-box {
            flex: 1;
            min-width: 280px;
            background-color: #f9f9f9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .insight-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }
        .insight-box h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #333;
        }
        .insight-box p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        .insight-box a {
            display: inline-block;
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        .insight-box a:hover {
            background-color: #3e8e41;
        }
    </style>
</head>
<body>
    <main>
        <div class="insights-container">
            <div class="insight-box">
                <h3>Monthly Spending Breakdown</h3>
                <p>View your spending habits broken down by month.</p>
                <a href="monthly_spending.php">View Details</a>
            </div>
            <div class="insight-box">
                <h3>Pending Payments</h3>
                <p>See all your pending payments that need attention.</p>
                <a href="pending_payments.php">View Details</a>
            </div>
            <div class="insight-box">
                <h3>Helper-wise Payment Report</h3>
                <p>See how much you've paid to each helper over time.</p>
                <a href="helper_payment_report.php">View Details</a>
            </div>
            <div class="insight-box">
                <h3>Task-Based Payment Breakdown</h3>
                <p>Get a report on how much each task cost you.</p>
                <a href="task_payment_breakdown.php">View Details</a>
            </div>
        </div>
    </main>
</body>
</html>
