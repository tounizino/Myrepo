(function ($) {
  'use strict';

  if (typeof CloudGamingPosts === 'undefined') {
    return;
  }

  const labels = CloudGamingPosts.i18n || {
    previous: '← Previous',
    next: 'Next →',
    noPosts: 'No posts found.',
    minRead: '%s min read'
  };

  function buildPagination($container, totalPages, currentPage) {
    const $pagination = $container.find('.latest-pagination');

    if (!$pagination.length) {
      return;
    }

    $pagination.empty();

    if (totalPages <= 1) {
      $pagination.hide();
      return;
    }

    $pagination.show();

    const $prev = $('<button/>', {
      type: 'button',
      class: 'prev',
      text: labels.previous,
      disabled: currentPage === 1
    });

    const $next = $('<button/>', {
      type: 'button',
      class: 'next',
      text: labels.next,
      disabled: currentPage === totalPages
    });

    $pagination.append($prev);

    for (let page = 1; page <= totalPages; page += 1) {
      const $pageBtn = $('<button/>', {
        type: 'button',
        text: page,
        'data-page': page
      });

      if (page === currentPage) {
        $pageBtn.addClass('active');
      }

      $pagination.append($pageBtn);
    }

    $pagination.append($next);
  }

  function renderError($container, message) {
    const $grid = $container.find('.latest-grid');
    $grid.html('<p class="latest-empty-message">' + message + '</p>');
  }

  function scrollIntoView($container) {
    const offsetTop = $container.offset().top;
    $('html, body').animate({ scrollTop: Math.max(0, offsetTop - 40) }, 300);
  }

  function loadPage($container, targetPage) {
    const currentPage = parseInt($container.data('current-page'), 10) || 1;
    const totalPages = parseInt($container.data('total-pages'), 10) || 1;

    if (targetPage === currentPage || targetPage < 1 || targetPage > totalPages) {
      return;
    }

    const postsPerPage = parseInt($container.data('posts-per-page'), 10) || 9;
    const $grid = $container.find('.latest-grid');
    const $pagination = $container.find('.latest-pagination');

    $container.addClass('is-loading');
    $pagination.addClass('is-loading');

    $.post(CloudGamingPosts.ajaxUrl, {
      action: 'cglp_load_posts',
      nonce: CloudGamingPosts.nonce,
      page: targetPage,
      posts_per_page: postsPerPage
    })
      .done(function (response) {
        if (!response || !response.success) {
          renderError($container, labels.noPosts);
          return;
        }

        const data = response.data || {};
        const html = data.html || '';
        const total = parseInt(data.total_pages, 10) || 1;
        const current = parseInt(data.current_page, 10) || targetPage;

        $grid.html(html || '<p class="latest-empty-message">' + labels.noPosts + '</p>');
        $container.data('current-page', current);
        $container.attr('data-current-page', current);
        $container.data('total-pages', total);
        $container.attr('data-total-pages', total);

        buildPagination($container, total, current);
        scrollIntoView($container);
      })
      .fail(function () {
        renderError($container, labels.noPosts);
      })
      .always(function () {
        $container.removeClass('is-loading');
        $pagination.removeClass('is-loading');
      });
  }

  $(function () {
    $('.cglp-latest-posts').each(function () {
      const $container = $(this);
      const totalPages = parseInt($container.data('total-pages'), 10) || 1;
      const currentPage = parseInt($container.data('current-page'), 10) || 1;

      buildPagination($container, totalPages, currentPage);

      $container.on('click', '.latest-pagination button[data-page]', function () {
        const page = parseInt($(this).data('page'), 10);
        if (page) {
          loadPage($container, page);
        }
      });

      $container.on('click', '.latest-pagination .prev', function () {
        const current = parseInt($container.data('current-page'), 10) || 1;
        loadPage($container, current - 1);
      });

      $container.on('click', '.latest-pagination .next', function () {
        const current = parseInt($container.data('current-page'), 10) || 1;
        loadPage($container, current + 1);
      });
    });
  });
})(jQuery);
