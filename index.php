<?php
session_start();
require_once 'config/firebase.php';
require_once 'config/odds.php';

// Helper functions
function getAnalysis($match) {
    $analyses = [
        'Manchester United vs Liverpool' => 'Home team strong form, expected to dominate possession. Key player: Bruno Fernandes.',
        'Real Madrid vs Barcelona' => 'El Clasico classic - expect goals from both sides. Watch for Vinicius Jr.',
        'Bayern Munich vs Borussia Dortmund' => 'High scoring match likely, over 2.5 goals. Kane in form.',
        'AC Milan vs Inter Milan' => 'Derby della Madonnina - tight contest expected. Defensive battle.',
        'Arsenal vs Chelsea' => 'London derby - home advantage crucial. Saka key player.',
        'Juventus vs Roma' => 'Defensive battle expected, under 2.5 goals likely.',
        'PSG vs Marseille' => 'Le Classique - PSG strong at home. Mbappe threat.',
        'Ajax vs Feyenoord' => 'De Klassieker - high intensity, goals expected.'
    ];
    return $analyses[$match] ?? 'Expert analysis available for premium members.';
}

function getUpcomingMatches($limit) {
    $matches = [
        ['home' => 'Arsenal', 'away' => 'Chelsea', 'time' => '15:30', 'date' => 'Today', 'league' => 'Premier League'],
        ['home' => 'Juventus', 'away' => 'Roma', 'time' => '18:00', 'date' => 'Today', 'league' => 'Serie A'],
        ['home' => 'Barcelona', 'away' => 'Atletico Madrid', 'time' => '20:00', 'date' => 'Today', 'league' => 'La Liga'],
        ['home' => 'Dortmund', 'away' => 'Leverkusen', 'time' => '17:30', 'date' => 'Tomorrow', 'league' => 'Bundesliga'],
        ['home' => 'PSG', 'away' => 'Marseille', 'time' => '19:45', 'date' => 'Tomorrow', 'league' => 'Ligue 1']
    ];
    return array_slice($matches, 0, $limit);
}

function getPopularLeagues() {
    return [
        'English Premier League',
        'La Liga',
        'Serie A',
        'Bundesliga',
        'Ligue 1',
        'UEFA Champions League',
        'Europa League',
        'Kenya Premier League'
    ];
}

// Get tips
$latestTips = getLatestTips(6);
$premiumTips = getPremiumTips(6);

// Live matches data
$liveMatches = [
    ['home' => 'Manchester United', 'away' => 'Liverpool', 'score' => '2-1', 'time' => "65'", 'league' => 'Premier League', 'status' => 'live'],
    ['home' => 'Real Madrid', 'away' => 'Barcelona', 'score' => '1-0', 'time' => "78'", 'league' => 'La Liga', 'status' => 'live'],
    ['home' => 'Bayern Munich', 'away' => 'Borussia Dortmund', 'score' => '3-2', 'time' => 'HT', 'league' => 'Bundesliga', 'status' => 'live'],
    ['home' => 'AC Milan', 'away' => 'Inter Milan', 'score' => '0-0', 'time' => "23'", 'league' => 'Serie A', 'status' => 'live']
];

