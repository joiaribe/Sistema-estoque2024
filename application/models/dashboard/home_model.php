<?php

use Query as Query;
use Developer\Tools\Url as Url;

class homeModel extends DashboardModel {

    public function __construct($action = false, $param = false) {
        parent::__construct();
        $id = Session::get('user_id');
        $url = URL;
        if ($action == 'loaded') {
            return $this->loaded($param);
        }
        return print <<<EOFPAGE
<script type="text/javascript">
(function ($) {
    "use strict";
    $(document).ready(function () {
        if ($.fn.plot) {

            var d1 = [{$this->CalcEarningDaily()}];
            var data = ([{
                label: "Intervalo de Dia(s)",
                data: d1,
                lines: {
                    show: true,
                    fill: true,
                    lineWidth: 2,
                    fillColor: {
                        colors: ["rgba(255,255,255,.1)", "rgba(160,220,220,.8)"]
                    }
                }
            }]);
            var options = {
                grid: {
                    backgroundColor: {
                        colors: ["#fff", "#fff"]
                    },
                    borderWidth: 0,
                    borderColor: "#f0f0f0",
                    margin: 0,
                    minBorderMargin: 0,
                    labelMargin: 20,
                    hoverable: true,
                    clickable: true
                },
                legend: {
                    labelBoxBorderColor: "#ccc",
                    show: false,
                    noColumns: 0
                },
                series: {
                    stack: true,
                    shadowSize: 0,
                    highlightColor: 'rgba(30,120,120,.5)'

                },
                // Tooltip
                tooltip: true,
                tooltipOpts: {
                    content: "%s %x R$: %y",
                    shifts: {
                      x: -60,
                      y: 25
                    }
                  },

                
                xaxis: {
                    tickLength: 0,
                    tickDecimals: 0,
                    show: true,
                    min: 2,

                    font: {
                        style: "normal",
                        color: "#666666"
                    }
                },
                yaxis: {
                    ticks: 3,
                    tickDecimals: 0,
                    show: true,
                    tickColor: "#f0f0f0",
                    font: {

                        style: "normal",


                        color: "#666666"
                    }
                },
                //        lines: {
                //            show: true,
                //            fill: true
                //
                //        },
                points: {
                    show: true,
                    radius: 2,
                    symbol: "circle"
                },
                colors: ["#87cfcb", "#48a9a7"]
            };
            var plot = $.plot($("#daily-visit-chart"), data, options);


            $(document).on('click', '.event-close', function () {
                windows.location = this;
                return false;
            });
                    
            
            $(function () {
                $('.evnt-input').keypress(function (e) {
                    var p = e.which;
                    var inText = $('.evnt-input').val();
                    if (p == 13) {
                        if (inText == "") {
                            alert('Campo vazio');
                        } else {
                            $.ajax({
                                url: '{$url}public/dashboard/js/evnt.php',
                                type: 'post',
                                data: {"DataText": inText, "UserId":{$id}},
                                success: function (response) {
                                    $('.event-list').html(response);
                                }
                            });
                        }
                        $(this).val('');
                        $('.event-list').scrollTo('100%', '100%', {
                            easing: 'swing'
                        });
                                    
                        return false;
                        e.epreventDefault();
                        e.stopPropagation();
                    }
                });
            });
            
            // DONUT
           {$this->ServicesDaily()}
        }



        /*==Slim Scroll ==*/
        if ($.fn.slimScroll) {
            $('.event-list').slimscroll({
                height: '305px',
                wheelStep: 20
            });
            $('.conversation-list').slimscroll({
                height: '360px',
                wheelStep: 35
            });
            $('.to-do-list').slimscroll({
                height: '300px',
                wheelStep: 35
            });
        }


        /*==Easy Pie chart ==*/
        if ($.fn.easyPieChart) {
            $('.epie-chart').easyPieChart({
                onStep: function(from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                },
                barColor: "#f8a20f",
                lineWidth: 5,
                size:80,
                trackColor: "#efefef",
                scaleColor:"#cccccc"

            });

        }

        if (Morris.EventEmitter) {
            // Use Morris.Area instead of Morris.Line
            Morris.Area({
                element: 'graph-area',
                padding: 10,
                behaveLikeLine: true,
                gridEnabled: false,
                gridLineColor: '#dddddd',
                axes: true,
                fillOpacity: .7,
                data: [{$this->CalcEarningGraph()}],
                lineColors: ['#ED5D5D', '#D6D23A', '#32D2C9'],
                xkey: 'period',
                ykeys: ['Receitas', 'Produtos', 'Serviços'],
                labels: ['Receitas', 'Produtos', 'Serviços'],
                pointSize: 0,
                lineWidth: 0,
                hideHover: 'auto',
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

                },yaxes: [{
                    /* First y axis */
                }, {
                    /* Second y axis */
                    position: "right" /* left or right */
                }]

            });

        }

        $('.progress-stat-bar li').each(function () {
            $(this).find('.progress-stat-percent').animate({
                height: $(this).attr('data-percent')
            }, 1000);
        });

        $('.todo-check label').click(function () {
            $.ajax({
                url: '{$url}public/dashboard/js/task.php',
                type: 'post',
                data: {"status": this, "UserId":{$id}},
                success: function (response) {
                    $('#sortable-todo').html(response);
               
                }
            });
            return false;
           // $(this).parents('li').children('.todo-title').toggleClass('line-through');
        });


        $('.stat-tab .stat-btn').click(function () {

            $(this).addClass('active');
            $(this).siblings('.btn').removeClass('active');

        });

        $('select.styled').customSelect();
        $("#sortable-todo").sortable();

        /*Chat*/
        $(function () {
            $('.chat-input').keypress(function (ev) {
                var p = ev.which;
                var chatTime = moment().format("h:mm");
                var chatText = $('.chat-input').val();
                if (p == 13) {
                    if (chatText == "") {
                        alert('Empty Field');
                    } else {
                        $.ajax({
                            url: '{$url}public/dashboard/js/chat.php',
                            type: 'post',
                            data: {"Text": chatText, "UserId":{$id}},
                            success: function (response) {
                                $('.conversation-list').html(response);
                            }
                        });
                    }
                    $(this).val('');
                    $('.conversation-list').scrollTo('100%', '100%', {
                        easing: 'swing'
                    });
                    return false;
                    ev.epreventDefault();
                    ev.stopPropagation();
                }
            });


            $('.chat-send .btn').click(function () {
                var chatTime = moment().format("h:mm");
                var chatText = $('.chat-input').val();
                if (chatText == "") {
                    alert('Empty Field');
                    $(".chat-input").focus();
                } else {
                    $.ajax({
                        url: '{$url}public/dashboard/js/chat.php',
                        type: 'post',
                        data: {"Text": chatText, "UserId":{$id}},
                        success: function (response) {
                            $('.conversation-list').html(response);
                        }
                    });
                    $('.chat-input').val('');
                    $(".chat-input").focus();
                    $('.conversation-list').scrollTo('100%', '100%', {
                        easing: 'swing'
                    });
                }
            });
        });

                
    });


})(jQuery);


if (Skycons) {
    /*==Weather==*/
    var skycons = new Skycons({
        "color": "#aec785"
    });
    // on Android, a nasty hack is needed: {"resizeClear": true}
    // you can add a canvas by it's ID...
    skycons.add("icon1", Skycons.RAIN);
    // start animation!
    skycons.play();
    // you can also halt animation with skycons.pause()
    // want to change the icon? no problem:
    skycons.set("icon1", Skycons.RAIN);

}

if (Gauge) {
    /*Knob*/
    var opts = {
        lines: 12, // The number of lines to draw
        angle: 0, // The length of each line
        lineWidth: 0.48, // The line thickness
        pointer: {
            length: 0.6, // The radius of the inner circle
            strokeWidth: 0.03, // The rotation offset
            color: '#464646' // Fill color
        },
        limitMax: 'true', // If true, the pointer will not go past the end of the gauge
        colorStart: '#fa8564', // Colors
        colorStop: '#fa8564', // just experiment with them
        strokeColor: '#F1F1F1', // to see which ones work best for you
        generateGradient: true
    };


    var target = document.getElementById('gauge'); // your canvas element
    var gauge = new Gauge(target).setOptions(opts); // create sexy gauge!
    gauge.maxValue = {$this->CalcEarningsThisMonth()}; // set max gauge value
    gauge.animationSpeed = 32; // set animation speed (32 is default value)
    gauge.set({$this->CalcExpenseThisMonth()}); // set actual value
    gauge.setTextField(document.getElementById("gauge-textfield"));

}
</script>
EOFPAGE;
    }

}

