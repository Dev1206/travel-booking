<?php
// Start output buffering
ob_start();

// Include configuration first
require_once __DIR__ . '/../config/config.php';

// Set page metadata
$page_title = "Make a Booking - " . SITE_NAME;
$page_description = "Book your preferred hotel, flight or tour with complete details.";
$page_keywords = "make booking, reservation, hotel booking, flight booking, tour booking";

// Include new header templates
require_once __DIR__ . '/../templates/headers/base-header.php';
require_once __DIR__ . '/../templates/headers/main-nav.php';

// Include necessary functions
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/service-functions.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit;
}

$error = '';
$message = '';
$service = null;

// Get service ID from URL
$serviceId = isset($_GET['service_id']) ? intval($_GET['service_id']) : 0;

if (!$serviceId) {
    header("Location: ../pages/index.php");
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Get service details
    $stmt = $conn->prepare("SELECT s.*, c.name as category_name 
                           FROM services s 
                           JOIN categories c ON s.category_id = c.id 
                           WHERE s.id = ?");
    $stmt->execute([$serviceId]);
    $service = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$service) {
        header("Location: ../pages/index.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Error fetching service details: " . $e->getMessage());
    $error = "Error fetching service details. Please try again.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Debug log the POST data and session
        error_log("=== Booking Attempt Start ===");
        error_log("POST data: " . print_r($_POST, true));
        error_log("Session data: " . print_r($_SESSION, true));
        error_log("Service ID: $serviceId");
        
        $checkIn = trim($_POST['check_in']);
        $checkOut = trim($_POST['check_out']);
        $guests = intval($_POST['guests']);
        
        error_log("Processed input - Check-in: $checkIn, Check-out: $checkOut, Guests: $guests");
        
        // Validate inputs
        if (empty($checkIn) || empty($checkOut) || $guests < 1) {
            $error = "Please fill in all required fields.";
            error_log("Validation failed - Empty fields or invalid guest count");
        } else {
            // Check if dates are valid
            $checkInDate = new DateTime($checkIn);
            $checkOutDate = new DateTime($checkOut);
            $today = new DateTime();
            
            error_log("Date validation - Check-in: {$checkInDate->format('Y-m-d')}, Check-out: {$checkOutDate->format('Y-m-d')}, Today: {$today->format('Y-m-d')}");
            
            if ($checkInDate < $today) {
                $error = "Check-in date cannot be in the past.";
                error_log("Validation failed - Check-in date in past");
            } elseif ($checkOutDate <= $checkInDate) {
                $error = "Check-out date must be after check-in date.";
                error_log("Validation failed - Invalid date range");
            } else {
                // Check service availability
                error_log("Checking service availability for service $serviceId");
                if (!isServiceAvailable($serviceId, $checkIn, $checkOut)) {
                    $error = "Service is not available for the selected dates.";
                    error_log("Service $serviceId not available for dates $checkIn to $checkOut");
                } else {
                    // Calculate total price
                    error_log("Calculating total price");
                    $totalPrice = calculateTotalPrice($serviceId, $checkIn, $checkOut, $guests);
                    
                    if ($totalPrice === false) {
                        error_log("Error calculating total price for service $serviceId");
                        $error = "Error calculating total price.";
                    } else {
                        error_log("Total price calculated: $totalPrice");
                        // Create booking
                        error_log("Attempting to create booking - User: {$_SESSION['user_id']}, Service: $serviceId");
                        
                        // Verify all required data
                        error_log("Booking data verification: " . json_encode([
                            'user_id' => $_SESSION['user_id'],
                            'service_id' => $serviceId,
                            'check_in' => $checkIn,
                            'check_out' => $checkOut,
                            'guests' => $guests,
                            'total_price' => $totalPrice,
                            'status' => 'pending'
                        ]));
                        
                        $bookingId = createBooking(
                            $_SESSION['user_id'],
                            $serviceId,
                            $checkIn,
                            $checkOut,
                            $guests,
                            $totalPrice
                        );
                        
                        if ($bookingId) {
                            error_log("Booking created successfully with ID: $bookingId");
                            // Send confirmation email
                            if (sendBookingConfirmationEmail($bookingId)) {
                                error_log("Confirmation email sent successfully");
                            } else {
                                error_log("Failed to send confirmation email");
                            }
                            
                            // Redirect to confirmation page
                            header("Location: booking-confirm.php?id=" . $bookingId);
                            exit;
                        } else {
                            error_log("Failed to create booking for user {$_SESSION['user_id']}, service $serviceId");
                            $error = "Error creating booking. Please try again.";
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        error_log("Booking error: " . $e->getMessage() . "\nStack trace: " . $e->getTraceAsString());
        $error = "An unexpected error occurred. Please try again.";
    }
    error_log("=== Booking Attempt End ===");
}
?>

<div class="container my-4">
    <div class="row">
        <div class="col-md-8">
            <h2>Book <?php echo htmlspecialchars($service['name']); ?></h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <?php if ($service['image_url']): ?>
                            <div class="col-md-4">
                                <img src="<?php echo strpos($service['image_url'], 'http') === 0 ? 
                                    htmlspecialchars($service['image_url']) : 
                                    '../' . htmlspecialchars($service['image_url']); ?>" 
                                     alt="<?php echo htmlspecialchars($service['name']); ?>" 
                                     class="img-fluid rounded">
                            </div>
                        <?php endif; ?>
                        <div class="col-md-8">
                            <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                            <p class="card-text">
                                <strong>Location:</strong> <?php echo htmlspecialchars($service['location']); ?><br>
                                <strong>Category:</strong> <?php echo htmlspecialchars($service['category_name']); ?><br>
                                <strong>Price:</strong> $<?php echo number_format($service['price'], 2); ?> per night/person
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="" class="needs-validation" novalidate>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="check_in" class="form-label">Check-in Date *</label>
                        <input type="date" class="form-control" id="check_in" name="check_in" required 
                               min="<?php echo date('Y-m-d'); ?>" 
                               value="<?php echo isset($_POST['check_in']) ? htmlspecialchars($_POST['check_in']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="check_out" class="form-label">Check-out Date *</label>
                        <input type="date" class="form-control" id="check_out" name="check_out" required 
                               min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" 
                               value="<?php echo isset($_POST['check_out']) ? htmlspecialchars($_POST['check_out']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="guests" class="form-label">Number of Guests *</label>
                    <input type="number" class="form-control" id="guests" name="guests" required min="1" max="10" 
                           value="<?php echo isset($_POST['guests']) ? htmlspecialchars($_POST['guests']) : '1'; ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Proceed to Payment</button>
                <a href="javascript:history.back()" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Price Calculator</h5>
                </div>
                <div class="card-body">
                    <div id="priceCalculator">
                        <p class="mb-2">Base price per night: $<?php echo number_format($service['price'], 2); ?></p>
                        <p class="mb-2">Number of nights: <span id="nightsCount">0</span></p>
                        <p class="mb-2">Number of guests: <span id="guestsCount">1</span></p>
                        <hr>
                        <h5>Total Price: $<span id="totalPrice">0.00</span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculatePrice() {
    console.group('Price Calculation');
    const checkInInput = document.getElementById('check_in');
    const checkOutInput = document.getElementById('check_out');
    const guestsInput = document.getElementById('guests');
    
    console.log('Input values:', {
        checkIn: checkInInput.value,
        checkOut: checkOutInput.value,
        guests: guestsInput.value
    });
    
    const checkIn = new Date(checkInInput.value);
    const checkOut = new Date(checkOutInput.value);
    const guests = parseInt(guestsInput.value) || 1;
    const basePrice = <?php echo $service['price']; ?>;
    
    console.log('Parsed values:', {
        checkIn: checkIn.toISOString(),
        checkOut: checkOut.toISOString(),
        guests: guests,
        basePrice: basePrice
    });
    
    document.getElementById('guestsCount').textContent = guests;
    
    if (checkInInput.value && checkOutInput.value && checkOut > checkIn) {
        const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        const total = basePrice * nights * guests;
        
        console.log('Calculation results:', {
            nights: nights,
            total: total,
            calculation: `${basePrice} * ${nights} * ${guests} = ${total}`
        });
        
        document.getElementById('nightsCount').textContent = nights;
        document.getElementById('totalPrice').textContent = total.toFixed(2);
    } else {
        console.log('Invalid date range or missing dates');
        document.getElementById('nightsCount').textContent = '0';
        document.getElementById('totalPrice').textContent = '0.00';
    }
    console.groupEnd();
}

// Add form submission handler
document.querySelector('form').addEventListener('submit', function(e) {
    console.group('Form Submission');
    const formData = new FormData(this);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    console.log('Form data being submitted:', data);
    
    // Validate form data
    const checkIn = new Date(data.check_in);
    const checkOut = new Date(data.check_out);
    const guests = parseInt(data.guests);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    console.log('Validation checks:', {
        'All fields filled': Boolean(data.check_in && data.check_out && data.guests),
        'Valid dates': checkOut > checkIn,
        'Future dates': checkIn >= today,
        'Guest count valid': guests > 0 && guests <= 10
    });
    
    // Calculate total price before submission
    const basePrice = <?php echo $service['price']; ?>;
    const nights = Math.ceil((checkOut - checkIn) / (1000 * 60 * 60 * 24));
    const total = basePrice * nights * guests;
    
    console.log('Final calculation before submission:', {
        basePrice: basePrice,
        nights: nights,
        guests: guests,
        total: total
    });
    console.groupEnd();
});

// Calculate price on page load
document.addEventListener('DOMContentLoaded', function() {
    calculatePrice();
});

// Event listeners for form inputs
document.getElementById('check_in').addEventListener('change', calculatePrice);
document.getElementById('check_out').addEventListener('change', calculatePrice);
document.getElementById('guests').addEventListener('change', calculatePrice);
</script>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>

<?php
// Flush the output buffer
ob_end_flush();
?> 