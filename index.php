<?php
require_once 'classes/Auth.php';
require_once 'classes/TicketManager.php';
require_once 'includes/functions.php';

session_start();

// Initialize classes
$auth = new Auth();
$ticketManager = new TicketManager();

// Simple routing
$page = $_GET['page'] ?? 'login';

// Check authentication for protected pages
$protectedPages = ['dashboard', 'ticket-form'];
if (in_array($page, $protectedPages) && !$auth->isAuthenticated()) {
    header('Location: index.php?page=login');
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handleFormSubmission($auth, $ticketManager);
}

// Display the appropriate page
switch ($page) {
    case 'login':
        displayLoginPage($auth);
        break;
    case 'dashboard':
        displayDashboardPage($auth, $ticketManager);
        break;
    case 'ticket-form':
        displayTicketFormPage($auth, $ticketManager);
        break;
    case 'logout':
        $auth->logout();
        header('Location: index.php?page=login');
        exit;
    default:
        header('Location: index.php?page=login');
        exit;
}
?>