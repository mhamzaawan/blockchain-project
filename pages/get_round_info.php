<?php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

try {
    $current_utc = gmdate('Y-m-d H:i:s');
    
    // Get current round info
    $sql = "SELECT * FROM lottery_rounds WHERE status = 'active' ORDER BY round_number DESC LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $timeRemaining = strtotime($row['end_time']) - strtotime($current_utc);
        $hours = floor($timeRemaining / 3600);
        $minutes = floor(($timeRemaining % 3600) / 60);
        $seconds = $timeRemaining % 60;
        
        $response = [
            'success' => true,
            'round_number' => $row['round_number'],
            'prize_pool' => number_format($row['prize_pool'], 3),
            'participant_count' => $row['participant_count'] ?? 0,
            'tickets_sold' => $row['tickets_sold'] ?? 0,
            'time_remaining' => sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds),
            'current_time' => $current_utc
        ];
        
        if ($timeRemaining <= 0 && $row['status'] === 'active') {
            // Close current round and create new one
            mysqli_query($conn, "UPDATE lottery_rounds SET status = 'completed' WHERE round_number = " . $row['round_number']);
            
            // Create new round
            $endTime = gmdate('Y-m-d H:i:s', strtotime('+24 hours'));
            $sql = "INSERT INTO lottery_rounds (start_time, end_time, status) VALUES (UTC_TIMESTAMP(), ?, 'active')";
            
            if($stmt = mysqli_prepare($conn, $sql)) {
                mysqli_stmt_bind_param($stmt, "s", $endTime);
                mysqli_stmt_execute($stmt);
                
                $response['round_number'] = mysqli_insert_id($conn);
                $response['prize_pool'] = '0.000';
                $response['participant_count'] = 0;
                $response['tickets_sold'] = 0;
                $response['time_remaining'] = '24:00:00';
            }
        }
    } else {
        // Create new round if none exists
        $endTime = gmdate('Y-m-d H:i:s', strtotime('+24 hours'));
        $sql = "INSERT INTO lottery_rounds (start_time, end_time, status) VALUES (UTC_TIMESTAMP(), ?, 'active')";
        
        if($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $endTime);
            mysqli_stmt_execute($stmt);
            
            $response = [
                'success' => true,
                'round_number' => mysqli_insert_id($conn),
                'prize_pool' => '0.000',
                'participant_count' => 0,
                'tickets_sold' => 0,
                'time_remaining' => '24:00:00',
                'current_time' => $current_utc
            ];
        }
    }
    
    echo json_encode($response);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>