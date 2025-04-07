# Travel Booking Website

A comprehensive web application for booking travel services including hotels, flights, and tours.

## Overview

This travel booking platform provides a complete solution for users to search, book, and manage travel services. The system includes user registration and authentication, service browsing, booking management, theme customization, and an administrative dashboard for platform management.

## Features

### User Features

- **Account Management**
  - User registration and authentication
  - Profile management
  - Password reset functionality
  - User booking history

- **Service Browsing**
  - Browse hotels, flights, and tours
  - Filter services by location, price, and other criteria
  - View detailed service information
  - Check service availability for specific dates

- **Booking Process**
  - Select service dates and number of guests
  - Review booking details before confirmation
  - Secure payment processing
  - Receive booking confirmations

- **User Experience**
  - Customizable themes (light/dark/nature)
  - Responsive design for all device types
  - Help center with guides and support
  - User preferences saved across sessions

### Admin Features

- **Dashboard**
  - Overview of system metrics
  - Recent bookings and user registrations
  - Revenue tracking and analytics

- **Service Management**
  - Add new hotels, flights, and tours
  - Edit existing service details
  - Manage service availability
  - Upload service images

- **Booking Management**
  - View all bookings
  - Update booking status (pending, confirmed, completed, cancelled)
  - Access detailed booking information
  - Filter bookings by status, date, or service

- **User Management**
  - View all registered users
  - Manage user accounts
  - Handle user permissions

## Technical Architecture

### Directory Structure

```
travel-booking/
├── admin/                # Admin interface files
│   ├── add-service.php   # Add new services
│   ├── admin-dashboard.php # Admin main dashboard
│   ├── booking-details.php # View booking details
│   ├── edit-service.php  # Edit existing services
│   ├── manage-services.php # Service management
│   ├── manage-users.php  # User management
│   └── view-bookings.php # View all bookings
│
├── assets/               # Static resources
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   ├── audio/            # Audio files
│   └── videos/           # Video files
│
├── config/               # Configuration files
│   └── config.php        # Main configuration
│
├── help/                 # Help center pages
│   ├── change-theme.php  # Theme customization guide
│   ├── contact-support.php # Contact support
│   ├── help-home.php     # Help center homepage
│   ├── how-to-book.php   # Booking guide
│   └── update-profile.php # Profile update guide
│
├── includes/             # Core functionality
│   ├── booking-functions.php # Booking related functions
│   ├── db.php            # Database connection
│   ├── footer.php        # Footer template
│   ├── functions.php     # General functions
│   ├── service-functions.php # Service related functions
│   └── theme-manager.php # Theme management
│
├── pages/                # Main site pages
│   ├── flights.php       # Flights listing
│   ├── hotels.php        # Hotels listing
│   ├── index.php         # Homepage
│   └── tours.php         # Tours listing
│
├── templates/            # Template files
│   ├── headers/          # Header templates
│   │   ├── admin-nav.php # Admin navigation
│   │   ├── base-header.php # Base header template
│   │   └── main-nav.php  # Main navigation
│   └── footer.php        # Footer template
│
└── user/                 # User interface files
    ├── booking-confirm.php # Booking confirmation
    ├── booking-history.php # User booking history
    ├── change-theme.php  # Theme customization
    ├── login.php         # User login
    ├── logout.php        # User logout
    ├── make-booking.php  # Create booking
    ├── profile.php       # User profile
    └── register.php      # User registration
```

## Technology Stack

- **Frontend**
  - HTML5, CSS3, JavaScript
  - Bootstrap 5 for responsive design
  - Font Awesome for icons
  - Custom theme system

- **Backend**
  - PHP 8.x
  - PDO for database interactions
  - Object-oriented programming

- **Database**
  - MySQL/MariaDB

- **Security**
  - Password hashing
  - Input validation and sanitization
  - Session management
  - CSRF protection

## Setup Instructions

### Prerequisites

- PHP 8.0 or higher
- MySQL/MariaDB
- Web server (Apache/Nginx)

### Installation

1. **Clone the repository**
   ```
   git clone https://github.com/Dev1206/travel-booking
   cd travel-booking
   ```

2. **Web server configuration**
   - Configure your web server to serve from the project directory
   - Ensure PHP has appropriate permissions

3. **Start the application**
   - Navigate to `http://localhost:8000/pages/index.php` in your browser
   - Default admin credentials: admin@travel.com / admin123
   - Default user credentials: test@email.com / test123

## User Guides

- **Making a Booking**: Step-by-step instructions in `help/how-to-book.php`
- **Theme Customization**: Guide available in `help/change-theme.php`
- **Profile Management**: Instructions in `help/update-profile.php`

## Admin Manual

### Adding a New Service

1. Login with admin credentials
2. Navigate to Admin Dashboard → Manage Services
3. Click "Add New Service"
4. Fill in service details and upload images
5. Set availability and pricing
6. Save the new service

### Managing Bookings

1. Access Admin Dashboard → View Bookings
2. Filter bookings as needed
3. Click on any booking to view details
4. Update booking status as required