class DashboardModel {

    /**
     * Days of week
     * @var array 
     */
    private $dias_da_semana = array(
        'Domingo',
        'Segunda-Feira',
        'Terça-Feira',
        'Quarta-Feira',
        'Quinta-Feira',
        'Sexta-Feira',
        'Sábado'
    );

    /**
     * Days list
     * @var array 
     */
    var $EarningDailyDays = array();

    /**
     * Interval in Days
     * @var Int 
     */
    var $EarningDailyInterval = 15;

    /**
     * Products Ids
     * @var array
     */
    var $Products = array();

    /**
     * Services list
     * @var array
     */
    var $Services = array();

    /**
     * Limit Reg
     * @var integer 
     */
    var $ServicesLimit = 3;

    /**
     * Limit Reg
     * @var integer 
     */
    var $ProductsLimits = 3;

    /**
     * Data Task Query Result
     * @var array 
     */
    var $dataTask = array();

    /**
     * Earning Dates
     * @var array 
     */
    private $EarningDates = array();

    /**
     * Magic Metthod
     */
    public function __construct() {
        $this->Action();
        $this->_CheckDaysEarningDaily();
        $this->_CheckServicesIds();
        $this->_CheckProductsIds();
    }

    /**
     * Check Actions
     * @return Void
     */
    private function Action() {
        $action = Url::getURL(2);
        if ($action == 'checked') {
            $this->TaskChecked();
        }
        if (isset($action)) {
            switch (Url::getURL(3)) {
                case 'remove_note':
                    $this->DelNote();
                    break;
                case 'add_task':
                    $this->AddTask();
                    break;
                case'delete_task':
                    $this->DelTask();
                    break;

                case 'alt_task':
                    $this->AltTask();
                    break;
            }
        }
    }

