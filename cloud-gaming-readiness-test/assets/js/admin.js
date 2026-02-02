jQuery(document).ready(function($) {
    const modal = $('#cgrt-platform-modal');
    const overlay = $('#cgrt-modal-overlay');

    function openModal() {
        modal.show();
        overlay.show();
    }

    function closeModal() {
        modal.hide();
        overlay.hide();
        $('#edit-servers-container').empty();
        $('#edit-platform-id').val('');
        $('#edit-platform-name').val('');
        $('#edit-platform-enabled').prop('checked', true);
    }

    $('#close-modal-btn, #cgrt-modal-overlay').on('click', closeModal);

    $('#add-platform-btn').on('click', function() {
        $('#modal-title').text('Add New Platform');
        openModal();
    });

    $(document).on('click', '.edit-platform-btn', function() {
        const platform = $(this).closest('tr').data('platform');
        $('#modal-title').text('Edit Platform: ' + platform.name);
        $('#edit-platform-id').val(platform.id);
        $('#edit-platform-name').val(platform.name);
        $('#edit-platform-enabled').prop('checked', platform.enabled);
        
        platform.servers.forEach(server => addServerRow(server));
        openModal();
    });

    function addServerRow(server = {region: '', url: '', protocol: 'https'}) {
        const row = $(`
            <div class="server-row" style="margin-bottom: 10px; border: 1px solid #eee; padding: 10px;">
                <input type="text" placeholder="Region" class="server-region" value="${server.region}">
                <input type="text" placeholder="URL" class="server-url" value="${server.url}">
                <select class="server-protocol">
                    <option value="https" ${server.protocol === 'https' ? 'selected' : ''}>HTTPS</option>
                    <option value="http" ${server.protocol === 'http' ? 'selected' : ''}>HTTP</option>
                </select>
                <button class="button remove-server-btn">Remove</button>
            </div>
        `);
        $('#edit-servers-container').append(row);
    }

    $('#add-server-btn').on('click', () => addServerRow());

    $(document).on('click', '.remove-server-btn', function() {
        $(this).closest('.server-row').remove();
    });

    $('#save-platform-btn').on('click', function() {
        const servers = [];
        $('.server-row').each(function() {
            servers.push({
                region: $(this).find('.server-region').val(),
                url: $(this).find('.server-url').val(),
                protocol: $(this).find('.server-protocol').val()
            });
        });

        const platformData = {
            id: $('#edit-platform-id').val(),
            name: $('#edit-platform-name').val(),
            enabled: $('#edit-platform-enabled').is(':checked'),
            servers: servers
        };

        $.post(ajaxurl, {
            action: 'cgrt_save_platform',
            nonce: cgrt_admin_nonce,
            platform: platformData
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert('Error saving platform: ' + response.data);
            }
        });
    });

    $(document).on('click', '.delete-platform-btn', function() {
        if (!confirm('Are you sure you want to delete this platform?')) return;
        const platform = $(this).closest('tr').data('platform');
        $.post(ajaxurl, {
            action: 'cgrt_delete_platform',
            nonce: cgrt_admin_nonce,
            id: platform.id
        }, function(response) {
            if (response.success) {
                location.reload();
            }
        });
    });
});
