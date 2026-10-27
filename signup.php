<?php
session_start();
require_once 'config/firebase.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($name)) {
        $error = "Please enter your full name.";
    } elseif (strlen($phone) < 10) {
        $error = "Invalid phone number. Must be 10 digits starting with 0 (e.g., 0712345678).";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $storedPhone = $phone;
        $email = $phone . '@surewin.com';
        $existingUser = getUserByPhone($phone);
        
        if ($existingUser) {
            $error = "Phone number already registered. Please login instead.";
        } else {
            $result = $firebase->signUp($email, $password);
            
            if (isset($result['error'])) {
                $errorMessage = $result['error'];
                if (strpos($errorMessage, 'EMAIL_EXISTS') !== false) {
                    $error = "This phone number is already registered. Please login.";
                } elseif (strpos($errorMessage, 'WEAK_PASSWORD') !== false) {
                    $error = "Password is too weak. Use a stronger password.";
                } else {
                    $error = "Signup failed: " . $errorMessage;
                }
            } elseif (isset($result['localId'])) {
                $uid = $result['localId'];
                
                $userData = [
                    'uid' => $uid,
                    'phone' => $storedPhone,
                    'name' => $name,
                    'subscription_type' => 'none',
                    'subscription_expiry' => null,
                    'status' => 'inactive',
                    'created_at' => date('Y-m-d H:i:s'),
                    'balance' => 0
                ];
                
                $saveResult = saveUserToFirestore($uid, $userData);
                
                if ($saveResult) {
                    $_SESSION['signup_success'] = "Account created successfully! Please login with your phone number.";
                    header('Location: login.php');
                    exit;
                } else {
                    $error = "Account created but data save failed. Please contact support.";
                }
            } else {
                $error = "Signup failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - SureWin Predictions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); min-height: 100vh; padding: 20px; }
        .card { border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); margin-top: 50px; }
        .btn-primary { background: linear-gradient(135deg, #ff4757, #ff6b81); border: none; border-radius: 50px; padding: 12px; font-weight: bold; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,71,87,0.4); }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">🏆 Create Your SureWin Account</h4>
                        <small>Where Smart Bets Win</small>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                        <?php if (isset($_SESSION['signup_success'])): ?><div class="alert alert-success"><?php echo $_SESSION['signup_success']; unset($_SESSION['signup_success']); ?></div><?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3"><label class="form-label fw-bold">Full Name</label><input type="text" class="form-control" name="name" placeholder="Samoo" required></div>
                            <div class="mb-3"><label class="form-label fw-bold">M-Pesa Phone Number</label><input type="tel" class="form-control" name="phone" placeholder="0712345678" required><small class="text-muted">Enter your phone number starting with 0 (e.g., 0712345678)</small></div>
                            <div class="mb-3"><label class="form-label fw-bold">Password</label><input type="password" class="form-control" name="password" required minlength="6"></div>
                            <div class="mb-4"><label class="form-label fw-bold">Confirm Password</label><input type="password" class="form-control" name="confirm_password" required></div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Create Account</button>
                        </form>
                        <div class="text-center mt-4"><p class="mb-0">Already have an account? <a href="login.php">Login here</a></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>