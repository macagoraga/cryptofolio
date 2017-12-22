 var loader = setInterval(portfolio, 10000);
 var graphrefresh;

$(function() {
  
    var graphs = {};
  
    $("#addcoin,#cancel").click(function() {
        $("#coinform").toggle();
    });

   
    portfolio();

    $(".item").on("click", function(event) {
        
        clearInterval(graphrefresh);
        var s = $(this).data("coin");
        $(".graph,.selectors,.info").remove();

        $(this).after("<tr class='info'>"+
            "<td colspan='5'><span id='volume'></span> <span id='high' class='positive'></span> <span id='low' class='negative'></span></td></tr>"+
            "<tr class='graph'><td colspan='5'><span id='graph'></span></td>" +
            "</tr><tr class='selectors'><td colspan='5'>" +
            "<button class='3month'>3M</button>" +
            "<button class='1month'>1M</button>" +
            "<button class='1week'>1W</button>" +
            "<button class='3d'>3D</button>" +
            "<button class='realtime'>REALTIME</button>" +
            "<button class='hide'>HIDE</button></tr>");

     
        $(".hide").on("click", function(event) {
            clearInterval(graphrefresh);
            $(".graph,.selectors,.info").remove();
        });

        $(".realtime").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'today', 300);
        });
        $(".1week").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'hour', 168);
        });
        $(".1month").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'history', 30);
        });
        $(".3d").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'hour', 72);
        });
        $(".3month").on("click", function(event) {
            clearInterval(graphrefresh);
            loadgraph(s, 'history', 90);
        });
       

        loadgraph(s, 'today', 100);


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
                        return "<span class='label'>€ " + d[0].value + "</span>";
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
});

function portfolio() {

    var symbolarr = [];
    $(".symbolholdersmall").each(function(){
        symbolarr.push($(this).text())

    })

    dataurl = "lib/update.php?symbols=" + symbolarr.join(",");
   
    $.ajax({
        type: "GET",
        url: dataurl,
        dataType: "json",
        success: function(json) {
          
           var euro = json.RAW.BTC.EUR.PRICE
           var out = json.RAW
           $("#euro").text(euro);

           Object.keys(out).forEach(function(key,index) {
                price = (out[key].BTC.PRICE*euro);
                $("."+key+" .currentprice").text(formatter(price,'n'))
                $("."+key+" .change").text(formatter(out[key].BTC.CHANGEPCT24HOUR,'p'))

            });


        },
        complete: function(json) {

            update();

        }
    });

}


function update() {

    var coinprice = 0;
    var btceur = 0;
    var totalvalue = 0;


    btceur = $("#euro").text()

    $(".value").each(function() {
        var coinpriceinbtc = 0;
        var totalcoins = 0
        var coinvalue = 0

        i = $(this).index(".value")

        coinpriceinbtc = $(".currentprice").eq(i).text().replace("€ ","")
        totalcoins = $(".owned").eq(i).text();
        coinvalue = (coinpriceinbtc*totalcoins);
        totalvalue += coinvalue;    
      
        $(".value").eq(i).text(formatter(coinvalue,'n'));

    })
    
    if($("#investment").length>0){
        
        var investment = $("#investment").text().replace("€ ","")
        i = investment
        valdiff = (totalvalue-investment)
        plpct = formatter(((valdiff/investment))*100,'p')

        $("#plpct").text(plpct)
        $("#investment").text(formatter(i,'n'))
        document.title = "CRYPTOFOLIO " + formatter(totalvalue,'n') +" "+plpct;
    }
    else{
        document.title = "CRYPTOFOLIO " + formatter(totalvalue,'n');

    }

    $("#totalValue").text(formatter(totalvalue,'n'))


    $(".change:contains('-'),#plpct:contains('-'),#totalValue:contains('-')").removeClass('positive negative').addClass('negative')
    $(".change:not(:contains('-')),#plpct:not(:contains('-')),#totalValue:not(:contains('-'))").removeClass('positive negative').addClass('positive')
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
