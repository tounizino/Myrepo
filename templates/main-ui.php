<div id="cge-app" class="cge-container bg-gray-900 text-white min-h-screen p-4 md:p-8">
    <!-- Header/Search Section -->
    <div class="max-w-6xl mx-auto mb-12 text-center">
        <h1 class="text-4xl font-bold mb-8 text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-600">
            Cloud Game Explorer
        </h1>
        
        <div class="relative max-w-2xl mx-auto mb-8">
            <input type="text" id="cge-search-input" 
                placeholder="Search for games (e.g. Cyberpunk 2077, Starfield)..." 
                class="w-full bg-gray-800 border-2 border-gray-700 rounded-full py-4 px-6 focus:outline-none focus:border-blue-500 text-lg transition-all">
            <button id="cge-search-btn" class="absolute right-2 top-2 bg-blue-600 hover:bg-blue-700 text-white rounded-full p-2 w-12 h-12 flex items-center justify-center transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </div>

        <!-- Platform Filters -->
        <div class="flex flex-wrap justify-center gap-2 mb-8" id="cge-filters">
            <button class="cge-filter-btn active px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="">All Platforms</button>
            <button class="cge-filter-btn px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="GeForce NOW">GeForce NOW</button>
            <button class="cge-filter-btn px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="Xbox Cloud Gaming">Xbox Cloud</button>
            <button class="cge-filter-btn px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="Boosteroid">Boosteroid</button>
            <button class="cge-filter-btn px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="PlayStation Plus">PlayStation Plus</button>
            <button class="cge-filter-btn px-4 py-2 rounded-full border border-gray-700 hover:border-blue-500 transition-all" data-platform="Amazon Luna">Amazon Luna</button>
        </div>
    </div>

    <!-- Results Section -->
    <div id="cge-results" class="max-w-6xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <!-- Cards will be injected here -->
        <div class="col-span-full text-center text-gray-500 py-20">
            <p class="text-xl">Search for a game to see where you can play it in the cloud.</p>
        </div>
    </div>

    <!-- Loading State -->
    <div id="cge-loader" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="animate-spin rounded-full h-16 w-16 border-t-2 border-b-2 border-blue-500"></div>
    </div>

    <!-- Footer Credits -->
    <div class="max-w-6xl mx-auto mt-20 pt-8 border-t border-gray-800 text-center text-gray-500 text-sm">
        <p>&copy; <?php echo date('Y'); ?> Cloud Game Explorer. All rights reserved.</p>
        <p>Data provided by RAWG.io. Credits to <a href="https://cloudloadout.com" class="text-blue-400 hover:underline">cloudloadout.com</a></p>
    </div>
</div>

<!-- Modal for expanded view (SPA approach) -->
<div id="cge-modal" class="fixed inset-0 bg-black bg-opacity-90 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto relative">
        <button id="cge-modal-close" class="absolute top-4 right-4 text-gray-400 hover:text-white text-3xl">&times;</button>
        <div id="cge-modal-content"></div>
    </div>
</div>

<script src="https://cdn.tailwindcss.com"></script>
<style>
    .cge-filter-btn.active {
        background-color: #2563eb;
        border-color: #2563eb;
        color: white;
    }
    .cge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.4);
    }
    .cge-card {
        transition: all 0.3s ease;
    }
</style>
