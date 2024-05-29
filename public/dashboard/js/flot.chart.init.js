

$(function () {
    var data1 = [];
    var totalPoints = 300;
    function GetData() {
        data1.shift();
        while (data1.length < totalPoints) {
            var prev = data1.length > 0 ? data1[data1.length - 1] : 50;
            var y = prev + Math.random() * 10 - 5;
            y = y < 0 ? 0 : (y > 100 ? 100 : y);
            data1.push(y);
        }
        var result = [];
        for (var i = 0; i < data1.length; ++i) {
            result.push([i, data1[i]])
        }
        return result;
    }
    var updateInterval = 100;


});

$(function () {
    var data1 = GenerateSeries(0);
    var data2 = GenerateSeries(100);
    var data3 = GenerateSeries(200);
    var dataset = [data1, data2, data3];
    function GenerateSeries(added) {
        var data = [];
        var start = 100 + added;
        var end = 200 + added;
        for (i = 1; i <= 100; i++) {
            var d = Math.floor(Math.random() * (end - start + 1) + start);
            data.push([i, d]);
            start++;
            end++;
        }
        return data;
    }
    var options = {
        series: {
            stack: true,
            shadowSize: 0
        },
        grid: {
            hoverable: true,
            clickable: true,
            tickColor: "#f9f9f9",
            borderWidth: 1,
            borderColor: "#eeeeee"
        },
        legend: {
            position: 'nw',
            labelBoxBorderColor: "#000000",
            container: $("#bar-chart #legendPlaceholder20"),
            noColumns: 0
        }
    };
    var plot;
    function ToggleSeries() {
        var d = [];
        $("#toggle-chart input[type='checkbox']").each(function () {
            if ($(this).is(":checked")) {
                var seqence = $(this).attr("id").replace("cbdata", "");
                d.push({
                    label: "data" + seqence,
                    data: dataset[seqence - 1]
                });
            }
        });
        options.series.lines = {};
        options.series.bars = {};
        $("#toggle-chart input[type='radio']").each(function () {
            if ($(this).is(":checked")) {
                if ($(this).val() == "line") {
                    options.series.lines = {
                        fill: true
                    };
                } else {
                    options.series.bars = {
                        show: true
                    };
                }
            }
        });
        $.plot($("#toggle-chart #toggle-chartContainer"), d, options);
    }
    $("#toggle-chart input").change(function () {
        ToggleSeries();
    });
    ToggleSeries();
});
