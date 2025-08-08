<?php

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}


function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}


function registerUser($pdo, $username, $email, $password, $confirm_password) {
    $errors = [];
  
    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 4) {
        $errors[] = "Username must be at least 4 characters";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
  
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Username or email already exists";
        }
    }
    
 
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        if ($stmt->execute([$username, $email, $hashed_password])) {
            return ['success' => true, 'user_id' => $pdo->lastInsertId()];
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
    
    return ['success' => false, 'errors' => $errors];
}


function loginUser($pdo, $username, $password) {
    $errors = [];
    
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            return ['success' => true];
        } else {
            $errors[] = "Invalid username or password";
        }
    }
    
    return ['success' => false, 'errors' => $errors];
}


function logoutUser() {
    // Unset all session variables
    $_SESSION = array();
    
    // Destroy the session
    session_destroy();
}


function redirect($url) {
    header("Location: $url");
    exit();
}


function getCurrentUser($pdo) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}


function canEditProduct($pdo, $product_id) {
    if (!isLoggedIn()) {
        return false;
    }
    
    if (isAdmin()) {
        return true;
    }
    
    $stmt = $pdo->prepare("SELECT created_by FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();
    
    return $product && $product['created_by'] == $_SESSION['user_id'];
}


function requireLogin($redirect_url = 'login.php') {
    if (!isLoggedIn()) {
        $_SESSION['error'] = "You must be logged in to access this page";
        redirect($redirect_url);
    }
}


function requireAdmin($redirect_url = 'index.php') {
    requireLogin($redirect_url);
    
    if (!isAdmin()) {
        $_SESSION['error'] = "You don't have permission to access this page";
        redirect($redirect_url);
    }
}