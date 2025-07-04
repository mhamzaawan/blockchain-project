<?php
session_start();
$isLoggedIn = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$username = $isLoggedIn ? $_SESSION["username"] : "";
$currentDateTime = date("Y-m-d H:i:s"); // Get current UTC time
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/web3@1.5.2/dist/web3.min.js"></script>
    <title>CryptoLotto - Decentralized Lottery Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            padding: 20px 0;
            position: fixed;
            width: 100%;
            top: 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-links a:hover {
            color: #ffa726;
        }

        .nav-links a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -5px;
            left: 0;
            background: #ffa726;
            transition: width 0.3s ease;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .wallet-btn {
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 107, 107, 0.3);
        }

        .wallet-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.4);
        }

        /* Hero Section */
        .hero {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.05)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            animation: float 20s infinite linear;
            z-index: -1;
        }

        @keyframes float {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .hero-content h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: slideInUp 1s ease;
        }

        .hero-content p {
            font-size: 1.3rem;
            margin-bottom: 40px;
            opacity: 0.9;
            animation: slideInUp 1s ease 0.2s both;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            animation: slideInUp 1s ease 0.4s both;
        }

        .btn-primary, .btn-secondary {
            padding: 15px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            color: white;
            box-shadow: 0 6px 20px rgba(255, 107, 107, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stats Section */
        .stats {
            padding: 100px 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            text-align: center;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px 20px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .stat-label {
            font-size: 1.1rem;
            opacity: 0.8;
        }

        /* Features Section */
        .features {
            padding: 100px 0;
        }

        .features h2 {
            text-align: center;
            font-size: 3rem;
            margin-bottom: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .feature-card p {
            opacity: 0.8;
            line-height: 1.6;
        }

        /* Current Lottery */
        .current-lottery {
            padding: 100px 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .lottery-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 60px;
            border-radius: 30px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
        }

        .prize-pool {
            font-size: 4rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
        }

        .lottery-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .info-item {
            text-align: center;
        }

        .info-label {
            opacity: 0.7;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* Footer */
        footer {
            padding: 60px 0 30px;
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-section h3 {
            margin-bottom: 20px;
            color: #ffa726;
        }

        .footer-section a {
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            display: block;
            margin-bottom: 10px;
            transition: color 0.3s ease;
        }

        .footer-section a:hover {
            color: white;
        }

        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 30px;
            opacity: 0.7;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .hero-content h1 {
                font-size: 2.5rem;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }

            .stats-grid,
            .features-grid {
                grid-template-columns: 1fr;
            }

            .lottery-card {
                padding: 40px 20px;
            }

            .prize-pool {
                font-size: 2.5rem;
            }

            .lottery-info {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Animations */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .countdown {
            font-family: 'Courier New', monospace;
            font-size: 2rem;
            color: #ffa726;
        }

        .auth-buttons {
    display: flex;
    gap: 10px;
    align-items: center;
}

.auth-buttons a {
    text-decoration: none;
}

.welcome-user {
    background: linear-gradient(45deg, #4CAF50, #45a049);
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    color: white;
    font-weight: bold;
}

/* Add to your existing style section */
.modal {
    display: none;
    position: fixed;
    z-index: 1001;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
}

.modal-content {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    margin: 15% auto;
    padding: 40px;
    border-radius: 20px;
    width: 90%;
    max-width: 500px;
    text-align: center;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.1);
    animation: modalSlideDown 0.3s ease-out;
}

@keyframes modalSlideDown {
    from {
        transform: translateY(-100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

.modal-content h2 {
    color: #ffa726;
    margin-bottom: 20px;
}

.modal-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
}

.modal-btn {
    padding: 12px 24px;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.modal-btn.primary {
    background: linear-gradient(45deg, #4CAF50, #45a049);
    color: white;
}

.modal-btn.secondary {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.modal-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.time-display {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background: rgba(0, 0, 0, 0.5);
    padding: 10px 20px;
    border-radius: 25px;
    backdrop-filter: blur(10px);
    color: white;
    font-size: 0.9rem;
    z-index: 1000;
}
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
    <div class="logo">🎰 CryptoLotto</div>
    <ul class="nav-links">
        <li><a href="#home">Home</a></li>
        <li><a href="pages/lottery.php" onclick="checkLoginAndRedirect(event)">Lottery</a></li>
        <li><a href="pages/history.html">History</a></li>
        <li><a href="pages/referrals.html">Referrals</a></li>
        <li><a href="pages/winners.html">Winners</a></li>
    </ul>
    <div class="auth-buttons" style="display: flex; gap: 10px; align-items: center;">
    <?php if($isLoggedIn): ?>
        <span class="wallet-btn" style="background: linear-gradient(45deg, #4CAF50, #45a049);">Welcome, 
            <?php echo htmlspecialchars($username); ?>
        </span>
        <a href="logout.php" class="wallet-btn" style="background: linear-gradient(45deg, #ff6b6b, #ffa726);">Logout</a>
    <?php else: ?>
        <a href="register.php" class="wallet-btn" style="background: linear-gradient(45deg, #4CAF50, #45a049);">Register</a>
        <a href="login.php" class="wallet-btn">Login</a>
    <?php endif; ?>
    <button class="wallet-btn" id="connectWallet">Connect Wallet</button>
</div>
</nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="hero-content">
            <h1>Win Big with CryptoLotto</h1>
            <p>The most transparent and fair decentralized lottery on the blockchain. Multiple winners, tiered pricing, and automated draws!</p>
            <div class="cta-buttons">
                <a href="pages/lottery.html" class="btn-primary" onclick="checkLoginAndRedirect(event)">
    🎫 Buy Tickets Now
</a>
                <a href="#features" class="btn-secondary">
                    📖 Learn More
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalPrizePool">152.4</div>
                    <div class="stat-label">ETH Total Prizes Won</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalPlayers">8,547</div>
                    <div class="stat-label">Total Players</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="totalRounds">89</div>
                    <div class="stat-label">Lottery Rounds</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="charityDonated">18.2</div>
                    <div class="stat-label">ETH Donated to Charity</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Current Lottery -->
    <section class="current-lottery">
        <div class="container">
            <div class="lottery-card pulse">
                <h2>Current Lottery Round #90</h2>
                <div class="prize-pool" id="currentPrizePool">45.8 ETH</div>
                <p>Current Prize Pool</p>
                
                <div class="lottery-info">
                    <div class="info-item">
                        <div class="info-label">Participants</div>
                        <div class="info-value" id="currentParticipants">234</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Time Left</div>
                        <div class="info-value countdown" id="timeLeft">02:15:43</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Ticket Price</div>
                        <div class="info-value">0.01 ETH</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Winners</div>
                        <div class="info-value">3</div>
                    </div>
                </div>

<a href="pages/lottery.html" class="btn-primary" onclick="checkLoginAndRedirect(event)">
    🎫 Join This Round
</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="container">
            <h2>Why Choose CryptoLotto?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">🎯</div>
                    <h3>Multiple Winners</h3>
                    <p>Every round has multiple winners with different prize tiers (60%, 30%, 10%) ensuring more players win!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💎</div>
                    <h3>NFT Tickets</h3>
                    <p>Your lottery tickets are minted as NFTs, giving you permanent ownership and potential collectible value.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🔗</div>
                    <h3>Referral Rewards</h3>
                    <p>Earn rewards by referring friends. Build your network and earn passive income from referrals.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">🎲</div>
                    <h3>Provably Fair</h3>
                    <p>Using Chainlink VRF for verifiable randomness. Every draw is transparent and auditable on-chain.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">💰</div>
                    <h3>Tiered Pricing</h3>
                    <p>Multiple ticket price tiers with different odds. Choose your risk level and potential rewards.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">❤️</div>
                    <h3>Charity Support</h3>
                    <p>10% of every prize pool automatically goes to charity. Play for fun while supporting good causes.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <a href="pages/lottery.html">Current Lottery</a>
                    <a href="pages/history.html">Lottery History</a>
                    <a href="pages/winners.html">Recent Winners</a>
                    <a href="pages/referrals.html">Referral Program</a>
                </div>
                <div class="footer-section">
                    <h3>Support</h3>
                    <a href="#">How to Play</a>
                    <a href="#">FAQ</a>
                    <a href="#">Contact Us</a>
                    <a href="#">Discord Community</a>
                </div>
                <div class="footer-section">
                    <h3>Legal</h3>
                    <a href="#">Terms of Service</a>
                    <a href="#">Privacy Policy</a>
                    <a href="#">Smart Contract</a>
                    <a href="#">Audit Report</a>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 CryptoLotto.</p>
            </div>
        </div>
    </footer>

    <script>
        // Wallet connection
        let web3, userAccount, lotteryContract;



// Initialize Web3 and Connect Wallet
async function connectWallet() {
    if (typeof window.ethereum !== 'undefined') {
        try {
            // Request account access
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            userAccount = accounts[0];
            
            // Initialize Web3
            web3 = new Web3(window.ethereum);
            
            // Update button text and style
            const walletBtn = document.getElementById('connectWallet');
            walletBtn.textContent = `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
            walletBtn.style.background = 'linear-gradient(45deg, #4CAF50, #45a049)';
            
            // Get and display wallet balance
            const balance = await web3.eth.getBalance(userAccount);
            const ethBalance = web3.utils.fromWei(balance, 'ether');
            console.log(`Wallet connected! Balance: ${ethBalance} ETH`);
            
            // Get the current network
            const networkId = await web3.eth.net.getId();
            console.log(`Connected to network ID: ${networkId}`);
            
        } catch (error) {
            console.error('Error connecting wallet:', error);
            alert('Failed to connect wallet. Please try again.');
        }
    } else {
        alert('Please install MetaMask to use this dApp!');
    }
}

// Handle account changes
if (typeof window.ethereum !== 'undefined') {
    window.ethereum.on('accountsChanged', async (accounts) => {
        if (accounts.length === 0) {
            // User disconnected their wallet
            document.getElementById('connectWallet').textContent = 'Connect Wallet';
            document.getElementById('connectWallet').style.background = 'linear-gradient(45deg, #ff6b6b, #ffa726)';
            userAccount = null;
        } else {
            // User switched accounts
            userAccount = accounts[0];
            document.getElementById('connectWallet').textContent = 
                `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
        }
    });

    // Handle network changes
    window.ethereum.on('chainChanged', (_chainId) => {
        window.location.reload();
    });
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('connectWallet').addEventListener('click', connectWallet);
});

        document.getElementById('connectWallet').addEventListener('click', async () => {
            if (typeof window.ethereum !== 'undefined') {
                try {
                    const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                    userAccount = accounts[0];
                    document.getElementById('connectWallet').textContent = `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
                    document.getElementById('connectWallet').style.background = 'linear-gradient(45deg, #4CAF50, #45a049)';
                } catch (error) {
                    console.error('Error connecting wallet:', error);
                }
            } else {
                alert('Please install MetaMask to use this dApp!');
            }
        });

        // Countdown timer
        function updateCountdown() {
            const countdownElement = document.getElementById('timeLeft');
            let timeLeft = parseInt(localStorage.getItem('lotteryTimeLeft') || '7863'); // Default 2:11:03
            
            const hours = Math.floor(timeLeft / 3600);
            const minutes = Math.floor((timeLeft % 3600) / 60);
            const seconds = timeLeft % 60;
            
            countdownElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            
            if (timeLeft > 0) {
                timeLeft--;
                localStorage.setItem('lotteryTimeLeft', timeLeft.toString());
            } else {
                countdownElement.textContent = '00:00:00';
                countdownElement.style.color = '#ff6b6b';
            }
        }

        // Update stats periodically
        function updateStats() {
            const prizePool = document.getElementById('currentPrizePool');
            const participants = document.getElementById('currentParticipants');
            
            // Simulate real-time updates
            const currentPool = parseFloat(prizePool.textContent);
            const newPool = currentPool + (Math.random() * 0.1);
            prizePool.textContent = `${newPool.toFixed(1)} ETH`;
            
            const currentParticipants = parseInt(participants.textContent);
            if (Math.random() < 0.3) { // 30% chance to add participant
                participants.textContent = currentParticipants + 1;
            }
        }

        // Initialize
        setInterval(updateCountdown, 1000);
        setInterval(updateStats, 5000);
        
        // Smooth scrolling
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Header scroll effect
        window.addEventListener('scroll', () => {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(0, 0, 0, 0.3)';
            } else {
                header.style.background = 'rgba(0, 0, 0, 0.1)';
            }
        });

        // Auto-update wallet connection status
        if (typeof window.ethereum !== 'undefined') {
            window.ethereum.on('accountsChanged', (accounts) => {
                if (accounts.length === 0) {
                    document.getElementById('connectWallet').textContent = 'Connect Wallet';
                    document.getElementById('connectWallet').style.background = 'linear-gradient(45deg, #ff6b6b, #ffa726)';
                } else {
                    userAccount = accounts[0];
                    document.getElementById('connectWallet').textContent = `${userAccount.substring(0, 6)}...${userAccount.substring(38)}`;
                }
            });
        }

        // Add to your existing script section
function checkLoginAndRedirect(event) {
    const isLoggedIn = <?php echo $isLoggedIn ? 'true' : 'false'; ?>;
    if (!isLoggedIn) {
        event.preventDefault();
        document.getElementById('loginModal').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('loginModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('loginModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}

// Update current time every second
function updateTime() {
    const timeDisplay = document.querySelector('.time-display');
    const now = new Date();
    const utcString = now.toISOString().replace('T', ' ').slice(0, 19);
    timeDisplay.textContent = utcString + ' UTC';
}

setInterval(updateTime, 1000);
    </script>

    <!-- Login Required Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <h2>🔒 Login Required</h2>
        <p>Please login to buy lottery tickets!</p>
        <div class="modal-buttons">
            <button class="modal-btn primary" onclick="window.location.href='login.php'">Login Now</button>
            <button class="modal-btn secondary" onclick="closeModal()">Cancel</button>
        </div>
    </div>
</div>

<!-- Time Display -->
<div class="time-display">
    <?php echo $currentDateTime; ?> UTC
</div>
</body>
</html>