// Recent results
$recentResults = [
    ['match' => 'Arsenal vs Chelsea', 'prediction' => 'Arsenal Win', 'result' => 'win', 'score' => '3-1', 'profit' => '+KES 550'],
    ['match' => 'PSG vs Marseille', 'prediction' => 'Over 2.5 Goals', 'result' => 'win', 'score' => '4-0', 'profit' => '+KES 600'],
    ['match' => 'Juventus vs Roma', 'prediction' => 'Both Teams Score', 'result' => 'loss', 'score' => '1-0', 'profit' => '-KES 500'],
    ['match' => 'Ajax vs Feyenoord', 'prediction' => 'Ajax Win', 'result' => 'win', 'score' => '2-0', 'profit' => '+KES 450']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=AW-18071299286"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-18071299286');
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SureWin - Football Predictions | Where Smart Bets Win</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        .navbar { background: rgba(0,0,0,0.9) !important; backdrop-filter: blur(10px); }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .hero-section { background: linear-gradient(135deg, #ff4757, #ff6b81); padding: 80px 0; position: relative; overflow: hidden; }
        .hero-section::before { content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: url('https://images.unsplash.com/photo-1529900748604-07564a03e7a6?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80') center/cover; opacity: 0.1; }
        .hero-content { position: relative; z-index: 1; }
        .stat-card { background: white; border-radius: 20px; padding: 25px; text-align: center; transition: all 0.3s; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .stat-card:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(0,0,0,0.2); }
        .stat-number { font-size: 2.5rem; font-weight: bold; color: #ff4757; }
        .premium-card { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px; padding: 30px; color: white; position: relative; overflow: hidden; cursor: pointer; transition: transform 0.3s; }
        .premium-card:hover { transform: translateY(-5px); }
        .premium-card::before { content: '⭐ PREMIUM'; position: absolute; top: 15px; right: -30px; background: gold; color: #000; padding: 5px 40px; transform: rotate(45deg); font-size: 12px; font-weight: bold; }
        .match-card { background: white; border-radius: 15px; padding: 20px; margin-bottom: 20px; transition: all 0.3s; box-shadow: 0 5px 15px rgba(0,0,0,0.08); }
        .match-card:hover { transform: translateX(5px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
        .premium-match-card { background: linear-gradient(135deg, #fff5f5, #ffffff); border-left: 4px solid #ffc107; }
        .live-score-card { background: linear-gradient(135deg, #1e3c72, #2a5298); border-radius: 15px; padding: 15px; margin-bottom: 15px; transition: transform 0.3s; color: white; }
        .live-score-card:hover { transform: translateY(-5px); }
        .live-badge { animation: pulse 1s infinite; background: #dc3545; color: white; padding: 3px 8px; border-radius: 20px; font-size: 10px; display: inline-block; }
        @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
        .btn-primary { background: linear-gradient(135deg, #ff4757, #ff6b81); border: none; border-radius: 50px; padding: 12px 30px; font-weight: bold; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,71,87,0.4); }
        .btn-light { border-radius: 50px; padding: 12px 30px; font-weight: bold; }
        .btn-outline-premium { background: transparent; border: 2px solid #ffc107; color: #ffc107; border-radius: 50px; padding: 8px 20px; font-weight: bold; transition: all 0.3s; }
        .btn-outline-premium:hover { background: #ffc107; color: #000; transform: translateY(-2px); }
        .footer { background: rgba(0,0,0,0.8); padding: 40px 0 20px; color: rgba(255,255,255,0.7); }
        .section-badge { display: inline-block; background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 50px; font-size: 12px; margin-bottom: 15px; }
        @media (max-width: 768px) { .hero-section h1 { font-size: 28px; } .stat-number { font-size: 1.8rem; } }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/"><i class="fas fa-chart-line"></i> <strong>SureWin</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="/"><i class="fas fa-home"></i> Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tips"><i class="fas fa-chart-line"></i> Predictions</a></li>
                    <li class="nav-item"><a class="nav-link" href="/live-scores"><i class="fas fa-clock"></i> Live Scores</a></li>
                    <li class="nav-item"><a class="nav-link" href="/results"><i class="fas fa-chart-simple"></i> Results</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item"><a class="nav-link" href="/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li class="nav-item"><a class="nav-link" href="/signup"><i class="fas fa-user-plus"></i> Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


    <div class="container py-5">
        <!-- PREMIUM SECTION - MOVED TO TOP (MAIN FOCUS) -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="premium-card mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3><i class="fas fa-crown"></i> Unlock Premium Predictions</h3>
                            <p>Get access to winning tips with 87% accuracy rate - used by 10,000+ successful bettors</p>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="badge bg-light text-dark"><i class="fas fa-check-circle text-success"></i> Daily Tips</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-check-circle text-success"></i> Expert Analysis</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-check-circle text-success"></i> 87% Win Rate</span>
                                <span class="badge bg-light text-dark"><i class="fas fa-check-circle text-success"></i> Money Back Guarantee</span>
                            </div>
                            <p class="mt-3 mb-0"><i class="fas fa-tag"></i> Daily: KES 50 | Weekly: KES 200 | Monthly: KES 500</p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                                    <i class="fas fa-rocket"></i> Subscribe Now
                                </button>
                            <?php else: ?>
                                <a href="/signup" class="btn btn-light btn-lg">
                                    <i class="fas fa-user-plus"></i> Sign Up to Subscribe
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 text-center mb-4">
                <span class="section-badge"><i class="fas fa-crown text-warning"></i> EXCLUSIVE CONTENT</span>
                <h2 class="text-white">🔥 Premium Predictions <small class="text-muted">(Subscribe to Unlock)</small></h2>
                <p class="text-white-50">Our expert analysts pick the best bets with highest probability of winning</p>
            </div>
            
            <?php if (!empty($premiumTips)): ?>
                <div class="row">
                    <?php foreach ($premiumTips as $index => $tip): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="match-card premium-match-card">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-crown"></i> Premium Pick #<?php echo $index + 1; ?>
                                </span>
                                <span class="text-danger fw-bold">
                                    <i class="fas fa-chart-line"></i> Odds: <?php echo number_format($tip['tip_odds'] ?? 2.00, 2); ?>
                                </span>
                            </div>
                            <div class="text-center mb-3">
                                <div class="fw-bold fs-5"><?php echo htmlspecialchars($tip['match'] ?? 'TBD'); ?></div>
                                <div class="text-muted small"><?php echo htmlspecialchars($tip['league'] ?? 'Unknown'); ?></div>
                                <div class="mt-2">
                                    <span class="badge bg-secondary">Confidence: High</span>
                                </div>
                            </div>
                            <div class="text-center p-3 bg-light rounded">
                                <i class="fas fa-lock fa-2x text-warning mb-2 d-block"></i>
                                <strong>Prediction: 🔒 Locked</strong>
                            </div>
                            <div class="mt-3 text-center">
                                <button class="btn btn-outline-premium w-100" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                                    <i class="fas fa-unlock-alt"></i> Subscribe to Reveal
                                </button>
                                <small class="text-muted d-block mt-2">Join 10,000+ successful members</small>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-2 mb-4">
                    <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#subscribeModal">
                        <i class="fas fa-gem"></i> Get All Premium Tips Now
                    </button>
                </div>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Premium tips coming soon! Subscribe to get notified when they're available.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- LIVE SCORES SECTION -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white mb-0"><i class="fas fa-clock text-danger"></i> Live Scores & Updates</h2>
                    <a href="/live-scores" class="btn btn-outline-light btn-sm">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <?php foreach ($liveMatches as $match): ?>
            <div class="col-md-6">
                <div class="live-score-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div><span class="live-badge">LIVE</span><small class="ms-2"><?php echo $match['league']; ?></small></div>
                        <span class="badge bg-dark"><?php echo $match['time']; ?></span>
                    </div>
                    <div class="text-center py-3">
                        <div class="row align-items-center">
                            <div class="col-5 text-end"><strong><?php echo $match['home']; ?></strong></div>
                            <div class="col-2"><span class="fw-bold fs-3"><?php echo $match['score']; ?></span></div>
                            <div class="col-5 text-start"><strong><?php echo $match['away']; ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- FREE TIPS SECTION -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white mb-0"><i class="fas fa-gift text-success"></i> Free Tips of the Day</h2>
                    <a href="/tips" class="btn btn-outline-light btn-sm">View All Tips <i class="fas fa-arrow-right"></i></a>
                </div>
                <p class="text-white-50 mb-4">Try our free predictions before upgrading to premium</p>
            </div>
            <?php if (!empty($latestTips)): ?>
                <?php foreach ($latestTips as $tip): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="match-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success">Free</span>
                            <span class="text-primary fw-bold">Odds: <?php echo number_format($tip['tip_odds'] ?? 1.85, 2); ?></span>
                        </div>
                        <div class="text-center mb-3">
                            <div class="fw-bold"><?php echo htmlspecialchars($tip['match'] ?? 'TBD'); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($tip['league'] ?? 'Unknown'); ?></div>
                        </div>
                        <div class="text-center p-2 bg-light rounded">
                            <strong>Tip: <?php echo htmlspecialchars($tip['tip'] ?? 'N/A'); ?></strong>
                        </div>
                        <div class="mt-2 text-center">
                            <small class="text-muted"><i class="fas fa-chart-line"></i> Analysis: <?php echo getAnalysis($tip['match'] ?? ''); ?></small>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center">No tips available yet. Check back later!</div>
                </div>
            <?php endif; ?>
        </div>

        <!-- RECENT RESULTS SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-white mb-0"><i class="fas fa-chart-simple"></i> Recent Results</h2>
                    <a href="/results" class="btn btn-outline-light btn-sm">View Full Results <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-hover bg-white rounded">
                        <thead class="table-dark">
                            <tr><th>Match</th><th>Prediction</th><th>Result</th><th>Score</th><th>Profit</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentResults as $result): ?>
                            <tr>
                                <td><?php echo $result['match']; ?></td>
                                <td><?php echo $result['prediction']; ?></td>
                                <td><span class="badge bg-<?php echo $result['result'] == 'win' ? 'success' : 'danger'; ?>"><?php echo strtoupper($result['result']); ?></span></td>
                                <td><?php echo $result['score']; ?></td>
                                <td class="<?php echo strpos($result['profit'], '+') === 0 ? 'text-success' : 'text-danger'; ?> fw-bold"><?php echo $result['profit']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Modal -->
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
                        <h4 class="mt-2">Get 87% Win Rate Tips</h4>
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
                    <div class="alert alert-info small mt-3">
                        <i class="fas fa-info-circle"></i> After subscribing, you'll get instant access to all premium predictions via STK Push to your phone.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container text-center">
            <p><strong>SureWin Football Predictions</strong> - Where Smart Bets Win</p>
            <p>&copy; 2026 SureWin. All rights reserved.</p>
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
                        window.location.href = '/login';
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