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

// --- Ensure student logged in 
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// -- Handle form submission ---
if (isset($_POST['submit'])) {
    $fullName = $_POST['fullName'];
    $phone = $_POST['phone'];
    $institution = $_POST['institution'];
    $studentId = $_POST['studentId'];
    $income = $_POST['income'];
    $incomeSource = $_POST['incomeSource'];
    $property_id = $_POST['property_id'];
    $created_at = date('Y-m-d H:i:s');

    // File uploads
    $incomeProof = "uploads/" . basename($_FILES["incomeProof"]["name"]);
    $idCopy = "uploads/" . basename($_FILES["idCopy"]["name"]);

    move_uploaded_file($_FILES["incomeProof"]["tmp_name"], $incomeProof);
    move_uploaded_file($_FILES["idCopy"]["tmp_name"], $idCopy);

    $sql = "INSERT INTO applications 
            (fullName, phone, institution, studentId, income, incomeSource, incomeProof, idCopy, property_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssss", $fullName, $phone, $institution, $studentId, $income, $incomeSource, $incomeProof, $idCopy, $property_id, $created_at);

    if ($stmt->execute()) {
        echo "<script>alert('Application submitted successfully!'); window.location='student_applications.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Apply for Property</title>
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
    max-width: 700px;
}
.clay-input {
    width: 100%;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
    border-radius: 16px;
    border: 2px solid rgba(59, 130, 246, 0.1);
    background: rgba(255,255,255,0.9);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}
.clay-input:focus {
    border-color: #3b82f6;
    outline: none;
}
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
</style>
</head>
<body class="bg-gray-100 font-sans">

<h1 class="text-3xl font-bold text-center mt-8 text-gray-800">Apply for Property</h1>

<form method="POST" enctype="multipart/form-data" class="clay-card" id="applicationForm">
    <input type="hidden" name="property_id" value="<?php echo $_GET['property_id']; ?>">
    <input type="text" name="fullName" placeholder="Full Name" class="clay-input" required>
    <input type="text" name="phone" placeholder="Phone" class="clay-input" required>
    <input type="text" name="institution" placeholder="Institution" class="clay-input" required>
    <input type="text" name="studentId" placeholder="Student ID" class="clay-input" required>
    <input type="number" name="income" placeholder="Income" class="clay-input" required>
    <input type="text" name="incomeSource" placeholder="Income Source" class="clay-input" required>
    <label class="block mb-2 font-semibold">Upload Income Proof:</label>
    <input type="file" name="incomeProof" class="clay-input" required>
    <label class="block mb-2 font-semibold">Upload ID Copy:</label>
    <input type="file" name="idCopy" class="clay-input" required>
    <button type="submit" name="submit" class="clay-button mt-4 w-full text-lg"><i class="fas fa-paper-plane mr-2"></i>Submit Application</button>
</form>

<script>
document.getElementById("applicationForm").addEventListener("submit", function(e) {
    const fullName = this.fullName.value.trim();
    const phone = this.phone.value.trim();
    const institution = this.institution.value.trim();
    const studentId = this.studentId.value.trim();
    const income = parseFloat(this.income.value.trim());
    const incomeSource = this.incomeSource.value.trim();
    const incomeProof = this.incomeProof.files[0];
    const idCopy = this.idCopy.files[0];

    // Full Name: letters and spaces
    if (!/^[a-zA-Z\s]+$/.test(fullName)) {
        alert("Please enter a valid full name (letters and spaces only).");
        e.preventDefault(); return false;
    }

    // Phone: digits, optionally + at start, 7-15 digits
    if (!/^\+?\d{7,15}$/.test(phone)) {
        alert("Please enter a valid phone number (7-15 digits, optional +).");
        e.preventDefault(); return false;
    }

    // Institution: letters, spaces
    if (!/^[a-zA-Z\s]+$/.test(institution)) {
        alert("Please enter a valid institution name.");
        e.preventDefault(); return false;
    }

    // Student ID: alphanumeric
    if (!/^[a-zA-Z0-9]+$/.test(studentId)) {
        alert("Student ID must be alphanumeric.");
        e.preventDefault(); return false;
    }

    // Income: positive number
    if (isNaN(income) || income <= 0) {
        alert("Please enter a valid income amount.");
        e.preventDefault(); return false;
    }

    // Income Source: letters and spaces
    if (!/^[a-zA-Z\s]+$/.test(incomeSource)) {
        alert("Please enter a valid income source.");
        e.preventDefault(); return false;
    }

    // File checks
    if (!incomeProof) {
        alert("Please upload your income proof.");
        e.preventDefault(); return false;
    }
    if (!idCopy) {
        alert("Please upload your ID copy.");
        e.preventDefault(); return false;
    }

    return true;
});
</script>

</body>
</html>
