<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost', 'root', '', 'student_info');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$matric_number = $_POST['matric_number'];
$password = $_POST['password'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$user_agent = $_SERVER['HTTP_USER_AGENT'];

// Function to validate input against SQL injection patterns
function validate_input($data) {
    $pattern = '/(\b(select|insert|update|delete|union|where|drop table|show tables|\;|--|\\|\|\|)\b)/i';
    if (preg_match($pattern, $data)) {
        return false; // Bad input detected
    }
    return true; // Input is safe
}

// Log the attempt with a potential SQL injection flag
function log_attempt($conn, $matric_number, $password, $ip_address, $user_agent, $login_success, $sql_injection_attempt) {
    try {
        $logStmt = $conn->prepare("INSERT INTO login_attempts (matric_number, password, login_time, ip_address, user_agent, success, sql_injection) VALUES (?, ?, NOW(), ?, ?, ?, ?)");
        $logStmt->bind_param("sssssb", $matric_number, $password, $ip_address, $user_agent, $login_success, $sql_injection_attempt);
        $logStmt->execute();
    } catch (Exception $e) {
        // Handle logging error (if any), without affecting user experience
    }
}

// Check for SQL injection in inputs
$sql_injection_attempt = false;
if (!validate_input($matric_number) || !validate_input($password)) {
    log_attempt($conn, $matric_number, $password, $ip_address, $user_agent, false, true);
    die('Invalid input detected.'); // Stop execution and provide feedback
}

// Attempt to authenticate user
$stmt = $conn->prepare("SELECT password FROM stuinfo WHERE matric_number = ?");
$stmt->bind_param("s", $matric_number);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$login_success = false;

if ($user && $password === $user['password']) {
    $_SESSION['matric_number'] = $matric_number;
    header('Location: dashboard.php');
    $login_success = true;
    exit();
} else {
    echo "Invalid matric number or password.";
}

// Log the attempt
log_attempt($conn, $matric_number, $password, $ip_address, $user_agent, $login_success, false);

$conn->close();
?>
