// Header scroll effect
const header = document.getElementById('mainHeader');
window.addEventListener('scroll', () => {
    if (window.scrollY > 20) {
        header.classList.add('is-scrolled');
    } else {
        header.classList.remove('is-scrolled');
    }
});

// Search modal toggle
const searchModal = document.getElementById('searchModal');
const openSearchBtn = document.getElementById('openSearch');
const closeSearchBtn = document.getElementById('closeSearch');
const searchInput = document.getElementById('cl-search-input');

function openSearch() {
    searchModal.classList.add('is-open');
    setTimeout(() => searchInput.focus(), 100);
}

function closeSearch() {
    searchModal.classList.remove('is-open');
}

if (openSearchBtn) {
    openSearchBtn.addEventListener('click', openSearch);
}

if (closeSearchBtn) {
    closeSearchBtn.addEventListener('click', closeSearch);
}

// Close on overlay click
if (searchModal) {
    searchModal.addEventListener('click', (e) => {
        if (e.target === searchModal) {
            closeSearch();
        }
    });
}

// Keyboard shortcuts
document.addEventListener('keydown', (e) => {
    if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
        e.preventDefault();
        openSearch();
    }
    if (e.key === 'Escape') {
        closeSearch();
    }
});
