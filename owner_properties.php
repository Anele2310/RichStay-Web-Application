<?php
session_start();
// Example: set a session name for demonstration if not already set
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Guest';
}
$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Owner Properties - RichStay</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!-- TailwindCSS & Fonts -->
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
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.8);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            backdrop-filter: blur(10px);
        }

        .clay-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 35px 70px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.9);
        }

        .clay-button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4);
            transition: all 0.3s ease;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
            padding: 0.5rem 1.2rem;
            display: inline-block;
            text-decoration: none;
        }

        .clay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(59, 130, 246, 0.6);
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #1d4ed8 100%);
            position: relative;
        }

        .gradient-bg::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }

        .property-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .property-card:hover {
            transform: translateY(-10px) scale(1.03);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
        }

        h1, h2, h3 {
            font-family: 'BioRhyme', serif;
            font-weight: 700;
        }

        footer {
            color: #6b7280;
            text-align: center;
            padding: 1.5rem 0;
        }
    </style>
</head>
<body class="relative">

    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-6 py-5 flex justify-between items-center relative z-10">
            <h1 class="text-2xl font-bold text-shadow">Owner Properties</h1>
            <nav class="flex space-x-4">
                <a href="ownerru.php" class="nav-button px-4 py-2 rounded-lg hover:bg-white hover:text-blue-800 transition">Home</a>
                <a href="add_property.php" class="nav-button px-4 py-2 rounded-lg hover:bg-white hover:text-blue-800 transition">Add a property</a>
                <a href="properties.php" class="nav-button px-4 py-2 rounded-lg hover:bg-white hover:text-blue-800 transition">View your properties</a>
                <a href="logout.php" class="nav-button px-4 py-2 rounded-lg hover:bg-white hover:text-blue-800 transition flex items-center">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </nav>
        </div>
    </header>
    

    <!-- Main Content -->
    <main class="container mx-auto px-6 py-10">
        <!-- Welcome Card -->
        <section class="clay-card p-8 mb-10 text-center">
            <h2 class="text-3xl text-gray-800 mb-4">Welcome👋</h2>
            <p class="text-gray-600 text-lg">
                Manage your listed properties effortlessly. Use the navigation above to explore, add, or view properties.
            </p>
        </section>

        <!-- Featured Properties -->
        <section>
            <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Featured Properties</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="property-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Cozy Apartment</h4>
                        <p class="text-gray-600">2 beds • 1 bath • Downtown</p>
                    </div>
                    <a href="#" class="clay-button text-sm">View</a>
                </div>

                <div class="property-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Family House</h4>
                        <p class="text-gray-600">4 beds • 3 baths • Suburbs</p>
                    </div>
                    <a href="#" class="clay-button text-sm">View</a>
                </div>

                <div class="property-card p-6">
                    <div class="mb-4">
                        <h4 class="text-xl font-semibold text-gray-800">Studio Loft</h4>
                        <p class="text-gray-600">1 bed • 1 bath • City center</p>
                    </div>
                    <a href="#" class="clay-button text-sm">View</a>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="mt-10">
        &copy; <?php echo date('Y'); ?> RichStay Owner Portal. All rights reserved.
    </footer>

</body>
</html>
