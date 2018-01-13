$(document).ready(function () {

    // Confirmação, por meio de confirm da exclusão de registros
    $(".btn-delete").click(
        function ( event ) {
            var acao = confirm("Are you sure you want to delete the record?");
            if ( acao == false ) {
                event.preventDefault();
            }
        }
    );

    if (document.getElementsByClassName("titulo-pagina")) {
        var titulo = document.getElementsByClassName("titulo-pagina")[0];
        titulo.innerHTML = titulo.innerHTML.replace("-&gt;", "<i class='fa fa-arrow-right'></i>");
    }
        
});