/* 
 * API para pegar dados pelo CEP
 * Não mudar nada !
 * as ID's dos campos precisa ser igual abaixo.
 */
$(document).ready(function() {
    $("#cep").blur(function() {
        consulta = $("#cep").val()
        var url = "http://cep.correiocontrol.com.br/" + consulta + ".json";
        $.ajax({
            url: url,
            type: "GET",
            dataType: "json",
            success: function(json) {
                $("#rua").val(json.logradouro)
                $("#bairro").val(json.bairro)
                $("#cidade").val(json.localidade)
                $("#uf").val(json.uf)
                $("#numero").focus();
            },
        });//ajax

    });//função blur
});