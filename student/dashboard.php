<?php
session_start();
if (!isset($_SESSION['matric_number'])) {
  header('Location: index.php');
  exit();
}

$conn = new mysqli('localhost', 'root', '', 'student_info');
$matric_number = $_SESSION['matric_number'];

$result = $conn->query("SELECT * FROM stuinfo WHERE matric_number='$matric_number'");
$user = $result->fetch_assoc();

echo "<h1>Welcome!!! Here's your information </h1>";
echo "<p>Gender: " . $user['gender'] . "</p>";
echo "<p>Race/Ethnicity: " . $user['race/ethnicity'] . "</p>";
echo "<p>Parent Education Level: " . $user['parental_level_of_education'] . "</p>";
echo "<p>Lunch: " . $user['lunch'] . "</p>";
echo "<p>Test Prep Score: " . $user['test_prep_score'] . "</p>";
echo "<p>Math Score: " . $user['math_score'] . "</p>";
echo "<p>Reading Score: " . $user['reading_score'] . "</p>";
echo "<p>Writing Score: " . $user['writing_score'] . "</p>";


$conn->close();
?>
