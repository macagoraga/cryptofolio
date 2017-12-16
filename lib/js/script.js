$(function() {
    "use strict";
    var graphs = {};
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: 'yyyy-mm-dd'
    });

    $("#addcoin,#cancel").click(function() {
        $("#coinform").toggle();
    });

    var loader = setInterval(portfolio, 10000);
    portfolio();

    $("body").on("click", "#switchtheme", function() {

        $("body").toggleClass('darkbackground');
        $("td").toggleClass('darkmode');
        $("tr").toggleClass('darkmodetr');
        $("#graph").toggleClass('darkmodegraph');
        $("button").toggleClass('darkmodebutton');
        $(".label").toggleClass('darklabel');

        if ($("body").hasClass('darkbackground')) {
            Cookies.set('theme', 'dark');
        } else {
            Cookies.set('theme', 'light');

        }

    });


    $(".item").on("click", function(event) {

        s = $(this).attr("id");
        $(".graph,.selectors").remove();

        $(this).after("<tr class='graph'><td colspan='5'><span id='graph'></span></td>" +
            "</tr><tr class='selectors'><td colspan='5'>" +
            "<button class='3month'>3M</button>" +
            "<button class='1month'>1M</button>" +
            "<button class='1week'>1W</button>" +
            "<button class='today'>TODAY</button>" +
            "<button class='realtime'>REALTIME</button>" +
            "<button class='hide'>HIDE</button></tr>");

        if ($("body").hasClass('darkbackground')) {
            $("#graph").addClass('darkmodegraph');
            $("button").toggleClass('darkmodebutton');
            $(".label").toggleClass('darklabel');
        }

        $(".hide").on("click", function(event) {
            $(".graph,.selectors").remove();
        });

        $(".realtime").on("click", function(event) {
            loadgraph(s, 'today', 30);
        });
        $(".1week").on("click", function(event) {
            loadgraph(s, 'history', 7);
        });
        $(".1month").on("click", function(event) {
            loadgraph(s, 'history', 30);
        });
        $(".3month").on("click", function(event) {
            loadgraph(s, 'history', 90);
        });
        $(".today").on("click", function(event) {
            loadgraph(s, 'today', 300);
        });

        loadgraph(s, 'today', 30);


    });



    function loadgraph(symbol, graphtype, limit) {

        dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit;

        if (graphtype == 'today') {
            formatted = '%H:%M';
        } else {
            formatted = '%Y-%m-%d';

        }
        chart = c3.generate({
            bindto: '#graph',

            data: {
                url: dataurl,
                mimeType: 'json',
                keys: {
                    x: 'TIMESTAMP',
                    value: ['HIGH', 'LOW', "OPEN", "CLOSE"]
                },
                xFormat: '%Y-%m-%d %H:%M:%S',
                type: 'spline'

            },
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: formatted
                    }
                },
                y: {
                    tick: {
                        format: function(x) {
                            return "€ " + x.toFixed(2);
                        }
                    }
                }
            },
            tooltip: {
                contents: function(d, defaultTitleFormat, defaultValueFormat, color) {
                    if ($("body").hasClass("darkbackground")) {
                        return "<span class='label darklabel'>High - € " + d[0].value + "</span>" + "<br>" + "<span class='label darklabel'>Low - € " + d[1].value + "</span>" + "<br>" + "<span class='label darklabel'>Open - € " + d[1].value + "</span>" + "<br>" + "<span class='label darklabel'>Close - € " + d[1].value + "</span>";
                    } else {
                        return "<span class='label'>High - € " + d[0].value + "</span>" + "<br>" + "<span class='label'>Low - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Open - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Close - € " + d[1].value + "</span>";
                    }

                },
                position: function() {
                    var position = c3.chart.internal.fn.tooltipPosition.apply(this, arguments);
                    position.top = -320;
                    return position;
                }
            },
            zoom: {
                enabled: true
            },
            size: {
                height: 350
            },
            point: {
                show: false
            }

        });


        var graphrefresh = setInterval(function() {

            chart.load({
                url: dataurl,
                mimeType: 'json',
                keys: {
                    x: 'TIMESTAMP',
                    value: ['OPEN', 'CLOSE', 'HIGH', 'LOW']
                },
                axis: {
                    x: {
                        type: 'timeseries',
                        tick: {
                            format: formatted
                        }
                    },
                    y: {
                        tick: {
                            format: d3.format("€.")
                        }
                    }
                }
            });
        }, 30000);

    }

    if (Cookies.get('theme') == 'dark') {
        $("#switchtheme").trigger("click");
    }

});

