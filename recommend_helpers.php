<?php
session_start();
include 'dbconnect.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Capture user inputs
$skills = isset($_GET['skills']) ? strtolower(trim($_GET['skills'])) : ''; // Convert to lowercase for case-insensitive matching
$location = isset($_GET['location']) ? strtolower(trim($_GET['location'])) : '';
$rating = isset($_GET['rating']) ? floatval($_GET['rating']) : 0;
$availability = isset($_GET['availability']) ? true : false;

// Build query dynamically
$sql = "
    SELECT 
        h.helper_id, 
        h.name, 
        h.phone_number,
        h.skills, 
        h.rating, 
        h.address, 
        COUNT(hr.hiring_id) AS tasks_completed,
        CASE 
            WHEN h.status = 'active' AND 
                 (SELECT COUNT(*) 
                  FROM hiring_records hr2 
                  WHERE hr2.helper_id = h.helper_id 
                  AND hr2.status = 'in_progress') = 0 
            THEN 'Available'
            ELSE 'Unavailable'
        END AS availability
    FROM 
        helpers h
    LEFT JOIN 
        hiring_records hr 
    ON 
        h.helper_id = hr.helper_id AND hr.status = 'completed'
    WHERE 
        h.status = 'active'
";

// Add flexible skills filter using LIKE and SOUNDEX
if (!empty($skills)) {
    $skillsArray = explode(',', $skills);
    $skillsFilter = [];
    foreach ($skillsArray as $skill) {
        $skill = trim($skill); // Trim spaces around each skill
        $skillsFilter[] = "(LOWER(h.skills) LIKE '%$skill%' OR SOUNDEX(h.skills) = SOUNDEX('$skill'))"; // Substring and phonetic match
    }
    $sql .= " AND (" . implode(' OR ', $skillsFilter) . ")"; // Use OR for multiple skills
}

// Add location filter
if (!empty($location)) {
    $sql .= " AND LOWER(h.address) LIKE '%$location%'"; // Use LOWER for case-insensitivity
}

// Add rating filter
if ($rating > 0) {
    $sql .= " AND h.rating >= $rating";
}

// Add availability filter
if ($availability) {
    $sql .= " AND (SELECT COUNT(*) 
                  FROM hiring_records hr2 
                  WHERE hr2.helper_id = h.helper_id 
                  AND hr2.status = 'in_progress') = 0";
}

$sql .= "
    GROUP BY h.helper_id, h.name, h.phone_number, h.skills, h.rating, h.address
    ORDER BY h.rating DESC, tasks_completed DESC;
";

// Execute query
$result = $conn->query($sql);

if (!$result) {
    die("Query Error: " . $conn->error);
}

$helpers = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find Helpers - TogetherA+</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }
        .feature-button-container {
            text-align: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .feature-button-container a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
        }
        .feature-button-container a:hover {
            background-color: #0056b3;
        }
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }
        .filter-form label {
            flex: 1 1 calc(25% - 10px);
        }
        .filter-form input, .filter-form button {
            width: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .filter-form button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        .filter-form button:hover {
            background-color: #0056b3;
        }
        .helper-list {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .helper-card {
            flex: 1 1 calc(25% - 20px);
            background-color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .helper-card h3 {
            margin-bottom: 10px;
        }
        .availability {
            color: green;
            font-weight: bold;
        }
        .not-available {
            color: red;
            font-weight: bold;
        }
        .hire-button {
            margin-top: auto;
            padding: 10px;
            background-color: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }
        .hire-button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <?php include 'header_user.php'; ?>
    <main>
        <!-- Explore More Features Button -->
        <div class="feature-button-container">
            <a href="helper_features.php">Explore More Helper Features</a>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="filter-form">
            <label>Skills:
                <input type="text" name="skills" placeholder="e.g., Cooking">
            </label>
            <label>Location:
                <input type="text" name="location" placeholder="City or Address">
            </label>
            <label>Minimum Rating:
                <input type="number" name="rating" step="0.1" min="0" max="5">
            </label>
            <label>Available Only:
                <input type="checkbox" name="availability">
            </label>
            <button type="submit">Find Helpers</button>
        </form>

        <!-- Helper Cards -->
        <div class="helper-list">
            <?php if (count($helpers) > 0): ?>
                <?php foreach ($helpers as $helper): ?>
                    <div class="helper-card">
                        <h3><?php echo htmlspecialchars($helper['name']); ?></h3>
                        <p>Skills: <?php echo htmlspecialchars($helper['skills']); ?></p>
                        <p>Phone: <?php echo htmlspecialchars($helper['phone_number']); ?></p>
                        <p>Rating: <?php echo number_format($helper['rating'], 2); ?></p>
                        <p>Location: <?php echo htmlspecialchars($helper['address']); ?></p>
                        <p>Tasks Completed: <?php echo $helper['tasks_completed']; ?></p>
                        <p class="<?php echo $helper['availability'] === 'Available' ? 'availability' : 'not-available'; ?>">
                            <?php echo $helper['availability']; ?>
                        </p>
                        <a href="hire_helper.php?helper_id=<?php echo $helper['helper_id']; ?>" class="hire-button">Hire</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No helpers match your criteria.</p>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
