function aviso(url) {
    if (confirm(' Deseja realmente excluir ? '))
        location.href = url;
}
function CustomWarning(url, msg) {
    if (confirm(msg))
        location.href = url;
}

function mark_all(msg, value) {
    if (confirm(msg))
        mark_broadcast.action = value;
    mark_broadcast.submit();
}

function del_all() {
    if (confirm(' Deseja realmente excluir ? '))
        delete_broadcast.submit();
}
(function ($) {
    /* Table select / checkboxes utility */
    $('.checkboxs thead :checkbox').change(function () {
        if ($(this).is(':checked'))
        {
            $('.checkboxs tbody :checkbox').prop('checked', true).parent().addClass('checked');
            $('.checkboxs tbody tr.selectable').addClass('selected');
            $('.checkboxs_actions').show();
        }
        else
        {
            $('.checkboxs tbody :checkbox').prop('checked', false).parent().removeClass('checked');
            $('.checkboxs tbody tr.selectable').removeClass('selected');
            $('.checkboxs_actions').hide();
        }
    });

    $('.checkboxs tbody').on('click', 'tr.selectable', function (e) {
        var c = $(this).find(':checkbox');
        var s = $(e.srcElement);

        if (e.srcElement.nodeName == 'INPUT')
        {
            if (c.is(':checked'))
                $(this).addClass('selected');
            else
                $(this).removeClass('selected');
        }
        else if (e.srcElement.nodeName != 'TD' && e.srcElement.nodeName != 'TR' && e.srcElement.nodeName != 'DIV')
        {
            return true;
        }
        else
        {
            if (c.is(':checked'))
            {
                c.prop('checked', false).parent().removeClass('checked');
                $(this).removeClass('selected');
            }
            else
            {
                c.prop('checked', true).parent().addClass('checked');
                $(this).addClass('selected');
            }
        }
        if ($('.checkboxs tr.selectable :checked').size() == $('.checkboxs tr.selectable :checkbox').size())
            $('.checkboxs thead :checkbox').prop('checked', true).parent().addClass('checked');
        else
            $('.checkboxs thead :checkbox').prop('checked', false).parent().removeClass('checked');

        if ($('.checkboxs tr.selectable :checked').size() >= 1)
            $('.checkboxs_actions').show();
        else
            $('.checkboxs_actions').hide();
    });

    if ($('.checkboxs tbody :checked').size() == $('.checkboxs tbody :checkbox').size() && $('.checkboxs tbody :checked').length)
        $('.checkboxs thead :checkbox').prop('checked', true).parent().addClass('checked');

    if ($('.checkboxs tbody :checked').length)
        $('.checkboxs_actions').show();

    $('.radioboxs tbody tr.selectable').click(function (e) {
        var c = $(this).find(':radio');
        if (e.srcElement.nodeName == 'INPUT')
        {
            if (c.is(':checked'))
                $(this).addClass('selected');
            else
                $(this).removeClass('selected');
        }
        else if (e.srcElement.nodeName != 'TD' && e.srcElement.nodeName != 'TR')
        {
            return true;
        }
        else
        {
            if (c.is(':checked'))
            {
                c.attr('checked', false);
                $(this).removeClass('selected');
            }
            else
            {
                c.attr('checked', true);
                $('.radioboxs tbody tr.selectable').removeClass('selected');
                $(this).addClass('selected');
            }
        }

        // sortable tables
        if ($(".js-table-sortable").length) {
            $(".js-table-sortable").sortable(
                    {
                        placeholder: "ui-state-highlight",
                        items: "tbody tr",
                        handle: ".js-sortable-handle",
                        forcePlaceholderSize: true,
                        helper: function (e, ui)
                        {
                            ui.children().each(function () {
                                $(this).width($(this).width());
                            });
                            return ui;
                        },
                        start: function (event, ui)
                        {
                            if (typeof mainYScroller != 'undefined')
                                mainYScroller.disable();
                            ui.placeholder.html('<td colspan="' + $(this).find('tbody tr:first td').size() + '">&nbsp;</td>');
                        },
                        stop: function () {
                            if (typeof mainYScroller != 'undefined')
                                mainYScroller.enable();
                        }
                    });
        }
    });




    "use strict";
    $(document).ready(function () {
        /*==Left Navigation Accordion ==*/
        if ($.fn.dcAccordion) {
            $('#nav-accordion').dcAccordion({
                eventType: 'click',
                autoClose: true,
                saveState: true,
                disableLink: true,
                speed: 'slow',
                showCount: false,
                autoExpand: true,
                classExpand: 'dcjq-current-parent'
            });
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
        /*==Nice Scroll ==*/
        if ($.fn.niceScroll) {


            $(".leftside-navigation").niceScroll({
                cursorcolor: "#1FB5AD",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

            $(".leftside-navigation").getNiceScroll().resize();
            if ($('#sidebar').hasClass('hide-left-bar')) {
                $(".leftside-navigation").getNiceScroll().hide();
            }
            $(".leftside-navigation").getNiceScroll().show();

            $(".right-stat-bar").niceScroll({
                cursorcolor: "#1FB5AD",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

        }

        /*==Easy Pie chart ==*/
        if ($.fn.easyPieChart) {

            $('.notification-pie-chart').easyPieChart({
                onStep: function (from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                },
                barColor: "#39b6ac",
                lineWidth: 3,
                size: 50,
                trackColor: "#efefef",
                scaleColor: "#cccccc"

            });

            $('.pc-epie-chart').easyPieChart({
                onStep: function (from, to, percent) {
                    $(this.el).find('.percent').text(Math.round(percent));
                },
                barColor: "#5bc6f0",
                lineWidth: 3,
                size: 50,
                trackColor: "#32323a",
                scaleColor: "#cccccc"

            });

        }

        /*== SPARKLINE==*/
        if ($.fn.sparkline) {

            $(".d-pending").sparkline([3, 1], {
                type: 'pie',
                width: '40',
                height: '40',
                sliceColors: ['#e1e1e1', '#8175c9']
            });



            var sparkLine = function () {
                $(".sparkline").each(function () {
                    var $data = $(this).data();
                    ($data.type == 'pie') && $data.sliceColors && ($data.sliceColors = eval($data.sliceColors));
                    ($data.type == 'bar') && $data.stackedBarColor && ($data.stackedBarColor = eval($data.stackedBarColor));

                    $data.valueSpots = {
                        '0:': $data.spotColor
                    };
                    $(this).sparkline($data.data || "html", $data);


                    if ($(this).data("compositeData")) {
                        $spdata.composite = true;
                        $spdata.minSpotColor = false;
                        $spdata.maxSpotColor = false;
                        $spdata.valueSpots = {
                            '0:': $spdata.spotColor
                        };
                        $(this).sparkline($(this).data("compositeData"), $spdata);
                    }
                    ;
                });
            };

            var sparkResize;
            $(window).resize(function (e) {
                clearTimeout(sparkResize);
                sparkResize = setTimeout(function () {
                    sparkLine(true)
                }, 500);
            });
            sparkLine(false);



        }

        /*==Collapsible==*/
        $('.widget-head').click(function (e) {
            var widgetElem = $(this).children('.widget-collapse').children('i');

            $(this)
                    .next('.widget-container')
                    .slideToggle('slow');
            if ($(widgetElem).hasClass('ico-minus')) {
                $(widgetElem).removeClass('ico-minus');
                $(widgetElem).addClass('ico-plus');
            } else {
                $(widgetElem).removeClass('ico-plus');
                $(widgetElem).addClass('ico-minus');
            }
            e.preventDefault();
        });

        /*==Sidebar Toggle==*/

        $(".leftside-navigation .sub-menu > a").click(function () {
            var o = ($(this).offset());
            var diff = 80 - o.top;
            if (diff > 0)
                $(".leftside-navigation").scrollTo("-=" + Math.abs(diff), 500);
            else
                $(".leftside-navigation").scrollTo("+=" + Math.abs(diff), 500);
        });



        $('.sidebar-toggle-box .fa-bars').click(function (e) {

            $(".leftside-navigation").niceScroll({
                cursorcolor: "#1FB5AD",
                cursorborder: "0px solid #fff",
                cursorborderradius: "0px",
                cursorwidth: "3px"
            });

            $('#sidebar').toggleClass('hide-left-bar');
            if ($('#sidebar').hasClass('hide-left-bar')) {
                $(".leftside-navigation").getNiceScroll().hide();
            }
            $(".leftside-navigation").getNiceScroll().show();
            $('#main-content').toggleClass('merge-left');
            e.stopPropagation();
            if ($('#container').hasClass('open-right-panel')) {
                $('#container').removeClass('open-right-panel')
            }
            if ($('.right-sidebar').hasClass('open-right-bar')) {
                $('.right-sidebar').removeClass('open-right-bar')
            }

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }


        });
        $('.toggle-right-box .fa-bars').click(function (e) {
            $('#container').toggleClass('open-right-panel');
            $('.right-sidebar').toggleClass('open-right-bar');
            $('.header').toggleClass('merge-header');

            e.stopPropagation();
        });

        $('.header,#main-content,#sidebar').click(function () {
            if ($('#container').hasClass('open-right-panel')) {
                $('#container').removeClass('open-right-panel')
            }
            if ($('.right-sidebar').hasClass('open-right-bar')) {
                $('.right-sidebar').removeClass('open-right-bar')
            }

            if ($('.header').hasClass('merge-header')) {
                $('.header').removeClass('merge-header')
            }


        });


        $('.panel .tools .fa').click(function () {
            var el = $(this).parents(".panel").children(".panel-body");
            if ($(this).hasClass("fa-chevron-down")) {
                $(this).removeClass("fa-chevron-down").addClass("fa-chevron-up");
                el.slideUp(200);
            } else {
                $(this).removeClass("fa-chevron-up").addClass("fa-chevron-down");
                el.slideDown(200);
            }
        });

        $('.panel .tools .fa-times').click(function () {
            $(this).parents(".panel").parent().remove();
        });

        // tool tips

        $('.tooltips').tooltip();

        // popovers

        $('.popovers').popover();


    });

    /*
     * UniformJS: Sexy form elements
     */
    if ($('.uniformjs').length)
        $('.uniformjs').find("select, input, button, textarea").uniform();
})(jQuery);