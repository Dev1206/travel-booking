<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';

// Only define functions if they don't already exist
if (!function_exists('getUserBookings')) {
    /**
     * Get all bookings for a specific user
     * 
     * @param int $userId The ID of the user
     * @return array Array of bookings with service details
     */
    function getUserBookings($userId) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $sql = "SELECT b.*, s.name as service_name, s.location, s.image_url, c.name as category_name
                    FROM bookings b
                    JOIN services s ON b.service_id = s.id
                    JOIN categories c ON s.category_id = c.id
                    WHERE b.user_id = ?
                    ORDER BY b.check_in_date DESC";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([$userId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching user bookings: " . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('getBookingById')) {
    /**
     * Get a specific booking by ID
     * 
     * @param int $bookingId The ID of the booking
     * @param int $userId Optional user ID to verify booking ownership
     * @return array|false The booking details or false if not found
     */
    function getBookingById($bookingId, $userId = null) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $sql = "SELECT b.*, s.name as service_name, s.location, s.image_url, c.name as category_name
                    FROM bookings b
                    JOIN services s ON b.service_id = s.id
                    JOIN categories c ON s.category_id = c.id
                    WHERE b.id = ?" . ($userId ? " AND b.user_id = ?" : "");
            
            $stmt = $conn->prepare($sql);
            $params = [$bookingId];
            if ($userId) {
                $params[] = $userId;
            }
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching booking details: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('updateBookingStatus')) {
    /**
     * Update booking status
     * 
     * @param int $bookingId The ID of the booking
     * @param string $status The new status ('confirmed', 'cancelled', 'pending')
     * @param int $userId Optional user ID to verify booking ownership
     * @return bool True if successful, false otherwise
     */
    function updateBookingStatus($bookingId, $status, $userId = null) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $sql = "UPDATE bookings SET status = ? WHERE id = ?" . 
                   ($userId ? " AND user_id = ?" : "");
            
            $stmt = $conn->prepare($sql);
            $params = [$status, $bookingId];
            if ($userId) {
                $params[] = $userId;
            }
            
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error updating booking status: " . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('getAllBookings')) {
    /**
     * Get all bookings for admin dashboard
     * 
     * @param int $userId Optional filter by specific user
     * @return array Array of all bookings with service and user details
     */
    function getAllBookings($userId = null) {
        try {
            $db = Database::getInstance();
            $conn = $db->getConnection();
            
            $sql = "SELECT b.*, s.name as service_name, s.location, s.image_url, 
                           c.name as category_name, u.first_name, u.last_name, u.email as user_email
                    FROM bookings b
                    JOIN services s ON b.service_id = s.id
                    JOIN categories c ON s.category_id = c.id
                    JOIN users u ON b.user_id = u.id";
            
            if ($userId) {
                $sql .= " WHERE b.user_id = ?";
            }
            
            $sql .= " ORDER BY b.created_at DESC";
            
            $stmt = $conn->prepare($sql);
            
            if ($userId) {
                $stmt->execute([$userId]);
            } else {
                $stmt->execute();
            }
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all bookings: " . $e->getMessage());
            return [];
        }
    }
} 