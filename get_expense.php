<?php
// get_expenses.php
session_start();
require_once 'db_config.php';

$response = ['success' => false, 'message' => 'Could not fetch expenses.', 'data' => []];

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || !isset($_SESSION["id"])) {
    $response['message'] = 'User not logged in.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION["id"];

// Fetch expenses ordered by date (most recent first)
$sql = "SELECT id, description, amount, DATE_FORMAT(expense_date, '%Y-%m-%d') as expense_date_formatted
        FROM expenses
        WHERE user_id = :user_id
        ORDER BY expense_date DESC, created_at DESC"; // Order by date, then creation time

try {
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $expenses = $stmt->fetchAll();
            $response['success'] = true;
            $response['message'] = 'Expenses fetched successfully.';
            $response['data'] = $expenses; // Contains array of expense objects
        } else {
            $response['message'] = 'Failed to execute query.';
            error_log("Get Expenses Error: Could not execute select statement.");
        }
        unset($stmt);
    } else {
         $response['message'] = 'Database error (prepare). Please try again.';
         error_log("Get Expenses Error: Could not prepare select statement.");
    }
} catch (PDOException $e) {
    $response['message'] = 'Database error. Please try again later.';
    error_log("Get Expenses PDOException: " . $e->getMessage());
}

unset($pdo); // Close connection

header('Content-Type: application/json');
echo json_encode($response);
exit;
?>