    /**
     * Delete task
     * @return Void
     */
    private function TaskChecked() {
        $s = filter_input(INPUT_POST, 'taskcheck');
        $status = $s == 'on' ? true : false;
        $q = new Query;
        $q
                ->update('task')
                ->set(array(
                    'status' => $status
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(3)
                        )
                )
                ->run();
        Dashboard\Call_JS::retornar(URL . 'dashboard/index#sortable-todo');
        die();
    }

    /**
     * Delete task
     * @return Void
     */
    private function DelTask() {
        $q = new Query;
        $q
                ->delete_from('task')
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        Dashboard\Call_JS::retornar(URL . 'dashboard/index#sortable-todo');
    }

    /**
     * Update task
     * @return Void
     */
    private function AltTask() {
        $q = new Query;
        $q
                ->update('task')
                ->set(array(
                    'id_user' => Session::get('user_id'),
                    'title' => filter_input(INPUT_POST, 'name')
                        )
                )
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        Dashboard\Call_JS::retornar(URL . 'dashboard/index#sortable-todo');
    }

    /**
     * Add a new task
     * @return Void
     */
    private function AddTask() {
        $q = new Query;
        $q
                ->insert_into(
                        'task', array(
                    'id_user' => Session::get('user_id'),
                    'title' => filter_input(INPUT_POST, 'name')
                        )
                )
                ->run();
        Dashboard\Call_JS::retornar(URL . 'dashboard/index#sortable-todo');
    }

    /**
     * Del Note
     * @return Void
     */
    private function DelNote() {
        $q = new Query;
        $q
                ->delete_from('notes')
                ->where_equal_to(
                        array(
                            'id' => Url::getURL(4)
                        )
                )
                ->run();
        Dashboard\Call_JS::retornar(URL . 'dashboard/index#event_list');
    }

    /**
     * loade object
     * @param string $param Object to be called
     * @return Object
     */
    protected function loaded($param) {
        switch ($param) {
            case 'BuildProductsDaily':
                $result = $this->BuildProductsDaily();
                break;
            case 'BuildMonthlyIncome':
                $result = $this->BuildMonthlyIncome();
                break;
            case 'BuildHistoryIncome':
                $result = $this->BuildHistoryIncome();
                break;
            case 'BuildNotes':
                $result = $this->BuildNotes();
                break;
            case 'BuildChat':
                $result = $this->BuildChat();
                break;
            case 'BuildTask':
                $result = $this->BuildTask();
                break;
            default :
                $result = false;
                break;
        }
        return print $result;
    }

