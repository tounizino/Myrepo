document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('cge-search-input');
    const searchBtn = document.getElementById('cge-search-btn');
    const resultsContainer = document.getElementById('cge-results');
    const loader = document.getElementById('cge-loader');
    const filters = document.querySelectorAll('.cge-filter-btn');
    const modal = document.getElementById('cge-modal');
    const modalClose = document.getElementById('cge-modal-close');
    const modalContent = document.getElementById('cge-modal-content');

    let currentQuery = '';
    let currentPlatform = '';

    const performSearch = (query, platform = '') => {
        loader.classList.remove('hidden');
        
        const data = new FormData();
        data.append('action', 'cge_search_games');
        data.append('nonce', cgeData.nonce);
        data.append('search', query);
        data.append('platform', platform);

        fetch(cgeData.ajaxUrl, {
            method: 'POST',
            body: data
        })
        .then(response => response.json())
        .then(data => {
            loader.classList.add('hidden');
            if (data.success) {
                renderResults(data.data);
            } else {
                resultsContainer.innerHTML = `<div class="col-span-full text-center text-red-500">${data.data.error || 'An error occurred'}</div>`;
            }
        })
        .catch(err => {
            loader.classList.add('hidden');
            console.error(err);
            resultsContainer.innerHTML = `<div class="col-span-full text-center text-red-500">Failed to connect to search service.</div>`;
        });
    };

    const renderResults = (games) => {
        if (!games || games.length === 0) {
            resultsContainer.innerHTML = `<div class="col-span-full text-center text-gray-500 py-20"><p class="text-xl">No games found for your search.</p></div>`;
            return;
        }

        resultsContainer.innerHTML = games.map(game => `
            <div class="cge-card bg-gray-800 rounded-xl overflow-hidden cursor-pointer group" data-game-id="${game.id}">
                <div class="relative h-48">
                    <img src="${game.image}" alt="${game.name}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <span class="bg-blue-600 text-white px-4 py-2 rounded-full text-sm">View Details</span>
                    </div>
                </div>
                <div class="p-4">
                    <h3 class="font-bold text-lg mb-2 truncate">${game.name}</h3>
                    <div class="flex flex-wrap gap-2">
                        ${game.cloud_availability.filter(a => a.available).slice(0, 3).map(a => `
                            <span class="bg-gray-700 text-xs px-2 py-1 rounded text-gray-300">${a.platform}</span>
                        `).join('')}
                        ${game.cloud_availability.filter(a => a.available).length > 3 ? `<span class="text-xs text-gray-500">+${game.cloud_availability.filter(a => a.available).length - 3} more</span>` : ''}
                    </div>
                </div>
            </div>
        `).join('');

        // Add event listeners to cards
        document.querySelectorAll('.cge-card').forEach(card => {
            card.addEventListener('click', () => {
                const gameId = card.getAttribute('data-game-id');
                const game = games.find(g => g.id == gameId);
                showGameDetails(game);
            });
        });
    };

    const showGameDetails = (game) => {
        modalContent.innerHTML = `
            <div class="md:flex">
                <div class="md:w-1/2">
                    <img src="${game.image}" alt="${game.name}" class="w-full h-full object-cover">
                </div>
                <div class="md:w-1/2 p-8">
                    <h2 class="text-3xl font-bold mb-4">${game.name}</h2>
                    <div class="mb-6 text-gray-400">
                        <p>Released: ${game.released || 'N/A'}</p>
                        <p>Rating: ${game.rating || 'N/A'} / 5</p>
                    </div>
                    
                    <h3 class="text-xl font-bold mb-4 border-b border-gray-700 pb-2">Cloud Availability</h3>
                    <div class="grid grid-cols-1 gap-3">
                        ${game.cloud_availability.map(a => `
                            <a href="${a.url}" target="_blank" class="flex items-center justify-between p-3 rounded-lg ${a.available ? 'bg-blue-900 bg-opacity-30 border border-blue-500 hover:bg-opacity-50' : 'bg-gray-700 opacity-50 cursor-not-allowed'} transition-all">
                                <div class="flex items-center">
                                    <span class="font-semibold">${a.platform}</span>
                                </div>
                                <div class="flex items-center">
                                    ${a.available ? 
                                        '<span class="text-green-400 text-xs flex items-center">● Available <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg></span>' : 
                                        '<span class="text-gray-500 text-xs">Not Available</span>'}
                                </div>
                            </a>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    };

    searchBtn.addEventListener('click', () => {
        currentQuery = searchInput.value;
        performSearch(currentQuery, currentPlatform);
    });

    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            currentQuery = searchInput.value;
            performSearch(currentQuery, currentPlatform);
        }
    });

    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            filters.forEach(f => f.classList.remove('active'));
            btn.classList.add('active');
            currentPlatform = btn.getAttribute('data-platform');
            performSearch(currentQuery, currentPlatform);
        });
    });

    modalClose.addEventListener('click', () => {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    });

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
});
