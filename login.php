 
<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'dbconnect.php'; // Use the provided DB connection

    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Check in users table
    $sql_user = "SELECT * FROM users WHERE email = '$email'";
    $result_user = $conn->query($sql_user);

    if ($result_user->num_rows > 0) {
        $row = $result_user->fetch_assoc();
        if (password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id'] = $row['user_id'];
            $_SESSION['role'] = 'user';
            header('Location: homepage.html');
            exit;
        } else {
            $error = "Invalid password.";
        }
    } else {
        // Check in helpers table
        $sql_helper = "SELECT * FROM helpers WHERE email = '$email'";
        $result_helper = $conn->query($sql_helper);

        if ($result_helper->num_rows > 0) {
            $row = $result_helper->fetch_assoc();
            if (password_verify($password, $row['password_hash'])) {
                $_SESSION['helper_id'] = $row['helper_id'];
                $_SESSION['role'] = 'helper';
                header('Location: homepage2.html');
                exit;
            } else {
                $error = "Invalid password.";
            }
        } else {
            // Check in admins table
            $sql_admin = "SELECT * FROM admins WHERE email = '$email'";
            $result_admin = $conn->query($sql_admin);

            if ($result_admin->num_rows > 0) {
                $row = $result_admin->fetch_assoc();
                if (password_verify($password, $row['password_hash'])) {
                    $_SESSION['admin_id'] = $row['admin_id'];
                    $_SESSION['role'] = 'admin';
                    header('Location: admin/index.php');
                    exit;
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "No account found with that email.";
            }
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TogetherA+</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: url('dis4.avif') no-repeat center center fixed;
            background-size: cover;
        }

        .container {
            display: flex;
            width: 80%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .form-container {
            flex: 1;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .form-container h1 {
            margin-bottom: 20px;
            font-size: 26px;
            color: #333;
            text-align: center;
        }

        .form-container form {
            width: 100%;
            max-width: 400px;
        }

        .form-container input, .form-container button {
            display: block;
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .form-container button {
            background-color: #6A5ACD;
            color: white;
            font-size: 16px;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .form-container button:hover {
            background-color: #5949bd;
        }

        .form-container a {
            display: block;
            margin: 10px 0;
            color: #6A5ACD;
            text-decoration: none;
            font-size: 14px;
            text-align: center;
        }

        .form-container a:hover {
            text-decoration: underline;
        }

        .image-container {
            flex: 1;
            background: #6A5ACD;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .image-container p {
            margin-top: 20px;
            font-size: 16px;
            text-align: center;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="form-container">
        <h1>Login to TogetherA+</h1>
        <form method="POST">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
            <a href="forgot_password.php">Forgot password?</a>
            <a href="register.php">New User? Register here</a>
        </form>
    </div>

    <div class="image-container">
        <img src="dis4.avif" alt="Login to TogetherA+">
    </div>
</div>
</body>
</html>
