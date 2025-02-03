<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'dbconnect.php'; // Use the improved DB connection

    // Role (user or helper)
    $role = $_POST['role'] ?? null;

    // Validate input
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Validation checks
    if (!$email) {
        die("<script>alert('Invalid email format.'); window.history.back();</script>");
    }
    if (strlen($password) < 6) {
        die("<script>alert('Password must be at least 6 characters long.'); window.history.back();</script>");
    }

    try {
        if ($role === 'user') {
            $disability = $conn->real_escape_string(trim($_POST['disability']));

            $stmt = $conn->prepare(
                "INSERT INTO users (name, email, phone_number, address, password_hash, user_type, verification_status) 
                 VALUES (?, ?, ?, ?, ?, 'disabled_individual', 'pending')"
            );
            $stmt->bind_param('sssss', $name, $email, $phone, $address, $hashed_password);
        } elseif ($role === 'helper') {
            $skills = $conn->real_escape_string(trim($_POST['skills']));

            // Handle optional document upload
            $upload_dir = 'uploads/';
            $file_path = null;

            if (isset($_FILES['skill_verification_doc']) && $_FILES['skill_verification_doc']['error'] === UPLOAD_ERR_OK) {
                $file_name = uniqid() . '-' . basename($_FILES['skill_verification_doc']['name']);
                $file_path = $upload_dir . $file_name;

                if (!move_uploaded_file($_FILES['skill_verification_doc']['tmp_name'], $file_path)) {
                    die("<script>alert('Failed to upload verification document.'); window.history.back();</script>");
                }
            }

            $stmt = $conn->prepare(
                "INSERT INTO helpers (name, email, phone_number, address, password_hash, skills, verification_status, profile_photo) 
                 VALUES (?, ?, ?, ?, ?, ?, 'pending', ?)"
            );
            $stmt->bind_param('sssssss', $name, $email, $phone, $address, $hashed_password, $skills, $file_path);
        } else {
            die("<script>alert('Invalid registration role.'); window.history.back();</script>");
        }

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Awaiting verification.'); window.location.href='login.php';</script>";
        } else {
            throw new Exception("Error: " . $stmt->error);
        }

        $stmt->close();
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage(), 3, 'logs/registration_errors.log');
        die("<script>alert('An error occurred during registration. Please try again later.'); window.history.back();</script>");
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - TogetherA+</title>
 
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('bg1.jpg') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .form-container {
            flex: 1;
            padding: 40px;
        }

        .image-container {
            flex: 1;
            background: #6A5ACD;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 0;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .form-container h1 {
            margin-bottom: 10px;
            font-size: 26px;
            color: #333;
            text-align: center;
        }

        .form-container p {
            margin-bottom: 20px;
            color: #666;
            text-align: center;
        }

        .tabs {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        .tabs button {
            padding: 10px 20px;
            background: none;
            border: none;
            border-bottom: 2px solid transparent;
            cursor: pointer;
            font-size: 16px;
            color: #666;
        }

        .tabs button.active {
            border-bottom: 2px solid #6A5ACD;
            color: #333;
            font-weight: bold;
        }

        form {
            display: none;
        }

        form.active {
            display: block;
        }

        form input, form button {
            display: block;
            width: 90%;
            margin: 10px auto;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        form button {
            background-color: #6A5ACD;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        form button:hover {
            background-color: #5949bd;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h1>Welcome to TogetherA+</h1>
        <p>Please fill out the form to join our platform.</p>

        <div class="tabs">
            <button class="active" onclick="showForm('user')">Register as User</button>
            <button onclick="showForm('helper')">Register as Helper</button>
        </div>

        <form id="user" class="active" method="POST">
            <input type="hidden" name="role" value="user">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="disability" placeholder="Disability Type" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register as User</button>
        </form>

        <form id="helper" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="role" value="helper">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Enter your email (e.g., name@example.com)" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="text" name="address" placeholder="Address" required>
            <input type="text" name="skills" placeholder="Skills (e.g., tutoring, cleaning)" required>
            <input type="file" name="skill_verification_doc" accept=".pdf,.jpg,.jpeg,.png">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register as Helper</button>
        </form>
    </div>

    <div class="image-container">
        <img src="dis2.jpg" alt="TogetherA+ Registration">
    </div>
</div>

<script>
    function showForm(role) {
        document.querySelectorAll('form').forEach(form => form.classList.remove('active'));
        document.querySelector(`#${role}`).classList.add('active');
        document.querySelectorAll('.tabs button').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.tabs button[onclick="showForm('${role}')"]`).classList.add('active');
    }
</script>
</body>
</html>

