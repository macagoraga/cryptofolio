$(function()
{
    var chart
    graphs = {}

    $("#addcoin,#cancel").click(function()
    {
        $("#coinform").toggle()
    })
    var loader = setInterval(portfolio, 10000);
    portfolio()

    $(".item").on("click", function(event)
    {

        $(".graph,.selectors").remove()
        var symbol
        var dataurl

        symbol = $(this).attr("id")

        $(this).after("<tr class='graph'><td colspan='5'><span id='graph'></span></td></tr><tr class='selectors'><td colspan='5'><button class='history'>LAST 3 MONTHS</button><button class='today'>NOW</button><button class='hide'>HIDE</button></tr>")


        $(".hide").on("click", function(event)
        {
            $(".graph,.selectors").remove()
        })

        $(".history").on("click", function(event)
        {
            loadgraph(symbol, 'history', 90)
        })

        $(".today").on("click", function(event)
        {
            loadgraph(symbol, 'today', 60)
        })

        loadgraph(symbol, 'today', 60);

    })


    function loadgraph(symbol, graphtype, limit)
    {

        dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit

        if (graphtype == 'today')
        {
            formatted = '%H:%M'
        }
        else
        {
            formatted = '%Y-%m-%d'

        }
        chart = c3.generate(
        {
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
                        format: function (x) { return "€ "+x; }
                    }
                }
            },
            tooltip: {
                contents: function(d, defaultTitleFormat, defaultValueFormat, color)
                {
                    return "<span class='label'>High - € " + d[0].value + "</span>" + "<br>" + "<span class='label'>Low - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Open - € " + d[1].value + "</span>" + "<br>" + "<span class='label'>Close - € " + d[1].value + "</span>"
                },
                position: function()
                {
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
            legend: {
                show: false
            }

        });

        var graphrefresh = setInterval(function()
        {

            chart.load(
            {
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


})



function portfolio()
{
    if (window.matchMedia('screen and (min-width: 600px)').matches)
    {
        var graphWidth = "200px"
        var graphHeight = "64px"
    }
    else
    {
        var graphWidth = "100px"
        var graphHeight = "40px"
    }
    $.ajax(
    {
        type: "GET",
        url: "portfolio.php",
        dataType: "json",
        success: function(json)
        {
            for (var i in json)
            {
                if (json[i].change > 0)
                {
                    change = "+" + json[i].change
                }
                else
                {
                    change = json[i].change
                }
                $("#" + json[i].symbol + " .change").text(change + "%");
                $("#" + json[i].symbol + " .price").text("€ " + json[i].price);
                $("#" + json[i].symbol + " .updown").text("€ " + json[i].updown);
                $("#" + json[i].symbol + " .imgholder").prop("src", json[i].image);
            }
        },
        complete: function(json)
        {
            update()
        }
    })
}

function update()
{
    var profits = 0;
    var profitpct = 0;
    var profittmp = 0;
    var sum = 0;

    $(".paid").each(function()
    {
        sum += parseFloat($(this).text().replace("€ ", ""));
    });
    $(".updown").each(function()
    {
        profits += parseFloat($(this).text().replace("€ ", ""));
    });
    profitpct = ((profits / sum) * 100);
    if (profitpct > 0)
    {
        profitpctformatted = " (+" + profitpct.toFixed(2) + "%)"
        $("#profitpct").removeClass("positive negative").addClass("positive")
    }
    else
    {
        profitpctformatted = profitpct.toFixed(2) + "%"
        $("#profitpct").removeClass("positive negative").addClass("negative")
    }
    $("#total").text("€ " + sum.toFixed(2))
    $("#profit").text("€ " + parseFloat(profits).toFixed(2))
    $("#currentvalue").text("€ " + parseFloat(sum + profits).toFixed(2))
    $("#profitpct").text(profitpctformatted)
    $(".updown:contains('-'),.change:contains('-'),#profit:contains('-')").removeClass('positive negative').addClass('negative')
    $(".updown:not(:contains('-')),.change:not(:contains('-')),#profit:not(:contains('-'))").removeClass('positive negative').addClass('positive')
    $("#totals,#holder").show()

    $('.negative').each(function(i)
    {
        $(this).animate(
        {
            backgroundColor: '#F1414A',
            color: '#ffffff'
        }, 'fast', 'swing', function()
        {
            $(this).animate(
            {
                backgroundColor: '#ffffff',
                color: '#F1414A'
            }, 'fast', 'swing');
        })
    })
    $('.positive').each(function(i)
    {
        $(this).animate(
        {
            backgroundColor: '#80BD72',
            color: '#ffffff'
        }, 'fast', 'swing', function()
        {
            $(this).animate(
            {
                backgroundColor: '#ffffff',
                color: '#80BD72'
            }, 'fast', 'swing');
        })
    })
    document.title = "CRYPTOFOLIO  (€ " + profits.toFixed(2) + "  " + profitpctformatted + "%)";
}