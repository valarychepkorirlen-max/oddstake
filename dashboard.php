<?php
session_start();
require_once 'config/firebase.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userData = getUserFromFirestore($userId);

if (!$userData) {
    if (isset($_SESSION['user_phone'])) {
        $userData = getUserByPhone($_SESSION['user_phone']);
    }
    if (!$userData) {
        session_destroy();
        header('Location: login.php?error=session_expired');
        exit;
    }
}

$_SESSION['user_phone'] = $userData['phone'];
$_SESSION['user_name'] = $userData['name'] ?? 'User';

$hasActive = false;
$expiry = null;

if (isset($userData['status']) && $userData['status'] === 'active') {
    if (isset($userData['subscription_expiry']) && !empty($userData['subscription_expiry'])) {
        $expiry = DateTime::createFromFormat('Y-m-d H:i:s', $userData['subscription_expiry']);
        $now = new DateTime();
        $hasActive = $expiry && $expiry > $now;
    }
}

$payments = [];
try {
    $paymentsResult = $firebase->queryDocuments('payments', 'user_id', '==', $userId);
    if (is_array($paymentsResult)) {
        $payments = array_slice($paymentsResult, 0, 10);
    }
} catch (Exception $e) {
    error_log("getUserPayments error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SureWin Predictions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); min-height: 100vh; }
        .dashboard-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 20px; transition: transform 0.3s; }
        .dashboard-card:hover { transform: translateY(-5px); }
        .stat-card { text-align: center; padding: 20px; border-radius: 15px; color: white; }
        .stat-card h3 { font-size: 2rem; margin-bottom: 0; }
        .bg-success-gradient { background: linear-gradient(135deg, #11998e, #38ef7d); }
        .bg-warning-gradient { background: linear-gradient(135deg, #f093fb, #f5576c); }
        .bg-info-gradient { background: linear-gradient(135deg, #4facfe, #00f2fe); }
        .btn-subscribe { background: linear-gradient(135deg, #ff4757, #ff6b81); border: none; border-radius: 50px; padding: 10px 25px; color: white; font-weight: bold; }
        .navbar { background: rgba(0,0,0,0.8) !important; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container"><a class="navbar-brand" href="index.php"><i class="fas fa-chart-line"></i> <strong>SureWin</strong></a>
            <div class="collapse navbar-collapse"><ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="tips.php">Predictions</a></li>
                <li class="nav-item"><a class="nav-link" href="live-scores.php">Live Scores</a></li>
                <li class="nav-item"><a class="nav-link" href="results.php">Results</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul></div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="dashboard-card p-4 mb-4">
            <div class="row align-items-center">
                <div class="col-md-8"><h2 class="mb-2">Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>! 👋</h2><p class="text-muted mb-0">Phone: <?php echo formatPhoneForDisplay($userData['phone'] ?? ''); ?></p></div>
                <div class="col-md-4 text-md-end"><?php if (!$hasActive): ?><button class="btn-subscribe" data-bs-toggle="modal" data-bs-target="#subscribeModal">Subscribe Now</button><?php endif; ?></div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4"><div class="stat-card bg-success-gradient"><h3><?php echo $hasActive ? 'Active' : 'Inactive'; ?></h3><p class="mb-0">Subscription Status</p><?php if ($hasActive && $expiry): ?><small>Expires: <?php echo $expiry->format('d M Y'); ?></small><?php endif; ?></div></div>
            <div class="col-md-4"><div class="stat-card bg-info-gradient"><h3><?php echo count($payments); ?></h3><p class="mb-0">Total Payments</p></div></div>
            <div class="col-md-4"><div class="stat-card bg-warning-gradient"><h3><?php echo $hasActive ? 'Unlimited' : '0'; ?></h3><p class="mb-0">Premium Tips Access</p></div></div>
        </div>
        
        <div class="dashboard-card p-4">
            <h4 class="mb-3">Recent Payments</h4>
            <?php if (count($payments) > 0): ?>
                <div class="table-responsive"><table class="table"><thead><tr><th>Date</th><th>Amount</th><th>Type</th><th>Receipt</th><th>Status</th></tr></thead>
                <tbody><?php foreach ($payments as $payment): ?><tr><td><?php echo isset($payment['created_at']) ? date('d M Y', strtotime($payment['created_at'])) : 'N/A'; ?></td>
                <td>KES <?php echo number_format($payment['amount'] ?? 0, 2); ?></td>
                <td><?php echo ucfirst($payment['subscription_type'] ?? 'N/A'); ?></td>
                <td><?php echo $payment['mpesa_receipt'] ?? '-'; ?></td>
                <td><span class="badge bg-<?php echo ($payment['status'] ?? 'pending') === 'completed' ? 'success' : 'warning'; ?>"><?php echo $payment['status'] ?? 'pending'; ?></span></td></tr><?php endforeach; ?></tbody></table></div>
            <?php else: ?><p class="text-muted text-center py-3">No payments yet. Subscribe to get premium tips!</p><?php endif; ?>
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