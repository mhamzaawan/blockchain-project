<?php
// Put this in config.php or a separate setup.php file
function setupLotteryTables($conn) {
    // Create lottery_rounds table
    $sql = "CREATE TABLE IF NOT EXISTS lottery_rounds (
        round_number INT AUTO_INCREMENT PRIMARY KEY,
        start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        end_time TIMESTAMP,
        prize_pool DECIMAL(18,8) DEFAULT 0,
        participant_count INT DEFAULT 0,
        tickets_sold INT DEFAULT 0,
        status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
        winning_numbers VARCHAR(255),
        winner_addresses JSON
    )";
    mysqli_query($conn, $sql);

    // Create tickets table if not exists
    $sql = "CREATE TABLE IF NOT EXISTS tickets (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ticket_hash VARCHAR(66) NOT NULL,
        user_id INT,
        round_number INT,
        tier VARCHAR(20),
        price DECIMAL(18,8),
        numbers VARCHAR(255),
        status ENUM('active', 'won', 'lost') DEFAULT 'active',
        transaction_hash VARCHAR(66),
        purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (round_number) REFERENCES lottery_rounds(round_number)
    )";
    mysqli_query($conn, $sql);

    // Check if we have an active round
    $sql = "SELECT COUNT(*) as count FROM lottery_rounds WHERE status = 'active'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    if ($row['count'] == 0) {
        // Create initial round
        $endTime = gmdate('Y-m-d H:i:s', strtotime('+24 hours'));
        $sql = "INSERT INTO lottery_rounds (start_time, end_time, status) 
                VALUES (UTC_TIMESTAMP(), ?, 'active')";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $endTime);
            mysqli_stmt_execute($stmt);
        }
    }
}

// Call this function when setting up
setupLotteryTables($conn);
?>