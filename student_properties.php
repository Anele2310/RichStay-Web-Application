<?php
session_start();

// --- DB connection ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rental_portal";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Fetch all properties ---
$sql = "SELECT * FROM properties1 ORDER BY created_at DESC";
$result = $conn->query($sql);
$properties = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $properties[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Properties | RichStay</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=BioRhyme:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%); min-height:100vh; }
.clay-card { background: rgba(255,255,255,0.95); border-radius:20px; box-shadow:0 25px 50px -12px rgba(0,0,0,0.15); transition: all 0.3s ease; backdrop-filter: blur(10px);}
.clay-card:hover { transform: translateY(-4px); box-shadow: 0 35px 70px -12px rgba(0,0,0,0.25);}
.clay-button { background: linear-gradient(135deg,#3b82f6 0%,#1d4ed8 100%); border-radius:16px; color:white; font-weight:600; transition: all 0.3s ease; padding:0.6rem 1.4rem; display:inline-block; text-decoration:none;}
.clay-button:hover { transform: translateY(-2px); background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%);}
h1,h2 { font-family: 'BioRhyme', serif; font-weight:700; }
.clay-input { background: #f9fafb; border-radius: 12px; border: 1px solid #d1d5db; outline: none; transition: all 0.2s ease;}
.clay-input:focus { border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.3);}
</style>
</head>
<body>

<header class="bg-gradient-to-r from-blue-700 to-purple-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay</h1>
        <nav class="flex space-x-4">
            <a href="student_applications.php" class="hover:text-red-300 flex items-center">Applications</a>
            <a href="logout.php" class="hover:text-red-300 flex items-center"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
        </nav>
    </div>
</header>

<main class="container mx-auto px-6 py-10">
    <h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center">Available Properties</h2>

    <!-- Filters -->
    <div class="clay-card p-8 mb-12">
        <div class="grid md:grid-cols-5 gap-6">
            <input type="text" id="filter-location" placeholder="Search location..." class="clay-input p-4">
            <select id="filter-type" class="clay-input p-4">
                <option value="">All Types</option>
                <option value="Apartment">Apartment</option>
                <option value="House">House</option>
                <option value="Shared Room">Shared Room</option>
                <option value="Studio">Studio</option>
            </select>
            <input type="number" id="filter-min-price" placeholder="Min Price (R)" class="clay-input p-4">
            <input type="number" id="filter-max-price" placeholder="Max Price (R)" class="clay-input p-4">
            <button onclick="filterProperties()" class="clay-button p-4 text-lg font-semibold">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
        </div>
    </div>

    <!-- Property Catalogue -->
    <div id="property-list" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <?php if (empty($properties)): ?>
            <p class="text-center text-gray-500">No properties found.</p>
        <?php else: ?>
            <?php foreach ($properties as $property): ?>
                <div class="clay-card p-6 property-card" 
                    data-location="<?php echo strtolower($property['area']); ?>" 
                    data-type="<?php echo strtolower($property['property_type']); ?>"
                    data-price="<?php echo $property['rent']; ?>">

                    <h3 class="text-xl font-bold mb-2 text-gray-800"><?php echo htmlspecialchars($property['title']); ?></h3>
                    <p class="text-gray-600 mb-1"><strong>Type:</strong> <?php echo $property['property_type']; ?></p>
                    <p class="text-gray-600 mb-1"><strong>Rent:</strong> R<?php echo number_format($property['rent'],2); ?></p>
                    <p class="text-gray-600 mb-1"><strong>Bedrooms:</strong> <?php echo $property['bedrooms']; ?> | <strong>Bathrooms:</strong> <?php echo $property['bathrooms']; ?></p>
                    <p class="text-gray-600 mb-1"><strong>Area:</strong> <?php echo $property['area']; ?></p>
                    <p class="text-gray-700 mb-4"><?php echo nl2br(htmlspecialchars($property['description'])); ?></p>

                    <a href="apply.php?property_id=<?php echo $property['property_id']; ?>" class="clay-button">Apply</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</main>

<!-- Google Map -->
<section class="container mx-auto px-6 pb-10">
    <h2 class="text-2xl font-semibold mb-4 text-gray-800 text-center">Explore Nearby Areas</h2>
    <div class="clay-card overflow-hidden">
        <iframe 
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3580.6602402282596!2d31.0218!3d-29.8587!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ef707cfa60c0b7d%3A0x2b2b8a880a8121a5!2sDurban!5e0!3m2!1sen!2sza!4v1697735500000!5m2!1sen!2sza" 
            width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<footer class="text-center text-gray-600 py-6">
    &copy; <?php echo date('Y'); ?> RichStay Student Portal
</footer>

<script>
function filterProperties() {
    const locationInput = document.getElementById('filter-location').value.toLowerCase();
    const typeInput = document.getElementById('filter-type').value.toLowerCase();
    const minPrice = parseFloat(document.getElementById('filter-min-price').value) || 0;
    const maxPrice = parseFloat(document.getElementById('filter-max-price').value) || Infinity;

    const cards = document.querySelectorAll('.property-card');

    cards.forEach(card => {
        const location = card.dataset.location;
        const type = card.dataset.type;
        const price = parseFloat(card.dataset.price);

        const matchesLocation = !locationInput || location.includes(locationInput);
        const matchesType = !typeInput || type === typeInput;
        const matchesPrice = price >= minPrice && price <= maxPrice;

        if (matchesLocation && matchesType && matchesPrice) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>

</body>
</html>
