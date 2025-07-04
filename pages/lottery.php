<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/web3@1.5.2/dist/web3.min.js"></script>
    <title>CryptoLotto - Buy Tickets</title>
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
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        header {
            padding: 20px 0;
            background: rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
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
        }

        .nav-links a:hover {
            color: #ffa726;
        }

        .wallet-status {
            background: linear-gradient(45deg, #4CAF50, #45a049);
            border: none;
            padding: 12px 24px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
        }

        /* Main Content */
        .main-content {
            padding: 40px 0;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 40px;
            align-items: start;
        }

        /* Lottery Info Card */
        .lottery-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .round-title {
            font-size: 2rem;
            margin-bottom: 20px;
            text-align: center;
        }

        .prize-display {
            text-align: center;
            margin-bottom: 30px;
        }

        .prize-amount {
            font-size: 3.5rem;
            font-weight: bold;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
        }

        .prize-label {
            opacity: 0.8;
            font-size: 1.1rem;
        }

        .lottery-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 15px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ffa726;
            margin-bottom: 5px;
        }

        .stat-label {
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .countdown-timer {
            text-align: center;
            margin-bottom: 30px;
        }

        .countdown {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            color: #ffa726;
            background: rgba(0, 0, 0, 0.2);
            padding: 15px 25px;
            border-radius: 15px;
            display: inline-block;
        }

        .winner-distribution {
            margin-top: 30px;
        }

        .winner-distribution h3 {
            margin-bottom: 15px;
            color: #ffa726;
        }

        .distribution-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .distribution-item:last-child {
            border-bottom: none;
        }

        /* Ticket Purchase Panel */
        .ticket-panel {
            background: rgba(255, 255, 255, 0.1);
            padding: 40px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 20px;
        }

        .panel-title {
            font-size: 1.8rem;
            margin-bottom: 30px;
            text-align: center;
            color: #ffa726;
        }

        .ticket-tiers {
            margin-bottom: 30px;
        }

        .tier-option {
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
        }

        .tier-option:hover {
            border-color: #ffa726;
            background: rgba(255, 167, 38, 0.1);
        }

        .tier-option.selected {
            border-color: #ffa726;
            background: rgba(255, 167, 38, 0.2);
        }

        .tier-option.selected::after {
            content: '✓';
            position: absolute;
            top: 10px;
            right: 15px;
            color: #ffa726;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .tier-name {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .tier-price {
            color: #ffa726;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }

        .tier-odds {
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .quantity-selector {
            margin-bottom: 30px;
        }

        .quantity-selector label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .qty-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .qty-btn:hover {
            background: #ffa726;
            border-color: #ffa726;
        }

        .qty-input {
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px;
            border-radius: 10px;
            text-align: center;
            width: 80px;
            font-size: 1.1rem;
        }

        .total-cost {
            background: rgba(0, 0, 0, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }

        .total-label {
            opacity: 0.7;
            margin-bottom: 5px;
        }

        .total-amount {
            font-size: 1.8rem;
            font-weight: bold;
            color: #ffa726;
        }

        .referral-section {
            margin-bottom: 30px;
        }

        .referral-input {
            width: 100%;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px;
            border-radius: 10px;
            margin-top: 10px;
        }

        .referral-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .buy-button {
            width: 100%;
            background: linear-gradient(45deg, #ff6b6b, #ffa726);
            border: none;
            padding: 15px;
            border-radius: 15px;
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }

        .buy-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 107, 0.4);
        }

        .buy-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .emergency-refund {
            width: 100%;
            background: rgba(255, 99, 99, 0.2);
            border: 1px solid #ff6363;
            color: #ff6363;
            padding: 12px;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .emergency-refund:hover {
            background: rgba(255, 99, 99, 0.3);
        }

        /* User Tickets Section */
        .user-tickets {
            margin-top: 40px;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .tickets-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .ticket-item {
            background: rgba(255, 255, 255, 0.05);
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .ticket-id {
            font-family: 'Courier New', monospace;
            color: #ffa726;
        }

        .ticket-tier {
            background: rgba(255, 167, 38, 0.2);
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8rem;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
        }

        .modal-content {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 15% auto;
            padding: 40px;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal h2 {
            margin-bottom: 20px;
            color: #ffa726;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            margin-top: -30px;
            margin-right: -20px;
        }

        .close:hover {
            color: white;
        }

        .success-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .transaction-hash {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
            margin: 20px 0;
            word-break: break-all;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .ticket-panel {
                position: static;
            }

            .lottery-stats {
                grid-template-columns: 1fr;
            }

            .nav-links {
                display: none;
            }

            .countdown {
                font-size: 1.8rem;
            }

            .prize-amount {
                font-size: 2.5rem;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(76, 175, 80, 0.9);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            transform: translateX(400px);
            transition: transform 0.3s ease;
            z-index: 1001;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.error {
            background: rgba(244, 67, 54, 0.9);
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <nav class="container">
            <div class="logo">🎰 CryptoLotto</div>
            <ul class="nav-links">
                <li><a href="../index.php">Home</a></li>
                <li><a href="lottery.php" style="color: #ffa726;">Lottery</a></li>
                <li><a href="history.html">History</a></li>
                <li><a href="referrals.html">Referrals</a></li>
                <li><a href="winners.html">Winners</a></li>
            </ul>
            <div class="wallet-status" id="walletStatus">0x1234...5678</div>
        </nav>
    </header>

    <!-- Main Content -->
    <div class="container main-content">
        <!-- Lottery Information -->
        <div class="lottery-info">
            <h1 class="round-title">🎊 Lottery Round #90</h1>
            
            <div class="prize-display">
                <div class="prize-amount" id="prizeAmount">45.8 ETH</div>
                <div class="prize-label">Total Prize Pool</div>
            </div>

            <div class="lottery-stats">
                <div class="stat-item">
                    <div class="stat-value" id="participantCount">234</div>
                    <div class="stat-label">Participants</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="ticketsSold">1,847</div>
                    <div class="stat-label">Tickets Sold</div>
                </div>
            </div>

            <div class="countdown-timer">
                <div class="countdown" id="countdown">02:15:43</div>
                <div style="margin-top: 10px; opacity: 0.8;">Time until draw</div>
            </div>

            <div class="winner-distribution">
                <h3>🏆 Prize Distribution</h3>
                <div class="distribution-item">
                    <span>🥇 1st Place (60%)</span>
                    <span id="firstPrize">27.5 ETH</span>
                </div>
                <div class="distribution-item">
                    <span>🥈 2nd Place (30%)</span>
                    <span id="secondPrize">13.7 ETH</span>
                </div>
                <div class="distribution-item">
                    <span>🥉 3rd Place (10%)</span>
                    <span id="thirdPrize">4.6 ETH</span>
                </div>
                <div class="distribution-item">
                    <span>❤️ Charity (10%)</span>
                    <span id="charityAmount">4.6 ETH</span>
                </div>
            </div>
        </div>

        <!-- Ticket Purchase Panel -->
        <div class="ticket-panel">
            <h2 class="panel-title">🎫 Buy Tickets</h2>
            
            <div class="ticket-tiers">
                <div class="tier-option selected" data-tier="bronze" data-price="0.01">
                    <div class="tier-name">🥉 Bronze Ticket</div>
                    <div class="tier-price">0.000 ETH</div>
                    <div class="tier-odds">Standard odds</div>
                </div>
                <div class="tier-option" data-tier="silver" data-price="0.05">
                    <div class="tier-name">🥈 Silver Ticket</div>
                    <div class="tier-price">0.05 ETH</div>
                    <div class="tier-odds">5x better odds</div>
                </div>
                <div class="tier-option" data-tier="gold" data-price="0.1">
                    <div class="tier-name">🥇 Gold Ticket</div>
                    <div class="tier-price">0.1 ETH</div>
                    <div class="tier-odds">10x better odds</div>
                </div>
            </div>

            <div class="quantity-selector">
                <label>Number of Tickets:</label>
                <div class="quantity-controls">
                    <button class="qty-btn" id="decreaseQty">-</button>
                    <input type="number" class="qty-input" id="ticketQuantity" value="1" min="1" max="100">
                    <button class="qty-btn" id="increaseQty">+</button>
                </div>
            </div>

            <div class="total-cost">
                <div class="total-label">Total Cost:</div>
                <div class="total-amount" id="totalCost">0.01 ETH</div>
            </div>

            <div class="referral-section">
                <label>🔗 Referral Code (Optional):</label>
                <input type="text" class="referral-input" id="referralCode" placeholder="Enter referral code to earn rewards">
            </div>

            <button class="buy-button" id="buyTickets">
                🎫 Buy Tickets
            </button>

            <button class="emergency-refund" id="emergencyRefund" style="display: none;">
                🚨 Emergency Refund
            </button>
        </div>
    </div>

    <!-- User Tickets Section -->
    <div class="container">
        <div class="user-tickets">
            <div class="tickets-header">
                <h3>🎫 Your Tickets for Round #90</h3>
                <span id="userTicketCount">3 tickets</span>
            </div>
            <div id="userTicketsList">
                <div class="ticket-item">
                    <div>
                        <span class="ticket-id">#0x1a2b...3c4d</span>
                        <span class="ticket-tier">Bronze</span>
                    </div>
                    <div>0.01 ETH</div>
                </div>
                <div class="ticket-item">
                    <div>
                        <span class="ticket-id">#0x5e6f...7g8h</span>
                        <span class="ticket-tier">Silver</span>
                    </div>
                    <div>0.05 ETH</div>
                </div>
                <div class="ticket-item">
                    <div>
                        <span class="ticket-id">#0x9i0j...1k2l</span>
                        <span class="ticket-tier">Bronze</span>
                    </div>
                    <div>0.01 ETH</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="success-icon">🎉</div>
            <h2>Tickets Purchased Successfully!</h2>
            <p>Your tickets have been minted as NFTs and recorded on the blockchain.</p>
            <div class="transaction-hash" id="txHash">
                Tx: 0x1234567890abcdef...
            </div>
            <p>Good luck in the draw! 🍀</p>
        </div>
    </div>

    <!-- Notification -->
    <div id="notification" class="notification"></div>

    <script>
        // Global variables
        let selectedTier = 'bronze';
        let selectedPrice = 0.000;
        let ticketQuantity = 1;
        let userTickets = [];

        // Add these global variables at the top of your script section
let web3;
let userAccount;

// Add this function to initialize Web3
async function initWeb3() {
    if (typeof window.ethereum !== 'undefined') {
        web3 = new Web3(window.ethereum);
        return true;
    }
    return false;
}

// Add this function to check wallet connection
async function checkWalletConnection() {
    if (!web3) {
        showNotification('Please connect your wallet first!', 'error');
        return false;
    }

    const accounts = await web3.eth.getAccounts();
    if (accounts.length === 0) {
        try {
            await window.ethereum.request({ method: 'eth_requestAccounts' });
            return true;
        } catch (error) {
            showNotification('Please connect your wallet!', 'error');
            return false;
        }
    }
    return true;
}

// Add this function to check balance
async function checkBalance(requiredAmount) {
    try {
        const accounts = await web3.eth.getAccounts();
        const balance = await web3.eth.getBalance(accounts[0]);
        const balanceInEth = web3.utils.fromWei(balance, 'ether');
        
        if (parseFloat(balanceInEth) < requiredAmount) {
            showNotification(`Insufficient balance. You need ${requiredAmount} ETH!`, 'error');
            return false;
        }
        return true;
    } catch (error) {
        console.error('Error checking balance:', error);
        showNotification('Error checking balance!', 'error');
        return false;
    }
}

        // Initialize
        document.addEventListener('DOMContentLoaded', async function() {
    // Initialize Web3
    await initWeb3();
    
    // Rest of your initialization code...
    initializePage();
    setupEventListeners();
    updateCountdown();
    setInterval(updateCountdown, 1000);
    setInterval(updateStats, 10000);
});

        function initializePage() {
            updateTotalCost();
            loadUserTickets();
        }

        function setupEventListeners() {
            // Tier selection
            document.querySelectorAll('.tier-option').forEach(option => {
                option.addEventListener('click', function() {
                    document.querySelectorAll('.tier-option').forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    selectedTier = this.dataset.tier;
                    selectedPrice = parseFloat(this.dataset.price);
                    updateTotalCost();
                });
            });

            // Quantity controls
            document.getElementById('decreaseQty').addEventListener('click', () => {
                const qtyInput = document.getElementById('ticketQuantity');
                if (qtyInput.value > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                    ticketQuantity = parseInt(qtyInput.value);
                    updateTotalCost();
                }
            });

            document.getElementById('increaseQty').addEventListener('click', () => {
                const qtyInput = document.getElementById('ticketQuantity');
                if (qtyInput.value < 100) {
                    qtyInput.value = parseInt(qtyInput.value) + 1;
                    ticketQuantity = parseInt(qtyInput.value);
                    updateTotalCost();
                }
            });

            document.getElementById('ticketQuantity').addEventListener('input', function() {
                ticketQuantity = parseInt(this.value) || 1;
                updateTotalCost();
            });

            // Buy tickets button
            document.getElementById('buyTickets').addEventListener('click', buyTickets);

            // Emergency refund
            document.getElementById('emergencyRefund').addEventListener('click', emergencyRefund);

            // Modal close
            document.querySelector('.close').addEventListener('click', () => {
                document.getElementById('successModal').style.display = 'none';
            });

            window.addEventListener('click', (event) => {
                const modal = document.getElementById('successModal');
                if (event.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }

        function updateTotalCost() {
            const total = (selectedPrice * ticketQuantity).toFixed(3);
            document.getElementById('totalCost').textContent = `${total} ETH`;
        }

        function updateCountdown() {
            const countdownElement = document.getElementById('countdown');
            let timeLeft = parseInt(localStorage.getItem('lotteryTimeLeft') || '7863');
            
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
                document.getElementById('buyTickets').disabled = true;
                document.getElementById('emergencyRefund').style.display = 'block';
            }
        }

        function updateStats() {
            // Simulate real-time updates
            const prizeAmount = document.getElementById('prizeAmount');
            const participantCount = document.getElementById('participantCount');
            const ticketsSold = document.getElementById('ticketsSold');
            
            const currentPrize = parseFloat(prizeAmount.textContent);
            const newPrize = currentPrize + (Math.random() * 0.1);
            prizeAmount.textContent = `${newPrize.toFixed(1)} ETH`;
            
            // Update prize distribution
            updatePrizeDistribution(newPrize);
            
            if (Math.random() < 0.3) {
                const currentParticipants = parseInt(participantCount.textContent);
                participantCount.textContent = currentParticipants + Math.floor(Math.random() * 3) + 1;
                
                const currentTickets = parseInt(ticketsSold.textContent.replace(',', ''));
                ticketsSold.textContent = (currentTickets + Math.floor(Math.random() * 5) + 1).toLocaleString();
            }
        }

        function updatePrizeDistribution(totalPrize) {
            document.getElementById('firstPrize').textContent = `${(totalPrize * 0.6).toFixed(1)} ETH`;
            document.getElementById('secondPrize').textContent = `${(totalPrize * 0.3).toFixed(1)} ETH`;
            document.getElementById('thirdPrize').textContent = `${(totalPrize * 0.1).toFixed(1)} ETH`;
            document.getElementById('charityAmount').textContent = `${(totalPrize * 0.1).toFixed(1)} ETH`;
        }

        async function buyTickets() {
    const button = document.getElementById('buyTickets');
    const originalText = button.textContent;
    const totalCost = selectedPrice * ticketQuantity;

    try {
        // Initialize Web3 if not initialized
        if (!web3) {
            const initialized = await initWeb3();
            if (!initialized) {
                showNotification('Please install MetaMask!', 'error');
                return;
            }
        }

        // Check wallet connection
        const isConnected = await checkWalletConnection();
        if (!isConnected) return;

        // Check balance
        const hasBalance = await checkBalance(totalCost);
        if (!hasBalance) return;

        // Show loading state
        button.innerHTML = '<span class="loading"></span> Processing...';
        button.disabled = true;

        // Get the current account
        const accounts = await web3.eth.getAccounts();
        const account = accounts[0];

        // Prepare the transaction
        const totalCostWei = web3.utils.toWei(totalCost.toString(), 'ether');

        // Send transaction
        try {
            const transaction = await web3.eth.sendTransaction({
                from: account,
                to: '0x2d4e6a7F88b067f3f4B27e8f190839b16528eE52', // Replace with your contract address
                value: totalCostWei,
                gas: 21000 * ticketQuantity // Adjust gas based on your contract
            });

            // Add tickets to user's collection
            for (let i = 0; i < ticketQuantity; i++) {
                const ticketId = generateTicketId();
                userTickets.push({
                    id: ticketId,
                    tier: selectedTier,
                    price: selectedPrice
                });
            }

            // Update UI
            loadUserTickets();

            // Show success modal with actual transaction hash
            document.getElementById('txHash').textContent = `Tx: ${transaction.transactionHash}`;
            document.getElementById('successModal').style.display = 'block';

            // Show notification
            showNotification('Tickets purchased successfully! 🎉', 'success');

            // Reset form
            document.getElementById('ticketQuantity').value = 1;
            ticketQuantity = 1;
            updateTotalCost();

        } catch (error) {
            console.error('Transaction error:', error);
            showNotification('Transaction failed: ' + error.message, 'error');
        }

    } catch (error) {
        console.error('Purchase error:', error);
        showNotification('Error: ' + error.message, 'error');
    } finally {
        button.textContent = originalText;
        button.disabled = false;
    }
}

        function loadUserTickets() {
            const ticketsList = document.getElementById('userTicketsList');
            const ticketCount = document.getElementById('userTicketCount');
            
            ticketCount.textContent = `${userTickets.length} tickets`;
            
            if (userTickets.length === 0) {
                ticketsList.innerHTML = '<p style="text-align: center; opacity: 0.7;">No tickets purchased yet</p>';
                return;
            }
            
            ticketsList.innerHTML = userTickets.map(ticket => `
                <div class="ticket-item">
                    <div>
                        <span class="ticket-id">#${ticket.id}</span>
                        <span class="ticket-tier">${ticket.tier.charAt(0).toUpperCase() + ticket.tier.slice(1)}</span>
                    </div>
                    <div>${ticket.price} ETH</div>
                </div>
            `).join('');
        }

        function generateTicketId() {
            return '0x' + Math.random().toString(16).substr(2, 8) + '...' + Math.random().toString(16).substr(2, 4);
        }

        function generateTxHash() {
            return '0x' + Math.random().toString(16).substr(2, 64);
        }

        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            notification.textContent = message;
            notification.className = `notification ${type} show`;
            
            setTimeout(() => {
                notification.classList.remove('show');
            }, 3000);
        }

        function emergencyRefund() {
            if (confirm('Are you sure you want to request an emergency refund? This will refund your entry fees.')) {
                showNotification('Emergency refund initiated. Please wait for processing.', 'success');
                // Implement emergency refund logic
            }
        }

        // Check if wallet is connected
        if (typeof window.ethereum !== 'undefined') {
            window.ethereum.request({ method: 'eth_accounts' })
                .then(accounts => {
                    if (accounts.length > 0) {
                        const account = accounts[0];
                        document.getElementById('walletStatus').textContent = `${account.substring(0, 6)}...${account.substring(38)}`;
                    }
                });
        }
    </script>
</body>
</html>