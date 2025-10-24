<?php
function handleFormSubmission($auth, $ticketManager) {
    if (isset($_POST['login'])) {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if ($auth->login($email, $password)) {
            header('Location: index.php?page=dashboard');
            exit;
        } else {
            $_SESSION['error'] = 'Invalid email or password';
        }
    }
    
    if (isset($_POST['create_ticket']) || isset($_POST['update_ticket'])) {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $status = $_POST['status'] ?? 'open';
        $priority = $_POST['priority'] ?? 'medium';
        $user = $auth->getUser();
        
        $ticketData = [
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'priority' => $priority,
            'createdBy' => $user['email']
        ];
        
        if (isset($_POST['create_ticket'])) {
            $ticketManager->createTicket($ticketData);
            $_SESSION['message'] = 'Ticket created successfully!';
        } else {
            $ticketId = $_POST['ticket_id'] ?? '';
            $ticketManager->updateTicket($ticketId, $ticketData);
            $_SESSION['message'] = 'Ticket updated successfully!';
        }
        
        header('Location: index.php?page=dashboard');
        exit;
    }
    
    if (isset($_POST['delete_ticket'])) {
        $ticketId = $_POST['ticket_id'] ?? '';
        $ticketManager->deleteTicket($ticketId);
        $_SESSION['message'] = 'Ticket deleted successfully!';
        header('Location: index.php?page=dashboard');
        exit;
    }
}

