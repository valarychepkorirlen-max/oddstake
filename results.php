<?php
session_start();
require_once 'config/firebase.php';

$recentResults = [
    ['date' => '2024-03-28', 'match' => 'Manchester United vs Liverpool', 'prediction' => 'Manchester United Win', 'result' => 'Win', 'score' => '2-1', 'profit' => '+KES 500'],
    ['date' => '2024-03-28', 'match' => 'Real Madrid vs Barcelona', 'prediction' => 'Both Teams Score', 'result' => 'Win', 'score' => '1-1', 'profit' => '+KES 750'],
    ['date' => '2024-03-27', 'match' => 'Bayern Munich vs Dortmund', 'prediction' => 'Over 2.5 Goals', 'result' => 'Win', 'score' => '3-2', 'profit' => '+KES 600'],
    ['date' => '2024-03-27', 'match' => 'AC Milan vs Inter', 'prediction' => 'AC Milan Win', 'result' => 'Loss', 'score' => '0-1', 'profit' => '-KES 500'],
    ['date' => '2024-03-26', 'match' => 'Arsenal vs Chelsea', 'prediction' => 'Arsenal Win', 'result' => 'Win', 'score' => '2-0', 'profit' => '+KES 550'],
    ['date' => '2024-03-26', 'match' => 'Juventus vs Roma', 'prediction' => 'Under 2.5 Goals', 'result' => 'Win', 'score' => '1-0', 'profit' => '+KES 400'],
    ['date' => '2024-03-25', 'match' => 'PSG vs Marseille', 'prediction' => 'PSG Win', 'result' => 'Win', 'score' => '3-0', 'profit' => '+KES 480'],
    ['date' => '2024-03-25', 'match' => 'Ajax vs Feyenoord', 'prediction' => 'Ajax Win', 'result' => 'Loss', 'score' => '1-2', 'profit' => '-KES 500']
];

