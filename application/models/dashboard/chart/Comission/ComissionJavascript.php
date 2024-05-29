<?php

namespace chart\Comission;

#require 'ComissionConfig.php';

/**
 * HTML used in all class
 * @todo Definir os principais navegadores e o restante como Outros, definir cor de cada navegador ou SO.
 */
class ComissionJavascript extends ComissionConfig {

    /**
     * Prepare data for pie graph
     * @param array $data
     * @return String
     */
    private function _prepare_data_pie($data) {
        $result = '';
        foreach ($data as $k => $v) {
            $l = ucfirst($k);
            $result.= <<<EOF
                    {
                     label: '{$l}',
                     formatted: '{$v['PERCENT']}%',
                     value: {$v['TOTAL']}
                     },
EOF;
        }
        return $result;
    }

    /**
     * Make Script for Top Products
     * @param array $data
     * @return JS
     */
    protected function MakeGetSallers($data) {
        return print <<<EOF
<script>
    // Use Morris.Area instead of Morris.Line
    Morris.Donut({
        element: 'pdonutContainer',
        data: [{$this->_prepare_data_pie($data)}],
        backgroundColor: '#fff',
        labelColor: '#1fb5ac',
        colors: [
            '#E67A77','#D9DD81','#79D1CF','#95D7BB'
        ],
        formatter: function (x, data) { return data.formatted; }
    });
</script>
EOF;
    }

    /**
     * Make Script for Top Services
     * @param array $data
     * @return JS
     */
    protected function MakeTopEmployee($data) {
        return print <<<EOFPAGE
    <script>   
       
    // Use Morris.Area instead of Morris.Line
    Morris.Donut({
        element: 'graph-donut',
        data: [{$this->_prepare_data_pie($data)}],
        backgroundColor: '#fff',
        labelColor: '#1fb5ac',
        colors: [
            '#E67A77','#D9DD81','#79D1CF','#95D7BB'
        ],
        formatter: function (x, data) { return data.formatted; }
    });
</script>
EOFPAGE;
    }

    /**
     * prepare data for inventory graph
     * @param array $data
     * @return String
     */
    private function _prepare_data_graph_inventory($data) {
        $result = '';
        // sort date
        usort($data, function($a1, $a2) {
            @$value1 = strtotime($a1['date']);
            @$value2 = strtotime($a2['date']);
            return $value1 - $value2;
        });
        foreach ($data as $key => $value) {
            $result.= "[$key, $value],";
        }
        return $result;
    }

    private function _prepare_data_graph_report($data) {
        $result = '';

        foreach ($data as $key => $value) {
            $result.= "[$key, $value],";
        }
        return $result;
    }

    /**
     * Check data2 exist any value for show line
     * @param array $data
     * @return string
     */
    private function MAKE_GRAPH_REPORT_CheckData2($data) {
        $arr = array();
        foreach ($data as $value) {
            $arr[] = ($value == false) ? false : true;
        }

        if (in_array(true, $arr)) {
            return <<<EOF
{
        data: data7_2,
        label: "Não Pagas",

        points: {
            show: true
        },
        lines: {
            show: true
        },
        yaxis: 2
    }
EOF;
        }
    }

    protected function MAKE_GRAPH_REPORT($data1, $data2) {
        return print <<<EOF
        <script>
var data7_1 = [{$this->_prepare_data_graph_report($data1)}];
var data7_2 = [{$this->_prepare_data_graph_report($data2)}];
$(function() {
    $.plot($("#visitors-chart #visitors-container"), [{
        data: data7_1,
        label: "Pagas",
        lines: {
            fill: true
        }
    },{$this->MAKE_GRAPH_REPORT_CheckData2($data2)}
    ],
        {
            series: {
                lines: {
                    show: true,
                    fill: false
                },
                points: {
                    show: true,
                    lineWidth: 2,
                    fill: true,
                    fillColor: "#ffffff",
                    symbol: "circle",
                    radius: 5
                },
                shadowSize: 0
            },
            
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#f9f9f9",
                borderWidth: 1,
                borderColor: "#eeeeee"
            },
            colors: ["#79D1CF", "#E67A77"],
            tooltip: true,
            tooltipOpts: {
                content: "%s %x R$: %y",
                shifts: {
                  x: -60,
                  y: 25
                }
              },
            xaxis: {
                 
                 mode: "time",
                 dayNames: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                 monthNames: ["Jan", "Fev", "Mai", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                 timeformat: "%a %d de %b", minTickSize: [4, "day"]

            },
            yaxes: [{
                /* First y axis */
            }, {
                /* Second y axis */
                position: "right" /* left or right */
            }]
        }
    );
});
        </script>
EOF;
    }

    protected function MakeInventory() {
        return print <<<EOF
        <script>
$(function() {
        var Receitas = [{$this->_prepare_data_graph_inventory($this->DataChartInventory)}];
        var Despesas = [{$this->_prepare_data_graph_inventory($this->DataChartInventoryD)}];
        var Lucro = [{$this->_prepare_data_graph_inventory($this->DataChartInventoryL)}];
        var ticks = [{$this->_prepare_data_graph_inventory($this->DataChartInventoryN)}];
        
        var data = [{
            label: " Receitas ",
            data: Receitas,
            lines: {
                show: true,
                fill: true
            },
            points: {
                show: true
            }
        }, {
            label: " Despesas ",
            data: Despesas,
            lines: {
                show: true
            },
            points: {
                show: true
            }
        }, {
            label: " Lucro ",
            data: Lucro,
            bars: {
                show: true
            }
        }];
        var options = {
            xaxis: {
                ticks: ticks,
                mode: "time",
                dayNames: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
                monthNames: ["Jan", "Fev", "Mai", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"],
                timeformat: "%a %d de %b", minTickSize: [4, "day"]
        
            },
            series: {
                shadowSize: 0
            },
            grid: {
                hoverable: true,
                clickable: true,
                tickColor: "#f9f9f9",
                borderWidth: 1,
                borderColor: "#eeeeee"
            },
            colors: ["#79D1CF", "#E67A77"],
            tooltip: true,
            tooltipOpts: {
                content: "%s %x R$: %y",
                shifts: {
                  x: -60,
                  y: 25
                }
            },
            legend: {
                labelBoxBorderColor: "#000000",container: $("#legendcontainer26"),
                noColumns: 0
            }
        };
        var plot = $.plot($("#combine-chart #combine-chartContainer"),
                data, options);
    });
        </script>
EOF;
    }

}
