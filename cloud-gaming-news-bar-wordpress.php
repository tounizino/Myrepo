<?php
/**
 * Cloud Gaming News Bar — WordPress Widget / Shortcode
 * 
 * Usage:
 *   1. Copy this whole file into your theme's functions.php or a custom plugin
 *   2. Use [cloud_gaming_news] shortcode in any page/post/widget area
 *   3. Or call <?php cloud_gaming_news_render(); ?> directly in your theme
 * 
 * To add/edit news items, modify the $news_items array below.
 */

// Prevent direct access
if (!defined('ABSPATH')) exit;

/**
 * Render the Cloud Gaming News Bar
 */
function cloud_gaming_news_render() {
  // ─── EDIT YOUR NEWS ITEMS HERE ───
  $news_items = [
    [
      'id'        => 1,
      'title'     => 'NVIDIA GeForce NOW Ultimate Adds RTX 5090 Support',
      'category'  => 'Hardware',
      'date'      => '2 hours ago',
      'icon'      => '⚡',
      'iconColor' => 'purple',
      'desc'      => 'GeForce NOW Ultimate tier now powered by NVIDIA Blackwell RTX 5090 GPUs for unmatched cloud streaming performance at up to 240 FPS.',
      'detail'    => 'NVIDIA has officially rolled out RTX 5090 support across all GeForce NOW Ultimate data centers. Subscribers can now experience native 4K at 240 FPS with full ray tracing and DLSS 4 upscaling. The upgrade also reduces streaming latency by up to 35% compared to the previous RTX 4080 Super PODs. Available immediately for Ultimate members on all supported devices.',
      'stats'     => ['240 FPS', '4K Native', 'DLSS 4', '35% lower latency'],
      'link'      => '#',
      'linkText'  => 'Read full announcement'
    ],
    [
      'id'        => 2,
      'title'     => 'Xbox Cloud Gaming Lands on Samsung 2025 Smart TVs',
      'category'  => 'Platform',
      'date'      => '6 hours ago',
      'icon'      => '🎮',
      'iconColor' => 'green',
      'desc'      => 'Microsoft expands Xbox Cloud Gaming to Samsung 2025 smart TV lineup with native controller support and 120 FPS streaming.',
      'detail'    => 'Microsoft and Samsung have partnered to bring native Xbox Cloud Gaming to Samsung\'s 2025 Smart TV lineup (Neo QLED, OLED, and The Frame). The built-in app supports Bluetooth controller pairing, 120 FPS at 1440p, and quick resume. No console required — just a Game Pass Ultimate subscription and a Wi-Fi 6 connection. Rolling out starting this week across 28 countries.',
      'stats'     => ['120 FPS', '1440p', 'Wi-Fi 6', '28 countries'],
      'link'      => '#',
      'linkText'  => 'See supported TV models'
    ],
    [
      'id'        => 3,
      'title'     => 'Amazon Luna Rebrands to "Luna+" with New Publisher Deals',
      'category'  => 'Update',
      'date'      => '1 day ago',
      'icon'      => '☁️',
      'iconColor' => 'orange',
      'desc'      => 'Amazon rebrands cloud gaming service as Luna+, adds Ubisoft+, EA Play, and indie publisher channels at a reduced monthly price.',
      'detail'    => 'Amazon has unveiled the biggest overhaul to Luna since launch. Now called Luna+, the service drops to $9.99/month and includes access to Ubisoft+, EA Play, and a curated indie collection from Devolver Digital and Annapurna Interactive. All channels feature cloud saves, cross-play, and streaming to Fire TV, web, iOS, Android, and select LG smart TVs. New 4K HDR streaming and spatial audio support also launch with the rebrand.',
      'stats'     => ['$9.99/mo', '4K HDR', 'Spatial Audio', 'Ubisoft+ & EA Play'],
      'link'      => '#',
      'linkText'  => 'Explore Luna+ plans'
    ]
  ];

  // Escape everything for safety
  $safe_items = [];
  foreach ($news_items as $item) {
    $safe_items[] = [
      'id'        => intval($item['id']),
      'title'     => esc_html($item['title']),
      'category'  => esc_html($item['category']),
      'date'      => esc_html($item['date']),
      'icon'      => esc_html($item['icon']),
      'iconColor' => esc_attr($item['iconColor']),
      'desc'      => esc_html($item['desc']),
      'detail'    => esc_html($item['detail']),
      'stats'     => array_map('esc_html', $item['stats']),
      'link'      => esc_url($item['link']),
      'linkText'  => esc_html($item['linkText'])
    ];
  }

  // JSON for JS
  $news_json = json_encode($safe_items, JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);

  // ─── OUTPUT ───
  ob_start();
  ?>
  <style>
    /* ── Reset / Base ── */
    .cloud-news-wrapper *,
    .cloud-news-wrapper *::before,
    .cloud-news-wrapper *::after {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    .cloud-news-wrapper {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
      width: 100%;
      max-width: 1100px;
      margin: 0 auto;
      padding: 0;
      background: transparent;
      position: relative;
    }

    /* ── Ticker Bar ── */
    .cloud-news-ticker {
      position: relative;
      width: 100%;
      height: 54px;
      background: rgba(255, 255, 255, 0.55);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(0, 0, 0, 0.06);
      border-radius: 14px;
      display: flex;
      align-items: center;
      overflow: hidden;
      cursor: pointer;
      transition: border-color 0.3s ease;
    }

    .cloud-news-ticker:hover {
      border-color: rgba(0, 0, 0, 0.12);
    }

    .cloud-news-ticker-label {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 0 18px 0 20px;
      height: 100%;
      flex-shrink: 0;
      border-right: 1px solid rgba(0, 0, 0, 0.07);
      user-select: none;
    }

    .cloud-news-ticker-label .pulse-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: #3478f6;
      flex-shrink: 0;
      animation: cnPulse 2s ease-in-out infinite;
    }

    @keyframes cnPulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.35; }
    }

    .cloud-news-ticker-label span {
      font-size: 12px;
      font-weight: 600;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      color: rgba(0, 0, 0, 0.5);
    }

    .cloud-news-ticker-track-wrap {
      flex: 1;
      overflow: hidden;
      height: 100%;
      position: relative;
    }

    .cloud-news-ticker-track {
      display: flex;
      align-items: center;
      height: 100%;
      white-space: nowrap;
      will-change: transform;
    }

    .cloud-news-ticker-item {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 0 28px;
      height: 100%;
      font-size: 14px;
      font-weight: 500;
      color: rgba(0, 0, 0, 0.75);
      cursor: pointer;
      transition: color 0.2s ease;
      position: relative;
      flex-shrink: 0;
    }

    .cloud-news-ticker-item::after {
      content: '';
      position: absolute;
      right: 0;
      top: 50%;
      transform: translateY(-50%);
      width: 3px;
      height: 3px;
      border-radius: 50%;
      background: rgba(0, 0, 0, 0.18);
    }

    .cloud-news-ticker-item:last-child::after { display: none; }

    .cloud-news-ticker-item .ticker-cat {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 2px 8px;
      border-radius: 6px;
      background: rgba(52, 120, 246, 0.08);
      color: #3478f6;
    }

    .cloud-news-ticker-item:hover { color: #3478f6; }

    .cloud-news-ticker-arrow {
      flex-shrink: 0;
      padding: 0 18px 0 10px;
      display: flex;
      align-items: center;
      color: rgba(0, 0, 0, 0.25);
      transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
    }

    .cloud-news-ticker-arrow svg { width: 18px; height: 18px; }

    .cloud-news-ticker-arrow.open {
      transform: rotate(180deg);
      color: #3478f6;
    }

    /* ── News Cards Container ── */
    .cloud-news-cards {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.55s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.4s ease;
      opacity: 0;
    }

    .cloud-news-cards.open {
      max-height: 2000px;
      opacity: 1;
    }

    .cloud-news-cards-inner {
      display: flex;
      flex-direction: column;
      gap: 10px;
      padding: 12px 0 0 0;
    }

    .cloud-news-card {
      background: rgba(255, 255, 255, 0.55);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(0, 0, 0, 0.06);
      border-radius: 14px;
      padding: 20px 24px;
      display: grid;
      grid-template-columns: auto 1fr auto;
      gap: 20px;
      align-items: start;
      cursor: pointer;
      transition: border-color 0.3s ease, background 0.3s ease;
      position: relative;
    }

    .cloud-news-card:hover {
      border-color: rgba(0, 0, 0, 0.12);
      background: rgba(255, 255, 255, 0.7);
    }

    .cloud-news-card .card-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: 18px;
      background: rgba(52, 120, 246, 0.08);
      color: #3478f6;
    }

    .cloud-news-card .card-icon.purple { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
    .cloud-news-card .card-icon.green  { background: rgba(16, 185, 129, 0.08); color: #10b981; }
    .cloud-news-card .card-icon.orange { background: rgba(251, 146, 60, 0.08); color: #fb923c; }
    .cloud-news-card .card-icon.pink   { background: rgba(236, 72, 153, 0.08); color: #ec4899; }
    .cloud-news-card .card-icon.cyan   { background: rgba(6, 182, 212, 0.08); color: #06b6d4; }

    .cloud-news-card .card-content { min-width: 0; }

    .cloud-news-card .card-meta {
      display: flex;
      align-items: center;
      gap: 10px;
      margin-bottom: 5px;
      flex-wrap: wrap;
    }

    .cloud-news-card .card-category {
      font-size: 10px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      padding: 2px 10px;
      border-radius: 6px;
      background: rgba(52, 120, 246, 0.08);
      color: #3478f6;
    }

    .cloud-news-card .card-category.purple { background: rgba(139, 92, 246, 0.08); color: #8b5cf6; }
    .cloud-news-card .card-category.green  { background: rgba(16, 185, 129, 0.08); color: #10b981; }
    .cloud-news-card .card-category.orange { background: rgba(251, 146, 60, 0.08); color: #fb923c; }

    .cloud-news-card .card-date {
      font-size: 11px;
      color: rgba(0, 0, 0, 0.35);
      font-weight: 450;
    }

    .cloud-news-card .card-title {
      font-size: 16px;
      font-weight: 600;
      color: rgba(0, 0, 0, 0.85);
      line-height: 1.4;
      margin-bottom: 4px;
    }

    .cloud-news-card .card-desc {
      font-size: 13px;
      color: rgba(0, 0, 0, 0.5);
      line-height: 1.55;
      display: -webkit-box;
      -webkit-line-clamp: 2;
      -webkit-box-orient: vertical;
      overflow: hidden;
    }

    .cloud-news-card .card-expand-icon {
      flex-shrink: 0;
      width: 28px;
      height: 28px;
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(0, 0, 0, 0.2);
      transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease, background 0.3s ease;
      margin-top: 4px;
    }

    .cloud-news-card .card-expand-icon svg { width: 16px; height: 16px; }

    .cloud-news-card:hover .card-expand-icon {
      color: #3478f6;
      background: rgba(52, 120, 246, 0.06);
    }

    .cloud-news-card .card-expand-icon.open {
      transform: rotate(180deg);
      color: #3478f6;
      background: rgba(52, 120, 246, 0.06);
    }

    .cloud-news-detail {
      max-height: 0;
      overflow: hidden;
      transition: max-height 0.45s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.35s ease, padding 0.35s ease;
      opacity: 0;
      padding: 0 24px 0 84px;
    }

    .cloud-news-detail.open {
      max-height: 800px;
      opacity: 1;
      padding: 0 24px 18px 84px;
    }

    .cloud-news-detail-inner {
      padding-top: 14px;
      border-top: 1px solid rgba(0, 0, 0, 0.06);
    }

    .cloud-news-detail p {
      font-size: 14px;
      line-height: 1.7;
      color: rgba(0, 0, 0, 0.6);
      margin-bottom: 12px;
    }

    .cloud-news-detail .detail-stats {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 14px;
    }

    .cloud-news-detail .detail-stat {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      font-weight: 500;
      color: rgba(0, 0, 0, 0.45);
      padding: 4px 12px;
      border-radius: 8px;
      background: rgba(0, 0, 0, 0.03);
    }

    .cloud-news-detail .detail-stat svg { width: 14px; height: 14px; opacity: 0.6; }

    .cloud-news-detail .detail-link {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      font-size: 13px;
      font-weight: 500;
      color: #3478f6;
      text-decoration: none;
      padding: 6px 16px;
      border-radius: 8px;
      background: rgba(52, 120, 246, 0.06);
      transition: background 0.25s ease;
    }

    .cloud-news-detail .detail-link:hover {
      background: rgba(52, 120, 246, 0.12);
    }

    .cloud-news-detail .detail-link svg { width: 14px; height: 14px; }

    @media (max-width: 700px) {
      .cloud-news-ticker { height: 46px; border-radius: 10px; }
      .cloud-news-ticker-label { padding: 0 12px 0 14px; }
      .cloud-news-ticker-label span { display: none; }
      .cloud-news-ticker-item { font-size: 13px; padding: 0 16px; }
      .cloud-news-ticker-item .ticker-cat { display: none; }
      .cloud-news-card {
        grid-template-columns: 36px 1fr;
        gap: 14px;
        padding: 16px 18px;
      }
      .cloud-news-card .card-icon { width: 36px; height: 36px; font-size: 15px; }
      .cloud-news-card .card-expand-icon { display: none; }
      .cloud-news-detail { padding: 0 18px 0 68px; }
      .cloud-news-detail.open { padding: 0 18px 16px 68px; }
    }

    @media (max-width: 480px) {
      .cloud-news-ticker-label .pulse-dot { width: 6px; height: 6px; }
      .cloud-news-ticker-item { font-size: 12px; padding: 0 12px; }
      .cloud-news-ticker-arrow { padding: 0 12px 0 6px; }
      .cloud-news-ticker-arrow svg { width: 15px; height: 15px; }
    }
  </style>

  <div class="cloud-news-wrapper" data-cn-wrapper></div>

  <script>
  (function() {
    'use strict';

    const NEWS_ITEMS = <?php echo $news_json; ?>;

    const wrapper = document.querySelector('[data-cn-wrapper]');
    if (!wrapper) return;

    // ─── Build HTML ───
    wrapper.innerHTML = `
      <div class="cloud-news-ticker" data-cn-ticker>
        <div class="cloud-news-ticker-label">
          <span class="pulse-dot"></span>
          <span>Live</span>
        </div>
        <div class="cloud-news-ticker-track-wrap">
          <div class="cloud-news-ticker-track" data-cn-track></div>
        </div>
        <div class="cloud-news-ticker-arrow" data-cn-arrow>
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
      </div>
      <div class="cloud-news-cards" data-cn-cards>
        <div class="cloud-news-cards-inner" data-cn-cards-inner></div>
      </div>
    `;

    const ticker     = wrapper.querySelector('[data-cn-ticker]');
    const track      = wrapper.querySelector('[data-cn-track]');
    const arrow      = wrapper.querySelector('[data-cn-arrow]');
    const cardsWrap  = wrapper.querySelector('[data-cn-cards]');
    const cardsInner = wrapper.querySelector('[data-cn-cards-inner]');
    let isOpen = false;
    let tickerAnimId = null;
    let tickerPos = 0;

    // ─── Build ticker ───
    function buildTicker() {
      const items = NEWS_ITEMS.map(n =>
        `<span class="cloud-news-ticker-item" data-id="${n.id}">
          <span class="ticker-cat">${n.category}</span>
          ${n.title}
        </span>`
      ).join('');
      track.innerHTML = items + items;
    }

    // ─── Build cards ───
    function buildCards() {
      cardsInner.innerHTML = NEWS_ITEMS.map(n => {
        const statsHtml = n.stats.map(s =>
          `<span class="detail-stat">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            ${s}
          </span>`
        ).join('');

        return `
          <div class="cloud-news-card" data-card-id="${n.id}">
            <div class="card-icon ${n.iconColor}">${n.icon}</div>
            <div class="card-content">
              <div class="card-meta">
                <span class="card-category ${n.iconColor}">${n.category}</span>
                <span class="card-date">${n.date}</span>
              </div>
              <div class="card-title">${n.title}</div>
              <div class="card-desc">${n.desc}</div>
            </div>
            <div class="card-expand-icon" data-card-expand>
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="cloud-news-detail" data-card-detail>
              <div class="cloud-news-detail-inner">
                <div class="detail-stats">${statsHtml}</div>
                <p>${n.detail}</p>
                <a href="${n.link}" class="detail-link" target="_blank" rel="noopener">
                  ${n.linkText}
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
              </div>
            </div>
          </div>`;
      }).join('');
    }

    // ─── Ticker anim ───
    function startTicker() {
      if (tickerAnimId) return;
      function step() {
        const itemWidth = track.scrollWidth / 2;
        tickerPos -= 0.6;
        if (Math.abs(tickerPos) >= itemWidth) tickerPos = 0;
        track.style.transform = `translateX(${tickerPos}px)`;
        tickerAnimId = requestAnimationFrame(step);
      }
      tickerAnimId = requestAnimationFrame(step);
    }

    function stopTicker() {
      if (tickerAnimId) { cancelAnimationFrame(tickerAnimId); tickerAnimId = null; }
    }

    // ─── Toggle bar ───
    function toggleBar() {
      isOpen = !isOpen;
      arrow.classList.toggle('open', isOpen);
      cardsWrap.classList.toggle('open', isOpen);
      if (isOpen) {
        stopTicker();
      } else {
        cardsInner.querySelectorAll('.cloud-news-card').forEach(c => {
          c.querySelector('[data-card-detail]').classList.remove('open');
          c.querySelector('[data-card-expand]').classList.remove('open');
        });
        startTicker();
      }
    }

    function toggleCard(cardEl) {
      const detail = cardEl.querySelector('[data-card-detail]');
      const expand = cardEl.querySelector('[data-card-expand]');
      const isOpenDetail = detail.classList.contains('open');
      cardsInner.querySelectorAll('.cloud-news-card').forEach(c => {
        if (c !== cardEl) {
          c.querySelector('[data-card-detail]').classList.remove('open');
          c.querySelector('[data-card-expand]').classList.remove('open');
        }
      });
      if (isOpenDetail) {
        detail.classList.remove('open');
        expand.classList.remove('open');
      } else {
        detail.classList.add('open');
        expand.classList.add('open');
      }
    }

    function scrollToCard(id) {
      const card = cardsInner.querySelector(`[data-card-id="${id}"]`);
      if (!card) return;
      if (!isOpen) {
        isOpen = true;
        arrow.classList.add('open');
        cardsWrap.classList.add('open');
        stopTicker();
      }
      cardsInner.querySelectorAll('.cloud-news-card').forEach(c => {
        c.querySelector('[data-card-detail]').classList.remove('open');
        c.querySelector('[data-card-expand]').classList.remove('open');
      });
      card.querySelector('[data-card-detail]').classList.add('open');
      card.querySelector('[data-card-expand]').classList.add('open');
      setTimeout(() => {
        const top = card.getBoundingClientRect().top + window.scrollY - 110;
        window.scrollTo({ top, behavior: 'smooth' });
      }, 80);
    }

    // ─── Events ───
    ticker.addEventListener('click', (e) => {
      if (e.target.closest('.cloud-news-ticker-item')) return;
      toggleBar();
    });

    track.addEventListener('click', (e) => {
      const item = e.target.closest('.cloud-news-ticker-item');
      if (!item) return;
      scrollToCard(parseInt(item.dataset.id, 10));
    });

    cardsInner.addEventListener('click', (e) => {
      const card = e.target.closest('.cloud-news-card');
      if (!card || e.target.closest('.detail-link')) return;
      toggleCard(card);
    });

    ticker.addEventListener('mouseenter', stopTicker);
    ticker.addEventListener('mouseleave', () => { if (!isOpen) startTicker(); });

    buildTicker();
    buildCards();
    startTicker();
  })();
  </script>
  <?php
  return ob_get_clean();
}

// ─── Register Shortcode ───
add_shortcode('cloud_gaming_news', 'cloud_gaming_news_render');