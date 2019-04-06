(function($) {
    $('.selectize').selectize();

    /**
     * Generar llave
     */
    $('.generar_llave').on('click', function (e) {
        e.preventDefault();


        const $button = $(this);
        let $title = '';
        const    $post_id = $('#post_ID').val();
       if( $('input[name="post_title"]').length ) {
           $title = $('input[name="post_title"]').text();
       } else if( $('#post-title-0').length ) {
           $title = $('#post-title-0').val();
       } else {
           $title = "Fecha del dia";
       }
        $button.addClass('is-busy').prop('disabled',true);
        if( $title.length < 2 ) {
            alert('Primero añade título a la fecha a disputar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }

        if( ! $post_id.length ) {
            alert('Primero guarda la fecha antes de continuar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }

        const data = {
            'action': 'generar_llave',
            'participantes': $('.dartsl_participantes').val(),
            'torneo_id': $('.dartsl_torneo').val(),
            'title': $title,
            'post_id': $post_id
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            if( response.error ) {
                alert(response.error);
            }
            if( response.success) {
                $('#generar_llave_success').html('Llave generada en <a href="https://challonge.com/'+response.success+'" target="_blank">https://challonge.com/'+response.success+'</a>')
                $('.comenzar_torneo_p').fadeIn();
            }
            $button.removeClass('is-busy').prop('disabled',false);
        }, 'json');
    });
    /**
     * Arranca torneo
     */
    $('.comenzar_torneo').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const $post_id = $('#post_ID').val();

        $button.addClass('is-busy').prop('disabled',true);

        if( ! $post_id.length ) {
            alert('Primero guarda la fecha antes de continuar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }

        const data = {
            'action': 'comenzar_torneo',
            'post_id': $post_id
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            if( response.error ) {
                alert(response.error);
            }
            if( response.success) {
                $('.generar_llave').hide();
                $('.comenzar_torneo').hide();
                $('.dartsl_participantes')[0].selectize.disable();
                $('#generar_llave_success').append(' <span> Torneo comenzado!</span>')
            }
            $button.removeClass('is-busy').prop('disabled',false);
        }, 'json');
    });

    $('.cargar_resultados').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const $post_id = $('#post_ID').val();

        $button.addClass('is-busy').prop('disabled',true);

        if( ! $post_id.length ) {
            alert('Primero guarda la fecha antes de continuar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }

        const data = {
            'action': 'cargar_resultados',
            'post_id': $post_id
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            if( response.error ) {
                alert(response.error);
            }
            if( response.success) {
                if(response.success.matches) {
                    const $partidos = response.success.matches,
                        $participantes = response.success.participants;
                    let $html_partidos ='',
                        $html_rankings = '';

                    $partidos.forEach(function (partido, index, array) {
                        $html_partidos += '<tr>' +
                            '<td class="left_match">' +
                                '<input type="hidden" name="winner[]" value="'+ partido.winner+'">' +
                                '<div class="match_name"><span>' + partido.player1_name + ' </span><input type="hidden" name="player1_id[]" value="'+ partido.player1_id+'"></div>' +
                                '<div class="match_avg">Avg <input type="number" step="0.01" name="player1_avg[]" value=""></div>' +
                                '<div class="match_co">CO <input type="number" step="0.01" name="player1_co[]" value=""></div>' +
                            '</td>' +
                            '<td><input type="text" class="player_score" name="player1_score[]" value="'+ partido.player1_score+'" disabled><input type="hidden" name="player1_score[]" value="'+ partido.player1_score+'"></td>' +
                            '<td><input type="text" class="player_score" name="player2_score[]" value="'+ partido.player2_score+'" disabled><input type="hidden" name="player2_score[]" value="'+ partido.player2_score+'"></td>' +
                            '<td class="right_match">' +
                            '<div class="match_name"><span>' + partido.player2_name + '</span> <input type="hidden" name="player2_id[]" value="'+ partido.player2_id+'"></div>' +
                            '<div class="match_avg"><input type="number" step="0.01" name="player2_avg[]" value=""> Avg</div>' +
                            '<div class="match_co"><input type="number" step="0.01" name="player2_co[]" value=""> CO</div>' +
                            '</td>' +
                            '</tr>';
                    });

                    $participantes.forEach(function (participante, index, array) {
                        $html_rankings += '<input type="hidden" name="ranking['+participante.participant.misc+']" value="'+ participante.participant.final_rank+'">';
                    });
                    $('#partidos').html($html_partidos).hide().fadeIn();
                    $('#rankings').html($html_rankings);
                    $('.cargar_resultados').hide();
                    $('.editor-post-publish-button').trigger('click');
                }
            }
            $button.removeClass('is-busy').prop('disabled',false);
        }, 'json');
    });

    $('.obtener_datos').on('click', function (e) {
        e.preventDefault();

        const $button = $(this);
        const $post_id = $('#post_ID').val();
        const $url = $('.challonge_url_field').val();

        $button.addClass('is-busy').prop('disabled',true);

        if( ! $post_id.length ) {
            alert('Primero guarda la fecha antes de continuar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }
        if( ! $url.length ) {
            alert('Primero agrega la url de challonge antes de continuar');
            $button.removeClass('is-busy').prop('disabled',false);
            return;
        }

        const data = {
            'action': 'obtener_datos_existentes',
            'post_id': $post_id,
            'url': $url
        };

        // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.post(ajaxurl, data, function(response) {
            if( response.error ) {
                alert(response.error);
            }
            if( response.success) {
                if(response.success.matches) {
                    const $partidos = response.success.matches,
                    $participantes = response.success.participants;
                    let $html_partidos ='',
                        $html_rankings = '';

                    $partidos.forEach(function (partido, index, array) {
                        $html_partidos += '<tr>' +
                            '<td class="left_match">' +
                                '<input type="hidden" name="winner[]" value="'+ partido.winner+'">' +
                                '<div class="match_name">' + partido.player1_name + ' <select name="player1_id[]" class="dartsl_participante challo_' + partido.player1_challonge_id + '" placeholder="Nombre en la web?"></select></div>' +
                                '<div class="match_avg">Avg <input type="number" step="0.01" name="player1_avg[]" value=""></div>' +
                                '<div class="match_co">CO <input type="number" step="0.01" name="player1_co[]" value=""></div>' +
                            '</td>' +
                            '<td><input type="text" class="player_score" name="player1_score[]" value="'+ partido.player1_score+'" disabled><input type="hidden" name="player1_score[]" value="'+ partido.player1_score+'"></td>' +
                            '<td><input type="text" class="player_score" name="player2_score[]" value="'+ partido.player2_score+'" disabled><input type="hidden" name="player2_score[]" value="'+ partido.player2_score+'"></td>' +
                            '<td class="right_match">' +
                            '<div class="match_name">' + partido.player2_name + ' <select name="player2_id[]" class="dartsl_participante challo_' + partido.player2_challonge_id + '" placeholder="Nombre en la web?"></select></div>' +
                            '<div class="match_avg"><input type="number" step="0.01" name="player2_avg[]" value=""> Avg</div>' +
                            '<div class="match_co"><input type="number" step="0.01" name="player2_co[]" value=""> CO</div>' +
                            '</td>' +
                            '</tr>';
                    });
                    $participantes.forEach(function (participante, index, array) {
                        console.log(participante);
                        $html_rankings += '<input type="hidden" name="ranking[challo_'+participante.participant.id+']" value="'+ participante.participant.final_rank+'">';
                    });
                    $('#partidos').html($html_partidos).hide().fadeIn();
                    $('#rankings').html($html_rankings);
                    $('.dartsl_participante').append($('.dartsl_participantes_placeholder > option').clone()).selectize();
                    $('.dartsl_participante').each(function() {
                        const $input = $(this);

                        $(this).on('change',  function(e) {
                            const real_id = $input.val();
                            const $classy = $input.attr('class').split(' ').filter(function (a){ return a.indexOf('challo_') !== -1}).join('.');
                            // actualizo otros selects
                            jQuery('.'+$classy).each(function(){
                                if( typeof jQuery(this)[0].selectize != 'undefined') {
                                    jQuery(this)[0].selectize.setValue(real_id,true);
                                }
                            });
                            // actualizo rankins
                            $('input[name="ranking['+$classy+']"]').attr('name','ranking['+real_id+']')
                        });

                    });
                }
            }
            $button.removeClass('is-busy').prop('disabled',false);
        }, 'json');
    });

})(jQuery);