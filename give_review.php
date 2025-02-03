<?php
session_start();
include 'dbconnect.php'; // Include database connection

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to give a review.'); window.location.href = 'login.php';</script>";
    exit;
}

if (!isset($_GET['hiring_id'])) {
    echo "<script>alert('Invalid request.'); window.location.href = 'progress_page.php';</script>";
    exit;
}

$hiring_id = (int)$_GET['hiring_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = (float)$_POST['rating']; // Accept decimal values
    $review = $conn->real_escape_string($_POST['review']);
    $user_id = $_SESSION['user_id'];

    // Validate rating (allow decimals between 1 and 5)
    if ($rating < 1 || $rating > 5) {
        echo "<script>alert('Invalid rating. Please provide a rating between 1 and 5, including decimal values.');</script>";
        exit;
    }

    // Check if the review already exists
    $check_query = "SELECT * FROM reviews WHERE hiring_id = $hiring_id AND user_id = $user_id";
    $check_result = $conn->query($check_query);

    if ($check_result->num_rows > 0) {
        echo "<script>alert('You have already submitted a review for this task.'); window.location.href = 'progress_page.php';</script>";
        exit;
    }

    // Insert review into the reviews table
    $insert_query = "INSERT INTO reviews (user_id, helper_id, hiring_id, rating, comment, created_at)
                     SELECT hr.user_id, hr.helper_id, hr.hiring_id, $rating, '$review', NOW()
                     FROM hiring_records hr
                     WHERE hr.hiring_id = $hiring_id";

    if ($conn->query($insert_query) === TRUE) {
        // Update the average rating in the helpers table
        $update_rating_query = "
            UPDATE helpers h
            SET h.rating = (
                SELECT AVG(r.rating)
                FROM reviews r
                WHERE r.helper_id = h.helper_id
            )
            WHERE h.helper_id = (
                SELECT hr.helper_id
                FROM hiring_records hr
                WHERE hr.hiring_id = $hiring_id
            )";

        if ($conn->query($update_rating_query) === TRUE) {
            echo "<script>
                alert('Thank you for your review! The helper’s average rating has been updated.');
                window.location.href = 'progress_page.php';
            </script>";
        } else {
            echo "<script>alert('Error updating average rating: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: Unable to submit review. " . $conn->error . "');</script>";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Give Review</title>
    <style>
        .review-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .review-container h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-group button {
            width: 100%;
            padding: 10px;
            background-color: #4caf50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-group button:hover {
            background-color: #43a047;
        }
    </style>
</head>
<body>
    <div class="review-container">
        <h1>Give Your Review</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="rating">Rating (1.0 to 5.0):</label>
                <input type="number" id="rating" name="rating" step="0.1" min="1" max="5" required>
            </div>
            <div class="form-group">
                <label for="review">Review:</label>
                <textarea id="review" name="review" rows="5" placeholder="Write your review here..." required></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Submit Review</button>
            </div>
        </form>
    </div>
</body>
</html>
