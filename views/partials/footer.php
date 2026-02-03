<?php
// views/partials/footer.php
?>
    </main>
    <footer class="bg-slate-900 text-slate-400 py-12 border-t border-slate-800 mt-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="col-span-1 md:col-span-1">
                    <a href="./" class="text-2xl font-bold text-white flex items-center gap-2 mb-6">
                        <img src="/images/Marti Logo.png" alt="MartiSoor" class="h-8 w-auto">
                        <span class="text-2xl font-bold text-white ml-2">MartiSoor</span>
                    </a>
                    <p class="text-sm leading-relaxed">
                        Experience the best gourmet dining in town. Fresh ingredients, expert chefs, and a passion for flavor.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-6">Quick Links</h3>
                    <ul class="space-y-4 text-sm">
                        <li><a href="./" class="hover:text-orange-500 transition">Our Menu</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Reserved Tables</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Gift Cards</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Contact Us</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-6">Support</h3>
                    <ul class="space-y-4 text-sm">
                        <li><a href="./" class="hover:text-orange-500 transition">Help Center</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Order Tracking</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Privacy Policy</a></li>
                        <li><a href="./" class="hover:text-orange-500 transition">Terms of Service</a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-white font-bold mb-6">Newsletter</h3>
                    <p class="text-sm mb-4">Subscribe to get special offers and menu updates.</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="bg-slate-800 border-none rounded-l-lg px-4 py-2 w-full focus:ring-1 focus:ring-orange-500 text-white">
                        <button class="bg-orange-600 text-white px-4 py-2 rounded-r-lg hover:bg-orange-700 transition">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-800 text-center text-sm">
                <p>&copy; <?php echo date('Y'); ?> MartiSoor Restaurant. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
