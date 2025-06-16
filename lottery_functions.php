<?php
// lottery_functions.php
class LotteryManager {
    private $conn;
    private $roundDuration = 24 * 60 * 60; // 24 hours in seconds
    
    public function __construct($db_connection) {
        $this->conn = $db_connection;
    }

    // Get current active round or create new one
    public function getCurrentRound() {
        $sql = "SELECT * FROM lottery_rounds WHERE status = 'active' ORDER BY round_number DESC LIMIT 1";
        $result = mysqli_query($this->conn, $sql);

        if(mysqli_num_rows($result) == 0) {
            return $this->createNewRound();
        }

        $round = mysqli_fetch_assoc($result);
        
        // Check if round should end
        if(strtotime($round['end_time']) <= time()) {
            $this->endRound($round['round_number']);
            return $this->createNewRound();
        }

        return $round;
    }

    // Create new lottery round
    private function createNewRound() {
        $endTime = date('Y-m-d H:i:s', time() + $this->roundDuration);
        $sql = "INSERT INTO lottery_rounds (start_time, end_time, status) VALUES (NOW(), ?, 'active')";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $endTime);
            mysqli_stmt_execute($stmt);
            
            return $this->getCurrentRound(); // Get the newly created round
        }
        
        return false;
    }

    // Generate random numbers for a ticket
    public function generateTicketNumbers() {
        $numbers = array();
        while(count($numbers) < 6) {
            $num = rand(1, 49);
            if(!in_array($num, $numbers)) {
                $numbers[] = $num;
            }
        }
        sort($numbers);
        return $numbers;
    }

    // Purchase ticket
    public function purchaseTicket($userId, $tier, $price, $roundNumber) {
        $numbers = implode(',', $this->generateTicketNumbers());
        $ticketHash = hash('sha256', $userId . time() . rand());
        
        $sql = "INSERT INTO tickets (ticket_hash, user_id, round_number, tier, price, numbers, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'active')";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "siisds", 
                $ticketHash, $userId, $roundNumber, $tier, $price, $numbers);
            
            if(mysqli_stmt_execute($stmt)) {
                // Update round statistics
                $this->updateRoundStats($roundNumber, $price);
                return $ticketHash;
            }
        }
        
        return false;
    }

    // Update round statistics
    private function updateRoundStats($roundNumber, $ticketPrice) {
        $sql = "UPDATE lottery_rounds SET 
                prize_pool = prize_pool + ?, 
                participant_count = (SELECT COUNT(DISTINCT user_id) FROM tickets WHERE round_number = ?),
                tickets_sold = (SELECT COUNT(*) FROM tickets WHERE round_number = ?)
                WHERE round_number = ?";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "diii", 
                $ticketPrice, $roundNumber, $roundNumber, $roundNumber);
            mysqli_stmt_execute($stmt);
        }
    }

    // End round and determine winners
    public function endRound($roundNumber) {
        // Generate winning numbers
        $winningNumbers = $this->generateTicketNumbers();
        
        // Get all tickets for this round
        $sql = "SELECT t.*, u.username, u.wallet_address 
                FROM tickets t 
                JOIN users u ON t.user_id = u.id 
                WHERE t.round_number = ? AND t.status = 'active'";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $roundNumber);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            $winners = [
                'first' => [],
                'second' => [],
                'third' => []
            ];
            
            // Process each ticket
            while($ticket = mysqli_fetch_assoc($result)) {
                $ticketNumbers = explode(',', $ticket['numbers']);
                $matches = count(array_intersect($ticketNumbers, $winningNumbers));
                
                // Apply tier multipliers
                $tierMultipliers = [
                    'bronze' => 1,
                    'silver' => 5,
                    'gold' => 10
                ];
                
                $matches += $tierMultipliers[$ticket['tier']];
                
                // Determine winner tier
                if($matches >= 6) {
                    $winners['first'][] = $ticket;
                } elseif($matches == 5) {
                    $winners['second'][] = $ticket;
                } elseif($matches == 4) {
                    $winners['third'][] = $ticket;
                }
            }
            
            // Get round prize pool
            $round = mysqli_fetch_assoc(mysqli_query($this->conn, 
                "SELECT * FROM lottery_rounds WHERE round_number = $roundNumber"));
            
            $prizePool = $round['prize_pool'];
            
            // Calculate prize distribution
            $prizes = [
                'first' => $prizePool * 0.6,
                'second' => $prizePool * 0.3,
                'third' => $prizePool * 0.1
            ];
            
            // Distribute prizes
            foreach($winners as $tier => $tierWinners) {
                if(!empty($tierWinners)) {
                    $prizePerWinner = $prizes[$tier] / count($tierWinners);
                    foreach($tierWinners as $winner) {
                        // Update ticket status
                        mysqli_query($this->conn, 
                            "UPDATE tickets SET status = 'won' WHERE id = {$winner['id']}");
                        
                        // Record winning
                        $sql = "INSERT INTO winning_tickets (ticket_id, prize_amount, tier) VALUES (?, ?, ?)";
                        if($stmt = mysqli_prepare($this->conn, $sql)) {
                            mysqli_stmt_bind_param($stmt, "ids", 
                                $winner['id'], $prizePerWinner, $tier);
                            mysqli_stmt_execute($stmt);
                        }
                    }
                }
            }
            
            // Close the round
            $sql = "UPDATE lottery_rounds SET 
                    status = 'completed',
                    winning_numbers = ?,
                    winner_addresses = ?
                    WHERE round_number = ?";
            
            if($stmt = mysqli_prepare($this->conn, $sql)) {
                $winningNumbersStr = implode(',', $winningNumbers);
                $winnersJson = json_encode($winners);
                mysqli_stmt_bind_param($stmt, "ssi", 
                    $winningNumbersStr, $winnersJson, $roundNumber);
                mysqli_stmt_execute($stmt);
            }
            
            return true;
        }
        
        return false;
    }

    // Get user's tickets for current round
    public function getUserTickets($userId, $roundNumber) {
        $sql = "SELECT * FROM tickets WHERE user_id = ? AND round_number = ? ORDER BY purchase_date DESC";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ii", $userId, $roundNumber);
            mysqli_stmt_execute($stmt);
            return mysqli_stmt_get_result($stmt);
        }
        
        return false;
    }

    // Get round statistics
    public function getRoundStats($roundNumber) {
        $sql = "SELECT * FROM lottery_rounds WHERE round_number = ?";
        
        if($stmt = mysqli_prepare($this->conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "i", $roundNumber);
            mysqli_stmt_execute($stmt);
            return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        }
        
        return false;
    }
}
?>