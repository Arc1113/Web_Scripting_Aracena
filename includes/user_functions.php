<?php
// User management functions for JSON-based user storage

function getUsersFilePath() {
    return __DIR__ . '/../data/users.json';
}

function getUsers() {
    $filePath = getUsersFilePath();
    
    if (!file_exists($filePath)) {
        // Create directory if it doesn't exist
        $dir = dirname($filePath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        
        // Create empty users file
        file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }
    
    $content = file_get_contents($filePath);
    if ($content === false) {
        throw new Exception('Unable to read users file');
    }
    
    $users = json_decode($content, true);
    if ($users === null) {
        // If JSON is invalid, backup the file and start fresh
        $backupPath = $filePath . '.backup.' . date('Y-m-d_H-i-s');
        copy($filePath, $backupPath);
        
        file_put_contents($filePath, json_encode([], JSON_PRETTY_PRINT));
        return [];
    }
    
    return $users;
}

function saveUsers($users) {
    $filePath = getUsersFilePath();
    
    // Create directory if it doesn't exist
    $dir = dirname($filePath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    $jsonData = json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($jsonData === false) {
        throw new Exception('Unable to encode user data to JSON');
    }
    
    // Write to temporary file first, then rename for atomic operation
    $tempPath = $filePath . '.tmp';
    $result = file_put_contents($tempPath, $jsonData, LOCK_EX);
    
    if ($result === false) {
        throw new Exception('Unable to write to users file');
    }
    
    if (!rename($tempPath, $filePath)) {
        // If rename fails, try to clean up temp file
        @unlink($tempPath);
        throw new Exception('Unable to save users file');
    }
    
    return true;
}

function userExists($username) {
    $users = getUsers();
    
    foreach ($users as $user) {
        if (isset($user['username']) && $user['username'] === $username) {
            return true;
        }
    }
    
    return false;
}

function emailExists($email) {
    $users = getUsers();
    
    foreach ($users as $user) {
        if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
            return true;
        }
    }
    
    return false;
}

function saveUser($userData) {
    try {
        $users = getUsers();
        
        // Check if user already exists (double-check)
        if (userExists($userData['username'])) {
            throw new Exception('Username already exists');
        }
        
        if (emailExists($userData['email'])) {
            throw new Exception('Email already exists');
        }
        
        // Add user to array
        $users[] = $userData;
        
        // Save updated users array
        return saveUsers($users);
        
    } catch (Exception $e) {
        error_log('Error saving user: ' . $e->getMessage());
        return false;
    }
}

function validateUser($username, $password) {
    try {
        $users = getUsers();
        
        foreach ($users as $user) {
            if (isset($user['username'], $user['password']) && 
                $user['username'] === $username && 
                password_verify($password, $user['password'])) {
                return $user;
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log('Error validating user: ' . $e->getMessage());
        return false;
    }
}

function getUserById($userId) {
    try {
        $users = getUsers();
        
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] === $userId) {
                return $user;
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log('Error getting user by ID: ' . $e->getMessage());
        return false;
    }
}

function getUserByUsername($username) {
    try {
        $users = getUsers();
        
        foreach ($users as $user) {
            if (isset($user['username']) && $user['username'] === $username) {
                return $user;
            }
        }
        
        return false;
        
    } catch (Exception $e) {
        error_log('Error getting user by username: ' . $e->getMessage());
        return false;
    }
}

function updateUser($userId, $updateData) {
    try {
        $users = getUsers();
        $userIndex = -1;
        
        // Find user index
        foreach ($users as $index => $user) {
            if (isset($user['id']) && $user['id'] === $userId) {
                $userIndex = $index;
                break;
            }
        }
        
        if ($userIndex === -1) {
            return false;
        }
        
        // Update user data
        $updateData['updated_at'] = date('Y-m-d H:i:s');
        $users[$userIndex] = array_merge($users[$userIndex], $updateData);
        
        return saveUsers($users);
        
    } catch (Exception $e) {
        error_log('Error updating user: ' . $e->getMessage());
        return false;
    }
}

function deleteUser($userId) {
    try {
        $users = getUsers();
        $newUsers = [];
        $found = false;
        
        foreach ($users as $user) {
            if (isset($user['id']) && $user['id'] === $userId) {
                $found = true;
                continue; // Skip this user (delete)
            }
            $newUsers[] = $user;
        }
        
        if (!$found) {
            return false;
        }
        
        return saveUsers($newUsers);
        
    } catch (Exception $e) {
        error_log('Error deleting user: ' . $e->getMessage());
        return false;
    }
}

function getUserStats() {
    try {
        $users = getUsers();
        
        $stats = [
            'total_users' => count($users),
            'users_by_gender' => [],
            'users_by_country' => [],
            'recent_registrations' => 0
        ];
        
        $thirtyDaysAgo = date('Y-m-d H:i:s', strtotime('-30 days'));
        
        foreach ($users as $user) {
            // Gender stats
            if (isset($user['gender'])) {
                $gender = $user['gender'];
                $stats['users_by_gender'][$gender] = ($stats['users_by_gender'][$gender] ?? 0) + 1;
            }
            
            // Country stats
            if (isset($user['country']) && !empty($user['country'])) {
                $country = $user['country'];
                $stats['users_by_country'][$country] = ($stats['users_by_country'][$country] ?? 0) + 1;
            }
            
            // Recent registrations
            if (isset($user['created_at']) && $user['created_at'] >= $thirtyDaysAgo) {
                $stats['recent_registrations']++;
            }
        }
        
        return $stats;
        
    } catch (Exception $e) {
        error_log('Error getting user stats: ' . $e->getMessage());
        return false;
    }
}
?>