<?php
require_once __DIR__ . '/config.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] == 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Please log in to access this page';
        header('Location: ' . SITE_URL . 'login.php');
        exit();
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = 'Access denied. Admin only.';
        header('Location: ' . SITE_URL . 'home.php');
        exit();
    }
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if (isAdmin()) {
            header('Location: ' . SITE_URL . 'admin/dashboard.php');
        } else {
            header('Location: ' . SITE_URL . 'patient/dashboard.php');
        }
        exit();
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    return [
        'user_id' => $_SESSION['user_id'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'email' => $_SESSION['email'],
        'role' => $_SESSION['role']
    ];
}
?>