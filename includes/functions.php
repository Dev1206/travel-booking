<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';

function createBooking($userId, $serviceId, $checkIn, $checkOut, $guests, $totalPrice, $status = 'pending') {
    try {
        error_log("Creating booking with parameters: " . json_encode([
            'userId' => $userId,
            'serviceId' => $serviceId,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'guests' => $guests,
            'totalPrice' => $totalPrice,
            'status' => $status
        ]));

        // Input validation
        if (!$userId || !$serviceId || !$checkIn || !$checkOut || !$guests || !$totalPrice) {
            error_log("Invalid booking parameters - userId: $userId, serviceId: $serviceId, guests: $guests, totalPrice: $totalPrice");
            return false;
        }

        $db = Database::getInstance();
        $conn = $db->getConnection();
        error_log("Database connection established");
        
        // Begin transaction
        $conn->beginTransaction();
        error_log("Transaction started");
        
        // Verify service exists and is available
        $stmt = $conn->prepare("SELECT id, is_available FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service) {
            error_log("Service $serviceId not found");
            $conn->rollBack();
            return false;
        }
        
        if (!$service['is_available']) {
            error_log("Service $serviceId is not available");
            $conn->rollBack();
            return false;
        }
        
        error_log("Service verification passed");
        
        // Check for overlapping bookings
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings 
                               WHERE service_id = ? 
                               AND status != 'cancelled'
                               AND ((check_in_date BETWEEN ? AND ?) 
                               OR (check_out_date BETWEEN ? AND ?))");
        
        $stmt->execute([$serviceId, $checkIn, $checkOut, $checkIn, $checkOut]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            error_log("Overlapping booking found for service $serviceId");
            $conn->rollBack();
            return false;
        }
        
        error_log("No overlapping bookings found");
        
        try {
            // Insert the booking
            $stmt = $conn->prepare("INSERT INTO bookings (user_id, service_id, check_in_date, check_out_date, 
                                   number_of_guests, total_price, status, created_at) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
            
            error_log("Executing booking insert with values: " . json_encode([
                $userId, $serviceId, $checkIn, $checkOut, $guests, $totalPrice, $status
            ]));
            
            $success = $stmt->execute([$userId, $serviceId, $checkIn, $checkOut, $guests, $totalPrice, $status]);
            
            if (!$success) {
                error_log("Failed to execute booking insert statement. Error info: " . json_encode($stmt->errorInfo()));
                $conn->rollBack();
                return false;
            }
            
            $bookingId = $conn->lastInsertId();
            error_log("Booking inserted successfully with ID: $bookingId");
            
            // Commit transaction
            $conn->commit();
            error_log("Transaction committed");
            return $bookingId;
            
        } catch (PDOException $e) {
            error_log("Database error during booking insert: " . $e->getMessage());
            $conn->rollBack();
            return false;
        }
        
    } catch (PDOException $e) {
        error_log("Database error in createBooking: " . $e->getMessage());
        if (isset($conn)) {
            $conn->rollBack();
        }
        return false;
    } catch (Exception $e) {
        error_log("General error in createBooking: " . $e->getMessage());
        if (isset($conn)) {
            $conn->rollBack();
        }
        return false;
    }
}

function getBookingById($bookingId) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT b.*, s.name as service_name, s.price, s.location, s.image_url,
                               c.name as category_name, u.email as user_email,
                               u.first_name, u.last_name, u.phone
                               FROM bookings b
                               JOIN services s ON b.service_id = s.id
                               JOIN categories c ON s.category_id = c.id
                               JOIN users u ON b.user_id = u.id
                               WHERE b.id = ?");
        
        $stmt->execute([$bookingId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching booking: " . $e->getMessage());
        return false;
    }
}

function getUserBookings($userId) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT b.*, s.name as service_name, s.location, s.image_url,
                               c.name as category_name
                               FROM bookings b
                               JOIN services s ON b.service_id = s.id
                               JOIN categories c ON s.category_id = c.id
                               WHERE b.user_id = ?
                               ORDER BY b.created_at DESC");
        
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching user bookings: " . $e->getMessage());
        return false;
    }
}

function getAllBookings() {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->query("SELECT b.*, s.name as service_name, s.location,
                             c.name as category_name, u.email as user_email,
                             u.first_name, u.last_name
                             FROM bookings b
                             JOIN services s ON b.service_id = s.id
                             JOIN categories c ON s.category_id = c.id
                             JOIN users u ON b.user_id = u.id
                             ORDER BY b.created_at DESC");
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching all bookings: " . $e->getMessage());
        return false;
    }
}

function updateBookingStatus($bookingId, $status) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $bookingId]);
    } catch (PDOException $e) {
        error_log("Error updating booking status: " . $e->getMessage());
        return false;
    }
}

function calculateTotalPrice($serviceId, $checkIn, $checkOut, $guests) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $stmt = $conn->prepare("SELECT price FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service) {
            return false;
        }
        
        $checkInDate = new DateTime($checkIn);
        $checkOutDate = new DateTime($checkOut);
        $nights = $checkInDate->diff($checkOutDate)->days;
        
        // Base calculation: price per night * number of nights * number of guests
        $totalPrice = $service['price'] * $nights * $guests;
        
        return $totalPrice;
    } catch (PDOException $e) {
        error_log("Error calculating total price: " . $e->getMessage());
        return false;
    }
}

function sendBookingConfirmationEmail($bookingId) {
    $booking = getBookingById($bookingId);
    if (!$booking) {
        return false;
    }
    
    $to = $booking['user_email'];
    $subject = "Booking Confirmation - " . SITE_NAME;
    
    $message = "Dear " . $booking['first_name'] . " " . $booking['last_name'] . ",\n\n";
    $message .= "Your booking has been confirmed!\n\n";
    $message .= "Booking Details:\n";
    $message .= "Service: " . $booking['service_name'] . "\n";
    $message .= "Location: " . $booking['location'] . "\n";
    $message .= "Check-in: " . $booking['check_in_date'] . "\n";
    $message .= "Check-out: " . $booking['check_out_date'] . "\n";
    $message .= "Guests: " . $booking['number_of_guests'] . "\n";
    $message .= "Total Price: $" . number_format($booking['total_price'], 2) . "\n\n";
    $message .= "Thank you for choosing " . SITE_NAME . "!\n";
    
    $headers = "From: " . SITE_EMAIL . "\r\n";
    $headers .= "Reply-To: " . SITE_EMAIL . "\r\n";
    
    return mail($to, $subject, $message, $headers);
}

function isServiceAvailable($serviceId, $checkIn, $checkOut) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        // Check if service exists and is available
        $stmt = $conn->prepare("SELECT is_available FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);
        $service = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$service || !$service['is_available']) {
            return false;
        }
        
        // Check for overlapping bookings
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings 
                               WHERE service_id = ? 
                               AND status != 'cancelled'
                               AND ((check_in_date BETWEEN ? AND ?) 
                               OR (check_out_date BETWEEN ? AND ?))");
        
        $stmt->execute([$serviceId, $checkIn, $checkOut, $checkIn, $checkOut]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'] == 0;
    } catch (PDOException $e) {
        error_log("Error checking service availability: " . $e->getMessage());
        return false;
    }
} 