function displayLoginPage($auth) {
    $error = $_SESSION['error'] ?? '';
    unset($_SESSION['error']);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Ticket Management</title>
        <style>
            /* Red & Black Theme */
            :root {
                --color-open: #dc2626;
                --color-in-progress: #ea580c;
                --color-closed: #404040;
                --max-width: 1440px;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                line-height: 1.6;
                color: #000000;
                background-color: #ffffff;
            }

            .container {
                max-width: var(--max-width);
                margin: 0 auto;
                padding: 0 1rem;
            }

            .hero-wave {
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                padding: 2rem 0;
                position: relative;
                overflow: hidden;
                color: white;
            }

            .ticket-grid {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                padding: 1rem 0;
            }

            .nav-desktop {
                display: flex;
                gap: 1rem;
                background: #000000;
                padding: 1rem;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
            }

            .nav-mobile {
                display: none;
                background: #000000;
                padding: 1rem;
            }

            .ticket-card {
                border: 2px solid;
                border-radius: 8px;
                padding: 1.5rem;
                transition: all 0.3s ease;
                background: white;
            }

            .ticket-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .status-open {
                background-color: #fef2f2;
                color: #dc2626;
                border-color: #dc2626;
            }

            .status-in-progress {
                background-color: #fff7ed;
                color: #ea580c;
                border-color: #ea580c;
            }

            .status-closed {
                background-color: #f5f5f5;
                color: #404040;
                border-color: #404040;
            }

            button:focus-visible,
            input:focus-visible,
            select:focus-visible {
                outline: 2px solid #dc2626;
                outline-offset: 2px;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #000000;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #000000;
                border-radius: 4px;
                font-size: 1rem;
                background: white;
                color: #000000;
            }

            .form-textarea {
                min-height: 100px;
                resize: vertical;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 4px;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.2s;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
            }

            .btn-sm {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .btn-primary {
                background-color: #dc2626;
                color: white;
            }

            .btn-primary:hover {
                background-color: #b91c1c;
            }

            .btn-secondary {
                background-color: #000000;
                color: white;
            }

            .btn-secondary:hover {
                background-color: #404040;
            }

            .login-container {
                max-width: 400px;
                margin: 2rem auto;
                padding: 2rem;
                border: 2px solid #000000;
                border-radius: 8px;
                background: white;
            }

            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                font-weight: 600;
                border: 2px solid;
            }

            .nav-buttons {
                display: flex;
                gap: 0.5rem;
                align-items: center;
                flex-wrap: wrap;
            }

            .nav-user {
                color: white;
                font-weight: 600;
            }

            @media (min-width: 768px) {
                .ticket-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1.5rem;
                }
                
                .nav-desktop {
                    display: flex;
                }
                
                .nav-mobile {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                .ticket-grid {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 2rem;
                }
            }

            @media (max-width: 767px) {
                .container {
                    padding: 0 0.5rem;
                }
                
                .nav-desktop {
                    display: none;
                }
                
                .nav-mobile {
                    display: block;
                }
                
                .nav-buttons {
                    flex-direction: column;
                    width: 100%;
                }
                
                .nav-buttons .btn {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .btn {
                    padding: 0.75rem 1rem;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="hero-wave">
            <div class="container">
                <div class="login-container">
                    <h1 style="text-align: center; margin-bottom: 2rem; color: white">
                        Ticket Management System
                    </h1>
                    
                    <?php if ($error): ?>
                        <div style="color: #fecaca; background-color: #7f1d1d; padding: 0.75rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #fecaca">
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <input type="hidden" name="login" value="1">
                        
                        <div class="form-group">
                            <label for="email" class="form-label" style="color: white">
                                Email Address
                            </label>
                            <input
                                id="email"
                                type="email"
                                class="form-input"
                                name="email"
                                required
                                placeholder="Enter your email"
                            >
                        </div>

                        <div class="form-group">
                            <label for="password" class="form-label" style="color: white">
                                Password
                            </label>
                            <input
                                id="password"
                                type="password"
                                class="form-input"
                                name="password"
                                required
                                placeholder="Enter your password"
                            >
                        </div>

                        <button 
                            type="submit" 
                            class="btn btn-primary"
                            style="width: 100%; margin-bottom: 1rem"
                        >
                            Sign In
                        </button>

                        <div style="border-top: 1px solid white; padding-top: 1rem">
                            <p style="color: white; margin-bottom: 0.5rem">Demo Accounts:</p>
                            <button 
                                type="button"
                                class="btn btn-secondary"
                                style="width: 100%; margin-bottom: 0.5rem"
                                onclick="fillCredentials('admin@example.com', 'admin123')"
                            >
                                Use Admin Account
                            </button>
                            <button 
                                type="button"
                                class="btn btn-secondary"
                                style="width: 100%"
                                onclick="fillCredentials('user@example.com', 'user123')"
                            >
                                Use Regular User Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function fillCredentials(email, password) {
                document.getElementById('email').value = email;
                document.getElementById('password').value = password;
            }
        </script>
    </body>
    </html>
    <?php
}

function displayDashboardPage($auth, $ticketManager) {
    $user = $auth->getUser();
    $message = $_SESSION['message'] ?? '';
    $filter = $_GET['filter'] ?? 'all';
    unset($_SESSION['message']);
    
    $tickets = $ticketManager->getTicketsByStatus($filter);
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard - Ticket Management</title>
        <style>
            /* Red & Black Theme */
            :root {
                --color-open: #dc2626;
                --color-in-progress: #ea580c;
                --color-closed: #404040;
                --max-width: 1440px;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                line-height: 1.6;
                color: #000000;
                background-color: #ffffff;
            }

            .container {
                max-width: var(--max-width);
                margin: 0 auto;
                padding: 0 1rem;
            }

            .hero-wave {
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                padding: 2rem 0;
                position: relative;
                overflow: hidden;
                color: white;
            }

            .ticket-grid {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                padding: 1rem 0;
            }

            .nav-desktop {
                display: flex;
                gap: 1rem;
                background: #000000;
                padding: 1rem;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
            }

            .nav-mobile {
                display: none;
                background: #000000;
                padding: 1rem;
            }

            .ticket-card {
                border: 2px solid;
                border-radius: 8px;
                padding: 1.5rem;
                transition: all 0.3s ease;
                background: white;
            }

            .ticket-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .status-open {
                background-color: #fef2f2;
                color: #dc2626;
                border-color: #dc2626;
            }

            .status-in-progress {
                background-color: #fff7ed;
                color: #ea580c;
                border-color: #ea580c;
            }

            .status-closed {
                background-color: #f5f5f5;
                color: #404040;
                border-color: #404040;
            }

            button:focus-visible,
            input:focus-visible,
            select:focus-visible {
                outline: 2px solid #dc2626;
                outline-offset: 2px;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #000000;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #000000;
                border-radius: 4px;
                font-size: 1rem;
                background: white;
                color: #000000;
            }

            .form-textarea {
                min-height: 100px;
                resize: vertical;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 4px;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.2s;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
            }

            .btn-sm {
                padding: 0.5rem 1rem;
                font-size: 0.875rem;
            }

            .btn-primary {
                background-color: #dc2626;
                color: white;
            }

            .btn-primary:hover {
                background-color: #b91c1c;
            }

            .btn-secondary {
                background-color: #000000;
                color: white;
            }

            .btn-secondary:hover {
                background-color: #404040;
            }

            .login-container {
                max-width: 400px;
                margin: 2rem auto;
                padding: 2rem;
                border: 2px solid #000000;
                border-radius: 8px;
                background: white;
            }

            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                font-weight: 600;
                border: 2px solid;
            }

            .nav-buttons {
                display: flex;
                gap: 0.5rem;
                align-items: center;
                flex-wrap: wrap;
            }

            .nav-user {
                color: white;
                font-weight: 600;
            }

            .filter-form {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            @media (min-width: 768px) {
                .ticket-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 1.5rem;
                }
                
                .nav-desktop {
                    display: flex;
                }
                
                .nav-mobile {
                    display: none !important;
                }
            }

            @media (min-width: 1024px) {
                .ticket-grid {
                    grid-template-columns: repeat(3, 1fr);
                    gap: 2rem;
                }
            }

            @media (max-width: 767px) {
                .container {
                    padding: 0 0.5rem;
                }
                
                .nav-desktop {
                    display: none;
                }
                
                .nav-mobile {
                    display: block;
                }
                
                .nav-buttons {
                    flex-direction: column;
                    width: 100%;
                }
                
                .nav-buttons .btn {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .filter-form {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .filter-form .form-select {
                    width: 100%;
                }
                
                .btn {
                    padding: 0.75rem 1rem;
                    font-size: 0.9rem;
                }
            }
        </style>
    </head>
    <body>
        <!-- Header with Wave -->
        <header class="hero-wave">
            <div class="container">
                <h1 style="color: white; text-align: center; padding: 1rem 0">
                    Ticket Dashboard
                </h1>
            </div>
        </header>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop">
            <span class="nav-user">Welcome, <?= htmlspecialchars($user['name']) ?></span>
            
            <div class="nav-buttons">
                <a href="index.php?page=ticket-form" class="btn btn-primary">Create Ticket</a>
                
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="dashboard">
                    <select name="filter" class="form-select" onchange="this.form.submit()" style="min-width: 120px">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Tickets</option>
                        <option value="open" <?= $filter === 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="in_progress" <?= $filter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="closed" <?= $filter === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </form>
                
                <a href="index.php?page=logout" class="btn btn-secondary">Logout</a>
            </div>
        </nav>

        <!-- Mobile Navigation -->
        <nav class="nav-mobile">
            <div class="nav-buttons">
                <span class="nav-user" style="color: white; text-align: center; display: block; margin-bottom: 0.5rem">Welcome, <?= htmlspecialchars($user['name']) ?></span>
                
                <a href="index.php?page=ticket-form" class="btn btn-primary">Create Ticket</a>
                
                <form method="GET" class="filter-form">
                    <input type="hidden" name="page" value="dashboard">
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All Tickets</option>
                        <option value="open" <?= $filter === 'open' ? 'selected' : '' ?>>Open</option>
                        <option value="in_progress" <?= $filter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="closed" <?= $filter === 'closed' ? 'selected' : '' ?>>Closed</option>
                    </select>
                </form>
                
                <a href="index.php?page=logout" class="btn btn-secondary">Logout</a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="container">
            <div style="padding: 2rem 0">
                <?php if ($message): ?>
                    <div style="background-color: #dcfce7; color: #166534; padding: 1rem; border-radius: 4px; margin-bottom: 1rem; border: 1px solid #22c55e">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <div class="ticket-grid">
                    <?php foreach ($tickets as $ticket): ?>
                        <article class="ticket-card <?= getStatusClass($ticket['status']) ?>" aria-labelledby="ticket-title-<?= $ticket['id'] ?>">
                            <header style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 0.75rem">
                                <h3 id="ticket-title-<?= $ticket['id'] ?>" style="margin: 0; font-size: 1.125rem; font-weight: 600">
                                    <?= htmlspecialchars($ticket['title']) ?>
                                </h3>
                                <span class="status-badge <?= getStatusClass($ticket['status']) ?>">
                                    <?= ucfirst(str_replace('_', ' ', $ticket['status'])) ?>
                                </span>
                            </header>
                            
                            <p style="color: #374151; margin-bottom: 1rem">
                                <?= htmlspecialchars($ticket['description']) ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.875rem; border-top: 1px solid rgba(0,0,0,0.1); padding-top: 0.75rem">
                                <div>
                                    <span style="font-weight: 600">Priority: <?= ucfirst($ticket['priority']) ?></span>
                                    <br>
                                    <span style="color: #666; font-size: 0.8rem">
                                        Created: <?= date('M j, Y', strtotime($ticket['createdAt'])) ?>
                                    </span>
                                </div>
                                <div style="display: flex; gap: 0.5rem">
                                    <a href="index.php?page=ticket-form&id=<?= $ticket['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="POST" style="margin: 0">
                                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                                        <button type="submit" name="delete_ticket" class="btn btn-secondary btn-sm" onclick="return confirm('Are you sure you want to delete this ticket?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($tickets)): ?>
                    <div style="text-align: center; padding: 3rem; color: #666">
                        <p>No tickets found. Create your first ticket to get started!</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>

        <script>
            function confirmDelete() {
                return confirm('Are you sure you want to delete this ticket?');
            }
        </script>
    </body>
    </html>
    <?php
}

function displayTicketFormPage($auth, $ticketManager) {
    $user = $auth->getUser();
    $ticketId = $_GET['id'] ?? '';
    $isEditing = !empty($ticketId);
    
    // Get ticket data if editing
    $ticket = null;
    if ($isEditing) {
        $ticket = $ticketManager->getTicketById($ticketId);
        if (!$ticket) {
            header('Location: index.php?page=dashboard');
            exit;
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $isEditing ? 'Edit Ticket' : 'Create Ticket' ?> - Ticket Management</title>
        <style>
            /* Red & Black Theme */
            :root {
                --color-open: #dc2626;
                --color-in-progress: #ea580c;
                --color-closed: #404040;
                --max-width: 1440px;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                line-height: 1.6;
                color: #000000;
                background-color: #ffffff;
            }

            .container {
                max-width: var(--max-width);
                margin: 0 auto;
                padding: 0 1rem;
            }

            .hero-wave {
                background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
                padding: 2rem 0;
                position: relative;
                overflow: hidden;
                color: white;
            }

            .nav-desktop {
                display: flex;
                gap: 1rem;
                background: #000000;
                padding: 1rem;
                flex-wrap: wrap;
                justify-content: space-between;
                align-items: center;
            }

            .nav-mobile {
                display: none;
                background: #000000;
                padding: 1rem;
            }

            .ticket-card {
                border: 2px solid;
                border-radius: 8px;
                padding: 1.5rem;
                transition: all 0.3s ease;
                background: white;
            }

            .ticket-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .status-open {
                background-color: #fef2f2;
                color: #dc2626;
                border-color: #dc2626;
            }

            .status-in-progress {
                background-color: #fff7ed;
                color: #ea580c;
                border-color: #ea580c;
            }

            .status-closed {
                background-color: #f5f5f5;
                color: #404040;
                border-color: #404040;
            }

            button:focus-visible,
            input:focus-visible,
            select:focus-visible {
                outline: 2px solid #dc2626;
                outline-offset: 2px;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .form-label {
                display: block;
                margin-bottom: 0.5rem;
                font-weight: 600;
                color: #000000;
            }

            .form-input,
            .form-select,
            .form-textarea {
                width: 100%;
                padding: 0.75rem;
                border: 2px solid #000000;
                border-radius: 4px;
                font-size: 1rem;
                background: white;
                color: #000000;
            }

            .form-textarea {
                min-height: 100px;
                resize: vertical;
            }

            .btn {
                padding: 0.75rem 1.5rem;
                border: none;
                border-radius: 4px;
                font-size: 1rem;
                cursor: pointer;
                transition: background-color 0.2s;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                text-align: center;
                white-space: nowrap;
            }

            .btn-primary {
                background-color: #dc2626;
                color: white;
            }

            .btn-primary:hover {
                background-color: #b91c1c;
            }

            .btn-secondary {
                background-color: #000000;
                color: white;
            }

            .btn-secondary:hover {
                background-color: #404040;
            }

            .login-container {
                max-width: 400px;
                margin: 2rem auto;
                padding: 2rem;
                border: 2px solid #000000;
                border-radius: 8px;
                background: white;
            }

            .status-badge {
                padding: 0.25rem 0.75rem;
                border-radius: 9999px;
                font-size: 0.875rem;
                font-weight: 600;
                border: 2px solid;
            }

            .nav-buttons {
                display: flex;
                gap: 0.5rem;
                align-items: center;
                flex-wrap: wrap;
            }

            .nav-user {
                color: white;
                font-weight: 600;
            }

            @media (min-width: 768px) {
                .nav-desktop {
                    display: flex;
                }
                
                .nav-mobile {
                    display: none !important;
                }
            }
               
            @media (max-width: 767px) {
                .nav-desktop {
                    display: none;
                }
                
                .nav-mobile {
                    display: block;
                }
                
                .container {
                    padding: 0 0.5rem;
                }
                
                .btn {
                    padding: 0.75rem 1rem;
                    font-size: 0.9rem;
                }
                
                .nav-buttons {
                    flex-direction: column;
                    width: 100%;
                }
                
                .nav-buttons .btn {
                    width: 100%;
                    margin-bottom: 0.5rem;
                }
                
                .nav-user {
                    text-align: center;
                    display: block;
                    margin-bottom: 0.5rem;
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <!-- Header with Wave -->
        <header class="hero-wave">
            <div class="container">
                <h1 style="color: white; text-align: center; padding: 1rem 0">
                    <?= $isEditing ? 'Edit Ticket' : 'Create New Ticket' ?>
                </h1>
            </div>
        </header>

        <!-- Desktop Navigation -->
        <nav class="nav-desktop">
            <a href="index.php?page=dashboard" class="btn btn-secondary">← Back to Dashboard</a>
            <span class="nav-user">Welcome, <?= htmlspecialchars($user['name']) ?></span>
            <a href="index.php?page=logout" class="btn btn-secondary">Logout</a>
        </nav>

        <!-- Mobile Navigation -->
        <nav class="nav-mobile">
            <div class="nav-buttons">
                <span class="nav-user">Welcome, <?= htmlspecialchars($user['name']) ?></span>
                <a href="index.php?page=dashboard" class="btn btn-secondary">← Back to Dashboard</a>
                <a href="index.php?page=logout" class="btn btn-secondary">Logout</a>
            </div>
        </nav>

        <!-- Form -->
        <main class="container">
            <div style="max-width: 600px; margin: 2rem auto; padding: 0 1rem">
                <form method="POST">
                    <?php if ($isEditing): ?>
                        <input type="hidden" name="ticket_id" value="<?= $ticket['id'] ?>">
                        <input type="hidden" name="update_ticket" value="1">
                    <?php else: ?>
                        <input type="hidden" name="create_ticket" value="1">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="title" class="form-label">
                            Ticket Title *
                        </label>
                        <input
                            id="title"
                            type="text"
                            class="form-input"
                            name="title"
                            value="<?= htmlspecialchars($ticket['title'] ?? '') ?>"
                            required
                            aria-required="true"
                            placeholder="Enter a descriptive title"
                        >
                    </div>

                    <div class="form-group">
                        <label for="description" class="form-label">
                            Description *
                        </label>
                        <textarea
                            id="description"
                            class="form-textarea"
                            name="description"
                            rows="6"
                            required
                            aria-required="true"
                            placeholder="Describe the issue or request in detail..."
                        ><?= htmlspecialchars($ticket['description'] ?? '') ?></textarea>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem">
                        <div class="form-group">
                            <label for="status" class="form-label">
                                Status
                            </label>
                            <select
                                id="status"
                                class="form-select"
                                name="status"
                            >
                                <option value="open" <?= ($ticket['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
                                <option value="in_progress" <?= ($ticket['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                <option value="closed" <?= ($ticket['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="priority" class="form-label">
                                Priority
                            </label>
                            <select
                                id="priority"
                                class="form-select"
                                name="priority"
                            >
                                <option value="low" <?= ($ticket['priority'] ?? 'medium') === 'low' ? 'selected' : '' ?>>Low</option>
                                <option value="medium" <?= ($ticket['priority'] ?? 'medium') === 'medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="high" <?= ($ticket['priority'] ?? 'medium') === 'high' ? 'selected' : '' ?>>High</option>
                            </select>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem">
                        <a href="index.php?page=dashboard" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <?= $isEditing ? 'Update Ticket' : 'Create Ticket' ?>
                        </button>
                    </div>
                </form>
            </div>
        </main>

        <script>
            function confirmDelete() {
                return confirm('Are you sure you want to delete this ticket?');
            }
        </script>
    </body>
    </html>
    <?php
}

function getStatusClass($status) {
    $classes = [
        'open' => 'status-open',
        'in_progress' => 'status-in-progress',
        'closed' => 'status-closed'
    ];
    return $classes[$status] ?? 'status-open';
}
?>