<?php
require "/Applications/XAMPP/xamppfiles/htdocs/inc/bootstrap.php";
/**
 * Handles OPTIONS request for CORS.
 */
function handleOptionsRequest() {
    header('Access-Control-Allow-Origin: http://localhost:3001');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Custom-Header');
    header('Referrer-Policy: no-referrer');
    exit;
}


/**
 * Sets CORS headers for regular requests.
 */
function setCorsHeaders() {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Custom-Header');
    header('Referrer-Policy: no-referrer-when-downgrade');
}


/**
 * Routes the request to the appropriate controller and method.
 *
 * @param array $uriSegments The URI segments.
 */
function routeRequest($uriSegments) {
    if ($uriSegments[2] == 'user') {
        require "/Applications/XAMPP/xamppfiles/htdocs/controller/Api/UserController.php";
        $controller = new UserController();
    } elseif ($uriSegments[2] == 'music') {
        require PROJECT_ROOT_PATH . "/controller/Api/SongController.php";
        $controller = new SongController();
    }

    $methodName = $uriSegments[3] . 'Action';

    if (isset($controller) && method_exists($controller, $methodName)) {
        $controller->{$methodName}();
    } else {
        header("HTTP/1.1 404 Not Found");
        exit("BAD 1");
    }
}

/**
 * Validates URI segments for the expected format.
 *
 * @param array $uriSegments The URI segments.
 * @return bool True if valid, false otherwise.
 */
function validateUriSegments($uriSegments) {
    return (isset($uriSegments[2]) && ($uriSegments[2] == 'user' || $uriSegments[2] == 'music') && isset($uriSegments[3]));
}

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    handleOptionsRequest();
}

// Set CORS headers for regular requests
setCorsHeaders();
// Parse the URI
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uriSegments = explode('/', $uri);
// Validate URI segments
if (!validateUriSegments($uriSegments)) {
    header("HTTP/1.1 404 Not Found");
    exit("BAD 1");
}
// Route requests based on the URI
routeRequest($uriSegments);






?>
