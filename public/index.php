<!-- coded by William Rodzen-Staff -->

<?php
$pageTitle = "Advanced Schedule Builder | Home";
$pageCSS = [
    '../assets/css/global.css',
    '../assets/css/index.css'
];
$pageJS = [
    '../assets/js/index.js'
];

include '../includes/header.php';
?>
<div class="main-content">
    <section class="hero-section">
        <div class="container">
            <h1 class="hero-title">Welcome to Your Future</h1>
            <p class="hero-subtitle">
                Build and manage your schedule effortlessly with our intuitive Advanced Schedule Builder.
            </p>
            <a href="register.php" class="get-started">Get Started</a>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="text-center">Our Features</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <i class="bi bi-lightbulb feature-icon"></i>
                    <h5>Intuitive Design</h5>
                    <p>Easily create and customize your schedules with our user-friendly interface.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-graph-up feature-icon"></i>
                    <h5>Track Progress</h5>
                    <p>Monitor your academic progress and stay on top of your goals.</p>
                </div>
                <div class="feature-card">
                    <i class="bi bi-people feature-icon"></i>
                    <h5>Collaborate</h5>
                    <p>Connect with peers and manage group schedules seamlessly.</p>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>
