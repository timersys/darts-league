(function($) {
    $('.selectize').selectize();
    $('.generar_llave').on('click', function (e) {
        e.preventDefault();
        var $title = $('input[name="post_title"]').text();

        if( $title.length < 2 ) {
            alert('Primero añade nombre a la fecha');
            return;
        }

        var data = {
            'action': 'generar_llave',
            'participantes': $('.dartsl_participantes').val(),
            'title': $title
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            alert('Got this from the server: ' + response);
        });
    });
})(jQuery);