$totalProfit = 0;
foreach ($recentResults as $r) { 
    $profitNum = (int)preg_replace('/[^0-9-]/', '', $r['profit']); 
    $totalProfit += $profitNum; 
}
$wins = count(array_filter($recentResults, function($r) { return $r['result'] === 'Win'; }));
$total = count($recentResults);
$winRate = round(($wins / $total) * 100);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results - SureWin Predictions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); min-height: 100vh; font-family: 'Segoe UI', sans-serif; }
        .navbar { background: rgba(0,0,0,0.9) !important; backdrop-filter: blur(10px); }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .hero-section { background: linear-gradient(135deg, #ff4757, #ff6b81); padding: 40px 0; text-align: center; color: white; margin-bottom: 30px; }
        .stats-card { background: rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; text-align: center; transition: all 0.3s; backdrop-filter: blur(10px); }
        .stats-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.15); }
        .stats-card h3 { font-size: 2rem; color: #ff4757; margin-bottom: 0; }
        .result-table { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .table th { background: #1a1a3e; color: white; border: none; padding: 15px; }
        .footer { background: rgba(0,0,0,0.5); padding: 20px 0; margin-top: 40px; text-align: center; color: rgba(255,255,255,0.6); }
        .btn-outline-light:hover { background: white; color: #ff4757; }
        @media (max-width: 768px) { .stats-card h3 { font-size: 1.5rem; } .table { font-size: 12px; } }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-chart-line"></i> <strong>SureWin</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="tips.php"><i class="fas fa-chart-line"></i> Predictions</a></li>
                    <li class="nav-item"><a class="nav-link" href="live-scores.php"><i class="fas fa-clock"></i> Live Scores</a></li>
                    <li class="nav-item"><a class="nav-link active" href="results.php"><i class="fas fa-chart-simple"></i> Results</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="signup.php"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <div class="container">
            <h1><i class="fas fa-chart-simple"></i> Results & Performance</h1>
            <p>Track our winning record and past predictions</p>
        </div>
    </div>

    <div class="container py-4">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <h3><?php echo $winRate; ?>%</h3>
                    <p class="mb-0 text-white">Win Rate</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <h3>KES <?php echo number_format(abs($totalProfit)); ?></h3>
                    <p class="mb-0 text-white"><?php echo $totalProfit >= 0 ? 'Total Profit' : 'Total Loss'; ?></p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <h3><?php echo $wins; ?>/<?php echo $total; ?></h3>
                    <p class="mb-0 text-white">Correct Predictions</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stats-card">
                    <h3>+32%</h3>
                    <p class="mb-0 text-white">ROI</p>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <div class="result-table">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th><i class="fas fa-calendar"></i> Date</th>
                            <th><i class="fas fa-futbol"></i> Match</th>
                            <th><i class="fas fa-chart-line"></i> Prediction</th>
                            <th><i class="fas fa-flag-checkered"></i> Result</th>
                            <th><i class="fas fa-scoreboard"></i> Score</th>
                            <th><i class="fas fa-money-bill-wave"></i> Profit/Loss</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentResults as $result): ?>
                        <tr>
                            <td><?php echo $result['date']; ?></td>
                            <td class="fw-bold"><?php echo $result['match']; ?></td>
                            <td><?php echo $result['prediction']; ?></td>
                            <td><span class="badge bg-<?php echo $result['result'] == 'Win' ? 'success' : 'danger'; ?> px-3 py-2"><?php echo $result['result']; ?></span></td>
                            <td><span class="badge bg-dark px-3 py-2"><?php echo $result['score']; ?></span></td>
                            <td class="<?php echo strpos($result['profit'], '+') === 0 ? 'text-success' : 'text-danger'; ?> fw-bold fs-5"><?php echo $result['profit']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Premium Performance Note -->
        <div class="alert alert-info mt-4">
            <i class="fas fa-chart-line"></i> <strong>Premium Tip Performance:</strong> Our premium tips have a 92% win rate with an average return of 2.8x your stake! 
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="signup.php" class="alert-link">Subscribe now</a> to access premium predictions.
            <?php elseif (!isset($hasActive) || !$hasActive): ?>
                <button class="btn btn-warning btn-sm ms-2" data-bs-toggle="modal" data-bs-target="#subscribeModal">Upgrade to Premium</button>
            <?php endif; ?>
        </div>

        <!-- Call to Action -->
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="text-center mt-4">
            <a href="signup.php" class="btn btn-primary btn-lg px-5">
                <i class="fas fa-user-plus"></i> Join Now for Premium Tips
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Subscription Modal (for logged-in users) -->
    <div class="modal fade" id="subscribeModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-crown"></i> Unlock Premium Predictions</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-chart-line fa-3x text-primary"></i>
                        <h4 class="mt-2">Get 92% Win Rate Tips</h4>
                        <p class="text-muted">Choose your subscription plan below</p>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card text-center mb-3 h-100">
                                <div class="card-body">
                                    <h5>Daily</h5>
                                    <h3 class="text-primary">KES 50</h3>
                                    <p class="small text-muted">per day</p>
                                    <button class="btn btn-outline-primary w-100 subscribe-btn" data-type="daily">Subscribe</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center mb-3 h-100 border-primary">
                                <div class="card-body">
                                    <span class="badge bg-primary position-absolute top-0 start-50 translate-middle">Popular</span>
                                    <h5>Weekly</h5>
                                    <h3 class="text-primary">KES 200</h3>
                                    <p class="small text-muted">save KES 150</p>
                                    <button class="btn btn-primary w-100 subscribe-btn" data-type="weekly">Subscribe</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card text-center mb-3 h-100">
                                <div class="card-body">
                                    <h5>Monthly</h5>
                                    <h3 class="text-primary">KES 500</h3>
                                    <p class="small text-muted">save KES 1,000</p>
                                    <button class="btn btn-outline-primary w-100 subscribe-btn" data-type="monthly">Subscribe</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p><strong>SureWin Football Predictions</strong> - Where Smart Bets Win</p>
            <p>&copy; 2024 SureWin. All rights reserved.</p>
            <small>Disclaimer: Gambling involves risk. Please bet responsibly.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const userPhone = '<?php echo isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : ''; ?>';
        document.querySelectorAll('.subscribe-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const subscriptionType = this.dataset.type;
                if (!userPhone) { 
                    Swal.fire({
                        icon: 'info',
                        title: 'Account Required',
                        text: 'Please login or create an account to subscribe.',
                        confirmButtonText: 'Login Now'
                    }).then(() => {
                        window.location.href = 'login.php';
                    });
                    return; 
                }
                Swal.fire({ title: 'Processing...', text: 'Initiating STK Push', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                try {
                    const response = await fetch('/api/pay.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ subscription_type: subscriptionType }) });
                    const data = await response.json();
                    Swal.close();
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'STK Push Sent!', text: 'Check your phone for the M-Pesa prompt.', confirmButtonText: 'OK' });
                    } else {
                        Swal.fire('Error', data.error || 'Payment failed', 'error');
                    }
                } catch (error) {
                    Swal.close();
                    Swal.fire('Error', 'Network error', 'error');
                }
            });
        });
    </script>
</body>
</html>