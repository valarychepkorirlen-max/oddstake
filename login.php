<?php
session_start();
require_once 'config/firebase.php';

$error = null;

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    
    $storedPhone = formatPhoneForStorage($phone);
    $email = $storedPhone . '@surewin.com';
    
    $result = $firebase->signIn($email, $password);
    
    if (isset($result['error'])) {
        $error = "Invalid phone number or password";
    } elseif (isset($result['localId'])) {
        $uid = $result['localId'];
        $idToken = $result['idToken'];
        
        $userData = getUserFromFirestore($uid);
        
        if ($userData) {
            $_SESSION['user_id'] = $uid;
            $_SESSION['user_phone'] = $userData['phone'];
            $_SESSION['user_name'] = $userData['name'] ?? '';
            $_SESSION['id_token'] = $idToken;
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = "User data not found. Please contact support.";
        }
    } else {
        $error = "Login failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SureWin Predictions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); min-height: 100vh; padding: 20px; }
        .card { border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); margin-top: 80px; }
        .btn-primary { background: linear-gradient(135deg, #ff4757, #ff6b81); border: none; border-radius: 50px; padding: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">🏆 Welcome Back</h4>
                        <small>Login to access premium predictions</small>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
                        <form method="POST">
                            <div class="mb-3"><label class="form-label fw-bold">M-Pesa Phone Number</label><input type="tel" class="form-control" name="phone" placeholder="0712345678" required></div>
                            <div class="mb-4"><label class="form-label fw-bold">Password</label><input type="password" class="form-control" name="password" required></div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Login</button>
                        </form>
                        <div class="text-center mt-4"><p class="mb-0">Don't have an account? <a href="signup.php">Sign up here</a></p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>