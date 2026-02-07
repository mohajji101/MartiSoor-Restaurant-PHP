<?php
// views/home.php
// Main landing page
// Bogga ugu weyn ee soo dhaweynta
include 'partials/header.php';
?>

<!-- Hero Section -->
<section class="relative h-[80vh] flex items-center overflow-hidden">
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?auto=format&fit=crop&w=1920&q=80"
            alt="Hero background" class="w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="max-w-2xl">
            <span class="text-orange-500 font-bold tracking-widest uppercase mb-4 block">Premium Dining
                Experience</span>
            <h1 class="text-5xl md:text-7xl font-bold text-white mb-6 leading-tight">
                Taste the Excellence of <span class="text-orange-500">Fine Dining</span>
            </h1>
            <p class="text-slate-300 text-xl mb-10 leading-relaxed">
                Delicious food delivered to your door or enjoyed in our cozy atmosphere. The best ingredients, the best
                chefs, the best flavors.
            </p>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="./products"
                    class="bg-orange-600 text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-orange-700 transition shadow-xl shadow-orange-900/40 text-center">
                    Order Now
                </a>
                <a href="#about"
                    class="bg-white/10 hover:bg-white/20 backdrop-blur-md text-white px-8 py-4 rounded-full font-bold text-lg transition border border-white/20 text-center">
                    Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-12 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
            <div>
                <div class="text-4xl font-bold text-orange-600 mb-2">15k+</div>
                <div class="text-slate-500 font-medium">Happy Customers</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-orange-600 mb-2">50+</div>
                <div class="text-slate-500 font-medium">Expert Chefs</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-orange-600 mb-2">100?</div>
                <div class="text-slate-500 font-medium">Fresh Ingredients</div>
            </div>
            <div>
                <div class="text-4xl font-bold text-orange-600 mb-2">30m</div>
                <div class="text-slate-500 font-medium">Avg Delivery</div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-4">Explore Our Menu</h2>
            <div class="w-20 h-1.5 bg-orange-500 mx-auto rounded-full"></div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div
                class="group relative overflow-hidden rounded-3xl shadow-xl h-96 transition-transform hover:-translate-y-2">
                <img src="https://images.unsplash.com/photo-1513104890138-7c749659a591?auto=format&fit=crop&w=600&q=80"
                    class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Pizza & Fast Food</h3>
                    <p class="text-slate-300 mb-4">Hot, cheesy, and delicious.</p>
                    <a href="./products?category=pizza"
                        class="text-orange-500 font-bold hover:text-orange-400 transition flex items-center gap-2">
                        Browse Category <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl shadow-xl h-96 transition-transform hover:-translate-y-2">
                <img src="https://images.unsplash.com/photo-1467003909585-2f8a72700288?auto=format&fit=crop&w=600&q=80"
                    class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Healthy Salads</h3>
                    <p class="text-slate-300 mb-4">Fresh and nutritious options.</p>
                    <a href="./products?category=salad"
                        class="text-orange-500 font-bold hover:text-orange-400 transition flex items-center gap-2">
                        Browse Category <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div
                class="group relative overflow-hidden rounded-3xl shadow-xl h-96 transition-transform hover:-translate-y-2">
                <img src="https://images.unsplash.com/photo-1551024506-0bccd828d307?auto=format&fit=crop&w=600&q=80"
                    class="w-full h-full object-cover transition duration-500 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8">
                    <h3 class="text-2xl font-bold text-white mb-2">Desserts & Drinks</h3>
                    <p class="text-slate-300 mb-4">The perfect sweet ending.</p>
                    <a href="./products?category=desserts"
                        class="text-orange-500 font-bold hover:text-orange-400 transition flex items-center gap-2">
                        Browse Category <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'partials/footer.php'; ?>