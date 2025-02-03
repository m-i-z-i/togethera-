<?php
session_start();
include 'dbconnect.php'; // Include database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Initialize $trusted_contacts as an empty array
$trusted_contacts = [];

// Handle deletion of a contact
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_contact_id'])) {
    $delete_contact_id = intval($_POST['delete_contact_id']);
    $sql_delete = "DELETE FROM trusted_contacts WHERE contact_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_delete);
    if ($stmt) {
        $stmt->bind_param("ii", $delete_contact_id, $user_id);
        $stmt->execute();
    }
}

// Fetch all trusted contacts for the logged-in user
$sql = "SELECT contact_id, name, phone_number, relationship FROM trusted_contacts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result) {
        $trusted_contacts = $result->fetch_all(MYSQLI_ASSOC); // Fetch all contacts as an array
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trusted Contacts - TogetherA+</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        .trusted-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .trusted-heading {
            font-size: 24px;
            margin-bottom: 10px;
            text-align: center;
        }

        .trusted-description {
            text-align: center;
            color: grey;
            margin-bottom: 20px;
        }

        .trusted-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .trusted-form-group {
            flex: 1 1 calc(50% - 20px);
        }

        .trusted-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .trusted-add-btn {
            background-color: #007bff;
            color: white;
            padding: 8px 8px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .trusted-add-btn:hover {
            background-color: #0056b3;
        }

        .trusted-contacts-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .trusted-contact-item {
            background-color: #f4f4f4;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
            flex: 1 1 calc(50% - 20px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .trusted-contact-info {
            text-align: left;
        }

        .trusted-delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .trusted-delete-btn:hover {
            background-color: #a71d2a;
        }

        .trusted-no-contacts {
            text-align: center;
            color: grey;
        }
    </style>
</head>
<body>
    <?php include('header_user.php'); ?> <!-- Include header -->
    <main>
        <div class="trusted-container">
            <h1 class="trusted-heading">Trusted Contacts</h1>
            <p class="trusted-description">Add trusted family members or friends who can assist you in your tasks.</p>

            <!-- Add Trusted Contact Form -->
            <form id="trusted-form" action="add_trusted_contact.php" method="POST" class="trusted-form">
                <div class="trusted-form-group">
                    <label for="trusted-name">Name</label>
                    <input type="text" id="trusted-name" name="name" class="trusted-input" placeholder="Enter name" required>
                </div>
                <div class="trusted-form-group">
                    <label for="trusted-phone">Phone Number</label>
                    <input type="tel" id="trusted-phone" name="phone_number" class="trusted-input" placeholder="Enter phone number" required>
                </div>
                <div class="trusted-form-group">
                    <label for="relationship">Relationship</label>
                    <input type="text" id="relationship" name="relationship" class="trusted-input" placeholder="Enter relationship">
                </div>
                <button type="submit" class="trusted-add-btn">Add Contact</button>
            </form>

            <!-- Trusted Contacts List -->
            <h2 class="trusted-heading">Your Trusted Contacts</h2>
            <div class="trusted-contacts-list">
                <?php if (count($trusted_contacts) > 0): ?>
                    <?php foreach ($trusted_contacts as $contact): ?>
                        <div class="trusted-contact-item">
                            <div class="trusted-contact-info">
                                <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                <small>(<?php echo htmlspecialchars($contact['relationship']); ?>)</small>
                                <small>Phone: <?php echo htmlspecialchars($contact['phone_number']); ?></small>
                            </div>
                            <form method="POST" style="margin: 0;">
                                <input type="hidden" name="delete_contact_id" value="<?php echo $contact['contact_id']; ?>">
                                <button type="submit" class="trusted-delete-btn">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="trusted-no-contacts">You haven't added any trusted contacts yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 TogetherA+. All rights reserved.</p>
    </footer>
</body>
</html>
