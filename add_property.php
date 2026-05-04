<?php
session_start();

// --- Database connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Message handling ---
$message = "";

// --- Handle form submission ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $property_type = $_POST['property_type'];
    $rent = $_POST['rent'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $square_meters = $_POST['square_meters'];
    $address = $_POST['address'];
    $area = $_POST['area'];
    $distance = $_POST['distance'];
    $description = $_POST['description'];
    $landlord_id = $_SESSION['user_id']; // landlord logged in
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO properties1 
        (title, property_type, rent, bedrooms, bathrooms, square_meters, address, area, distance, description, landlord_id, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdiidssssss", 
        $title, $property_type, $rent, $bedrooms, $bathrooms, 
        $square_meters, $address, $area, $distance, $description, 
        $landlord_id, $created_at
    );

    if ($stmt->execute()) {
        $message = "Property added successfully!";
    } else {
        $message = "Error adding property: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Property | RichStay</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=BioRhyme:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    min-height: 100vh;
}
.clay-card {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}
.clay-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 35px 70px -12px rgba(0,0,0,0.25);
}
.clay-button {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border-radius: 16px;
    color: white;
    font-weight: 600;
    transition: all 0.3s ease;
    padding: 0.6rem 1.4rem;
}
.clay-button:hover {
    transform: translateY(-2px);
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
}
h1, h2 {
    font-family: 'BioRhyme', serif;
    font-weight: 700;
}
.message-success {
    background: #22c55e;
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 1rem;
}
.message-error {
    background: #ef4444;
    color: white;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    text-align: center;
    margin-bottom: 1rem;
}
</style>
</head>
<body>

<header class="bg-gradient-to-r from-blue-700 to-purple-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay</h1>
        <nav class="flex space-x-4">
            <a href="ownerru.php" class="hover:text-blue-200">Home</a>
            <a href="add_property.php" class="font-semibold underline">Add Property</a>
            <a href="logout.php" class="hover:text-red-300 flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>
</header>

<main class="container mx-auto px-6 py-10">
    <div class="max-w-2xl mx-auto clay-card p-8">

        <?php if ($message): ?>
            <div class="<?php echo strpos($message,'✅')===0?'message-success':'message-error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <h2 class="text-2xl text-gray-800 mb-6 text-center">Add a New Property</h2>

        <form id="propertyForm" method="POST" class="space-y-4" onsubmit="return validateForm()">
            <div>
                <label class="block text-gray-700">Title</label>
                <input type="text" name="title" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Property Type</label>
                <select name="property_type" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                    <option value="">Select Type</option>
                    <option value="Apartment">Apartment</option>
                    <option value="House">House</option>
                    <option value="Shared Room">Shared Room</option>
                    <option value="Studio">Studio</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Rent (R)</label>
                    <input type="number" step="0.01" name="rent" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700">Square Meters</label>
                    <input type="number" name="square_meters" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-gray-700">Bedrooms</label>
                    <input type="number" name="bedrooms" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-gray-700">Bathrooms</label>
                    <input type="number" name="bathrooms" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                </div>
            </div>

            <div>
                <label class="block text-gray-700">Address</label>
                <input type="text" name="address" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Area</label>
                <select name="area" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
                    <option value="">Select Area</option>
                    <option value="Near UKZN">Near UKZN</option>
                    <option value="Near DUT">Near DUT</option>
                    <option value="Durban Central">Durban Central</option>
                    <option value="Berea">Berea</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700">Distance in km</label>
                <input type="text" name="distance" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700">Description</label>
                <textarea name="description" rows="4" required class="w-full p-3 border rounded-lg focus:ring-2 focus:ring-blue-400"></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="clay-button w-full mt-4">Add Property</button>
            </div>
        </form>
    </div>
</main>

<footer class="text-center text-gray-600 py-6">
    &copy; <?php echo date('Y'); ?> RichStay Owner Portal
</footer>

<script>
function validateForm() {
    const form = document.forms["propertyForm"];
    const rent = parseFloat(form["rent"].value);
    const bedrooms = parseInt(form["bedrooms"].value);
    const bathrooms = parseInt(form["bathrooms"].value);
    const sqm = parseFloat(form["square_meters"].value);
    const distance = form["distance"].value.trim();

    if (isNaN(rent) || rent <= 0) {
        alert("Please enter a valid rent amount.");
        return false;
    }
    if (isNaN(bedrooms) || bedrooms <= 0) {
        alert("Please enter a valid number of bedrooms.");
        return false;
    }
    if (isNaN(bathrooms) || bathrooms <= 0) {
        alert("Please enter a valid number of bathrooms.");
        return false;
    }
    if (isNaN(sqm) || sqm <= 0) {
        alert("Please enter a valid square meter value.");
        return false;
    }
    // Distance: must be a decimal (up to 3 digits before and 2 digits after decimal)
    const distancePattern = /^\d{1,3}(\.\d{1,2})?$/;
    if (!distancePattern.test(distance)) {
        alert("Please enter a valid distance as a decimal (e.g., 5, 12.34, 123.45).");
        return false;
    }

    return true;
}
</script>

</script>

</body>
</html>
