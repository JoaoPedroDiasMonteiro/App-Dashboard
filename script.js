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

    // ajax
    $('#competencia').change((e) => {
        let competencia = $(e.target).val()
        $.ajax({
            // método, url, dados, sucesso, erro
            type: 'GET',
            url: 'app.php',
            data: 'competencia=' + competencia, // x-www-form-urlencoded
            dataType: 'json',
            success: dados => { 
                $('#numeroVendas').html(dados.numeroVendas)
                $('#totalVendas').html('R$ '+dados.totalVendas)
                $('#clientesAtivos').html(dados.clientesAtivos)
                $('#clientesInativos').html(dados.clientesInativos)
                $('#sugestoes').html(dados.sugestoes)
                $('#elogios').html(dados.elogios)
                $('#reclamacoes').html(dados.reclamacoes)
                $('#despesas').html(dados.despesas)
                // console.log(dados);
             },
            error: erros => { console.log(erros) }
        })
        

    })


})