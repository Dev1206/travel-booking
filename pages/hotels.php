<?php
// Include configuration first
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/service-functions.php';

// Set page metadata
$page_title = "Hotels - " . SITE_NAME;
$page_description = "Find and book the perfect hotel for your stay. Browse our selection of luxury resorts and budget-friendly options.";
$page_keywords = "hotels, accommodation, resorts, rooms, booking, luxury hotels, budget hotels";

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

    // Get hotels with filters
    $filters = [
        'location' => $location,
        'min_price' => $min_price,
        'max_price' => $max_price
    ];
    
    $hotels = getServicesByCategory('Hotels', $filters, $sort);
} catch (PDOException $e) {
    error_log("Error in hotels page: " . $e->getMessage());
    $error = "An error occurred while fetching hotels.";
}
?>

<div class="container mt-4">
    <h1 class="mb-4">Hotels</h1>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="" class="row g-3">
                <div class="col-md-4">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" id="location" name="location" 
                           value="<?php echo htmlspecialchars($location); ?>" placeholder="Enter city or country">
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

    <!-- Hotels Listing -->
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($hotels as $hotel): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?php echo !empty($hotel['image_url']) ? htmlspecialchars($hotel['image_url']) : 
                            'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'; ?>" 
                             class="card-img-top" alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($hotel['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($hotel['description']); ?></p>
                            <p class="card-text">
                                <strong>Location:</strong> <?php echo htmlspecialchars($hotel['location']); ?><br>
                                <strong>Price:</strong> $<?php echo number_format($hotel['price'], 2); ?> per night
                            </p>
                            <?php if ($hotel['review_count'] > 0): ?>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Rating: <?php echo number_format($hotel['average_rating'], 1); ?>/5
                                        (<?php echo $hotel['review_count']; ?> reviews)
                                    </small>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="card-footer bg-white border-top-0">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="<?php echo SITE_URL; ?>/user/make-booking.php?service_id=<?php echo $hotel['id']; ?>" 
                                   class="btn btn-primary w-100">Book Now</a>
                            <?php else: ?>
                                <a href="<?php echo SITE_URL; ?>/user/login.php" class="btn btn-primary w-100">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (empty($hotels)): ?>
                <div class="col-12">
                    <div class="alert alert-info">No hotels found matching your criteria.</div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php
require_once __DIR__ . '/../templates/footer.php';
?> 