<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: indexru.html");
    exit();
}

// Greeting for a locked in student
if (isset($_SESSION['name'])) {
    echo "<h2 style='text-align:center; color:#1d4ed8; margin-top:20px;'>Hi " . htmlspecialchars($_SESSION['name']) . "! Welcome to your Student Dashboard </h2>";
} else {
    echo "<h2 style='text-align:center; color:#1d4ed8; margin-top:20px;'>Welcome, Student!</h2>";
}
?>   

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RichStay - Modern Student Rental Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=BioRhyme:wght@400;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    <style>
        * {
            box-sizing: border-box;
        }
        
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
            border-radius: 20px;
            box-shadow: 0 10px 25px -5px rgba(59, 130, 246, 0.4), inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
            border: none;
            color: white;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .clay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(59, 130, 246, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.3);
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        }
        
        .clay-input {
            background: rgba(255, 255, 255, 0.9);
            border: 2px solid rgba(59, 130, 246, 0.1);
            border-radius: 16px;
            box-shadow: inset 0 2px 8px rgba(0, 0, 0, 0.06), 0 0 0 1px rgba(255, 255, 255, 0.8);
            transition: all 0.3s ease;
            backdrop-filter: blur(5px);
        }
        
        .clay-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), inset 0 2px 8px rgba(0, 0, 0, 0.06);
            outline: none;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #1d4ed8 100%);
            position: relative;
        }
        
        .gradient-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1) 0%, transparent 50%, rgba(255, 255, 255, 0.05) 100%);
            pointer-events: none;
        }
        
        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
            animation: modalFadeIn 0.3s ease;
        }
        
        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .modal-content {
            animation: modalSlideIn 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        
        @keyframes modalSlideIn {
            from { transform: scale(0.8) translateY(-50px); opacity: 0; }
            to { transform: scale(1) translateY(0); opacity: 1; }
        }
        
        .chat-message {
            max-width: 70%;
            margin-bottom: 16px;
            animation: messageSlide 0.3s ease;
        }
        
        .chat-message.sent {
            margin-left: auto;
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            border-radius: 20px 20px 5px 20px;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }
        
        .chat-message.received {
            margin-right: auto;
            background: rgba(255, 255, 255, 0.95);
            color: #374151;
            border-radius: 20px 20px 20px 5px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        @keyframes messageSlide {
            from { transform: translateX(-20px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        .stats-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(255, 255, 255, 0.8) 100%);
            border-radius: 24px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
            transition: all 0.4s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.2);
        }
        
        .property-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 24px;
            box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        
        .property-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.2);
        }
        
        .nav-button {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .nav-button:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'BioRhyme', serif;
            font-weight: 700;
            letter-spacing: -0.025em;
        }
        
        .feature-icon {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
            transition: all 0.3s ease;
        }
        
        .feature-icon:hover {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 30px rgba(59, 130, 246, 0.4);
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        /* PDF Export Optimizations */
        @media print {
            .tab-content:not(.active) {
                display: block !important;
            }
            .modal {
                display: none !important;
            }
            body {
                background: white;
            }
            .clay-card, .stats-card, .property-card {
                break-inside: avoid;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }
        }
        
        /* Ensure content doesn't overflow */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        /* Smooth scrolling for PDF export */
        html {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="min-h-screen">
    <header class="gradient-bg text-white shadow-2xl relative overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-blue-800/20"></div>
    <div class="container mx-auto px-6 py-8 relative z-10">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-4 animate-float">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <i class="fas fa-home text-2xl"></i>
                </div>
                <h1 class="text-4xl font-bold text-shadow">RichStay</h1>
            </div>

            <!-- Navigation -->
            <nav class="hidden md:flex space-x-4">
                <!-- links to redirect -->
                <a href="studentru.php" class="nav-button px-6 py-3 hover:text-blue-200 transition">Home</a>
                <a href="student_properties.php" class="nav-button px-6 py-3 hover:text-blue-200 transition">Properties</a>
                <a href="student_applications.php" class="nav-button px-6 py-3 hover:text-blue-200 transition">Your applications</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg font-semibold text-white flex items-center transition">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout</a>

            </nav>
        </div>
    </div>
</header>
    <div id="home" class="tab-content active">
        
        <section class="gradient-bg text-white py-24 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-blue-800/10"></div>
            <div class="container mx-auto px-6 text-center relative z-10">
                <h2 class="text-6xl font-bold mb-8 text-shadow animate-float">Find Your Perfect Student Accommodation</h2>
                <p class="text-2xl mb-12 max-w-4xl mx-auto opacity-90 leading-relaxed">RichStay connects students with quality rental properties while providing landlords with reliable tenants through advanced screening and transparent processes.</p>
                
               <!-- <div class="clay-card p-8 max-w-5xl mx-auto">
                    <div class="grid md:grid-cols-4 gap-6">
                        <input type="text" placeholder="Location (e.g., Near UKZN)" class="clay-input p-4 text-gray-700 font-medium">
                        <select class="clay-input p-4 text-gray-700 font-medium">
                            <option>Property Type</option>
                            <option>Apartment</option>
                            <option>House</option>
                            <option>Shared Room</option>
                            <option>Studio</option>
                        </select>
                        <select class="clay-input p-4 text-gray-700 font-medium">
                            <option>Price Range</option>
                            <option>R2000-R4000</option>
                            <option>R4000-R6000</option>
                            <option>R6000-R8000</option>
                            <option>R8000+</option>
                        </select>
                        <button class="clay-button p-4 text-lg font-semibold">
                            <i class="fas fa-search mr-3"></i>Search
                        </button>
                    </div>-->
                </div>
            </div>
        </section>
        <section class="py-24 bg-gradient-to-br from-gray-50 to-gray-100">
            <div class="container mx-auto px-6">
                <h3 class="text-5xl font-bold text-center mb-16 text-gray-800">Why Choose RichStay?</h3>
                <div class="grid md:grid-cols-3 gap-10">
                    <div class="clay-card text-center p-10">
                        <div class="feature-icon w-20 h-20 mx-auto mb-8 flex items-center justify-center">
                            <i class="fas fa-shield-alt text-3xl text-white"></i>
                        </div>
                        <h4 class="text-2xl font-semibold mb-6 text-gray-800">Advanced Tenant Screening</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Reduce tenant arrears with comprehensive credit checks and financial verification tools.</p>
                    </div>
                    <div class="clay-card text-center p-10">
                        <div class="feature-icon w-20 h-20 mx-auto mb-8 flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-3xl text-white"></i>
                        </div>
                        <h4 class="text-2xl font-semibold mb-6 text-gray-800">Location-Based Search</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Find properties near universities and key landmarks with integrated GPS mapping.</p>
                    </div>
                    <div class="clay-card text-center p-10">
                        <div class="feature-icon w-20 h-20 mx-auto mb-8 flex items-center justify-center">
                            <i class="fas fa-comments text-3xl text-white"></i>
                        </div>
                        <h4 class="text-2xl font-semibold mb-6 text-gray-800">Direct Communication</h4>
                        <p class="text-gray-600 text-lg leading-relaxed">Connect directly with landlords without traditional agent intermediaries.</p>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-24 gradient-bg text-white relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-blue-800/20"></div>
            <div class="container mx-auto px-6 relative z-10">
                <div class="grid md:grid-cols-4 gap-8 text-center">
                    <div class="stats-card p-8 text-gray-800">
                        <div class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">1,250+</div>
                        <div class="text-gray-600 text-lg font-medium">Active Properties</div>
                    </div>
                    <div class="stats-card p-8 text-gray-800">
                        <div class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">3,400+</div>
                        <div class="text-gray-600 text-lg font-medium">Registered Students</div>
                    </div>
                    <div class="stats-card p-8 text-gray-800">
                        <div class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">850+</div>
                        <div class="text-gray-600 text-lg font-medium">Verified Landlords</div>
                    </div>
                    <div class="stats-card p-8 text-gray-800">
                        <div class="text-5xl font-bold mb-4 bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">95%</div>
                        <div class="text-gray-600 text-lg font-medium">Satisfaction Rate</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Properties Tab -->
    <div id="properties" class="tab-content">
        <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
            <div class="container mx-auto px-6">
                <div class="flex justify-between items-center mb-12">
                    <h3 class="text-5xl font-bold text-gray-800">Available Properties</h3>
        
            
                </div>

                <!-- Filters -->
                <div class="clay-card p-8 mb-12">
                    <div class="grid md:grid-cols-5 gap-6">
                        <select class="clay-input p-4">
                            <option>All Areas</option>
                            <option>Near UKZN</option>
                            <option>Near DUT</option>
                            <option>Durban Central</option>
                            <option>Berea</option>
                        </select>
                        <select class="clay-input p-4">
                            <option>All Types</option>
                            <option>Apartment</option>
                            <option>House</option>
                            <option>Shared Room</option>
                        </select>
                        <input type="number" placeholder="Min Price (R)" class="clay-input p-4">
                        <input type="number" placeholder="Max Price (R)" class="clay-input p-4">
                        <button class="clay-button p-4 text-lg font-semibold">Filter</button>
                    </div>
                </div>

                <!-- Property Grid -->
                <div id="properties-list" 
                     hx-get="handlers/properties.php?action=list" 
                     hx-trigger="load, refresh from:body" 
                     hx-swap="innerHTML"
                     class="grid md:grid-cols-3 gap-10 mb-16">
                    <!-- Properties will be loaded dynamically -->
                    <div class="col-span-3 text-center py-12">
                        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Loading properties...</p>
                    </div>
                </div>

                <!-- Map Section -->
                <div class="clay-card p-8">
                    <h4 class="text-2xl font-bold mb-8 text-gray-800">Property Locations</h4>
                    <div class="h-96 bg-gradient-to-br from-blue-100 to-green-100 rounded-2xl flex items-center justify-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-200/30 to-green-200/30"></div>
                        <div class="text-center relative z-10">
                            <i class="fas fa-map text-8xl text-blue-500 mb-6"></i>
                        
                            <iframe 
                             width="300%" 
                             height="300%" 
                             frameborder="50" 
                             style="border:50" 
                             referrerpolicy="no-referrer-when-downgrade"
                             src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d358253.2096803566!2d30.783098799999996!3d-29.857876449999998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ef70733c14c2a03%3A0x93f5f74a8307e5a6!2sDurban!5e0!3m2!1sen!2sza!4v1691750335673!5m2!1sen!2sza" 
                             allowfullscreen>
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>



    <!-- Messages Tab -->
     <!--
    <div id="messages" class="tab-content">
        <section class="py-20 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
            <div class="container mx-auto px-6">
                <h3 class="text-5xl font-bold text-gray-800 mb-12">Messages</h3>
                
                <div class="grid lg:grid-cols-3 gap-8">
                    
                    <div class="clay-card">
                        <div class="p-8 border-b border-gray-200">
                            <h4 class="text-2xl font-bold text-gray-800">Conversations</h4>
                        </div>
                        <div class="h-96 overflow-y-auto">
                            <div class="p-6 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors" onclick="loadChat('landlord1')">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                                        <span class="text-white font-bold">JP</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="font-bold text-gray-900">John Patel (Landlord)</div>
                                        <div class="text-sm text-gray-500">About UKZN apartment...</div>
                                    </div>
                                    <div class="text-xs text-gray-400">2h ago</div>
                                </div>
                            </div>
                            <div class="p-6 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors" onclick="loadChat('student1')">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center">
                                        <span class="text-white font-bold">AM</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="font-bold text-gray-900">Amanda Mhlongo (Student)</div>
                                        <div class="text-sm text-gray-500">Application status update</div>
                                    </div>
                                    <div class="text-xs text-gray-400">5h ago</div>
                                </div>
                            </div>
                            <div class="p-6 border-b border-gray-100 hover:bg-blue-50 cursor-pointer transition-colors" onclick="loadChat('landlord2')">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center">
                                        <span class="text-white font-bold">MK</span>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <div class="font-bold text-gray-900">Mike Khumalo (Landlord)</div>
                                        <div class="text-sm text-gray-500">Lease agreement discussion</div>
                                    </div>
                                    <div class="text-xs text-gray-400">1d ago</div>
                                </div>
                            </div>
                        </div>
                    </div>-->

                    <!-- Chat Area -->
                    <!--  

                    <div class="lg:col-span-2 clay-card">
                        <div class="p-8 border-b border-gray-200">
                            <h4 class="text-2xl font-bold text-gray-800">Chat with John Patel</h4>
                            <p class="text-gray-500 text-lg">Landlord - Modern Apartment near UKZN</p>
                        </div>
                        <div class="h-80 overflow-y-auto p-6 space-y-6" id="chatMessages">
                            <div class="chat-message received p-6">
                                <div class="text-lg">Hi! I saw your application for the apartment near UKZN. Are you currently a student there?</div>
                                <div class="text-sm text-gray-500 mt-2">John Patel • 2 hours ago</div>
                            </div>
                            <div class="chat-message sent p-6">
                                <div class="text-lg">Yes, I'm a final year Computer Science student at UKZN Westville. I'm looking for accommodation closer to campus.</div>
                                <div class="text-sm text-blue-100 mt-2">You • 1 hour ago</div>
                            </div>
                            <div class="chat-message received p-6">
                                <div class="text-lg">Perfect! The apartment is just 0.5km from the campus. Would you like to schedule a viewing?</div>
                                <div class="text-sm text-gray-500 mt-2">John Patel • 45 minutes ago</div>
                            </div>
                        </div>
                        <div class="p-6 border-t border-gray-200">
                            <div class="flex space-x-4">
                                <input type="text" placeholder="Type your message..." class="clay-input flex-1 p-4 text-lg" id="messageInput">
                                <button onclick="sendMessage()" class="clay-button px-8 py-4">
                                    <i class="fas fa-paper-plane text-xl"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    -->

    
    <!-- Modals -->
    <!-- Login Modal -->
     <!--
    <div id="loginModal" class="modal">
        <div class="modal-content clay-card max-w-md w-full mx-4 p-10">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-3xl font-bold text-gray-800">Welcome Back</h3>
                <button onclick="closeModal('loginModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div id="login-response" class="mb-4"></div>
            <form hx-post="handlers/auth.php" 
                  hx-vals='{"action": "login", "csrf_token": ""}'
                  hx-target="#login-response"
                  hx-swap="innerHTML"
                  class="space-y-6">
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-3">Email Address</label>
                    <input type="email" name="email" required class="clay-input w-full p-4 text-lg" placeholder="your.email@student.ukzn.ac.za">
                </div>
                <div>
                    <label class="block text-gray-700 text-lg font-semibold mb-3">Password</label>
                    <input type="password" name="password" required class="clay-input w-full p-4 text-lg" placeholder="Enter your password">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-gray-600">
                        <input type="checkbox" name="remember" class="mr-3 w-5 h-5">
                        Remember me
                    </label>
                    <a href="#" class="text-blue-600 hover:text-blue-800 font-semibold">Forgot Password?</a>
                </div>
                <button type="submit" class="clay-button w-full py-4 text-lg font-semibold">
                    <i class="fas fa-sign-in-alt mr-3"></i>Sign In
                </button>
                <div class="text-center text-gray-600">
                    Don't have an account? 
                    <button type="button" onclick="closeModal('loginModal'); openModal('registerModal');" class="text-blue-600 hover:text-blue-800 font-semibold">Sign up here</button>
                </div>
            </form>
        </div>
    </div>
    -->

    <!-- Register Modal -->
    <!-- <div id="registerModal" class="modal">
        <div class="modal-content clay-card max-w-lg w-full mx-4 p-10">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-3xl font-bold text-gray-800">Join RichStay</h3>
                <button onclick="closeModal('registerModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="registerForm" action="handler.php" method="POST" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">First Name</label>
                        <input name="firstName" id="firstName" type="text" class="clay-input w-full p-4" placeholder="John">
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Last Name</label>
                        <input name="lastName" id="lastName" type="text" class="clay-input w-full p-4" placeholder="Doe">
                    </div>
                </div>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">Email</label>
                    <input name = "email" id="registerEmail" type="email" class="clay-input w-full p-4" placeholder="your@email.com">
                </div>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">User Type</label>
                    <select name="userType" id="userType" class="clay-input w-full p-4">
                        <option>Select user type</option>
                        <option>Student/Tenant</option>
                        <option>Landlord</option>
                    </select>
                </div>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">Password</label>
                    <input name="password" id="registerPassword" type="password" class="clay-input w-full p-4" placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">Confirm Password</label>
                    <input name="confirmPassword" id="confirmPassword" type="password" class="clay-input w-full p-4" placeholder="••••••••">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" class="mr-3 w-5 h-5">
                    <span class="text-gray-600">I agree to the Terms of Service and Privacy Policy</span>
                </div>
                <button type="submit" class="clay-button w-full p-4 text-xl font-bold">Create Account</button>
            </form>
        </div>
    </div>-->

    <!-- Register Modal with php -->
     <!-- Register Modal -->
      <!-- 
<div id="registerModal" class="modal">
    <div class="modal-content clay-card max-w-lg w-full mx-4 p-10">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-3xl font-bold text-gray-800">Join RichStay</h3>
            <button onclick="closeModal('registerModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="registerForm" action="handler.php" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="register"> 

            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">First Name</label>
                    <input name="firstName" id="firstName" type="text" class="clay-input w-full p-4" placeholder="John">
                </div>
                <div>
                    <label class="block text-lg font-semibold text-gray-700 mb-3">Last Name</label>
                    <input name="lastName" id="lastName" type="text" class="clay-input w-full p-4" placeholder="Doe">
                </div>
            </div>

            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Email</label>
                <input name="email" id="registerEmail" type="email" class="clay-input w-full p-4" placeholder="your@email.com">
            </div>

            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">User Type</label>
                <select name="userType" id="userType" class="clay-input w-full p-4">
                    <option value="">Select user type</option>
                    <option value="student">Student/Tenant</option>
                    <option value="landlord">Landlord</option>
                </select>
            </div>

            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Password</label>
                <input name="password" id="registerPassword" type="password" class="clay-input w-full p-4" placeholder="••••••••">
            </div>

            <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Confirm Password</label>
                <input name="confirmPassword" id="confirmPassword" type="password" class="clay-input w-full p-4" placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="terms" class="mr-3 w-5 h-5">
                <span class="text-gray-600">I agree to the Terms of Service and Privacy Policy</span>
            </div>

            <button type="submit" class="clay-button w-full p-4 text-xl font-bold">Create Account</button>
        </form>
    </div>
</div> -->


    <!-- Application Modal -->
    <!--<div id="applicationModal" class="modal">
        <div class="modal-content clay-card max-w-3xl w-full mx-4 max-h-screen overflow-y-auto p-10">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-3xl font-bold text-gray-800">Apply for Property</h3>
                <button onclick="closeModal('applicationModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form class="space-y-8">
                <div>
                    <h4 class="text-2xl font-bold mb-6 text-gray-800">Personal Information</h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">Full Name</label>
                            <input type="text" class="clay-input w-full p-4" placeholder="Your full name">
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">Phone Number</label>
                            <input type="tel" class="clay-input w-full p-4" placeholder="Your phone number">
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="text-2xl font-bold mb-6 text-gray-800">Academic Information</h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">University/College</label>
                            <select class="clay-input w-full p-4">
                                <option>Select institution</option>
                                <option>University of KwaZulu-Natal</option>
                                <option>Durban University of Technology</option>
                                <option>University of South Africa</option>
                                <option>Richfield Graduate Institute</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">Student ID</label>
                            <input type="text" class="clay-input w-full p-4" placeholder="Your student ID">
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-2xl font-bold mb-6 text-gray-800">Financial Information</h4>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">Monthly Income</label>
                            <input type="number" class="clay-input w-full p-4" placeholder="R0.00">
                        </div>
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-3">Income Source</label>
                            <select class="clay-input w-full p-4">
                                <option>Select income source</option>
                                <option>NSFAS</option>
                                <option>Parents/Guardian</option>
                                <option>Part-time job</option>
                                <option>Bursary</option>
                                <option>Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-2xl font-bold mb-6 text-gray-800">Document Upload</h4>
                    <div class="space-y-6">
                        <div class="clay-card border-2 border-dashed border-blue-300 p-8 text-center">
                            <i class="fas fa-cloud-upload-alt text-5xl text-blue-500 mb-4"></i>
                            <p class="text-gray-700 text-lg font-semibold">Upload Proof of Income</p>
                            <input type="file" class="hidden" id="incomeProof">
                            <label for="incomeProof" class="cursor-pointer text-blue-600 hover:underline font-semibold">Click to upload</label>
                        </div>
                        <div class="clay-card border-2 border-dashed border-blue-300 p-8 text-center">
                            <i class="fas fa-id-card text-5xl text-blue-500 mb-4"></i>
                            <p class="text-gray-700 text-lg font-semibold">Upload ID Copy</p>
                            <input type="file" class="hidden" id="idCopy">
                            <label for="idCopy" class="cursor-pointer text-blue-600 hover:underline font-semibold">Click to upload</label>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-6">
                    <button type="button" onclick="closeModal('applicationModal')" class="flex-1 bg-gray-300 text-gray-700 p-4 rounded-2xl hover:bg-gray-400 transition text-lg font-semibold">Cancel</button>
                    <button type="submit" class="flex-1 clay-button p-4 text-lg font-semibold">Submit Application</button>
                </div>
            </form>
        </div>
    </div>-->

    <!--Application Modal with php-->
    <!-- Application Modal -->
<div id="applicationModal" class="modal">
    <div class="modal-content clay-card max-w-3xl w-full mx-4 max-h-screen overflow-y-auto p-10">
        <div class="flex justify-between items-center mb-8">
            <h3 class="text-3xl font-bold text-gray-800">Apply for Property</h3>
            <button onclick="closeModal('applicationModal')" class="text-gray-400 hover:text-gray-600 text-2xl">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form action="handler.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <!-- 🔑 Hidden action -->
            <input type="hidden" name="action" value="apply_property">

            <!-- Personal Information -->
            <div>
                <h4 class="text-2xl font-bold mb-6 text-gray-800">Personal Information</h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Full Name</label>
                        <input name="fullName" type="text" class="clay-input w-full p-4" placeholder="Your full name" required>
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Phone Number</label>
                        <input name="phone" type="tel" class="clay-input w-full p-4" placeholder="Your phone number" required>
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div>
                <h4 class="text-2xl font-bold mb-6 text-gray-800">Academic Information</h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">University/College</label>
                        <select name="institution" class="clay-input w-full p-4" required>
                            <option value="">Select institution</option>
                            <option value="ukzn">University of KwaZulu-Natal</option>
                            <option value="dut">Durban University of Technology</option>
                            <option value="unisa">University of South Africa</option>
                            <option value="richfield">Richfield Graduate Institute</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Student ID</label>
                        <input name="studentId" type="text" class="clay-input w-full p-4" placeholder="Your student ID" required>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div>
                <h4 class="text-2xl font-bold mb-6 text-gray-800">Financial Information</h4>
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Monthly Income</label>
                        <input name="income" type="number" class="clay-input w-full p-4" placeholder="R0.00" required>
                    </div>
                    <div>
                        <label class="block text-lg font-semibold text-gray-700 mb-3">Income Source</label>
                        <select name="incomeSource" class="clay-input w-full p-4" required>
                            <option value="">Select income source</option>
                            <option value="nsfas">NSFAS</option>
                            <option value="parents">Parents/Guardian</option>
                            <option value="job">Part-time job</option>
                            <option value="bursary">Bursary</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Document Upload -->
            <div>
                <h4 class="text-2xl font-bold mb-6 text-gray-800">Document Upload</h4>
                <div class="space-y-6">
                    <div class="clay-card border-2 border-dashed border-blue-300 p-8 text-center">
                        <i class="fas fa-cloud-upload-alt text-5xl text-blue-500 mb-4"></i>
                        <p class="text-gray-700 text-lg font-semibold">Upload Proof of Income</p>
                        <input type="file" name="incomeProof" class="hidden" id="incomeProof">
                        <label for="incomeProof" class="cursor-pointer text-blue-600 hover:underline font-semibold">Click to upload</label>
                    </div>
                    <div class="clay-card border-2 border-dashed border-blue-300 p-8 text-center">
                        <i class="fas fa-id-card text-5xl text-blue-500 mb-4"></i>
                        <p class="text-gray-700 text-lg font-semibold">Upload ID Copy</p>
                        <input type="file" name="idCopy" class="hidden" id="idCopy">
                        <label for="idCopy" class="cursor-pointer text-blue-600 hover:underline font-semibold">Click to upload</label>
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex space-x-6">
                <button type="button" onclick="closeModal('applicationModal')" class="flex-1 bg-gray-300 text-gray-700 p-4 rounded-2xl hover:bg-gray-400 transition text-lg font-semibold">Cancel</button>
                <button type="submit" class="flex-1 clay-button p-4 text-lg font-semibold">Submit Application</button>
            </div>
        </form>
    </div>
</div>



    <!-- Footer -->
    <footer class="bg-gradient-to-br from-gray-800 to-gray-900 text-white py-16">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-10">
                <div>
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center">
                            <i class="fas fa-home text-2xl text-white"></i>
                        </div>
                        <h4 class="text-2xl font-bold">RichStay</h4>
                    </div>
                    <p class="text-gray-300 text-lg leading-relaxed">Connecting Durban students with quality accommodation while providing landlords with reliable tenants.</p>
                </div>
                <div>
                    <h5 class="font-bold mb-6 text-xl">For Students</h5>
                    <ul class="space-y-3 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Find Properties</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Application Guide</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Student Resources</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Safety Tips</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6 text-xl">For Landlords</h5>
                    <ul class="space-y-3 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors text-lg">List Property</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Tenant Screening</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Property Management</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Legal Resources</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6 text-xl">Support</h5>
                    <ul class="space-y-3 text-gray-300">
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition-colors text-lg">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-12 pt-8 text-center text-gray-300">
                <p class="text-lg">&copy; 2024 RichStay. All rights reserved. Addressing Durban's student accommodation challenges.</p>
            </div>
        </div>



   
    </footer>

    <script>
        // Tab Management
        function showTab(tabName) {
            // Hide all tab contents
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));
            
            // Show selected tab
            document.getElementById(tabName).classList.add('active');
        }

        // Modal Management
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Chat functionality
        function loadChat(userId) {
            // Simulate loading different chat conversations
            const chatMessages = document.getElementById('chatMessages');
            // This would be dynamically loaded from backend
        }

        function sendMessage() {
            const messageInput = document.getElementById('messageInput');
            const message = messageInput.value.trim();
            if (message) {
                const chatMessages = document.getElementById('chatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = 'chat-message sent p-6';
                messageDiv.innerHTML = `
                    <div class="text-lg">${message}</div>
                    <div class="text-sm text-blue-100 mt-2">You • Just now</div>
                `;
                chatMessages.appendChild(messageDiv);
                messageInput.value = '';
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }

        // Initialize Charts when Analytics tab is shown
        function initializeCharts() {
            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
            if (revenueCtx) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                        datasets: [{
                            label: 'Monthly Revenue (R)',
                            data: [180000, 195000, 210000, 185000, 220000, 235000, 245000, 225000, 240000, 250000, 245000, 245000],
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 6,
                            pointBackgroundColor: 'rgb(59, 130, 246)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            }
                        }
                    }
                });
            }

            // Property Types Chart
            const propertyTypesCtx = document.getElementById('propertyTypesChart')?.getContext('2d');
            if (propertyTypesCtx) {
                new Chart(propertyTypesCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Apartments', 'Shared Rooms', 'Studios', 'Houses'],
                        datasets: [{
                            data: [450, 320, 180, 300],
                            backgroundColor: [
                                'rgb(59, 130, 246)',
                                'rgb(16, 185, 129)',
                                'rgb(245, 101, 101)',
                                'rgb(168, 85, 247)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    font: {
                                        size: 14
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Payment Chart
            const paymentCtx = document.getElementById('paymentChart')?.getContext('2d');
            if (paymentCtx) {
                new Chart(paymentCtx, {
                    type: 'bar',
                    data: {
                        labels: ['On Time', 'Late (1-7 days)', 'Late (8-30 days)', 'Arrears'],
                        datasets: [{
                            label: 'Number of Tenants',
                            data: [1200, 180, 95, 45],
                            backgroundColor: [
                                'rgb(16, 185, 129)',
                                'rgb(245, 158, 11)',
                                'rgb(245, 101, 101)',
                                'rgb(107, 114, 128)'
                            ],
                            borderRadius: 8,
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }

        // Initialize charts when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Add some delay to ensure canvas elements are rendered
            setTimeout(initializeCharts, 100);
            
            // Close modals when clicking outside
            document.querySelectorAll('.modal').forEach(modal => {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.classList.remove('active');
                        document.body.style.overflow = 'auto';
                    }
                });
            });

            // Enable Enter key for message sending
            const messageInput = document.getElementById('messageInput');
            if (messageInput) {
                messageInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        sendMessage();
                    }
                });
            }
        });

        // Simulated notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-6 right-6 p-6 rounded-2xl text-white z-50 shadow-2xl ${type === 'success' ? 'bg-gradient-to-r from-green-500 to-green-600' : type === 'error' ? 'bg-gradient-to-r from-red-500 to-red-600' : 'bg-gradient-to-r from-blue-500 to-blue-600'}`;
            notification.style.backdropFilter = 'blur(10px)';
            notification.textContent = message;
            document.body.appendChild(notification);
            
            // Animate in
            notification.style.transform = 'translateX(400px)';
            setTimeout(() => {
                notification.style.transform = 'translateX(0)';
                notification.style.transition = 'transform 0.3s ease';
            }, 10);
            
            setTimeout(() => {
                notification.style.transform = 'translateX(400px)';
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        }

        // Simulate real-time updates
        setInterval(() => {
            if (Math.random() > 0.98) {
                showNotification('New application received!', 'success');
            }
        }, 30000);
    </script>


</body>
</html>
