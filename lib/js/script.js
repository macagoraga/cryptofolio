 var loader = setInterval(portfolio, 10000);
 var graphrefresh;

$(function() {
  
    var graphs = {};
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: 'yyyy-mm-dd'
    });

    $("#addcoin,#cancel").click(function() {
        $("#coinform").toggle();
    });

   
    portfolio();

    $("body").on("click", "#switchtheme", function() {

        $("body").toggleClass('darkbackground');
        $(".totals td,.frame td").toggleClass('darkmode');
        $(".totals tr,.frame tr").toggleClass('darkmodetr');
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
        
        clearInterval(graphrefresh);
        var s = $(this).attr("id");
        $(".graph,.selectors,.info").remove();

        $(this).after("<tr class='info'>"+
            "<td colspan='5'><span id='volume'></span> <span id='high' class='positive'></span> <span id='low' class='negative'></span></td></tr>"+
            "<tr class='graph'><td colspan='5'><span id='graph'></span></td>" +
            "</tr><tr class='selectors'><td colspan='5'>" +
            "<button class='3month'>3M</button>" +
            "<button class='1month'>1M</button>" +
            "<button class='1week'>1W</button>" +
            "<button class='today'>TODAY</button>" +
            "<button class='realtime'>REALTIME</button>" +
            "<button class='hide'>HIDE</button></tr>");

        if ($("body").hasClass('darkbackground')) {
            $("#graph").addClass('darkmodegraph');
            $(".totals tr,.frame tr,tr.info").addClass('darkmodetr');
            $("button").toggleClass('darkmodebutton');
            $(".label").toggleClass('darklabel');
        }

        $(".hide").on("click", function(event) {
            clearInterval(graphrefresh);
            $(".graph,.selectors,.info").remove();
        });

        $(".realtime").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'today', 30);
        });
        $(".1week").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'history', 7);
        });
        $(".1month").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'history', 30);
        });
        $(".3month").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'history', 90);
        });
        $(".today").on("click", function(event) {
            loadgraph(s, 'today', 300);
        });

        loadgraph(s, 'today', 30);


    });



    function loadgraph(symbol, graphtype, limit) {
        

        $("#graph").html("");
        var chart;        

        var dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit;
        var xhr = $.getJSON( dataurl, function(result) {})

        xhr.success(function(result){
        inputdata = result;
        
        var stats = inputdata[inputdata.length - 2]

        $("#volume").text("Volume: "+parseInt(stats.VOLUME));
        $("#high").text("High: "+formatter(stats.HIGH,"n"));
        $("#low").text("Low: "+formatter(stats.LOW,"n"));

        if (graphtype == 'today') {
            formatted = '%H:%M';
        } else {
            formatted = '%Y-%m-%d';

        }
       
        chart = c3.generate({
            bindto: '#graph',
            data: {
                json: inputdata,
                keys: {
                    x: 'TIMESTAMP',
                    value: ["CLOSE"]
                },
                xFormat: '%Y-%m-%d %H:%M:%S',
                type: 'area',
                colors: {
                    CLOSE:'#e67e22'
                }
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
            area: {
                zerobased: false
            },
            tooltip: {
                contents: function(d, defaultTitleFormat, defaultValueFormat, color) {
                    if ($("body").hasClass("darkbackground")) {
                        return "<span class='label darklabel'>€ " + d[0].value + "</span>";
                    } else {
                        return "<span class='label'>€ " + d[0].value + "</span>";
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
            },
            grid: {
                y: {
                    show: true
                },
                x: {
                    show: true
                }
            },
            legend:{
                show:false
            }

        });

 })
        graphrefresh = setInterval(function() {

        var dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit;
        var xhr = $.getJSON( dataurl, function(result) {})

        xhr.success(function(result){
        inputdata = result;
        
        var stats = inputdata[inputdata.length - 2]

        $("#volume").text("Volume: "+parseInt(stats.VOLUME));
        $("#high").text("High: "+formatter(stats.HIGH,"n"));
        $("#low").text("Low: "+formatter(stats.LOW,"n"));

            chart.load({
                json: inputdata,
                keys: {
                    x: 'TIMESTAMP',
                    value: ['CLOSE']
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
        })
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

                $("#" + i.symbol + " .change").text(formatter(i.change,'p'));
                $("#" + i.symbol + " .price").text(formatter(i.price,'n'));
                $("#" + i.symbol + " .updown").text(formatter(i.updown,'n'));
                $("#" + i.symbol + " .imgholder").prop("src", i.image);
            }
        },
        complete: function(json) {
            update();
        }
    });

}


function update() {

    var profits = 0;
    var profitpct = 0;
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
    if($("#profitPercent").is(":contains('NaN%')")){
        clearInterval(loader);
        $(".totals,.frame").hide();
        $("#addcoin").trigger("click");

    }
    document.title = "CRYPTOFOLIO " + formatter(profits, "n") + "  " + formatter(profitPercent, "p");
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
            o = "€ " + c;
        }
    }

    return o;
}
