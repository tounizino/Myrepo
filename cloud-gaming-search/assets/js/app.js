/**
 * Cloud Gaming Search Engine
 * Frontend Application - SPA-style Premium UX
 *
 * Single-page experience with:
 * - Instant search (debounced)
 * - Live suggestions dropdown
 * - Netflix-style game grid
 * - Premium game detail modal
 * - Cloud availability matrix
 * - Dark/Light theme system
 * - Smooth animations
 */

(function () {
  'use strict';

  const CGS = window.CGS || {};
  const REST_URL = CGS.rest_url || '/wp-json/cloud-gaming/v1/';

  // ===========================================================================
  // State
  // ===========================================================================

  const state = {
    query: '',
    page: 1,
    games: [],
    total: 0,
    totalPages: 0,
    loading: false,
    searching: false,
    suggestions: [],
    selectedGame: null,
    top100: [],
    top100Page: 1,
    top100TotalPages: 0,
    theme: localStorage.getItem('cgs-theme') || 'dark',
    showTop100: true,
    debounceTimer: null,
    top100Loaded: false,
    searchLoaded: false,
  };

  // ===========================================================================
  // DOM References
  // ===========================================================================

  const $ = (sel, ctx) => (ctx || document).querySelector(sel);
  const $$ = (sel, ctx) => Array.from((ctx || document).querySelectorAll(sel));

  let app;
  let searchInput;
  let suggestionsEl;
  let gameGrid;
  let loadMoreBtn;
  let modalOverlay;
  let themeToggle;

  // ===========================================================================
  // API Client
  // ===========================================================================

  const api = {
    async get(endpoint, params = {}) {
      const url = new URL(REST_URL + endpoint, window.location.origin);
      Object.entries(params).forEach(([k, v]) => {
        if (v !== undefined && v !== null && v !== '') {
          url.searchParams.set(k, v);
        }
      });

      try {
        const res = await fetch(url.toString(), {
          headers: {
            'X-WP-Nonce': CGS.nonce || '',
            'Accept': 'application/json',
          },
        });
        if (!res.ok) throw new Error(`API error: ${res.status}`);
        const json = await res.json();
        return json.data || json;
      } catch (err) {
        console.error('[CGS] API error:', err);
        return null;
      }
    },

    search(query, page = 1, perPage = 20) {
      return this.get('search', { q: query, page, per_page: perPage });
    },

    game(id) {
      return this.get('game', { id });
    },

    availability(title) {
      return this.get('availability', { title });
    },

    top100(page = 1, perPage = 20) {
      return this.get('top100', { page, per_page: perPage });
    },

    platforms() {
      return this.get('platforms');
    },

    health() {
      return this.get('health');
    },
  };

  // ===========================================================================
  // Theme System
  // ===========================================================================

  const theme = {
    init() {
      state.theme = localStorage.getItem('cgs-theme') || 'dark';
      this.apply(state.theme);
    },

    apply(themeName) {
      document.documentElement.setAttribute('data-theme', themeName);
      if (app) app.setAttribute('data-theme', themeName);
      localStorage.setItem('cgs-theme', themeName);

      if (themeToggle) {
        themeToggle.textContent = themeName === 'dark' ? '☀️' : '🌙';
      }
    },

    toggle() {
      const next = state.theme === 'dark' ? 'light' : 'dark';
      state.theme = next;
      this.apply(next);
    },
  };

  // ===========================================================================
  // UI Components
  // ===========================================================================

  const ui = {
    // --- Create elements with ease ---
    el(tag, attrs = {}, children = []) {
      const el = document.createElement(tag);
      Object.entries(attrs).forEach(([key, val]) => {
        if (key === 'className') el.className = val;
        else if (key === 'dataset') Object.assign(el.dataset, val);
        else if (key === 'style' && typeof val === 'object') Object.assign(el.style, val);
        else if (key.startsWith('on')) el.addEventListener(key.slice(2).toLowerCase(), val);
        else el.setAttribute(key, val);
      });

      const childrenArr = Array.isArray(children) ? children : [children];
      childrenArr.forEach((child) => {
        if (child == null) return;
        if (typeof child === 'string' || typeof child === 'number') {
          el.appendChild(document.createTextNode(child));
        } else if (child instanceof Node) {
          el.appendChild(child);
        }
      });
      return el;
    },

    text(str) {
      return document.createTextNode(str);
    },

    // --- Spinner ---
    spinner() {
      const s = this.el('div', { className: 'cgs-spinner-icon' });
      const wrapper = this.el('div', { className: 'cgs-search-spinner active' }, [s]);
      return wrapper;
    },

    // --- Star rating ---
    starRating(rating) {
      return '\u2B50 ' + (rating ? rating.toFixed(1) : 'N/A');
    },

    // --- Cloud Badge ---
    cloudBadge(platform, status) {
      const labelMap = {
        geforce_now: 'GFN',
        xbox_cloud: 'Xbox',
        ps_plus_cloud: 'PS+',
        amazon_luna: 'Luna',
        boosteroid: 'Boo',
        airgpu: 'Air',
        shadow_pc: 'SHD',
      };

      const normalizedStatus = status === 'always available' ? 'available' : status;
      const cls = ['cgs-card-badge', normalizedStatus].filter(Boolean).join(' ');
      return this.el('span', { className: cls }, [labelMap[platform] || platform]);
    },

    // --- Platform icon for modal ---
    platformIcon(platform) {
      const icons = {
        geforce_now: '🎮',
        xbox_cloud: '🎯',
        ps_plus_cloud: '🎯',
        amazon_luna: '🌙',
        boosteroid: '🚀',
        airgpu: '☁️',
        shadow_pc: '👤',
      };
      return icons[platform] || '🎮';
    },

    // --- Skeleton ---
    renderSkeleton() {
      const header = this.el('div', { className: 'cgs-skeleton-header' });
      const grid = this.el('div', { className: 'cgs-skeleton-grid' });
      for (let i = 0; i < 12; i++) {
        grid.appendChild(this.el('div', { className: 'cgs-skeleton-card' }));
      }
      return this.el('div', { className: 'cgs-skeleton' }, [header, grid]);
    },

    // --- Empty State ---
    renderEmpty(message) {
      return this.el('div', { className: 'cgs-empty' }, [
        this.el('div', { className: 'cgs-empty-icon' }, ['🔍']),
        this.el('div', { className: 'cgs-empty-title' }, ['No Results']),
        this.el('div', { className: 'cgs-empty-text' }, [message || 'Try a different search query.']),
      ]);
    },

    // --- Game Card ---
    createGameCard(game, index) {
      const cover = game.cover
        ? this.el('img', {
            className: 'cgs-game-card-cover',
            src: game.cover,
            alt: game.title,
            loading: 'lazy',
          })
        : this.el('div', {
            className: 'cgs-game-card-cover',
            style: {
              background: 'linear-gradient(135deg, var(--cgs-bg-elevated), var(--cgs-bg-card))',
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'center',
              fontSize: '48px',
              color: 'var(--cgs-text-muted)',
            },
          }, ['🎮']);

      // Cloud badges
      const badgeEls = [];
      if (game.cloud) {
        Object.entries(game.cloud).forEach(([platform, status]) => {
          if (status === 'available' || status === 'always available') {
            badgeEls.push(this.cloudBadge(platform, status));
          }
        });
      }

      const badges = badgeEls.length > 0
        ? this.el('div', { className: 'cgs-card-badges' }, badgeEls.slice(0, 4))
        : null;

      const overlay = this.el('div', { className: 'cgs-game-card-overlay' }, [
        this.el('div', { className: 'cgs-game-card-title' }, [game.title]),
        game.release_year
          ? this.el('div', { className: 'cgs-game-card-year' }, [String(game.release_year)])
          : null,
        badges,
      ]);

      const rating = game.rating > 0
        ? this.el('div', { className: 'cgs-game-card-rating' }, [this.starRating(game.rating)])
        : null;

      const inner = this.el('div', { className: 'cgs-game-card-inner' }, [cover, overlay, rating]);

      const card = this.el('div', {
        className: 'cgs-game-card cgs-fade-in-stagger',
        dataset: { gameId: game.id, slug: game.slug },
        onClick: () => appActions.openGame(game),
      }, [inner]);

      return card;
    },

    // --- Game Grid ---
    renderGameGrid(games, container) {
      container.innerHTML = '';
      if (!games || games.length === 0) {
        container.appendChild(this.renderEmpty(CGS.strings?.no_results || 'No games found.'));
        return;
      }
      games.forEach((game, i) => {
        container.appendChild(this.createGameCard(game, i));
      });
    },

    // --- Suggestions Dropdown ---
    renderSuggestions(suggestions) {
      suggestionsEl.innerHTML = '';

      if (!suggestions || suggestions.length === 0) {
        suggestionsEl.classList.remove('active');
        return;
      }

      suggestions.slice(0, 6).forEach((game) => {
        const cover = game.cover
          ? this.el('img', { className: 'cgs-suggestion-cover', src: game.cover, alt: game.title, loading: 'lazy' })
          : this.el('div', { className: 'cgs-suggestion-cover', style: { background: 'var(--cgs-bg-elevated)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '20px' } }, ['🎮']);

        const title = this.el('div', { className: 'cgs-suggestion-title' }, [game.title]);
        const meta = this.el('div', { className: 'cgs-suggestion-meta' }, [
          game.release_year ? String(game.release_year) : '',
          game.genres?.length ? ' \u00B7 ' + game.genres.slice(0, 2).join(', ') : '',
        ]);
        const info = this.el('div', { className: 'cgs-suggestion-info' }, [title, meta]);

        const badge = game.rating > 0
          ? this.el('span', { className: 'cgs-suggestion-badge' }, [this.starRating(game.rating)])
          : null;

        const item = this.el('div', {
          className: 'cgs-suggestion-item',
          onClick: () => {
            suggestionsEl.classList.remove('active');
            searchInput.value = game.title;
            state.query = game.title;
            appActions.searchGames(game.title);
          },
        }, [cover, info, badge]);

        suggestionsEl.appendChild(item);
      });

      suggestionsEl.classList.add('active');
    },

    // --- Modal: Game Detail ---
    renderGameDetailModal(game) {
      const backdrop = game.background || game.cover || '';

      // Hero section
      const heroImage = backdrop
        ? this.el('img', { className: 'cgs-modal-hero-image', src: backdrop, alt: game.title })
        : null;

      const heroGradient = this.el('div', { className: 'cgs-modal-hero-gradient' });

      // Meta info
      const metaParts = [];
      if (game.release_year) metaParts.push(this.el('span', {}, [String(game.release_year)]));
      if (game.rating > 0) metaParts.push(this.el('span', { className: 'cgs-modal-hero-rating' }, [this.starRating(game.rating)]));
      if (game.metacritic) metaParts.push(this.el('span', { className: 'cgs-modal-hero-metacritic' }, ['MC ' + game.metacritic]));

      const heroMeta = this.el('div', { className: 'cgs-modal-hero-meta' }, metaParts);

      const heroContent = this.el('div', { className: 'cgs-modal-hero-content' }, [
        this.el('div', { className: 'cgs-modal-hero-title' }, [game.title]),
        heroMeta,
      ]);

      const hero = this.el('div', { className: 'cgs-modal-hero' }, [heroImage, heroGradient, heroContent]);

      // Close button
      const closeBtn = this.el('button', {
        className: 'cgs-modal-close',
        onClick: appActions.closeModal,
      }, ['\u2716']);

      // Body
      const body = this.el('div', { className: 'cgs-modal-body' });

      // Description
      if (game.description) {
        const desc = this.el('div', { className: 'cgs-modal-section' }, [
          this.el('div', { className: 'cgs-modal-section-title' }, ['About']),
          this.el('div', { className: 'cgs-modal-description' }, [game.description.substring(0, 500) + (game.description.length > 500 ? '...' : '')]),
        ]);
        body.appendChild(desc);
      }

      // Genres
      if (game.genres && game.genres.length > 0) {
        const genreTags = game.genres.map((g) => this.el('span', { className: 'cgs-genre-tag' }, [g]));
        const genres = this.el('div', { className: 'cgs-modal-section' }, [
          this.el('div', { className: 'cgs-modal-section-title' }, ['Genres']),
          this.el('div', { className: 'cgs-modal-genres' }, genreTags),
        ]);
        body.appendChild(genres);
      }

      // Platforms
      if (game.platforms && game.platforms.length > 0) {
        const platTags = game.platforms.slice(0, 10).map((p) => this.el('span', { className: 'cgs-platform-tag' }, [p]));
        const platformsSection = this.el('div', { className: 'cgs-modal-section' }, [
          this.el('div', { className: 'cgs-modal-section-title' }, ['Platforms']),
          this.el('div', { className: 'cgs-modal-genres' }, platTags),
        ]);
        body.appendChild(platformsSection);
      }

      // Cloud Availability Matrix
      const cloudData = game.cloud || {};
      if (Object.keys(cloudData).length > 0) {
        const matrixItems = Object.entries(cloudData).map(([platform, status]) => {
          const normalizedStatus = status === 'always available' ? 'always' : status;
          return this.el('div', { className: 'cgs-cloud-platform' }, [
            this.el('div', { className: 'cgs-cloud-platform-icon' }, [this.platformIcon(platform)]),
            this.el('div', { className: 'cgs-cloud-platform-name' }, [this.platformLabel(platform)]),
            this.el('div', { className: 'cgs-cloud-platform-status ' + normalizedStatus }, [
              status === 'available' || status === 'always available'
                ? '\u2713 ' + (CGS.strings?.available || 'Available')
                : status === 'not_available'
                  ? '\u2717 ' + (CGS.strings?.not_available || 'Not Available')
                  : '? ' + (CGS.strings?.unknown || 'Unknown'),
            ]),
          ]);
        });

        const matrix = this.el('div', { className: 'cgs-modal-section' }, [
          this.el('div', { className: 'cgs-modal-section-title' }, ['Cloud Availability']),
          this.el('div', { className: 'cgs-cloud-matrix' }, matrixItems),
        ]);
        body.appendChild(matrix);
      }

      const modal = this.el('div', { className: 'cgs-modal' }, [hero, closeBtn, body]);

      modalOverlay.innerHTML = '';
      modalOverlay.appendChild(modal);
      modalOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
    },

    platformLabel(key) {
      const labels = {
        geforce_now: 'GeForce NOW',
        xbox_cloud: 'Xbox Cloud',
        ps_plus_cloud: 'PS Plus',
        amazon_luna: 'Amazon Luna',
        boosteroid: 'Boosteroid',
        airgpu: 'AirGPU',
        shadow_pc: 'Shadow PC',
      };
      return labels[key] || key;
    },

    // --- Load More Button ---
    renderLoadMore() {
      if (loadMoreBtn) {
        loadMoreBtn.style.display = 'flex';
      }
    },

    hideLoadMore() {
      if (loadMoreBtn) {
        loadMoreBtn.style.display = 'none';
      }
    },
  };

  // ===========================================================================
  // App Actions
  // ===========================================================================

  const appActions = {
    async searchGames(query, page = 1, append = false) {
      if (!query || query.trim().length < 2) return;

      state.loading = true;
      state.searching = true;
      suggestionsEl.classList.remove('active');

      // Show spinner in search bar
      const searchContainer = searchInput.closest('.cgs-search-bar');
      const existingSpinner = searchContainer.querySelector('.cgs-search-spinner');
      if (!existingSpinner) {
        searchContainer.appendChild(ui.spinner());
      } else {
        existingSpinner.classList.add('active');
      }

      const results = await api.search(query, page, 20);

      // Remove spinner
      const spinner = searchContainer.querySelector('.cgs-search-spinner');
      if (spinner) spinner.classList.remove('active');

      state.loading = false;
      state.searching = false;

      // Hide Top 100 when searching
      const top100Section = document.getElementById('cgs-top100-section');
      if (top100Section) top100Section.style.display = 'none';

      // Show search results section
      let searchSection = document.getElementById('cgs-search-results-section');
      if (!searchSection) {
        searchSection = ui.el('div', {
          id: 'cgs-search-results-section',
          className: 'cgs-search-results',
        });
        gameGrid.parentNode.insertBefore(searchSection, gameGrid);
      }

      searchSection.style.display = 'block';

      if (!results || !results.games || results.games.length === 0) {
        searchSection.innerHTML = '';
        searchSection.appendChild(ui.renderEmpty());
        ui.hideLoadMore();
        return;
      }

      state.games = append ? [...state.games, ...results.games] : results.games;
      state.total = results.total || 0;
      state.totalPages = results.pages || 1;
      state.page = page;

      const headerTitle = `Results for "${query}"`;
      const header = document.getElementById('cgs-search-header');
      if (header) {
        header.style.display = 'flex';
        header.querySelector('.cgs-section-title').textContent = headerTitle;
        if (header.querySelector('.cgs-section-count')) {
          header.querySelector('.cgs-section-count').textContent = `${state.games.length} of ${state.total}`;
        }
      }

      if (append) {
        state.games.forEach((g, i) => {
          gameGrid.appendChild(ui.createGameCard(g, i + (page - 1) * 20));
        });
      } else {
        gameGrid.innerHTML = '';
        state.games.forEach((game, i) => {
          gameGrid.appendChild(ui.createGameCard(game, i));
        });
      }

      if (page < state.totalPages) {
        ui.renderLoadMore();
        loadMoreBtn.dataset.mode = 'search';
      } else {
        ui.hideLoadMore();
      }
    },

    async loadTop100() {
      if (state.top100Loaded) return;

      const showTop100 = app?.dataset?.showTop100;
      if (showTop100 === 'false') {
        ui.hideLoadMore();
        return;
      }

      gameGrid.innerHTML = '';
      gameGrid.appendChild(ui.renderSkeleton());

      const results = await api.top100(1, 30);

      gameGrid.innerHTML = '';

      if (!results || !results.games || results.games.length === 0) {
        gameGrid.appendChild(ui.renderEmpty('No top games available. Configure your RAWG API key.'));
        ui.hideLoadMore();
        return;
      }

      state.top100 = results.games;
      state.top100Page = 1;
      state.top100TotalPages = results.total_pages || 1;
      state.top100Loaded = true;

      ui.renderGameGrid(state.top100, gameGrid);

      if (state.top100TotalPages > 1) {
        ui.renderLoadMore();
        loadMoreBtn.dataset.mode = 'top100';
      } else {
        ui.hideLoadMore();
      }

      // Show header
      const header = document.getElementById('cgs-search-header');
      if (header) {
        header.style.display = 'flex';
        header.querySelector('.cgs-section-title').textContent = CGS.strings?.top_100_title || 'Top 100 Cloud Games';
        if (header.querySelector('.cgs-section-count')) {
          header.querySelector('.cgs-section-count').textContent = `${results.total || 100} games`;
        }
      }
    },

    async loadMoreTop100() {
      state.top100Page++;
      const results = await api.top100(state.top100Page, 30);

      if (results && results.games) {
        state.top100 = [...state.top100, ...results.games];
        results.games.forEach((game, i) => {
          gameGrid.appendChild(ui.createGameCard(game, i));
        });
      }

      if (state.top100Page >= state.top100TotalPages) {
        ui.hideLoadMore();
      }
    },

    async loadMoreSearch() {
      state.page++;
      await appActions.searchGames(state.query, state.page, true);
    },

    async openGame(game) {
      // Show loading state in modal
      modalOverlay.innerHTML = '';
      const skeleton = ui.renderSkeleton();
      skeleton.style.maxWidth = '800px';
      skeleton.style.margin = '0 auto';
      modalOverlay.appendChild(skeleton);
      modalOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';

      let fullGame = game;

      // Fetch full details if we have an ID
      if (game.id && game.id > 0) {
        const detail = await api.game(game.id);
        if (detail) {
          fullGame = detail;
        }
      }

      // Also fetch fresh availability
      if (!fullGame.cloud || Object.keys(fullGame.cloud).length === 0) {
        const avail = await api.availability(fullGame.title);
        if (avail && avail.platforms) {
          fullGame.cloud = avail.platforms;
          fullGame.cloud_confidence = avail.confidence;
        }
      }

      ui.renderGameDetailModal(fullGame);
    },

    closeModal() {
      modalOverlay.classList.remove('active');
      document.body.style.overflow = '';
      setTimeout(() => {
        modalOverlay.innerHTML = '';
      }, 400);
    },

    handleSearchInput(e) {
      const value = e.target.value.trim();
      state.query = value;

      // Show clear button
      const clearBtn = searchInput.parentElement.querySelector('.cgs-search-clear');
      if (clearBtn) {
        clearBtn.classList.toggle('visible', value.length > 0);
      }

      clearTimeout(state.debounceTimer);

      if (value.length < 2) {
        suggestionsEl.classList.remove('active');
        // Show top100 again
        const searchSection = document.getElementById('cgs-search-results-section');
        if (searchSection) searchSection.style.display = 'none';
        const top100Section = document.getElementById('cgs-top100-section');
        if (top100Section) top100Section.style.display = 'block';
        ui.hideLoadMore();
        state.searchLoaded = false;
        if (!state.top100Loaded) {
          appActions.loadTop100();
        }
        return;
      }

      state.debounceTimer = setTimeout(async () => {
        const results = await api.search(value, 1, 6);
        if (results && results.games) {
          state.suggestions = results.games;
          ui.renderSuggestions(results.games);
        }
      }, 250);
    },

    handleSearchSubmit(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        suggestionsEl.classList.remove('active');
        appActions.searchGames(state.query);
      }
    },

    clearSearch() {
      searchInput.value = '';
      state.query = '';
      const clearBtn = searchInput.parentElement.querySelector('.cgs-search-clear');
      if (clearBtn) clearBtn.classList.remove('visible');
      suggestionsEl.classList.remove('active');

      const searchSection = document.getElementById('cgs-search-results-section');
      if (searchSection) searchSection.style.display = 'none';
      const top100Section = document.getElementById('cgs-top100-section');
      if (top100Section) top100Section.style.display = 'block';
      const header = document.getElementById('cgs-search-header');
      if (header) header.style.display = 'none';
      ui.hideLoadMore();
      state.searchLoaded = false;

      searchInput.focus();
      if (!state.top100Loaded) {
        appActions.loadTop100();
      }
    },
  };

  // ===========================================================================
  // Initialize
  // ===========================================================================

  function init() {
    app = document.getElementById('cloud-gaming-app');
    if (!app) return;

    // Read data attributes
    state.showTop100 = app.dataset.showTop100 !== 'false';
    if (app.dataset.theme) {
      state.theme = app.dataset.theme;
    }

    theme.init();

    // --- Build App HTML ---
    app.innerHTML = '';

    // Theme toggle
    themeToggle = ui.el('button', {
      className: 'cgs-theme-toggle',
      onClick: theme.toggle,
      'aria-label': 'Toggle theme',
    }, [state.theme === 'dark' ? '☀️' : '🌙']);
    app.appendChild(themeToggle);

    // Hero section
    const heroParticles = ui.el('div', { className: 'cgs-hero-particles' });
    for (let i = 0; i < 20; i++) {
      const p = ui.el('div', {
        className: 'cgs-particle',
        style: {
          left: Math.random() * 100 + '%',
          bottom: '0',
          animationDelay: Math.random() * 6 + 's',
          animationDuration: (4 + Math.random() * 4) + 's',
        },
      });
      heroParticles.appendChild(p);
    }

    const hero = ui.el('div', { className: 'cgs-hero' }, [
      ui.el('div', { className: 'cgs-hero-background' }, [
        ui.el('div', { className: 'cgs-hero-gradient' }),
        heroParticles,
      ]),
      ui.el('div', { className: 'cgs-hero-content' }, [
        ui.el('div', { className: 'cgs-logo' }, [
          ui.el('div', { className: 'cgs-logo-icon' }, ['☁️']),
          ui.text('Cloud Loadout'),
        ]),
        ui.el('h1', { className: 'cgs-hero-title' }, [CGS.strings?.hero_title || 'Cloud Gaming Search Engine']),
        ui.el('p', { className: 'cgs-hero-subtitle' }, [CGS.strings?.hero_subtitle || 'Discover where to stream any game across every cloud platform']),
      ]),
    ]);

    app.appendChild(hero);

    // Search container
    const searchIcon = ui.el('span', { className: 'cgs-search-icon' }, ['🔍']);
    searchInput = ui.el('input', {
      className: 'cgs-search-input',
      type: 'text',
      placeholder: CGS.strings?.search_placeholder || 'Search thousands of cloud games...',
      'aria-label': 'Search games',
      onInput: appActions.handleSearchInput,
      onKeyDown: appActions.handleSearchSubmit,
    });
    const clearBtn = ui.el('button', {
      className: 'cgs-search-clear visible',
      onClick: appActions.clearSearch,
    }, ['✕']);
    // Start hidden
    clearBtn.classList.remove('visible');

    const searchBar = ui.el('div', { className: 'cgs-search-bar' }, [searchIcon, searchInput, clearBtn]);

    suggestionsEl = ui.el('div', { className: 'cgs-suggestions' });

    const searchContainer = ui.el('div', { className: 'cgs-search-container' }, [searchBar, suggestionsEl]);
    hero.querySelector('.cgs-hero-content').appendChild(searchContainer);

    // Section header
    const header = ui.el('div', {
      id: 'cgs-search-header',
      className: 'cgs-section-header',
      style: { display: 'none' },
    }, [
      ui.el('h2', { className: 'cgs-section-title' }, [
        ui.text(CGS.strings?.top_100_title || 'Top 100 Cloud Games'),
        ui.el('span', { className: 'cgs-section-count' }, ['...']),
      ]),
    ]);
    app.appendChild(header);

    // Game grid
    gameGrid = ui.el('div', { className: 'cgs-game-grid' });
    app.appendChild(gameGrid);

    // Load more
    loadMoreBtn = ui.el('div', { className: 'cgs-load-more' }, [
      ui.el('button', {
        className: 'cgs-btn-load-more',
        onClick: () => {
          const mode = loadMoreBtn.dataset.mode || 'top100';
          if (mode === 'top100') {
            appActions.loadMoreTop100();
          } else {
            appActions.loadMoreSearch();
          }
        },
      }, [CGS.strings?.load_more || 'Load More Games']),
    ]);
    app.appendChild(loadMoreBtn);

    // Modal overlay
    modalOverlay = ui.el('div', { className: 'cgs-modal-overlay', onClick: (e) => {
      if (e.target === modalOverlay) appActions.closeModal();
    }});
    // Close on escape
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') appActions.closeModal();
    });
    app.appendChild(modalOverlay);

    // Initial load
    appActions.loadTop100();
  }

  // ===========================================================================
  // Boot
  // ===========================================================================

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();