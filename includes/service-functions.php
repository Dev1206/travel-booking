<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/db.php';

/**
 * Get all services by category
 * 
 * @param string $category The category name (Hotels, Flights, Tours)
 * @param array $filters Optional filters (location, min_price, max_price)
 * @param string $sort Sort order (price_asc, price_desc, rating_desc)
 * @return array Array of services
 */
function getServicesByCategory($category, $filters = [], $sort = 'price_asc') {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Base query
        $sql = "SELECT s.*, c.name as category_name, 
                       COALESCE(AVG(r.rating), 0) as average_rating,
                       COUNT(r.id) as review_count
                FROM services s
                JOIN categories c ON s.category_id = c.id
                LEFT JOIN reviews r ON s.id = r.service_id
                WHERE c.name = ? AND s.is_available = 1";
        $params = [$category];

        // Add filters
        if (!empty($filters['location'])) {
            $sql .= " AND s.location LIKE ?";
            $params[] = "%" . $filters['location'] . "%";
        }
        if (isset($filters['min_price'])) {
            $sql .= " AND s.price >= ?";
            $params[] = $filters['min_price'];
        }
        if (isset($filters['max_price'])) {
            $sql .= " AND s.price <= ?";
            $params[] = $filters['max_price'];
        }

        // Group by to handle the aggregates
        $sql .= " GROUP BY s.id";

        // Add sorting
        switch ($sort) {
            case 'price_desc':
                $sql .= " ORDER BY s.price DESC";
                break;
            case 'rating_desc':
                $sql .= " ORDER BY average_rating DESC, review_count DESC";
                break;
            case 'price_asc':
            default:
                $sql .= " ORDER BY s.price ASC";
        }

        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching services: " . $e->getMessage());
        return [];
    }
}

/**
 * Add a new service
 */
function addService($data) {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        $sql = "INSERT INTO services (category_id, name, description, price, location, image_url, is_available) 
                VALUES (?, ?, ?, ?, ?, ?, 1)";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $data['category_id'],
            $data['name'],
            $data['description'],
            $data['price'],
            $data['location'],
            $data['image_url'] ?? null
        ]);

        return $conn->lastInsertId();
    } catch (PDOException $e) {
        error_log("Error adding service: " . $e->getMessage());
        return false;
    }
}

// Add some sample services if none exist
function addSampleServices() {
    try {
        $db = Database::getInstance();
        $conn = $db->getConnection();

        // Check if we have any services
        $stmt = $conn->query("SELECT COUNT(*) FROM services");
        if ($stmt->fetchColumn() > 0) {
            return; // Services already exist
        }

        // Get category IDs
        $categories = [];
        $stmt = $conn->query("SELECT id, name FROM categories");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $categories[$row['name']] = $row['id'];
        }

        // Sample hotels
        $hotels = [
            [
                'name' => 'Luxury Beach Resort',
                'description' => 'Experience ultimate luxury with ocean views and private beach access.',
                'price' => 299.99,
                'location' => 'Maldives',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'City Center Hotel',
                'description' => 'Modern comfort in the heart of the city.',
                'price' => 149.99,
                'location' => 'New York',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Mountain Lodge',
                'description' => 'Cozy retreat with stunning mountain views.',
                'price' => 199.99,
                'location' => 'Swiss Alps',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ]
        ];

        // Sample flights
        $flights = [
            [
                'name' => 'New York to London',
                'description' => 'Direct flight with premium service.',
                'price' => 599.99,
                'location' => 'New York',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Tokyo to Paris',
                'description' => 'Business class experience.',
                'price' => 799.99,
                'location' => 'Tokyo',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ]
        ];

        // Sample tours
        $tours = [
            [
                'name' => 'European Adventure',
                'description' => '10-day tour through Europe\'s most beautiful cities.',
                'price' => 1999.99,
                'location' => 'Europe',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ],
            [
                'name' => 'Safari Experience',
                'description' => '5-day safari adventure in Africa.',
                'price' => 1499.99,
                'location' => 'Kenya',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80'
            ]
        ];

        // Insert sample services
        foreach ($hotels as $hotel) {
            addService(array_merge($hotel, ['category_id' => $categories['Hotels']]));
        }
        foreach ($flights as $flight) {
            addService(array_merge($flight, ['category_id' => $categories['Flights']]));
        }
        foreach ($tours as $tour) {
            addService(array_merge($tour, ['category_id' => $categories['Tours']]));
        }

        return true;
    } catch (PDOException $e) {
        error_log("Error adding sample services: " . $e->getMessage());
        return false;
    }
} 