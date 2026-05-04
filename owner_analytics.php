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

// --- Owner session ---
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access");
}
$owner_id = $_SESSION['user_id'];

// --- Fetch property report data ---
$sql = "
SELECT 
    p.property_id,
    p.title,
    COUNT(a.application_id) AS total_applications,
    SUM(CASE WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) AS week_apps,
    SUM(CASE WHEN a.created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH) THEN 1 ELSE 0 END) AS month_apps,
    SUM(CASE WHEN a.status = 'Pending' THEN 1 ELSE 0 END) AS pending,
    SUM(CASE WHEN a.status = 'Approved' THEN 1 ELSE 0 END) AS approved,
    SUM(CASE WHEN a.status = 'Denied' THEN 1 ELSE 0 END) AS denied
FROM properties1 p
LEFT JOIN applications a ON p.property_id = a.property_id
WHERE p.landlord_id = ?
GROUP BY p.property_id, p.title
ORDER BY total_applications DESC;
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();

// --- Prepare data for Chart.js ---
$property_names = [];
$week_counts = [];
$month_counts = [];

while ($row = $result->fetch_assoc()) {
    $property_names[] = $row['title'];
    $week_counts[] = $row['week_apps'];
    $month_counts[] = $row['month_apps'];
    $report_rows[] = $row; // keep rows for table
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Property Reports | RichStay</title>
<link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body { background: #f3f4f6; font-family: 'Inter', sans-serif; }
.clay-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
    padding: 2rem;
    margin: 2rem auto;
    max-width: 1100px;
}
.table-header { font-weight: bold; background: #e5e7eb; padding: 0.75rem 1rem; }
.table-row { border-bottom: 1px solid #ddd; }
.status { font-weight: 600; }
.chart-container {
    max-width: 900px;
    margin: 3rem auto;
    background: white;
    border-radius: 16px;
    box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
    padding: 2rem;
}
@media (max-width: 1024px) {
    .chart-container { display: none; } /* Desktop only */
}
</style>
</head>
<body>

<header class="bg-gradient-to-r from-purple-700 to-indigo-600 text-white py-4 shadow-lg">
    <div class="container mx-auto flex justify-between items-center px-6">
        <h1 class="text-2xl font-bold">RichStay Owner Portal</h1>
        <a href="ownerru.php" class="hover:underline">Back to Home Page</a>
    </div>
</header>

<main class="container mx-auto px-6 py-10">
<h2 class="text-2xl font-semibold mb-6 text-gray-800 text-center">Property Application Reports</h2>

<div class="clay-card overflow-x-auto">
<table class="w-full">
    <thead>
        <tr>
            <th class="table-header">Property</th>
            <th class="table-header">Total</th>
            <th class="table-header">This Week</th>
            <th class="table-header">This Month</th>
            <th class="table-header text-yellow-600">Pending</th>
            <th class="table-header text-green-600">Approved</th>
            <th class="table-header text-red-600">Denied</th>
        </tr>
    </thead>
    <tbody>
    <?php if(!empty($report_rows)): ?>
        <?php foreach($report_rows as $row): ?>
            <tr class="table-row">
                <td class="p-2 font-semibold"><?php echo htmlspecialchars($row['title']); ?></td>
                <td class="p-2 text-center"><?php echo $row['total_applications']; ?></td>
                <td class="p-2 text-center"><?php echo $row['week_apps']; ?></td>
                <td class="p-2 text-center"><?php echo $row['month_apps']; ?></td>
                <td class="p-2 text-center text-yellow-600 font-bold"><?php echo $row['pending']; ?></td>
                <td class="p-2 text-center text-green-600 font-bold"><?php echo $row['approved']; ?></td>
                <td class="p-2 text-center text-red-600 font-bold"><?php echo $row['denied']; ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7" class="text-center p-4 text-gray-500">No applications found for your properties.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>

<!-- Chart Section -->
<div class="chart-container">
    <h3 class="text-xl font-semibold text-center mb-4 text-gray-800">Weekly vs Monthly Applications</h3>
    <canvas id="applicationsChart"></canvas>
</div>

</main>

<script>
const ctx = document.getElementById('applicationsChart').getContext('2d');
const applicationsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($property_names); ?>,
        datasets: [
            {
                label: 'This Week',
                data: <?php echo json_encode($week_counts); ?>,
                backgroundColor: 'rgba(99, 102, 241, 0.7)', // Indigo
                borderRadius: 8
            },
            {
                label: 'This Month',
                data: <?php echo json_encode($month_counts); ?>,
                backgroundColor: 'rgba(167, 139, 250, 0.7)', // Purple
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        },
        plugins: {
            legend: { position: 'bottom' },
            title: {
                display: true,
                text: 'Applications Overview (Desktop Only)',
                font: { size: 16 }
            }
        }
    }
});
</script>

</body>
</html>
