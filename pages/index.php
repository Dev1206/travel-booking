<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Home - " . SITE_NAME;
$page_description = "Book your perfect travel experience with " . SITE_NAME . ". Find hotels, flights, and tours worldwide.";
$page_keywords = "travel booking, hotels, flights, tours, vacation packages";

// Include header files
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';
?>

<div class="hero mb-5 bg-primary text-white py-5">
    <div class="container">
        <h1 class="display-4">Welcome to <?php echo SITE_NAME; ?></h1>
        <p class="lead">Your one-stop destination for all travel needs</p>
        <a href="<?php echo SITE_URL; ?>/pages/hotels.php" class="btn btn-light btn-lg">Start Booking</a>
    </div>
</div>

<div class="container">
    <h2 class="text-center mb-4">Featured Services</h2>
    
    <div class="row">
        <!-- Hotels -->
        <div class="col-md-4">
            <div class="card">
                <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="Hotels">
                <div class="card-body">
                    <h5 class="card-title">Hotels</h5>
                    <p class="card-text">Find the perfect hotel for your stay. From luxury resorts to budget-friendly options.</p>
                    <a href="<?php echo SITE_URL; ?>/pages/hotels.php" class="btn btn-primary">Browse Hotels</a>
                </div>
            </div>
        </div>
        
        <!-- Flights -->
        <div class="col-md-4">
            <div class="card">
                <img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="Flights">
                <div class="card-body">
                    <h5 class="card-title">Flights</h5>
                    <p class="card-text">Book your flights to destinations worldwide. Competitive prices and great deals.</p>
                    <a href="<?php echo SITE_URL; ?>/pages/flights.php" class="btn btn-primary">Browse Flights</a>
                </div>
            </div>
        </div>
        
        <!-- Tours -->
        <div class="col-md-4">
            <div class="card">
                <img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=800&q=80" class="card-img-top" alt="Tours">
                <div class="card-body">
                    <h5 class="card-title">Tours</h5>
                    <p class="card-text">Explore amazing destinations with our guided tours. Create unforgettable memories.</p>
                    <a href="<?php echo SITE_URL; ?>/pages/tours.php" class="btn btn-primary">Browse Tours</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 