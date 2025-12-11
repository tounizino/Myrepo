(function () {
  const ready = (fn) => {
    if (document.readyState !== 'loading') {
      fn();
    } else {
      document.addEventListener('DOMContentLoaded', fn);
    }
  };

  ready(() => {
    const nav = document.querySelector('[data-nav-toggle]');
    const navLinks = document.getElementById('fmhy-nav');

    if (nav && navLinks) {
      nav.addEventListener('click', () => {
        const expanded = nav.getAttribute('aria-expanded') === 'true';
        nav.setAttribute('aria-expanded', (!expanded).toString());
        navLinks.classList.toggle('is-open', !expanded);
      });
    }

    const themeToggle = document.querySelector('[data-theme-toggle]');
    const body = document.body;

    const applyTheme = (mode) => {
      body.dataset.theme = mode;
      document.cookie = `fmhy_theme=${mode};path=/;max-age=${60 * 60 * 24 * 365};SameSite=Lax`;
    };

    const initialTheme = (window.fmhyTheme && window.fmhyTheme.theme) || body.dataset.theme || 'light';
    applyTheme(initialTheme);

    themeToggle?.addEventListener('click', () => {
      const next = body.dataset.theme === 'dark' ? 'light' : 'dark';
      applyTheme(next);
    });

    document.querySelectorAll('.fmhy-nav__group summary').forEach((summary) => {
      summary.addEventListener('click', (event) => {
        const details = summary.parentElement;
        const isOpen = details.hasAttribute('open');
        document.querySelectorAll('.fmhy-nav__group').forEach((group) => {
          if (group !== details) {
            group.removeAttribute('open');
          }
        });
        if (isOpen) {
          details.removeAttribute('open');
        } else {
          details.setAttribute('open', 'open');
        }
        event.preventDefault();
      });
    });
  });
})();
