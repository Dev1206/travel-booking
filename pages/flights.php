<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/service-functions.php';

// Set page metadata
$page_title = "Flights - " . SITE_NAME;
$page_description = "Book your next flight with us. Find great deals on domestic and international flights.";
$page_keywords = "flights, air travel, airline tickets, plane tickets, cheap flights";

// Include header files
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Get filter parameters
$location = trim($_GET['location'] ?? '');
$min_price = floatval($_GET['min_price'] ?? 0);
$max_price = floatval($_GET['max_price'] ?? 99999);
$sort = $_GET['sort'] ?? 'price_asc';

try {
    // Add sample services if none exist
    addSampleServices();

    // Get flights with filters
    $filters = [
        'location' => $location,
        'min_price' => $min_price,
        'max_price' => $max_price
    ];
    
    $flights = getServicesByCategory('Flights', $filters, $sort);
} catch (PDOException $e) {
    error_log("Error in flights page: " . $e->getMessage());
    $error = "An error occurred while fetching flights.";
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Flights</h1>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="location" class="form-label">Departure City</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter departure city">
                </div>
                <div class="col-md-2">
                    <label for="min_price" class="form-label">Min Price</label>
                    <input type="number" class="form-control" id="min_price" name="min_price" 
                           value="<?php echo $min_price; ?>" min="0">
                </div>
                <div class="col-md-2">
                    <label for="max_price" class="form-label">Max Price</label>
                    <input type="number" class="form-control" id="max_price" name="max_price" 
                           value="<?php echo $max_price; ?>" min="0">
                </div>
                <div class="col-md-2">
                    <label for="sort" class="form-label">Sort By</label>
                    <select class="form-select" id="sort" name="sort">
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating_desc" <?php echo $sort === 'rating_desc' ? 'selected' : ''; ?>>Highest Rated</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Search</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Flights Listing -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($flights as $flight): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo !empty($flight['image_url']) ? htmlspecialchars($flight['image_url']) : 
                            'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($flight['name']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($flight['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($flight['description']); ?></p>
                            <p class="card-text">
                                <strong>From:</strong> <?php echo htmlspecialchars($flight['location']); ?><br>
                                <strong>Price:</strong> $<?php echo number_format($flight['price'], 2); ?>
                            </p>
                            <?php if ($flight['review_count'] > 0): ?>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Rating: <?php echo number_format($flight['average_rating'], 1); ?>/5
                                        (<?php echo $flight['review_count']; ?> reviews)
                                    </small>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="<?php echo SITE_URL; ?>/user/make-booking.php?service_id=<?php echo $flight['id']; ?>" 
                                   class="btn btn-primary w-100">Book Now</a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/user/login.php" class="btn btn-primary w-100">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($flights)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No flights found matching your criteria.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?> 