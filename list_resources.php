<?php
session_start();
include 'dbconnect.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You need to log in to view resources.");
}

// Check if the user is an admin
$user_id = $_SESSION['user_id'];
$is_admin_query = "SELECT role FROM admins WHERE admin_id = $user_id";
$result = $conn->query($is_admin_query);

$is_admin = $result->num_rows > 0;

// Handle resource deletion if the user is an admin
if ($is_admin && isset($_POST['delete_resource_id'])) {
    $resource_id = intval($_POST['delete_resource_id']);

    // Retrieve the file path to delete the file from the server
    $get_file_path_query = "SELECT link FROM resources WHERE resource_id = $resource_id";
    $file_result = $conn->query($get_file_path_query);

    if ($file_result->num_rows > 0) {
        $file_row = $file_result->fetch_assoc();
        $file_path = $file_row['link'];

        // Delete the file from the server
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Delete the resource from the database
        $delete_query = "DELETE FROM resources WHERE resource_id = $resource_id";
        if ($conn->query($delete_query) === TRUE) {
            $success = "Resource deleted successfully!";
        } else {
            $error = "Error deleting resource: " . $conn->error;
        }
    }
}

// Fetch resources from the database
$sql = "SELECT * FROM resources ORDER BY created_at DESC";
$result = $conn->query($sql);

$category_icons = [
    'audio' => '🎵',
    'video' => '🎥',
    'tutorial' => '📘',
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resources - TogetherA+</title>
    <style>
        /* Resource grid and card styles */
        .resource-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .resource-card {
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .resource-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }

        .resource-button,
        .delete-button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .resource-button {
            background-color: #444;
            color: white;
        }

        .resource-button:hover {
            background-color: grey;
        }

        .delete-button {
            background-color: red;
            color: white;
            margin-top: 10px;
        }

        .delete-button:hover {
            background-color: darkred;
        }
    </style>
</head>

<body>
    <?php include 'header_user.php'; ?>
    <main>
        <h1>Resources</h1>
        <?php if (isset($success)) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p style='color: red;'>$error</p>"; ?>
        <div class="resource-grid">
            <?php if ($result->num_rows > 0) : ?>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <div class="resource-card">
                        <div class="icon"><?php echo $category_icons[$row['category']] ?? '📁'; ?></div>
                        <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <a href="<?php echo htmlspecialchars($row['link']); ?>" class="resource-button" target="_blank">View Resource</a>
                        <?php if ($is_admin) : ?>
                            <form method="POST" style="margin-top: 10px;">
                                <input type="hidden" name="delete_resource_id" value="<?php echo $row['resource_id']; ?>">
                                <button type="submit" class="delete-button">Delete</button>
                            </form>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No resources found.</p>
            <?php endif; ?>
        </div>
    </main>
</body>

</html>
