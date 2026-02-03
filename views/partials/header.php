<?php
// views/partials/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MartiSoor Restaurant - Gourmet Dining</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .dark-glass { background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <nav class="sticky top-0 z-50 glass border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center">
                    <a href="/" class="text-2xl font-bold text-orange-600 flex items-center gap-2">
                    <img src="/images/Marti Logo.png" alt="MartiSoor" class="h-10 w-auto object-contain">
                        <span class="text-2xl font-bold text-orange-600 ml-2">MartiSoor</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="font-medium hover:text-orange-600 transition">Home</a>
                    <a href="/products" class="font-medium hover:text-orange-600 transition">Menu</a>
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="/admin" class="font-medium hover:text-orange-600 transition">Dashboard</a>
                        <?php endif; ?>
                        <div class="relative group">
                            <button class="flex items-center gap-2 font-medium hover:text-orange-600 transition">
                                <i class="fas fa-user-circle text-xl"></i>
                                <span><?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>
                            </button>
                            <div class="absolute right-0 w-48 hidden group-hover:block z-50">
                                <div class="h-2 w-full"></div> <!-- Bridge to prevent hover loss -->
                                <div class="bg-white rounded-xl shadow-xl border border-slate-100 py-2">
                                    <a href="/profile" class="block px-4 py-2 hover:bg-slate-50 transition">Profile</a>
                                    <a href="/orders" class="block px-4 py-2 hover:bg-slate-50 transition text-slate-700">My Orders</a>
                                    <hr class="my-1 border-slate-50">
                                    <a href="/logout" class="block px-4 py-2 text-red-600 hover:bg-red-50 transition">Logout</a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="font-medium hover:text-orange-600 transition">Login</a>
                        <a href="/register" class="bg-orange-600 text-white px-6 py-2 rounded-full font-medium hover:bg-orange-700 transition shadow-lg shadow-orange-200">Join Us</a>
                    <?php endif; ?>
                    
                    <?php 
                    $cart_count = 0;
                    if (isset($_SESSION['cart'])) {
                        foreach ($_SESSION['cart'] as $item) {
                            $cart_count += $item['quantity'];
                        }
                    }
                    ?>
                    <a href="/cart" class="relative p-2 text-slate-600 hover:text-orange-600 transition">
                        <i class="fas fa-shopping-bag text-xl"></i>
                        <?php if ($cart_count > 0): ?>
                            <span class="absolute top-0 right-0 bg-orange-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="md:hidden">
                    <button class="text-slate-600"><i class="fas fa-bars text-2xl"></i></button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Message Notification -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="flash-toast" class="fixed bottom-10 right-10 z-[100] transform translate-y-20 opacity-0 transition-all duration-500 ease-out">
            <div class="bg-slate-900 text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-3 border border-slate-800">
                <div class="w-8 h-8 rounded-full bg-orange-600 flex items-center justify-center text-sm">
                    <i class="fas fa-check"></i>
                </div>
                <span class="font-bold tracking-tight"><?php echo $_SESSION['flash_message']; ?></span>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toast = document.getElementById('flash-toast');
                setTimeout(() => {
                    toast.classList.remove('translate-y-20', 'opacity-0');
                }, 100);
                setTimeout(() => {
                    toast.classList.add('translate-y-20', 'opacity-0');
                }, 3000);
            });
        </script>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <main>
