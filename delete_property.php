<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access.");
}

$owner_id = $_SESSION['user_id'];

$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $property_id = intval($_GET['id']);

    // First delete related applications
    $conn->query("DELETE FROM applications WHERE property_id = $property_id");

    // Then delete the property (only if owned by the logged-in landlord)
    $sql = "DELETE FROM properties1 WHERE property_id = ? AND landlord_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $property_id, $owner_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: owner_properties.php?deleted=1");
    exit();
} else {
    echo "Invalid request.";
}
?>
