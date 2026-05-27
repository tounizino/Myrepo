document.addEventListener('DOMContentLoaded', function() {
    const themeToggle = document.getElementById('cl-theme-toggle');
    const body = document.body;

    // Check for saved theme
    const savedTheme = localStorage.getItem('cl-theme') || 'dark';
    body.setAttribute('data-theme', savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = body.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            body.setAttribute('data-theme', newTheme);
            localStorage.setItem('cl-theme', newTheme);
        });
    }

    // Ajax Search
    const searchInput = document.querySelector('.cl-search-input');
    const searchResults = document.querySelector('.cl-search-results');

    if (searchInput && searchResults) {
        let timeout = null;
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = searchInput.value;

            if (query.length < 2) {
                searchResults.innerHTML = '';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`${clDb.apiUrl}/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderResults(data);
                    });
            }, 300);
        });
    }

    function renderResults(results) {
        if (!results.length) {
            searchResults.innerHTML = '<div class="cl-no-results">No games found</div>';
            return;
        }

        const html = results.map(game => `
            <a href="/g/${game.slug}" class="cl-search-item">
                <img src="${game.cover_url}" alt="${game.name}">
                <div class="cl-search-item-info">
                    <div class="cl-search-item-name">${game.name}</div>
                </div>
            </a>
        `).join('');

        searchResults.innerHTML = html;
    }
});
