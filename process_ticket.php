<?php
// process_ticket.php
session_start();
require_once "config.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_SESSION["loggedin"]) || !$_SESSION["loggedin"]) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION["id"];
$tier = $data['tier'];
$quantity = $data['quantity'];
$price = $data['price'];
$transaction_hash = $data['transactionHash'];
$round_number = $data['roundNumber'];

try {
    // Start transaction
    mysqli_begin_transaction($conn);

    // Insert tickets
    $success = true;
    $ticket_ids = [];

    for ($i = 0; $i < $quantity; $i++) {
        // Generate random numbers for ticket
        $numbers = [];
        while (count($numbers) < 6) {
            $num = rand(1, 49);
            if (!in_array($num, $numbers)) {
                $numbers[] = $num;
            }
        }
        sort($numbers);
        $numbers_str = implode(',', $numbers);

        // Generate ticket hash
        $ticket_hash = hash('sha256', $user_id . time() . rand() . $i);

        $sql = "INSERT INTO tickets (ticket_hash, user_id, round_number, tier, price, numbers, status, transaction_hash, purchase_date) 
                VALUES (?, ?, ?, ?, ?, ?, 'active', ?, NOW())";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "siisdss", 
                $ticket_hash, $user_id, $round_number, $tier, $price, $numbers_str, $transaction_hash);

            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
                break;
            }

            $ticket_ids[] = [
                'id' => mysqli_insert_id($conn),
                'hash' => $ticket_hash,
                'numbers' => $numbers
            ];
        }
    }

    // Update round statistics
    if ($success) {
        $total_price = $price * $quantity;
        $sql = "UPDATE lottery_rounds SET 
                prize_pool = prize_pool + ?,
                participant_count = (SELECT COUNT(DISTINCT user_id) FROM tickets WHERE round_number = ?),
                tickets_sold = (SELECT COUNT(*) FROM tickets WHERE round_number = ?)
                WHERE round_number = ?";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "diii", 
                $total_price, $round_number, $round_number, $round_number);
            
            if (!mysqli_stmt_execute($stmt)) {
                $success = false;
            }
        }
    }

    if ($success) {
        mysqli_commit($conn);
        echo json_encode([
            'success' => true,
            'message' => 'Tickets purchased successfully',
            'tickets' => $ticket_ids
        ]);
    } else {
        mysqli_rollback($conn);
        echo json_encode([
            'success' => false,
            'message' => 'Error processing tickets'
        ]);
    }

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

mysqli_close($conn);
?>