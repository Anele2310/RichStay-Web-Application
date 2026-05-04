<?php
session_start();
$owner_id = $_SESSION['user_id']; // Logged-in landlord

// --- DB connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Fetch properties added by this owner ---
$sql = "SELECT * FROM properties1 WHERE landlord_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
$properties = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// --- Fetch application counts for each property ---
$app_counts = [];
if (!empty($properties)) {
    $ids = array_column($properties, 'property_id');
    $in = implode(',', array_fill(0, count($ids), '?'));

    // Build dynamic query
    $types = str_repeat('i', count($ids));
    $sql_app = "SELECT property_id, COUNT(*) as app_count FROM applications WHERE property_id IN ($in) GROUP BY property_id";
    $stmt_app = $conn->prepare($sql_app);

    // Bind dynamically
    $stmt_app->bind_param($types, ...$ids);
    $stmt_app->execute();
    $res_app = $stmt_app->get_result();
    while ($row = $res_app->fetch_assoc()) {
        $app_counts[$row['property_id']] = $row['app_count'];
    }
    $stmt_app->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Owner Properties | RichStay</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=BioRhyme:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); min-height:100vh; }
.clay-card { background: rgba(255,255,255,0.95); border-radius:20px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.15); transition: all 0.3s ease; backdrop-filter: blur(10px);}
.clay-card:hover { transform: translateY(-4px); box-shadow: 0 35px 70px -12px rgba(0,0,0,0.25);}
.clay-button { background: linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%); border-radius:16px; color:white; font-weight:600; transition: all 0.3s ease; padding:0.6rem 1.4rem; display:inline-block; text-decoration:none;}
.clay-button:hover { transform: translateY(-2px); background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%);}
.delete-btn { background: linear-gradient(135deg,#ef4444 0%,#b91c1c 100%); border-radius:16px; color:white; font-weight:600; transition: all 0.3s ease; padding:0.6rem 1.4rem; display:inline-block; text-decoration:none;}
.delete-btn:hover { transform: translateY(-2px); background: linear-gradient(135deg,#dc2626 0%,#991b1b 100%);}
h1,h2 { font-family: 'BioRhyme', serif; font-weight:700; }
.app-badge { background: #2563eb; color: white; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; margin-bottom: 0.5rem; display:inline-block;}
</style>
<script>
function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this property and all its applications?")) {
        window.location.href = "delete_property.php?id=" + id;
    }
}
</script>
</head>
<body>

<header class="bg-gradient-to-r from-blue-700 to-purple-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay</h1>
        <nav class="flex space-x-4">
            <a href="ownerru.php" class="font-semibold underline">Home</a>
            <a href="add_property.php" class="hover:text-blue-200">Add Property</a>
            <a href="logout.php" class="hover:text-red-300 flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>
</header>

<main class="container mx-auto px-6 py-10">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center">Your Properties</h2>

    <?php if (empty($properties)): ?>
        <p class="text-center text-gray-500">You haven't added any properties yet. <a href="add_property.php" class="text-blue-600 underline">Add one now</a>.</p>
    <?php else: ?>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($properties as $property): ?>
                <div class="clay-card p-6 relative">
                    <?php $count = $app_counts[$property['property_id']] ?? 0; ?>
                    <span class="app-badge"><?php echo $count; ?> Applications</span>
                    
                    <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($property['title']); ?></h3>
                    <p class="text-gray-600 mb-1"><strong>Type:</strong> <?php echo $property['property_type']; ?></p>
                    <p class="text-gray-600 mb-1"><strong>Rent:</strong> R<?php echo number_format($property['rent'],2); ?></p>
                    <p class="text-gray-600 mb-1"><strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?> | <strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?></p>
                    <p class="text-gray-600 mb-1"><strong>Area:</strong> <?php echo $property['area']; ?> | <strong>Distance:</strong> <?php echo $property['distance']; ?></p>
                    <p class="text-gray-600 mb-3"><strong>Size:</strong> <?php echo $property['square_meters']; ?> m²</p>
                    <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>

                    <div class="flex space-x-3">
                        <a href="owner_applications.php" class="clay-button">View Applications</a>
                        <button class="delete-btn" onclick="confirmDelete(<?php echo $property['property_id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<footer class="text-center text-gray-600 py-6">
    &copy; <?php echo date('Y'); ?> RichStay Owner Portal
</footer>

</body>
</html>
