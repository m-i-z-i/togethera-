<?php
session_start();
include 'dbconnect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Capture the helper ID from the query string
if (!isset($_GET['helper_id']) || empty($_GET['helper_id'])) {
    die("Helper ID is required.");
}

$helper_id = intval($_GET['helper_id']);
$user_id = $_SESSION['user_id']; // Assuming the logged-in user's ID is stored in the session

// Fetch helper details
$sql = "SELECT name, skills FROM helpers WHERE helper_id = $helper_id AND status = 'active'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Helper not found or unavailable.");
}

$helper = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hourly_rate = isset($_POST['hourly_rate']) ? floatval($_POST['hourly_rate']) : 0;

    if ($hourly_rate <= 0) {
        $error = "Please enter a valid hourly rate.";
    } else {
        // Insert hiring record into the database
        $sql = "
            INSERT INTO hiring_records (
                user_id, helper_id, hourly_rate, status, created_at
            ) VALUES (
                $user_id, $helper_id, $hourly_rate, 'pending', NOW()
            )
        ";

        $insert_result = mysqli_query($conn, $sql);

        if ($insert_result) {
            echo "<script>alert('Hiring completed.'); window.location.href = 'progress_page.php';</script>";
            
            exit;
        } else {
            $error = "Failed to hire helper. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hire Helper - TogetherA+</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: black;
            margin: 0;
            padding: 0;
        }
        .hire-container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .hire-container h2 {
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-group button {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #218838;
        }
        .error {
            color: red;
            font-weight: bold;
        }
        .text_color {
            color: black;
        }
    </style>
</head>
<body>
    <div class="hire-container">
        <h2 class="text_color">Hire Helper</h2>
        <p><strong class="text_color">Name:</strong> <?php echo htmlspecialchars($helper['name']); ?></p>
        <p><strong class="text_color">Skills:</strong> <?php echo htmlspecialchars($helper['skills']); ?></p>
        
        <?php if (isset($error)): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="hourly_rate" class="text_color">Hourly Rate (in $):</label>
                <input type="number" name="hourly_rate" id="hourly_rate" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <button type="submit">Confirm Hire</button>
            </div>
        </form>
    </div>
</body>
</html>
