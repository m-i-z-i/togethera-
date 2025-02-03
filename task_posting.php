<?php
session_start();
include 'dbconnect.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Error: User not logged in.");
}

// Retrieve user ID from session
$user_id = $_SESSION['user_id'];

// Check if the user is active and verified
$user_query = "SELECT status, verification_status FROM users WHERE user_id = $user_id";
$user_result = $conn->query($user_query);

if ($user_result->num_rows === 0) {
    die("Error: User not found.");
}

$user = $user_result->fetch_assoc();

if ($user['status'] !== 'active' || $user['verification_status'] !== 'verified') {
    die("Error: You must be an active and verified user to post a task.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs and sanitize them
    $title = $conn->real_escape_string($_POST['task-title']);
    $description = $conn->real_escape_string($_POST['description']);
    $hourly_rate = isset($_POST['hourly-rate']) ? (float) $_POST['hourly-rate'] : null;
    $task_type = $conn->real_escape_string($_POST['task-type']);
    $location = $conn->real_escape_string($_POST['location']);
    $urgency = $conn->real_escape_string($_POST['urgency']);

    // Validate required fields
    if (empty($title) || empty($description) || empty($task_type) || empty($urgency)) {
        echo "<script>alert('Error: Please fill out all required fields correctly.');</script>";
    } elseif ($hourly_rate > 0) {
        // Insert into database for hourly rate tasks
        $query = "INSERT INTO tasks (title, user_id, description, skill_required, hourly_rate, urgency, created_at) 
                  VALUES ('$title', $user_id, '$description', '$task_type', $hourly_rate, '$urgency', NOW())";

        if ($conn->query($query) === TRUE) {
            $task_id = $conn->insert_id;

            // Redirect to a success page or display a confirmation message
            echo "<script>
                alert('Task posted successfully! Task ID: $task_id');
                window.location.href = 'homepage.html';
            </script>";
        } else {
            // Handle database errors
            echo "<script>alert('Error: Unable to post task. Please try again. " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Invalid hourly rate value.');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post a Task - TogetherA+</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        header {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header .logo img {
            height: 50px;
        }

        header nav ul {
            list-style: none;
            display: flex;
        }

        header nav ul li {
            margin: 0 10px;
        }

        header nav ul li a {
            text-decoration: none;
            color: #fff;
        }

        .task-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .task-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .form-group button:hover {
            background-color: #0056b3;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            margin-top: 20px;
        }
    </style>
</head>

<body>

<?php include"header_user.php"?>

    <!-- Task Posting Form -->
    <div class="task-container">
        <h1>Post a Task</h1>

        <form method="POST" action="">
            <div class="form-group">
                <label for="task-title">Task Title</label>
                <input type="text" id="task-title" name="task-title" placeholder="e.g., Sign Language Assistance" required />
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" placeholder="Describe the task in detail" required></textarea>
            </div>
            <div class="form-group">
                <label for="hourly-rate">Hourly Rate ($)</label>
                <input type="number" id="hourly-rate" name="hourly-rate" placeholder="Enter hourly rate" required />
            </div>
            <div class="form-group">
                <label for="task-type">Task Type</label>
                <select id="task-type" name="task-type" required>
                    <option value="" disabled selected>Select Task Type</option>
                    <option value="sign-language">Sign Language Assistance</option>
                    <option value="reading-help">Reading and Note-taking</option>
                    <option value="outdoor-guidance">Outdoor Guidance</option>
                </select>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" placeholder="e.g., New York City (optional)" />
            </div>
            <div class="form-group">
                <label for="urgency">Urgency</label>
                <select id="urgency" name="urgency" required>
                    <option value="" disabled selected>Select Urgency</option>
                    <option value="low">Low</option>
                    <option value="medium">Medium</option>
                    <option value="high">High</option>
                </select>
            </div>
            <div class="form-group">
                <button type="submit">Post Task</button>
            </div>
        </form>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 TogetherA+. All rights reserved.</p>
    </footer>

</body>

</html>