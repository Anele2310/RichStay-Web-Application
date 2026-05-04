<?php
session_start();

// --- Database Connection ---
$host = "localhost";
$user = "root";
$password = ""; 
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Ensure owner is logged in ---
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];

// --- Handle status update ---
if (isset($_POST['update_status'])) {
    $app_id = $_POST['application_id'];
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE applications SET status=? WHERE application_id=?");
    $stmt->bind_param("si", $status, $app_id);
    $stmt->execute();
}

// --- Fetch all applications for this owner ---
$sql = "SELECT a.*, p.title AS property_title 
        FROM applications a 
        JOIN properties1 p ON a.property_id = p.property_id 
        WHERE p.landlord_id = ? 
        ORDER BY a.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Owner Applications</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<style>
.clay-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.8);
    transition: all 0.4s cubic-bezier(0.175,0.885,0.32,1.275);
    backdrop-filter: blur(10px);
    padding: 2rem;
    margin: 2rem auto;
    max-width: 1000px;
}
.clay-input, .clay-select {
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 16px;
    border: 2px solid rgba(59, 130, 246, 0.1);
    background: rgba(255,255,255,0.9);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}
.clay-select { appearance: none; }
.clay-button {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    font-weight: 600;
    border-radius: 20px;
    padding: 0.75rem 1.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}
.clay-button:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}
.table-header {
    font-weight: bold;
    background: #e5e7eb;
    padding: 0.75rem 1rem;
}
.table-row {
    border-bottom: 1px solid #ddd;
}
</style>
</head>
<body class="bg-gray-100 font-sans">

<header class="bg-gradient-to-r from-blue-700 to-purple-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay</h1>
        <nav class="flex space-x-4">
            <a href="owner_properties.php" class="hover:text-red-300 flex items-center">Properties</a>
            <a href="owner_analytics.php" class="hover:text-red-300 flex items-center">Analytics</a>
            <a href="logout.php" class="hover:text-red-300 flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>
</header>

<h1 class="text-3xl font-bold text-center mt-8 text-gray-800">Applications to Your Properties</h1>

<div class="clay-card overflow-x-auto">
<table class="w-full">
    <thead>
        <tr>
            <th class="table-header">Property</th>
            <th class="table-header">Student Name</th>
            <th class="table-header">Phone</th>
            <th class="table-header">Institution</th>
            <th class="table-header">Student ID</th>
            <th class="table-header">Income</th>
            <th class="table-header">Income Source</th>
            <th class="table-header">Status</th>
            <th class="table-header">Action</th>
        </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
        <tr class="table-row">
            <td class="p-2"><?php echo htmlspecialchars($row['property_title']); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($row['fullName']); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($row['institution']); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($row['studentId']); ?></td>
            <td class="p-2">R<?php echo number_format($row['income']); ?></td>
            <td class="p-2"><?php echo htmlspecialchars($row['incomeSource']); ?></td>
            <td class="p-2"><?php echo $row['status']; ?></td>
            <td class="p-2">
                <form method="POST">
                    <input type="hidden" name="application_id" value="<?php echo $row['application_id']; ?>">
                    <select name="status" class="clay-select">
                        <option value="Pending" <?php if($row['status']=="Pending") echo "selected"; ?>>Pending</option>
                        <option value="Approved" <?php if($row['status']=="Approved") echo "selected"; ?>>Approved</option>
                        <option value="Denied" <?php if($row['status']=="Denied") echo "selected"; ?>>Denied</option>
                    </select>
                    <button type="submit" name="update_status" class="clay-button w-full mt-1"><i class="fas fa-sync-alt mr-1"></i>Update</button>
                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

</body>
</html>
