<?php
/**
 * Application Entry Point
 * 
 * Main router that loads the appropriate page based on query parameters
 */

session_start();

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/utils/auth_middleware.php';

// Check authentication
checkAuth();

// Load routing system
require_once __DIR__ . '/controllers/routes.php';