function portfolio() {

   
    $.ajax({
        type: "GET",
        url: "portfolio.php",
        dataType: "json",
        success: function(json) {
            for (var item in json) {

                i = json[item];

                if (i.change > 0) {
                    c = "+" + i.change;
                } else {
                    c = i.change;
                }

                $("#" + i.symbol + " .change").text(i.change + "%");
                $("#" + i.symbol + " .price").text("€ " + i.price);
                $("#" + i.symbol + " .updown").text("€ " + i.updown);
                $("#" + i.symbol + " .imgholder").prop("src", i.image);
            }
        },
        complete: function(json) {
            update();
        }
    });
}

function formatter(i, t) {
    
    c = parseFloat(i).toFixed(2);

    if (t == "p") {
        if (c > 0) {
            o = "+" + c + "%";
        } else {
            o = c + "%";
        }
    }

    if (t == "n") {
        if (c > 0) {
            o = "€ " + c;
        } else {
            o = "€ -" + c;
        }
    }

    return o;
}

function update() {
    var profits = 0;
    var profitpct = 0;
    var profittmp = 0;
    var sum = 0;

    $(".paid").each(function() {
        sum += parseFloat($(this).text().replace("€ ", ""));
    });
    $(".updown").each(function() {
        profits += parseFloat($(this).text().replace("€ ", ""));
    });

    profitPercent = ((profits / sum) * 100);

    t = (sum + profits)

    $("#total").text(formatter(sum, "n"))
    $("#profit").text(formatter(profits, "n"))
    $("#totalValue").text(formatter(t, "n"))
    $("#profitPercent").text(formatter(profitPercent, "p"))

    $(".updownpct").each(function() {
        i = $(this).index(".updownpct")
        paid = $(".paid:eq(" + i + ")").text().replace("€ ", "")
        currentpl = parseFloat($(".updown:eq(" + i + ")").text().replace("€ ", ""))
        updownpct = ((currentpl / paid) * 100).toFixed(2)

        $(this).text(formatter(updownpct, "p"))

    })

    $(".updownpct:contains('-'),#profitPercent:contains('-'),.updown:contains('-'),.change:contains('-'),#profit:contains('-')").removeClass('positive negative').addClass('negative')
    $(".updownpct:not(:contains('-')),#profitPercent:not(:contains('-')),.updown:not(:contains('-')),.change:not(:contains('-')),#profit:not(:contains('-'))").removeClass('positive negative').addClass('positive')
    $("#totals,#holder").show()

    $('.negative').each(function(i) {
        $(this).animate({
            backgroundColor: '#F1414A',
            color: '#ffffff'
        }, 'fast', 'swing', function() {
            $(this).animate({
                backgroundColor: 'transparent',
                color: '#F1414A'
            }, 'fast', 'swing');
        })
    })
    $('.positive').each(function(i) {
        $(this).animate({
            backgroundColor: '#80BD72',
            color: '#ffffff'
        }, 'fast', 'swing', function() {
            $(this).animate({
                backgroundColor: 'transparent',
                color: '#80BD72'
            }, 'fast', 'swing');
        })
    })

    document.title = "CRYPTOFOLIO € " + formatter(profits, "n") + "  " + formatter(profitPercent, "p");
}