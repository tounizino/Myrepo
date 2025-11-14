/**
 * Admin interactions for Cloud Gaming Speed Test plugin
 */
(function($) {
    'use strict';

    const state = {
        servers: cgstAdmin.servers || [],
        articles: cgstAdmin.articles || []
    };

    function renderServers() {
        const $list = $('#cgst-server-list');
        if (!$list.length) {
            return;
        }

        if (!state.servers.length) {
            $list.html('<tr><td colspan="5">' + cgstAdmin.i18n.noServers + '</td></tr>');
            return;
        }

        const rows = state.servers.map(server => {
            const dataAttr = $('<div>').text(JSON.stringify(server)).html();
            return `
                <tr data-server='${dataAttr}'>
                    <td><strong>${server.name}</strong><br /><span class="cgst-pill">${server.id}</span></td>
                    <td>${server.location || ''}</td>
                    <td><code>${server.backend || ''}</code></td>
                    <td>${server.weight || 0}</td>
                    <td>
                        <div class="cgst-admin-actions">
                            <button type="button" class="button cgst-edit-server">${cgstAdmin.i18n.edit}</button>
                            <button type="button" class="button button-danger cgst-delete-server">${cgstAdmin.i18n.delete}</button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $list.html(rows.join(''));
    }

    function renderArticles() {
        const $list = $('#cgst-article-list');
        if (!$list.length) {
            return;
        }

        if (!state.articles.length) {
            $list.html('<tr><td colspan="4">' + cgstAdmin.i18n.noArticles + '</td></tr>');
            return;
        }

        const rows = state.articles.map(article => {
            const dataAttr = $('<div>').text(JSON.stringify(article)).html();
            return `
                <tr data-article='${dataAttr}'>
                    <td><strong>${article.title}</strong><br /><a href="${article.url}" target="_blank" rel="noopener noreferrer">${article.url}</a></td>
                    <td><span class="cgst-pill">${article.category || ''}</span></td>
                    <td>${article.description || ''}</td>
                    <td>
                        <div class="cgst-admin-actions">
                            <button type="button" class="button cgst-edit-article">${cgstAdmin.i18n.edit}</button>
                            <button type="button" class="button button-danger cgst-delete-article">${cgstAdmin.i18n.delete}</button>
                        </div>
                    </td>
                </tr>
            `;
        });

        $list.html(rows.join(''));
    }

    function resetServerForm() {
        $('#cgst-server-id').val('');
        $('#cgst-server-form')[0].reset();
    }

    function resetArticleForm() {
        $('#cgst-article-id').val('');
        $('#cgst-article-form')[0].reset();
    }

    function showNotice(message) {
        if (window.wp && wp.a11y && wp.a11y.speak) {
            wp.a11y.speak(message);
        }
        window.alert(message);
    }

    $(document).ready(function() {
        renderServers();
        renderArticles();

        // Server form submit
        $('#cgst-server-form').on('submit', function(event) {
            event.preventDefault();

            const formData = $(this).serializeArray();
            const payload = {};
            formData.forEach(item => {
                payload[item.name] = item.value;
            });

            payload.action = 'cgst_save_server';
            payload.nonce = cgstAdmin.nonce;

            $.post(cgstAdmin.ajaxurl, payload)
                .done(response => {
                    if (response.success) {
                        state.servers = response.data.servers;
                        renderServers();
                        resetServerForm();
                        showNotice(response.data.message);
                    } else {
                        showNotice(response.data.message || cgstAdmin.i18n.genericError);
                    }
                })
                .fail(() => {
                    showNotice(cgstAdmin.i18n.genericError);
                });
        });

        $('#cgst-server-reset').on('click', function() {
            resetServerForm();
        });

        $(document).on('click', '.cgst-edit-server', function() {
            const $row = $(this).closest('tr');
            const server = $row.data('server');
            if (!server) {
                return;
            }

            $('#cgst-server-id').val(server.id || '');
            $('#cgst-server-name').val(server.name || '');
            $('#cgst-server-location').val(server.location || '');
            $('#cgst-server-backend').val(server.backend || '');
            $('#cgst-server-weight').val(server.weight || 0);
            $('#cgst-download-path').val(server.download_path || 'download.php');
            $('#cgst-upload-path').val(server.upload_path || 'upload.php');
            $('#cgst-ping-path').val(server.ping_path || 'ping.php');
            $('#cgst-server-icon').val(server.icon || 'dashicons-admin-site');
            $('#cgst-geo-lat').val(server.geo && server.geo.lat ? server.geo.lat : '');
            $('#cgst-geo-lng').val(server.geo && server.geo.lng ? server.geo.lng : '');
            $('#cgst-server-notes').val(server.notes || '');
        });

        $(document).on('click', '.cgst-delete-server', function() {
            if (!window.confirm(cgstAdmin.i18n.deleteConfirm)) {
                return;
            }

            const $row = $(this).closest('tr');
            const server = $row.data('server');
            if (!server) {
                return;
            }

            $.post(cgstAdmin.ajaxurl, {
                action: 'cgst_delete_server',
                nonce: cgstAdmin.nonce,
                id: server.id
            })
                .done(response => {
                    if (response.success) {
                        state.servers = response.data.servers;
                        renderServers();
                        showNotice(response.data.message);
                    } else {
                        showNotice(response.data.message || cgstAdmin.i18n.genericError);
                    }
                })
                .fail(() => {
                    showNotice(cgstAdmin.i18n.genericError);
                });
        });

        // Articles
        $('#cgst-article-form').on('submit', function(event) {
            event.preventDefault();

            const formData = $(this).serializeArray();
            const payload = {};
            formData.forEach(item => {
                payload[item.name] = item.value;
            });

            payload.action = 'cgst_save_article';
            payload.nonce = cgstAdmin.nonce;

            $.post(cgstAdmin.ajaxurl, payload)
                .done(response => {
                    if (response.success) {
                        state.articles = response.data.articles;
                        renderArticles();
                        resetArticleForm();
                        showNotice(response.data.message);
                    } else {
                        showNotice(response.data.message || cgstAdmin.i18n.genericError);
                    }
                })
                .fail(() => {
                    showNotice(cgstAdmin.i18n.genericError);
                });
        });

        $('#cgst-article-reset').on('click', function() {
            resetArticleForm();
        });

        $(document).on('click', '.cgst-edit-article', function() {
            const $row = $(this).closest('tr');
            const article = $row.data('article');
            if (!article) {
                return;
            }

            $('#cgst-article-id').val(article.id || '');
            $('#cgst-article-title').val(article.title || '');
            $('#cgst-article-url').val(article.url || '');
            $('#cgst-article-description').val(article.description || '');
            $('#cgst-article-category').val(article.category || '');
        });

        $(document).on('click', '.cgst-delete-article', function() {
            if (!window.confirm(cgstAdmin.i18n.deleteConfirm)) {
                return;
            }

            const $row = $(this).closest('tr');
            const article = $row.data('article');
            if (!article) {
                return;
            }

            $.post(cgstAdmin.ajaxurl, {
                action: 'cgst_delete_article',
                nonce: cgstAdmin.nonce,
                id: article.id
            })
                .done(response => {
                    if (response.success) {
                        state.articles = response.data.articles;
                        renderArticles();
                        showNotice(response.data.message);
                    } else {
                        showNotice(response.data.message || cgstAdmin.i18n.genericError);
                    }
                })
                .fail(() => {
                    showNotice(cgstAdmin.i18n.genericError);
                });
        });
    });
})(jQuery);
