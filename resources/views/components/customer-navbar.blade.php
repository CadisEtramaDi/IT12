<nav class="bg-white/80 backdrop-blur-md shadow-sm border-b border-gray-200/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <a href="/" class="text-2xl font-bold text-[#0EA5E9]">
                    Minjee Balloon
                </a>
            </div>
            
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-700 hover:text-[#0EA5E9] transition-colors font-medium">
                    Home
                </a>
                <a href="/check-availability" class="text-gray-700 hover:text-[#0EA5E9] transition-colors font-medium">
                    Check Availability
                </a>
                <a href="/create-booking" class="text-gray-700 hover:text-[#0EA5E9] transition-colors font-medium">
                    Create Booking
                </a>
                <a href="/my-bookings" class="text-gray-700 hover:text-[#0EA5E9] transition-colors font-medium">
                    My Bookings
                </a>
                <a href="/contact" class="text-gray-700 hover:text-[#0EA5E9] transition-colors font-medium">
                    Contact
                </a>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700 hover:text-[#0EA5E9] focus:outline-none">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-gray-200/50 bg-white/90 backdrop-blur-md">
        <div class="px-2 pt-2 pb-3 space-y-1">
            <a href="/" class="block px-3 py-2 text-gray-700 hover:text-[#0EA5E9] hover:bg-white/50 rounded-md transition-colors font-medium">
                Home
            </a>
            <a href="/check-availability" class="block px-3 py-2 text-gray-700 hover:text-[#0EA5E9] hover:bg-white/50 rounded-md transition-colors font-medium">
                Check Availability
            </a>
            <a href="/create-booking" class="block px-3 py-2 text-gray-700 hover:text-[#0EA5E9] hover:bg-white/50 rounded-md transition-colors font-medium">
                Create Booking
            </a>
            <a href="/my-bookings" class="block px-3 py-2 text-gray-700 hover:text-[#0EA5E9] hover:bg-white/50 rounded-md transition-colors font-medium">
                My Bookings
            </a>
            <a href="/contact" class="block px-3 py-2 text-gray-700 hover:text-[#0EA5E9] hover:bg-white/50 rounded-md transition-colors font-medium">
                Contact
            </a>
        </div>
    </div>
</nav>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (menuButton && mobileMenu) {
            menuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
</script>
