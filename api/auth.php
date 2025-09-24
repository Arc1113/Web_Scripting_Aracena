<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// Only allow POST requests for main functionality
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed. Only POST requests are supported.']);
    exit;
}

// Include user management functions
require_once '../includes/user_functions.php';

// Get JSON input
$inputRaw = file_get_contents('php://input');
$input = json_decode($inputRaw, true);

if ($inputRaw === false) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Unable to read request data']);
    exit;
}

if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data: ' . json_last_error_msg()]);
    exit;
}

if (!$input || !isset($input['action'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request - missing action']);
    exit;
}

$action = $input['action'];

try {
    switch ($action) {
        case 'register':
            handleRegistration($input);
            break;
        
        case 'login':
            handleLogin($input);
            break;
        
        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

function handleRegistration($data) {
    // Validate required fields
    $required = ['fullname', 'email', 'username', 'password', 'confirm_password', 'gender'];
    $errors = [];
    
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
        }
    }
    
    // Email validation
    if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    // Password validation
    if (!empty($data['password'])) {
        if (strlen($data['password']) < 6) {
            $errors[] = 'Password must be at least 6 characters long';
        }
        
        if ($data['password'] !== $data['confirm_password']) {
            $errors[] = 'Passwords do not match';
        }
    }
    
    // Username validation
    if (!empty($data['username'])) {
        if (strlen($data['username']) < 3) {
            $errors[] = 'Username must be at least 3 characters long';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Username can only contain letters, numbers, and underscores';
        }
        
        if (userExists($data['username'])) {
            $errors[] = 'Username already exists';
        }
    }
    
    // Full name validation
    if (!empty($data['fullname']) && strlen(trim($data['fullname'])) < 2) {
        $errors[] = 'Full name must be at least 2 characters long';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode('<br>', $errors)
        ]);
        return;
    }
    
    // Prepare user data
    $userData = [
        'id' => generateUserId(),
        'fullname' => sanitizeInput($data['fullname']),
        'email' => sanitizeInput($data['email']),
        'username' => sanitizeInput($data['username']),
        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        'gender' => sanitizeInput($data['gender']),
        'hobbies' => isset($data['hobbies']) ? array_map('sanitizeInput', $data['hobbies']) : [],
        'country' => sanitizeInput($data['country'] ?? ''),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    if (saveUser($userData)) {
        echo json_encode([
            'success' => true, 
            'message' => 'Registration successful! You can now login.'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Registration failed. Please try again.'
        ]);
    }
}

function handleLogin($data) {
    $errors = [];
    
    if (empty($data['username']) || empty($data['password'])) {
        $errors[] = 'Username and password are required';
    }
    
    // Username validation
    if (!empty($data['username'])) {
        if (strlen($data['username']) < 3) {
            $errors[] = 'Invalid username format';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            $errors[] = 'Invalid username format';
        }
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false, 
            'message' => implode('<br>', $errors)
        ]);
        return;
    }
    
    $user = validateUser($data['username'], $data['password']);
    if ($user) {
        // Remove password from response
        unset($user['password']);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Welcome back, ' . htmlspecialchars($user['fullname']) . '!',
            'user' => $user
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid username or password'
        ]);
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateUserId() {
    return uniqid('user_', true);
}
?>