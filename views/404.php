<?php
// views/404.php
// Page Not Found View
// Bogga lama helin
include 'partials/header.php';
?>

<div class="min-h-[70vh] flex items-center justify-center py-24 px-4">
    <div class="text-center max-w-xl">
        <div class="relative inline-block mb-12">
            <h1 class="text-[12rem] font-black text-slate-100 leading-none">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <i class="fas fa-search text-7xl text-orange-600 opacity-20"></i>
            </div>
        </div>

        <h2 class="text-4xl font-bold text-slate-900 mb-6">Page Not Found</h2>
        <p class="text-slate-500 text-lg mb-10 leading-relaxed">
            Oops! The page you're looking for seems to have wandered off the menu.
            Let's get you back to some delicious content.
        </p>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="./"
                class="bg-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-700 transition shadow-xl shadow-orange-200">
                Back to Home
            </a>
            <a href="./products"
                class="bg-slate-900 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-slate-800 transition">
                Browse Menu
            </a>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>