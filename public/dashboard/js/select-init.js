$(document).ready(function () {
    $("#e0").select2();
    $("#e1").select2();
    $("#e8").select2();
    $("#e9").select2();
    $("#e10").select2();
    $("#e11").select2();
    $("#e12").select2();
    $("#e13").select2();
    $("#e14").select2();
    $("#e15").select2();
    $("#e2").select2({
        placeholder: "Select a State",
        allowClear: true
    });
    $("#e3").select2({
        minimumInputLength: 2
    });
});