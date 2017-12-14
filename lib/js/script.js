$(function() {
   



    var chart

    graphs = {}
    $('[data-toggle="datepicker"]').datepicker({
        autoHide: true,
        format: 'yyyy-mm-dd'
    });

    $("#addcoin,#cancel").click(function() {
        $("#coinform").toggle()
    })

    var loader = setInterval(portfolio, 10000);
    portfolio()

    $("body").on("click", "#switchtheme",function(){

        $("body").toggleClass('darkbackground')
        $("td").toggleClass('darkmode');
        $("tr").toggleClass('darkmodetr');
        $("#graph").toggleClass('darkmodegraph');
        $("button").toggleClass('darkmodebutton');
        $(".label").toggleClass('darklabel')

        if($("body").hasClass('darkbackground')){
            Cookies.set('theme', 'dark');
        }else{
            Cookies.set('theme', 'light');

        }
             
    })
   

    $(".item").on("click", function(event) {

        $(".graph,.selectors").remove()
        var symbol
        var dataurl

        symbol = $(this).attr("id")

        $(this).after("<tr class='graph'><td colspan='5'><span id='graph'></span></td>" +
            "</tr><tr class='selectors'><td colspan='5'>" +
            "<button class='3month'>3M</button>" +
            "<button class='1month'>1M</button>" +
            "<button class='1week'>1W</button>" +
            "<button class='today'>TODAY</button>" +
            "<button class='realtime'>REALTIME</button>" +
            "<button class='hide'>HIDE</button></tr>")

        if($("body").hasClass('darkbackground')){
             $("#graph").addClass('darkmodegraph');
             $("button").toggleClass('darkmodebutton');
             $(".label").toggleClass('darklabel')
        }

        $(".hide").on("click", function(event) {
            $(".graph,.selectors").remove()
        })

        $(".history").on("click", function(event) {
            loadgraph(symbol, 'history', 90)
        })
        $(".realtime").on("click", function(event) {
            loadgraph(symbol, 'today', 30)
        })
        $(".1week").on("click", function(event) {
            loadgraph(symbol, 'history', 7)
        })
        $(".1month").on("click", function(event) {
            loadgraph(symbol, 'history', 30)
        })
        $(".3month").on("click", function(event) {
            loadgraph(symbol, 'history', 90)
        })
        $(".today").on("click", function(event) {
            loadgraph(symbol, 'today', 300)
        })

        loadgraph(symbol, 'today', 30);


    })



    function loadgraph(symbol, graphtype, limit) {

        dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit

        if (graphtype == 'today') {
            formatted = '%H:%M'
        } else {
            formatted = '%Y-%m-%d'

        }
        var chart = c3.generate({
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
                    if($("body").hasClass("darkbackground")){
                       return "<span class='label darklabel'>High - € " + d[0].value + "</span>" + "<br>" + "<span class='label darklabel'>Low - € " + d[1].value + "</span>" + "<br>" + "<span class='label darklabel'>Open - € " + d[1].value + "</span>" + "<br>" + "<span class='label darklabel'>Close - € " + d[1].value + "</span>"
                    }else{
                       return "<span class='label'>High - € " + d[0].value + "</span>" + "<br>" + "<span class='label'>Low - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Open - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Close - € " + d[1].value + "</span>"
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
    
    if(Cookies.get('theme') == 'dark'){
        $("#switchtheme").trigger("click")
    }

})

function portfolio() {

    $.ajax({
        type: "GET",
        url: "portfolio.php",
        dataType: "json",
        success: function(json) {
            for (var i in json) {
                if (json[i].change > 0) {
                    change = "+" + json[i].change
                } else {
                    change = json[i].change
                }
                $("#" + json[i].symbol + " .change").text(change + "%");
                $("#" + json[i].symbol + " .price").text("€ " + json[i].price);
                $("#" + json[i].symbol + " .updown").text("€ " + json[i].updown);
                $("#" + json[i].symbol + " .imgholder").prop("src", json[i].image);
            }
        },
        complete: function(json) {
            update()
        }
    })
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
    profitpct = ((profits / sum) * 100);
    if (profitpct > 0) {
        profitpctformatted = " (+" + profitpct.toFixed(2) + "%)"
        $("#profitpct").removeClass("positive negative").addClass("positive")
    } else {
        profitpctformatted = " (-" + profitpct.toFixed(2) + "%)"
        $("#profitpct").removeClass("positive negative").addClass("negative")
    }
    $("#total").text("€ " + sum.toFixed(2))
    $("#profit").text("€ " + parseFloat(profits).toFixed(2))
    $("#currentvalue").text("€ " + parseFloat(sum + profits).toFixed(2))
    $("#profitpct").text(profitpctformatted)
    $(".updown:contains('-'),.change:contains('-'),#profit:contains('-')").removeClass('positive negative').addClass('negative')
    $(".updown:not(:contains('-')),.change:not(:contains('-')),#profit:not(:contains('-'))").removeClass('positive negative').addClass('positive')
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
    document.title = "CRYPTOFOLIO  (€ " + profits.toFixed(2) + "  " + profitpctformatted;
}


