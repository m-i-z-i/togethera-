<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TogetherA+</title>
    <style>
        /* General Page Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
            text-align: center;
        }

        header {
            background-color: #1f1f1f;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #333;
        }

        header .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        header .logo img {
            width: 120px; /* Reduced the size */
            height: auto;
            display: block;
        }

        header nav ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
        }

        header nav ul li {
            margin-left: 20px;
        }

        header nav ul li a {
            text-decoration: none;
            color: grey;
            transition: color 0.3s;
        }

        header nav ul li a:hover,
        header nav ul li a.active {
            color: white;
        }

        /* Section Styles */
        .about-section,
        .who-we-are,
        .mission-vision,
        .impact {
            max-width: 1000px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .about-section h1,
        .who-we-are h2,
        .impact h2 {
            font-size: 28px;
            margin-bottom: 10px;
            color: #333;
        }

        .about-section p,
        .who-we-are p,
        .impact p {
            font-size: 16px;
            color: grey;
            line-height: 1.6;
        }

        /* Mission and Vision Section */
        .mission-vision {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
            padding: 40px 20px;
            text-align: left;
        }

        .mission,
        .vision {
            flex: 1;
            padding: 20px;
            background-color: #f4f4f4;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .mission:hover,
        .vision:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .mission h3,
        .vision h3 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .mission p,
        .vision p {
            font-size: 14px;
            color: grey;
        }

        /* Impact Section */
        .impact ul {
            list-style: none;
            padding: 0;
        }

        .impact ul li {
            margin: 10px 0;
            font-size: 16px;
            color: #333;
        }

        .impact ul li strong {
            color: #555;
        }

        /* Footer */
        footer {
            background-color: #1f1f1f;
            text-align: center;
            padding: 20px;
            color: grey;
            font-size: 14px;
        }
    </style>
</head>

<body>

<header>
        <div class="logo">
            <img src="img/logo.png" alt="TogetherA+">
        </div>
        <nav>
            <ul>
                <li><a href="homepage.html">Home</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="task_posting.php">Tasks</a></li>
                <li><a href="progress_page.php">Running Jobs</a></li>
                <li><a href="list_resources.php">Resources</a></li>
                <li><a href="edit_user_profile.php">Profile</a></li>
                <li><a href="trusted_contacts.php">Trusted Contacts</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- About TogetherA+ -->
    <section class="about-section">
        <h1>About TogetherA+</h1>
        <p>
            Empowering individuals with disabilities by fostering connections, resources, and support.
        </p>
    </section>

    <!-- Who We Are -->
    <section class="who-we-are">
        <h2>Who We Are</h2>
        <p>
            TogetherA+ is a platform dedicated to empowering individuals with disabilities. We aim to bridge the gap
            between those in need of assistance and helpers who are willing to offer support. By fostering independence,
            dignity, and inclusivity, we are building a community where everyone can thrive.
        </p>
    </section>

    <!-- Mission and Vision Section -->
    <section class="mission-vision">
        <div class="mission">
            <h3>Our Mission</h3>
            <p>
                To create an inclusive platform where individuals with disabilities can find the support and
                resources they need to live independently and with dignity.
            </p>
        </div>
        <div class="vision">
            <h3>Our Vision</h3>
            <p>
                A world where disabilities are not barriers but opportunities to connect, grow, and empower.
            </p>
        </div>
    </section>

    <!-- Impact Section -->
    <section class="impact">
        <h2>Our Impact</h2>
        <p>TogetherA+ actively supports the UN Sustainable Development Goals (SDGs):</p>
        <ul>
            <li><strong>SDG 4:</strong> Ensuring inclusive and equitable education for all.</li>
            <li><strong>SDG 8:</strong> Promoting decent work opportunities for all.</li>
            <li><strong>SDG 10:</strong> Reducing inequalities and fostering social inclusion.</li>
        </ul>
    </section>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 TogetherA+. All rights reserved.</p>
    </footer>

</body>

</html>
