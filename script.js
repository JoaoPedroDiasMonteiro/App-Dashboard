$(document).ready(() => {

    $('#documentacao-btn').click(function (e) {
        // $('#pagina').load('documentacao.html')
        $.get('documentacao.html', data => {
            $('#pagina').html(data)
        })
    });

    $('#suporte-btn').click(function (e) {
        // $('#pagina').load('suporte.html')
        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    });


})