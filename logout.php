<?php
// index.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ActivityHub - IoT Management Portal</title>
    <style>
        :root {
            --primary: #0066cc;
            --text-main: #212529;
            --bg-light: #f8f9fa;
            --border-color: #dee2e6;
        }
        body {
            font-family: system-ui, -apple-system, sans-serif;
            color: var(--text-main);
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }
        .navbar {
            background: #ffffff;
            border-bottom: 1px solid var(--border-color);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .navbar a {
            color: var(--text-main);
            text-decoration: none;
            font-weight: 500;
            margin-left: 20px;
        }
        .navbar a.btn {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
        }
        .hero {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 20px;
            text-align: center;
        }
        .hero h1 {
            font-size: 2.5rem;
            color: #111;
            margin-bottom: 20px;
        }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 25px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }
        .feature-card h3 {
            margin-top: 0;
            color: var(--primary);
        }
        footer {
            text-align: center;
            padding: 40px 20px;
            border-top: 1px solid var(--border-color);
            font-size: 0.9rem;
            color: #6c757d;
            background: #ffffff;
            margin-top: 60px;
        }
    </style>
</head>
<body>

<nav class="navbar" aria-label="Main Navigation">
    <div style="font-weight: bold; font-size: 1.25rem; color: var(--primary);">ActivityHub</div>
    <div>
        <a href="index.php">Home</a>
        <?php if (isset($_SESSION['username'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <span style="margin-left: 20px; font-weight: 600;">👋 <?php echo htmlspecialchars($_SESSION['username']); ?></span>
            <a href="logout.php" style="color: #dc3545;">Log Out</a>
        <?php else: ?>
            <a href="login.php">Log In</a>
            <a href="register.php" class="btn">Register</a>
        <?php endif; ?>
    </div>
</nav>

<main>
    <section class="hero">
        <h1>Smart Environment Management Architecture</h1>
        <p style="font-size: 1.2rem; color: #495057;">
            Welcome to the centralized telemetry platform for our digital eco-system. Designed as a foundational block of our team's joint development infrastructure, this platform bridges embedded sensor networks with secure web distribution.
        </p>
        <div style="margin-top: 30px;">
            <a href="dashboard.php" style="background: var(--primary); color: white; padding: 12px 24px; text-align: center; border-radius: 4px; font-weight: bold; text-decoration: none; display: inline-block;">
                Launch Live Dashboard
            </a>
        </div>
    </section>

    <section class="features-grid" aria-label="Project Features Overview">
        <div class="feature-card">
            <h3>📡 Real-Time Telemetry</h3>
            [cite_start]<p>Monitors ambient conditions using an optimized micro-service data stream layout, processing active telemetry inputs smoothly[cite: 18, 148].</p>
        </div>
        <div class="feature-card">
            <h3>🗄️ Relational Persistence</h3>
            [cite_start]<p>Leverages a unified SQL data structure layer, guaranteeing that environment entries remain isolated, queryable, and highly organized[cite: 159, 178].</p>
        </div>
        <div class="feature-card">
            <h3>🌱 Sustainable Design</h3>
            [cite_start]<p>Engineered without heavy structural dependencies or visual overhead, reducing power draw per transmission cycle to adhere to eco-responsible best practices[cite: 165, 181].</p>
        </div>
    </section>
</main>

<footer>
    <p>&copy; [cite_start]2026 ActivityHub - Joint Project Group Team Web Platform[cite: 104, 140, 200]. All rights reserved.</p>
</footer>

</body>
</html>