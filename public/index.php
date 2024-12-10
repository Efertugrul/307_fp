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

<!DOCTYPE html>
<html lang="en">

<body>
    <div class="main-content">
        <!-- Hero Section -->
        <section class="hero-section d-flex align-items-center">
            <div class="container text-center">
                <h1 class="hero-title">Welcome to Your Future</h1>
                <p class="hero-subtitle mt-4">
                    Build and manage your schedule effortlessly with our intuitive Advanced Schedule Builder.
                </p>
                
                <a href="register.php" class="get-started btn-lg mt-3">Get Started</a>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section py-5">
            <div class="container">
                <h2 class="text-center text-white mb-5">Our Features</h2>
                <div class="row text-center g-4">
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <i class="bi bi-lightbulb feature-icon mb-3"></i>
                                <h5 class="card-title">Intuitive Design</h5>
                                <p class="card-text">Easily create and customize your schedules with our user-friendly interface.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <i class="bi bi-graph-up feature-icon mb-3"></i>
                                <h5 class="card-title">Track Progress</h5>
                                <p class="card-text">Monitor your academic progress and stay on top of your goals.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card feature-card h-100">
                            <div class="card-body">
                                <i class="bi bi-people feature-icon mb-3"></i>
                                <h5 class="card-title">Collaborate</h5>
                                <p class="card-text">Connect with peers and manage group schedules seamlessly.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php
    include '../includes/footer.php';
    ?>
</body>
</html>
