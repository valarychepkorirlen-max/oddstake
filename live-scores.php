<?php
session_start();
$scorebatEmbedToken = 'MTczMjUxXzE3NzQ3NDk0ODFfZjBmMmYwMjEyNTViMjAwNDk2NGMxNWM5YTM3MTQyODQ3MWNhZjZmNQ==';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Live Scores - SureWin Predictions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: linear-gradient(135deg, #0a0a2a 0%, #1a1a3e 100%); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; min-height: 100vh; }
        
        /* Navigation */
        .navbar { background: rgba(0,0,0,0.9) !important; backdrop-filter: blur(10px); }
        .navbar-brand { font-weight: bold; font-size: 1.5rem; }
        .navbar-brand i { color: #ff4757; }
        
        /* Hero Section */
        .hero-section { background: linear-gradient(135deg, #ff4757, #ff6b81); padding: 40px 0; text-align: center; color: white; margin-bottom: 30px; }
        .hero-section h1 { font-size: 2.5rem; font-weight: bold; }
        .hero-section p { opacity: 0.9; }
        
        /* Live Badge Animation */
        .live-badge { display: inline-block; background: #ff4757; color: white; padding: 5px 15px; border-radius: 30px; font-size: 12px; font-weight: bold; margin-bottom: 15px; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.05); } 100% { opacity: 1; transform: scale(1); } }
        
        /* Live Score Widget Container */
        .live-score-container { background: rgba(255,255,255,0.05); border-radius: 20px; overflow: hidden; backdrop-filter: blur(10px); margin-bottom: 30px; transition: all 0.3s ease; }
        .live-score-container:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
        .live-score-widget { width: 100%; min-height: 700px; border: none; display: block; }
        
        /* Info Cards */
        .info-card { background: rgba(255,255,255,0.1); border-radius: 15px; padding: 20px; text-align: center; transition: all 0.3s; backdrop-filter: blur(10px); }
        .info-card:hover { background: rgba(255,255,255,0.15); transform: translateY(-5px); }
        .info-card i { font-size: 2rem; color: #ff4757; margin-bottom: 10px; }
        .info-card h3 { color: white; font-size: 1.2rem; margin-bottom: 10px; }
        .info-card p { color: rgba(255,255,255,0.7); font-size: 0.85rem; }
        
        /* League Badges */
        .league-badge { background: rgba(255,255,255,0.1); border-radius: 50px; padding: 8px 20px; margin: 5px; display: inline-block; color: white; font-size: 0.85rem; transition: all 0.3s; }
        .league-badge:hover { background: #ff4757; transform: translateY(-2px); cursor: pointer; }
        
        /* Upcoming Matches */
        .upcoming-card { background: rgba(255,255,255,0.08); border-radius: 15px; padding: 15px; margin-bottom: 15px; transition: all 0.3s; border-left: 3px solid #ff4757; }
        .upcoming-card:hover { background: rgba(255,255,255,0.12); transform: translateX(5px); }
        .match-time { color: #ff4757; font-weight: bold; font-size: 0.9rem; }
        .match-league { color: rgba(255,255,255,0.5); font-size: 0.75rem; }
        
        /* Footer */
        .footer { background: rgba(0,0,0,0.5); padding: 20px 0; margin-top: 40px; text-align: center; color: rgba(255,255,255,0.6); font-size: 0.85rem; }
        
        /* Responsive */
        @media (max-width: 768px) {
            .hero-section h1 { font-size: 1.8rem; }
            .live-score-widget { height: 550px; }
            .info-card { margin-bottom: 15px; }
            .league-badge { padding: 5px 12px; font-size: 0.75rem; }
        }
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
                    <li class="nav-item"><a class="nav-link active" href="live-scores.php"><i class="fas fa-clock"></i> Live Scores</a></li>
                    <li class="nav-item"><a class="nav-link" href="results.php"><i class="fas fa-chart-simple"></i> Results</a></li>
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
            <span class="live-badge"><i class="fas fa-circle" style="font-size: 10px;"></i> LIVE NOW</span>
            <h1><i class="fas fa-futbol"></i> Live Football Scores</h1>
            <p>Real-time updates from matches around the world</p>
        </div>
    </div>

    <div class="container">
        <!-- Live Score Widget -->
        <div class="live-score-container">
            <iframe 
                src="https://www.scorebat.com/embed/livescore/?token=<?php echo $scorebatEmbedToken; ?>&pref=%7B%22nomaxwidth%22%3Atrue%7D" 
                frameborder="0" 
                allowfullscreen 
                allow="autoplay; fullscreen" 
                style="display:block; width:100%; height:750px; border:none;" 
                class="live-score-widget" 
                title="Live Football Scores | SureWin">
            </iframe>
        </div>

        <!-- Features Section -->
        <div class="row mt-4">
            <div class="col-md-4 mb-3">
                <div class="info-card">
                    <i class="fas fa-chart-line"></i>
                    <h3>Real-Time Updates</h3>
                    <p>Live scores updated as they happen with instant notifications</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-card">
                    <i class="fas fa-trophy"></i>
                    <h3>All Major Leagues</h3>
                    <p>Premier League, La Liga, Serie A, Bundesliga, UCL & more</p>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="info-card">
                    <i class="fas fa-chart-simple"></i>
                    <h3>Match Stats</h3>
                    <p>Lineups, substitutions, goals, cards, and detailed statistics</p>
                </div>
            </div>
        </div>

        <!-- Featured Leagues -->
        <div class="text-center mt-4">
            <h4 class="text-white mb-3"><i class="fas fa-globe"></i> Featured Leagues</h4>
            <div>
                <span class="league-badge"><i class="fas fa-trophy"></i> Premier League</span>
                <span class="league-badge"><i class="fas fa-futbol"></i> La Liga</span>
                <span class="league-badge"><i class="fas fa-flag-checkered"></i> Serie A</span>
                <span class="league-badge"><i class="fas fa-futbol"></i> Bundesliga</span>
                <span class="league-badge"><i class="fas fa-star"></i> Ligue 1</span>
                <span class="league-badge"><i class="fas fa-champions"></i> UCL</span>
                <span class="league-badge"><i class="fas fa-globe"></i> UEL</span>
                <span class="league-badge"><i class="fas fa-trophy"></i> Eredivisie</span>
                <span class="league-badge"><i class="fas fa-futbol"></i> Primeira Liga</span>
                <span class="league-badge"><i class="fas fa-flag"></i> KPL</span>
            </div>
        </div>

        <!-- Upcoming Matches Preview -->
        <div class="row mt-5">
            <div class="col-12">
                <h4 class="text-white mb-3"><i class="fas fa-calendar-alt"></i> Today's Upcoming Matches</h4>
            </div>
            <div class="col-md-4">
                <div class="upcoming-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white">Arsenal vs Chelsea</div>
                            <div class="match-league">Premier League</div>
                        </div>
                        <div class="match-time">15:30</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="upcoming-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white">Juventus vs Roma</div>
                            <div class="match-league">Serie A</div>
                        </div>
                        <div class="match-time">18:00</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="upcoming-card">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="fw-bold text-white">Barcelona vs Atletico Madrid</div>
                            <div class="match-league">La Liga</div>
                        </div>
                        <div class="match-time">20:00</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="text-center mt-5">
            <div class="premium-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 20px; padding: 30px;">
                <h3 class="text-white">Want Winning Predictions?</h3>
                <p class="text-white">Join SureWin today and get access to premium tips with 87% accuracy</p>
                <a href="signup.php" class="btn btn-light btn-lg px-5">
                    <i class="fas fa-user-plus"></i> Join Now
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container text-center">
            <p><strong>SureWin Football Predictions</strong> - Where Smart Bets Win</p>
            <p>&copy; 2026 SureWin. All rights reserved.</p>
            <small>Live scores provided by ScoreBat | Data updates every 30 seconds</small>
        </div>
    </footer>

    <!-- ScoreBat SDK Script -->
    <script>
        (function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = 'https://www.scorebat.com/embed/embed.js?v=arrv';
            fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'scorebat-jssdk'));
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-refresh live scores every 30 seconds
        let refreshInterval = setInterval(function() {
            const iframe = document.querySelector('.live-score-widget');
            if (iframe && document.visibilityState === 'visible') {
                iframe.src = iframe.src;
            }
        }, 30000);
        
        // Stop refreshing when page is hidden (performance optimization)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                clearInterval(refreshInterval);
            } else {
                refreshInterval = setInterval(function() {
                    const iframe = document.querySelector('.live-score-widget');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                }, 30000);
            }
        });
        
        // Add timestamp to show last update
        function updateTimestamp() {
            const timestamp = new Date().toLocaleTimeString();
            const footer = document.querySelector('.footer small');
            if (footer) {
                footer.innerHTML = `Live scores provided by ScoreBat | Last updated: ${timestamp} | Auto-refresh every 30 seconds`;
            }
        }
        updateTimestamp();
        setInterval(updateTimestamp, 30000);
    </script>
</body>
</html>