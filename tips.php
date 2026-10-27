<?php
session_start();
require_once 'config/firebase.php';

$isLoggedIn = isset($_SESSION['user_id']);
$hasActiveSubscription = false;
$userData = null;

if ($isLoggedIn) {
    $userId = $_SESSION['user_id'];
    $userData = getUserFromFirestore($userId);
    $hasActiveSubscription = $userData && hasActiveSubscription($userData);
}

$tips = getLatestTips(50);
$premiumTips = getPremiumTips(10);
$freeTips = getFreeTips(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Predictions - SureWin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); min-height: 100vh; }
        .navbar { background: rgba(0,0,0,0.9) !important; }
        .hero-section { background: linear-gradient(135deg, #ff4757, #ff6b81); padding: 40px 0; text-align: center; color: white; margin-bottom: 30px; }
        .tip-card { background: white; border-radius: 15px; margin-bottom: 20px; transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .tip-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.2); }
        .premium-badge { background: #ffc107; color: #000; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .free-badge { background: #28a745; color: white; padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: bold; }
        .locked-tip { background: #f8f9fa; border-radius: 10px; padding: 20px; text-align: center; color: #6c757d; }
        .btn-subscribe { background: linear-gradient(135deg, #ff4757, #ff6b81); border: none; border-radius: 50px; padding: 10px 25px; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container"><a class="navbar-brand" href="index.php"><i class="fas fa-chart-line"></i> <strong>SureWin</strong></a>
            <div class="collapse navbar-collapse"><ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link active" href="tips.php">Predictions</a></li>
                <li class="nav-item"><a class="nav-link" href="live-scores.php">Live Scores</a></li>
                <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
                <?php if ($isLoggedIn): ?><li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li><li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?><li class="nav-item"><a class="nav-link" href="login.php">Login</a></li><li class="nav-item"><a class="nav-link" href="signup.php">Sign Up</a></li><?php endif; ?>
            </ul></div>
        </div>
    </nav>

    <div class="hero-section"><div class="container"><h1><i class="fas fa-chart-line"></i> Expert Predictions</h1><p>Data-driven tips with 87% accuracy rate</p></div></div>

    <div class="container py-4">
        <div class="row">
            <div class="col-md-8">
                <?php if (!$hasActiveSubscription && $isLoggedIn): ?>
                    <div class="alert alert-warning mb-4">🔒 Premium Tips Locked! <button class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#subscribeModal">Subscribe Now</button></div>
                <?php endif; ?>
                
                <h4 class="text-white mb-3">🔥 Premium Predictions</h4>
                <?php foreach ($premiumTips as $tip): ?>
                <div class="tip-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0"><?php echo htmlspecialchars($tip['match'] ?? 'TBD'); ?></h5>
                                <span class="premium-badge">Premium</span>
                            </div>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($tip['league'] ?? 'Unknown'); ?> | Odds: <?php echo number_format($tip['tip_odds'] ?? 0, 2); ?></p>
                            <?php if ($hasActiveSubscription): ?>
                                <div class="alert alert-success p-2 mb-0"><strong>Prediction:</strong> <?php echo htmlspecialchars($tip['tip'] ?? 'No tip'); ?></div>
                                <?php if (isset($tip['confidence'])): ?><small class="text-muted">Confidence: <?php echo $tip['confidence']; ?></small><?php endif; ?>
                            <?php else: ?>
                                <div class="locked-tip"><i class="fas fa-lock"></i> Premium Tip - Subscribe to unlock</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <h4 class="text-white mb-3 mt-4"><i class="fas fa-gift text-success"></i> Free Tips</h4>
                <?php foreach ($freeTips as $tip): ?>
                <div class="tip-card p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0"><?php echo htmlspecialchars($tip['match'] ?? 'TBD'); ?></h5>
                                <span class="free-badge">Free</span>
                            </div>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($tip['league'] ?? 'Unknown'); ?> | Odds: <?php echo number_format($tip['tip_odds'] ?? 0, 2); ?></p>
                            <div class="alert alert-info p-2 mb-0"><strong>Tip:</strong> <?php echo htmlspecialchars($tip['tip'] ?? 'No tip'); ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="col-md-4">
                <div class="card sticky-top" style="top: 20px;">
                    <div class="card-body">
                        <h5>Subscription Plans</h5>
                        <ul class="list-unstyled"><li>✓ Daily: KES 50</li><li>✓ Weekly: KES 200</li><li>✓ Monthly: KES 500</li></ul>
                        <?php if (!$hasActiveSubscription && $isLoggedIn): ?>
                            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#subscribeModal">Subscribe Now</button>
                        <?php elseif (!$isLoggedIn): ?>
                            <a href="signup.php" class="btn btn-primary w-100">Sign Up to Subscribe</a>
                        <?php else: ?>
                            <div class="alert alert-success">✅ Active Subscription</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Modal -->
    <div class="modal fade" id="subscribeModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-primary text-white"><h5 class="modal-title">Subscribe to Premium Tips</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body"><div class="row"><div class="col-md-4"><div class="card text-center mb-3"><div class="card-body"><h5>Daily</h5><h3 class="text-primary">KES 50</h3><button class="btn btn-outline-primary w-100 subscribe-btn" data-type="daily">Subscribe</button></div></div></div>
    <div class="col-md-4"><div class="card text-center mb-3"><div class="card-body"><h5>Weekly</h5><h3 class="text-primary">KES 200</h3><button class="btn btn-outline-primary w-100 subscribe-btn" data-type="weekly">Subscribe</button></div></div></div>
    <div class="col-md-4"><div class="card text-center mb-3"><div class="card-body"><h5>Monthly</h5><h3 class="text-primary">KES 500</h3><button class="btn btn-outline-primary w-100 subscribe-btn" data-type="monthly">Subscribe</button></div></div></div></div></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const userPhone = '<?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>';
        document.querySelectorAll('.subscribe-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const subscriptionType = this.dataset.type;
                if (!userPhone) { window.location.href = 'login.php'; return; }
                Swal.fire({ title: 'Processing...', text: 'Initiating STK Push', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch('/api/pay.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ subscription_type: subscriptionType }) });
                    const data = await response.json();
                    Swal.close();
                    if (data.success) { Swal.fire({ icon: 'success', title: 'STK Push Sent!', text: 'Check your phone for the M-Pesa prompt.', confirmButtonText: 'OK' }); }
                    else { Swal.fire('Error', data.error || 'Payment failed', 'error'); }
                } catch (error) { Swal.close(); Swal.fire('Error', 'Network error', 'error'); }
            });
        });
    </script>
</body>
</html>