(function(document, $) {
    $(document).ready(function() {
        $('#ownshell-admin-save').on('click', function() {
            var allowedHosts = $('#allowed_hosts').val();
            var jwtSecret = $('#jwt_secret').val();
            var indicator = $('#ownshell-save-indicator');

            indicator.text('Saving...').removeClass('hidden').removeClass('success').removeClass('error');

            $.ajax({
                method: 'POST',
                url: OC.generateUrl('/apps/ownshell/settings/admin'),
                data: {
                    allowed_hosts: allowedHosts,
                    jwt_secret: jwtSecret
                },
                success: function(response) {
                    if (response.status === 'success') {
                        indicator.text('Saved!').addClass('success');
                    } else {
                        indicator.text('Save failed.').addClass('error');
                    }
                    setTimeout(function() {
                        indicator.addClass('hidden');
                    }, 3000);
                },
                error: function() {
                    indicator.text('Save failed.').addClass('error');
                    setTimeout(function() {
                        indicator.addClass('hidden');
                    }, 3000);
                }
            });
        });
    });
})(document, jQuery);
