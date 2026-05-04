<?php
// student_applications.php
session_start();

// --- Database connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// --- Handle form submission ---
$studentIdInput = $_POST['studentId'] ?? '';
$applications = [];

if ($studentIdInput) {
    $sql = "
        SELECT a.*, p.title AS property_title, p.rent, p.area
        FROM applications a
        JOIN properties1 p ON a.property_id = p.property_id
        WHERE a.studentId = ?
        ORDER BY a.created_at DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $studentIdInput);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Applications | RichStay</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<style>
body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f3f4f6, #e5e7eb); }
.clay-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    padding: 2rem;
    margin: 2rem auto;
    max-width: 1200px;
}
.table-header { font-weight: bold; background: #e5e7eb; padding: 0.75rem 1rem; }
.table-row { border-bottom: 1px solid #ddd; }
.status-badge { display:inline-block; padding:0.25rem 0.75rem; border-radius:12px; font-weight:600; color:white; text-align:center;}
.status-Pending { background-color: #fbbf24; }
.status-Approved { background-color: #10b981; }
.status-Denied { background-color: #ef4444; }
.clay-input { border-radius: 12px; border:1px solid #d1d5db; padding:0.5rem 1rem; width:300px; outline:none; transition: all 0.2s ease;}
.clay-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.3);}
.clay-button { background: linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%); border-radius:16px; color:white; font-weight:600; transition: all 0.3s ease; padding:0.6rem 1.4rem; display:inline-block; text-decoration:none;}
.clay-button:hover { transform: translateY(-2px); background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%);}
</style>
</head>
<body>

<header class="bg-gradient-to-r from-blue-700 to-purple-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay</h1>
        <nav class="flex space-x-4">
            <a href="student_properties.php" class="hover:text-red-300 flex items-center">Properties</a>
            <a href="logout.php" class="hover:text-red-300 flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>
</header>

<main class="container mx-auto px-6 py-10">
<h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center">My Applications</h2>

<!-- Student ID Form -->
<div class="text-center mb-8">
    <form method="POST">
        <input type="text" name="studentId" placeholder="Enter your student number" class="clay-input mr-4" required>
        <button type="submit" class="clay-button">View Applications</button>
    </form>
</div>

<?php if($studentIdInput): ?>
<div class="clay-card overflow-x-auto">
<table class="w-full">
    <thead>
        <tr>
            <th class="table-header">Property</th>
            <th class="table-header">Rent</th>
            <th class="table-header">Area</th>
            <th class="table-header">Full Name</th>
            <th class="table-header">Phone</th>
            <th class="table-header">Institution</th>
            <th class="table-header">Student ID</th>
            <th class="table-header">Income</th>
            <th class="table-header">Income Source</th>
            <th class="table-header">Status</th>
            <th class="table-header">Submitted On</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($applications)): ?>
        <?php foreach($applications as $row): ?>
            <tr class="table-row">
                <td class="p-2"><?php echo htmlspecialchars($row['property_title']); ?></td>
                <td class="p-2">R<?php echo number_format($row['rent']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['area']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['fullName']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['phone']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['institution']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['studentId']); ?></td>
                <td class="p-2">R<?php echo number_format($row['income']); ?></td>
                <td class="p-2"><?php echo htmlspecialchars($row['incomeSource']); ?></td>
                <td class="p-2">
                    <span class="status-badge status-<?php echo $row['status']; ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </td>
                <td class="p-2"><?php echo date("d M Y H:i", strtotime($row['created_at'])); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="11" class="p-4 text-center text-gray-500">No applications found for this student number.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
</main>

<footer class="text-center text-gray-600 py-6">
    &copy; <?php echo date('Y'); ?> RichStay Student Portal
</footer>

</body>
</html>