    /**
     * Make Info Products
     * @return boolean
     */
    private function _CheckProductsIds() {
        $q = new Query;
        $q
                ->select(
                        array(
                            'id_product',
                            'value'
                        )
                )
                ->from('input_product')
                ->where_between(
                        array('date(data)' => array(
                                date('Y-m-d', strtotime('today - 30 days')), date('Y-m-d')
                            )
                        )
                )
                ->group_by('id_product')
                ->order_by('value DESC')
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            foreach ($data as $value) {
                $this->Products[] = array(
                    'Product' => $value['id_product'],
                    'Value' => $value['value'],
                    'Name' => Func::array_table('produtos', array('id' => $value['id_product']), 'nome')
                );
            }
            return true;
        }
    }

    /**
     * Make Info Services
     * @return boolean
     */
    private function _CheckServicesIds() {
        $q = new Query();
        $q
                ->select()
                ->from('input_servico')
                ->group_by('id_service')
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            foreach ($data as $value) {

                $this->Services[] = array(
                    'Service' => $value,
                    'Name' => Func::array_table('servicos', array('id' => $value['id_service']), 'titulo')
                );
            }
            return true;
        }
    }

    /**
     * Check days to query where
     * @return Void
     */
    private function _CheckDaysEarningDaily() {
        $result = array();
        for ($i = 0; $i <= $this->EarningDailyInterval; $i++) {
            $result[] = array(
                'MONTH' => date("m", strtotime("-$i day")),
                'DAY' => date("d", strtotime("-$i day"))
            );
        }
        $this->EarningDailyDays = $result;
    }

    /**
     * Define a limit based on total amount
     * @return Integer
     */
    private function GetLimit() {
        $total = Func::_contarReg('input_servico');
        switch ($total) {
            case 1:
                $r = 1;
                break;
            case 2:
                $r = 2;
                break;
            default :
                $r = $this->ServicesLimit;
                break;
        }
        return $r;
    }

    /**
     * Services Daily JS Data
     * @return string
     */
    protected function ServicesDaily($limit = 3) {
        if (Func::_contarReg('input_servico') > 0) {
            $result = '';

            $q = new Query;
            $q
                    ->select()
                    ->from('input_servico')
                    ->where_equal_to(
                            array(
                                'YEAR(data)' => date('Y'),
                                'MONTH(data)' => date('m'),
                                'status' => true
                            )
                    )
                    ->group_by('id_service')
                    ->limit($limit)
                    ->run();
            $total = $q->get_selected_count();
            $data = $q->get_selected();
            if (!($total > 0 && $data)) {
                return false;
            } else {
                foreach ($data as $value) {
                    $result.= '{label: "' . Func::str_truncate($value['name'], 12) . '", data: ' . $total . '},';
                }
            }
            if ($total > 1) {
                return <<<EOF
var dataPie = [{$result}];
$.plot($(".sm-pie"), dataPie, {
    series: {
        pie: {
            innerRadius: 0.7,
            show: true,
            stroke: {
                width: 0.1,
                color: '#ffffff'
            }
        }
    },

    legend: {
        show: true
    },
    grid: {
        hoverable: true,
        clickable: true
    },

    colors: ["#ffdf7c", "#b2def7", "#efb3e6"]
});            
EOF;
            }
            return NULL;
        }
    }

    /**
     * Products Ids
     * @param Integer $pos Key postion
     * @param String $key Key value
     * @return array Key array
     */
    private function ProductsIds($pos, $key) {
        if (Func::_contarReg('input_product') > 0) {
            $data = array();
            for ($i = 0; $i < $this->ServicesLimit; $i++) {
                $where = array(
                    'YEAR(data)' => date('Y'),
                    'MONTH(data)' => date('m'),
                    'id_product' => @$this->Products[$i]['Product'],
                    'status' => true
                );
                $name = @Func::str_truncate($this->Products[$i]['Name'], 10);
                $total = @Func::_sum_values('input_product', 'value', $where);
                $count = @Func::_sum_values('input_product', 'qnt', $where);
                $calc = ($total !== 0) ? $this->calc_percent_products($total, @$this->Products[$i]['Product']) : 0;
                $data[] = array('NAME' => $name, 'DATA' => $total, 'PERCENT' => $calc);
            }
            return $data[$pos][$key];
        }
    }

    private function SUMREG($table, $column, array $where, array$where_not) {
        $q = new Query();
        $q
                ->select("SUM($column) AS total")
                ->from($table)
                ->where_equal_to($where)
                # ->where_not_equal_to($where_not)
                ->limit(1)
                ->run();
        $data = $q->get_selected();
        return !($data['total'] > 0) ? false : $data['total'];
    }

    /**
     * Cacalc pecent products
     * @param Integer $value Total products
     * @return boolean
     */
    private function calc_percent_products($value, $id) {
        $where = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'status' => true
        );
        $where_not = array('id_product' => $id);
        $total = Func::_contarReg('input_product', $where); #$this->SUMREG('input_product', 'value', $where, $where_not);
        # echo $total;
        $result = ($value / $total) * 100;
        if ($result !== 0) {
            return str_replace(',', '.', round($result, 2));
        } else {
            return false;
        }
    }

    /**
     * total products sales today
     * @return type
     */
    private function total_products_today() {
        $where = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y'),
            'status' => true
        );
        $total = Func::_sum_values('input_product', 'qnt', $where);
        return $total;
    }

    /**
     * Table Sum table
     * @param string $table
     * @param array $date Array date ranger
     * @return float
     */
    private function SumTable($table, $date) {
        $q = new Query;
        $q
                ->select("SUM(value) AS total")
                ->from($table)
                ->limit(1)
                ->where_equal_to(
                        array(
                            'status' => true
                        )
                )
                ->where_between(
                        array(
                            'data' => $date
                        )
                )
                ->run();
        # $q->show();
        $data = $q->get_selected();
        $total = !($data['total'] > 0) ? 0 : $data['total'];
        return $total;
    }

    /**
     * total products sales today
     * @return type
     */
    private function total_tri($limit = 4) {
        $data = array();
        for ($i = 1; $i <= $limit; $i++) {
            $d = ($i * 3) - 3;
            $d1 = $d + 3;
            $date = date("Y-m-d", strtotime("-$d day"));
            $this->EarningDates[$i] = $date;
            $where = array(
                date('Y-m-d H:i:s', strtotime("-$d1 MONTH")),
                date('Y-m-d H:i:s', strtotime("-$d MONTH"))
            );
            if ($i == 1) {
                $data[] = array(
                    'EARNING' => $this->SumTable('input_others', $where),
                    'PRODUCTS' => $this->SumTable('input_product', $where),
                    'SERVICES' => $this->SumTable('input_servico', $where),
                    'PERIOD' => date('Y-m-d H:i:s')
                );
            } else {
                $data[] = array(
                    'EARNING' => $this->SumTable('input_others', $where),
                    'PRODUCTS' => $this->SumTable('input_product', $where),
                    'SERVICES' => $this->SumTable('input_servico', $where),
                    'PERIOD' => date('Y-m-d H:i:s', strtotime("-$d MONTH"))
                );
            }
        }
        return $data;
    }

    /**
     * Chat loop
     * @return boolean|string
     */
    private function _ChatLoop() {
        $q = new Query;
        $q
                ->select()
                ->from('global_chat')
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            $result = '';
            foreach ($data as $value) {
                $avata = GetInfo::_foto($value['id_user']);
                $name = GetInfo::_name($value['id_user']);
                $nice_date = makeNiceTime::MakeNew($value['data']);
                $date = strftime('%d de %B, %Y &aacute;s %H:%M', strtotime($value['data']));
                $class = ($value['id_user'] !== Session::get('user_id')) ? ' odd' : NULL;
                $result .= <<<EOF
    <li class="clearfix{$class}">
        <div class="chat-avatar">
            <img src="{$avata}" width="42" height="42" alt="{$name}">  
        </div>
        <div class="conversation-text">
            <div class="ctext-wrap">
                <i title="{$name}">{$name}</i>
                <p>{$value['msg']}</p>
                <i title="enviado {$date}"> {$nice_date}</i> 
            </div>
        </div>
    </li>
EOF;
            }
            return $result;
        }
    }

    /**
     * note loops
     * @return boolean|string
     */
    private function _NotesLoop() {
        $q = new Query;
        $q
                ->select()
                ->from('notes')
                ->where_equal_to(
                        array(
                            'id_user' => Session::get('user_id')
                        )
                )
                ->run();
        $data = $q->get_selected();
        $count = $q->get_selected_count();
        if (!($data && $count > 0)) {
            return false;
        } else {
            $result = '';
            foreach ($data as $value) {
                $result .= '<li>' . $value['text'] . ' <a href="' . URL . 'dashboard/index/action/remove_note/' . $value['id'] . '" class="event-close"><i class="ico-close2"></i></a></li>';
            }
            return $result;
        }
    }

    /**
     * Products Daily
     * @return string
     */
    protected function BuildNotes() {
        $today = $this->dias_da_semana[date('w')];
        $result = <<<EOFPAGE
    <div class="row" id="event_list">
            <div class="col-md-12">
                <div class="event-calendar clearfix">
                    <div class="col-lg-7 calendar-block">
                        <div id="calendar" class="has-toolbar"></div>
                    </div>
                    <div class="col-lg-5 event-list-block">
                            <div class="cal-day"><span>Hoje</span>{$today}</div>
                            <ul class="event-list">{$this->_NotesLoop()}</ul>
                            <input type="text" class="form-control evnt-input" placeholder="Notas">
                    </div>
                </div>
            </div>
        </div>
EOFPAGE;
        return $result;
    }

    /**
     * Products Daily
     * @return string
     */
    protected function BuildChat() {
        $result = <<<EOFPAGE
            <div class="col-md-6">
                <!--chat start-->
                <section class="panel">
                    <header class="panel-heading">
                        Chat Global <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <div class="chat-conversation">
                            <ul class="conversation-list">{$this->_ChatLoop()}</ul>
                            <div class="row">
                                <div class="col-xs-9">
                                    <input type="text" class="form-control chat-input" placeholder="Insira uma mensagem">
                                </div>
                                <div class="col-xs-3 chat-send">
                                    <button type="submit" class="btn btn-default">Enviar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!--chat end-->
            </div>
EOFPAGE;
        return $result;
    }

    /**
     * Products Daily
     * @return string
     */
    protected function BuildTask() {
        $result = <<<EOFPAGE
                <!--todolist start-->
                <section class="panel">
                    <header class="panel-heading">
                        Tarefas <span class="tools pull-right">
                            <a href="javascript:;" class="fa fa-chevron-down"></a>
                            <a href="javascript:;" class="fa fa-times"></a>
                        </span>
                    </header>
                    <div class="panel-body">
                        <ul class="to-do-list" id="sortable-todo"></ul>
                        <div class="todo-action-bar">
                            <div class="row">
                                <div id="task_box_message" class="col-xs-7 btn-add-task">
                                    <input class="form-control chat-input" id="task-message" placeholder="Insira uma mensagem" type="text">
                                </div>
                                <div class="col-xs-4 btn-add-task">
                                    <a id="task_update_button" class="btn btn-default btn-info"><i class="fa fa-pencil"></i> Editar</a>
                                    <a id="task_new_button" class="btn btn-default btn-info"><i class="fa fa-plus"></i> Novo</a>
                                    <a id="task_show_new_button" href="javascript:;" class="btn btn-default btn-primary"><i class="fa fa-plus"></i> Adicionar Tarefa</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
EOFPAGE;
        return $result;
    }

    /**
     * Check total amount products daily percent bars
     * @param Integer $total
     * @return string
     */
    private function CheckPercentBars($total) {
        if ($total == 1) {
            $data_percent = <<<EOF
<li data-percent="{$this->ProductsIds(0, 'PERCENT')}%"><span class="progress-stat-percent pink"></span></li>
EOF;
        } elseif ($total == 2) {
            $data_percent = <<<EOF
<li data-percent="{$this->ProductsIds(0, 'PERCENT')}%"><span class="progress-stat-percent pink"></span></li>
<li data-percent="{$this->ProductsIds(1, 'PERCENT')}%"><span class="progress-stat-percent"></span></li>
EOF;
        } else {
            $data_percent = <<<EOF
<li data-percent="{$this->ProductsIds(0, 'PERCENT')}%"><span class="progress-stat-percent pink"></span></li>
<li data-percent="{$this->ProductsIds(1, 'PERCENT')}%"><span class="progress-stat-percent"></span></li>
<li data-percent="{$this->ProductsIds(2, 'PERCENT')}%"><span class="progress-stat-percent yellow-b"></span></li>
EOF;
        }
        return $this->BuildPercentBars();
        # return $data_percent;
    }

    private function somatotal($id) {
        $where = array(
            'YEAR(data)' => date('Y'),
            'MONTH(data)' => date('m'),
            'status' => true
        );

        $q = new Query();
        $q
                ->select(
                        array(
                            'id_product',
                            'COUNT(id_product) + qnt as total'
                        )
                )
                ->from('input_product')
                ->where_equal_to($where)
                ->where_not_equal_to(
                        array(
                            'id_product' => $id,
                        )
                )
                ->group_by('id_product')
                ->order_by('total desc')
                ->run();
        $arr = 0;
        foreach ($q->get_selected() as $v) {
            $arr = $arr + $v['total'];
        }

        return $arr;
    }

    private function BuildPercentBars() {
        $where = array(
            'YEAR(data)' => date('Y'),
            'MONTH(data)' => date('m'),
            'status' => true
        );


        $q = new Query();
        $q
                ->select(
                        array(
                            'id_product',
                            'COUNT(id_product) + qnt as total'
                        )
                )
                ->from('input_product')
                ->where_equal_to($where)
                ->group_by('id_product')
                ->order_by('total desc')
                ->limit(3)
                ->run();

        $arr = array();
        foreach ($q->get_selected() as $v) {
            $arr[] = array(
                'ID' => $v['id_product'],
                'TOTAL' => $v['total']
            );
        }


        $total = count($arr);
        $class = array('pink', '', 'yellow-b');
        $i = 0;
        $data_percent = '';
        foreach ($arr as $k => $v) {
            $result = 0;
            $subtotal = $this->somatotal($v["ID"]);
            if ($v["TOTAL"] > 0 && $subtotal > 0) {
                $result = ($v['TOTAL'] / $subtotal) * 100;
            }



            $total = str_replace(',', '.', round($result, 2));
            $c = $i++;
            $data_percent.= <<<EOF
<li data-percent="{$total}%"><span class="progress-stat-percent {$class[$c]}"></span></li>
EOF;
        }

        return $data_percent;
    }

    /**
     * Check total amount products daily bars
     * @param Integer $total
     * @return string
     */
    private function CheckBars($total) {
        if ($total == 1) {
            $bar_legend = <<<EOF
<li><span class="bar-legend-pointer pink"></span>  {$this->ProductsIds(0, 'NAME')}</li>
EOF;
        } elseif ($total == 2) {
            $bar_legend = <<<EOF
<li><span class="bar-legend-pointer pink"></span>  {$this->ProductsIds(0, 'NAME')}</li>
<li><span class="bar-legend-pointer green"></span>  {$this->ProductsIds(1, 'NAME')}</li>
EOF;
        } else {
            $bar_legend = <<<EOF
<li><span class="bar-legend-pointer pink"></span>  {$this->ProductsIds(0, 'NAME')}</li>
<li><span class="bar-legend-pointer green"></span>  {$this->ProductsIds(1, 'NAME')}</li>
<li><span class="bar-legend-pointer yellow-b"></span>  {$this->ProductsIds(2, 'NAME')}</li>
EOF;
        }
        return $bar_legend;
    }

    /**
     * Products Daily
     * @return string
     */
    protected function BuildProductsDaily() {
        $where = array(
            'MONTH(data)' => date('m'),
            'YEAR(data)' => date('Y')
        );
        $total = Func::_contarReg('input_product', $where);

        if ($total > 2) {
            $result = <<<EOFPAGE
<div class="col-md-3">
                <section class="panel">
                    <div class="panel-body">
                        <div class="top-stats-panel">
                            <h4 class="widget-h">Vendas Mensais</h4>
                            <div class="bar-stats">
                                <ul class="progress-stat-bar clearfix">{$this->CheckPercentBars($total)}</ul>
                                <ul class="bar-legend">{$this->CheckBars($total)}</ul>
                                <div class="daily-sales-info">
                                    <span class="sales-count">{$this->total_products_today()} </span> <span class="sales-label">Produtos Vendidos</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
EOFPAGE;
            return $result;
        }
    }

    /**
     * Make DATA JS
     * only days, current year and month
     * @return DATA JS
     */
    private function _MonthlyIcomeCheckData() {
        $cur_date = date("d"); //Y-m-
        $data = array();
        $result = false;

        for ($i = 1; $i <= $cur_date; $i++) {
            $where = array(
                'DAY(data)' => $i,
                'MONTH(data)' => date('m'),
                'YEAR(data)' => date('Y'),
                'status' => true
            );
            $others = Func:: _sum_values('input_others', 'value', $where);
            $products = Func:: _sum_values('input_product', 'value', $where);
            $services = Func:: _sum_values('input_servico', 'value', $where);
            $data [] = $others + $products + $services;
        }
        $i = 1;
        foreach ($data as $value) {
            $v = str_replace(",", ".", $value);
            $result.= ($i++ == count($data)) ? $v : $v . ",";
        }
        return $result;
    }

    /**
     * Make DATA JS
     * only months, current year
     * @return DATA JS
     */
    private function _HistoryIcomeCheckData() {
        $cur_date = date("m"); //Y-m-
        $data = array();
        $result = false;

        for ($i = 1; $i <= $cur_date; $i++) {
            $where = array(
                'MONTH(data)' => $i,
                'YEAR(data)' => date('Y'),
                'status' => true
            );
            $others = Func:: _sum_values('input_others', 'value', $where);
            $products = Func:: _sum_values('input_product', 'value', $where);
            $services = Func:: _sum_values('input_servico', 'value', $where);
            $data [] = $others + $products + $services;
        }
        $i = 1;
        foreach ($data as $value) {
            $v = str_replace(",", ".", $value);
            $result.= ($i++ == count($data)) ? $v : $v . ",";
        }
        return $result;
    }

    /**
     * Build Monthly Income
     * @return string
     */
    protected function BuildMonthlyIncome() {
        $total = Func::_contarReg('input_product', array('MONTH(data)' => date('m')));
        if ($total > 0) {
            $title = strftime("%B") . ' ' . date('Y');
            $result = <<<EOFPAGE
<!--widget graph start-->
                <section class="panel">
                    <div class="panel-body">
                        <div class="monthly-stats pink">
                            <div class="clearfix">
                                <h4 class="pull-left"> {$title}</h4>
                                <!-- Nav tabs -->
                                <div class="btn-group pull-right stat-tab">
                                    <a href="#line-chart" class="btn stat-btn active" data-toggle="tab"><i class="ico-stats"></i></a>
                                    <a href="#bar-chart" class="btn stat-btn" data-toggle="tab"><i class="ico-bars"></i></a>
                                </div>
                            </div>
                            <!-- Tab panes -->
                            <div class="tab-content">
                                <div class="tab-pane active" id="line-chart">
                                    <div class="sparkline" data-type="line" data-resize="true" data-height="75" data-width="90%" data-line-width="1" data-min-spot-color="false" data-max-spot-color="false" data-line-color="#ffffff" data-spot-color="#ffffff" data-fill-color="" data-highlight-line-color="#ffffff" data-highlight-spot-color="#e1b8ff" data-spot-radius="3" data-data="[{$this->_MonthlyIcomeCheckData()}]">
                                    </div>
                                </div>
                                <div class="tab-pane" id="bar-chart">
                                    <div class="sparkline" data-type="bar" data-resize="true" data-height="75" data-width="90%" data-bar-color="#d4a7f5" data-bar-width="10" data-data="[{$this->_MonthlyIcomeCheckData()}]"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                </section>
                <!--widget graph end-->
EOFPAGE;

            return $result;
        } else {
            return '<pre>Sem Dados Disponiveis no momento !</pre>';
        }
    }

    /**
     * Build History Income
     * @return string
     */
    protected function BuildHistoryIncome() {
        $total = Func::_contarReg('input_product', array('MONTH(data)' => date('m'))) + Func::_contarReg('input_others', array('MONTH(data)' => date('m'))) + Func::_contarReg('input_servico', array('MONTH(data)' => date('m')));
        if ($total > 0) {
            $where = array('YEAR(data)' => date('Y'));
            $services = Func:: _sum_values('input_servico', 'value', $where);
            $products = Func:: _sum_values('input_product', 'value', $where);
            $others = Func:: _sum_values('input_others', 'value', $where);
            $total = number_format($services + $products + $others);
            $result = <<<EOFPAGE
                <!--widget graph start-->
                <section class="panel">
                    <div class="panel-body">
                        <ul class="clearfix prospective-spark-bar">
                            <li class="pull-left spark-bar-label">
                                <span class="bar-label-value"> R$ {$total}</span>
                                <span>Histório Anual</span>
                            </li>
                            <li class="pull-right">
                                <div class="sparkline" data-type="bar" data-resize="true" data-height="40" data-width="90%" data-bar-color="#f6b0ae" data-bar-width="5" data-data="[{$this->_HistoryIcomeCheckData()}]"></div>
                            </li>
                        </ul>
                    </div>
                </section>
                <!--widget graph end-->
EOFPAGE;

            return $result;
        } else {
            return '<pre>Sem Dados Disponiveis no momento !</pre>';
        }
    }

    /**
     * JS Data
     * @return string
     */
    protected function CalcEarningGraph() {
        $result = '';
        $i = 1;
        foreach ($this->total_tri() as $value) {
            if ($i++ == count($this->total_tri())) {
                $result.= <<<EOF
{
    period: '{$value['PERIOD']}',
    Receitas: {$value['EARNING']},
    Produtos: {$value['PRODUCTS']},
    Serviços: {$value['SERVICES']}
}
EOF;
            } else {
                $result.= <<<EOF
{
    period: '{$value['PERIOD']}',
    Receitas: {$value['EARNING']},
    Produtos: {$value['PRODUCTS']},
    Serviços: {$value['SERVICES']
                        }
},
EOF;
            }
        }
        return $result;
    }

    /**
     * JS Data
     * @return string
     */
    protected function CalcEarningDaily() {
        $result = '';
        $i = 1;
        foreach ($this->EarningDailyDays as $value) {
            $where = array(
                'DAY(data)' => $value['DAY'],
                'MONTH(data)' => $value['MONTH'],
                'status' => true
            );
            $others = Func:: _sum_values('input_others', 'value', $where);
            $products = Func:: _sum_values('input_product', 'value', $where);
            $services = Func:: _sum_values('input_servico', 'value', $where);
            $total = $others + $products + $services;
            if ($i++ == count($this->EarningDailyDays)) {
                $result .= '[' . $i . ', ' . $total . ']';
            } else {
                $result .= '[' . $i . ',' . $total . ' ],';
            }
        }
        return $result;
    }

    /**
     * Calculate Expense This Month
     * @return float
     */
    protected function CalcExpenseThisMonth() {
        $where = array(
            'MONTH(data)' => date('m'),
            'status' => true
        );

        $others = Func:: _sum_values('output_others', 'value', $where);
        $products = Func:: _sum_values('output_product', 'value', $where);
        $services = Func:: _sum_values('output_servico', 'value', $where);
        return $others + $products + $services;
    }

    /**
     * Calculate Earnings This Month
     * @return float
     */
    protected function CalcEarningsThisMonth() {
        $where = array('MONTH(data)' => date
                    ('m'),
            'status' => true
        );
        $others = Func:: _sum_values('input_others', 'value', $where);
        $products = Func:: _sum_values('input_product', 'value', $where);
        $services = Func:: _sum_values('input_servico', 'value', $where);
        return $others + $products + $services;
    }

    /**
     * Text Change JS Data
     * @return type
     */
    protected function TextEarningDaily() {
        // total today
        $where = array(
            'DAY(data)' => date("d", strtotime("-1 day")),
            'MONTH(data)' => date("m", strtotime("-1 day")),
            'status' => true
        );
        $others = Func:: _sum_values('input_others', 'value', $where);
        $products = Func:: _sum_values('input_product', 'value', $where);
        $services = Func:: _sum_values('input_servico', 'value', $where);
        $total = $others + $products + $services;
        // total yesterday
        $y_where = array(
            'DAY(data)' => date('d'),
            'MONTH(data)' => date('m'),
            'status' => true
        );
        $y_others = Func:: _sum_values('input_others', 'value', $y_where);
        $y_products = Func:: _sum_values('input_product', 'value', $y_where);
        $y_services = Func:: _sum_values('input_servico', 'value', $y_where);
        $tota_yesterday = $y_others + $y_products + $y_services;
        $percent = number_format(( $tota_yesterday / $total) * 100);
        // row
        $row = ($total >= $percent) ? '<i class="fa fa-arrow-up"></i>' : '<i class="fa fa-arrow-down"></i>';
        return <<<EOFPAGE
        $(".visit-chart-value").text('R$ '+{$total});
        $(".visit-chart-title").text('{$percent}

    %');
        $(".visit-chart-title").append('{$row}');
EOFPAGE;
    }

}
