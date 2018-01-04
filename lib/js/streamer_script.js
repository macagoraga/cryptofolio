  var graphrefresh;

$(function() {
    $("body").on(
        "transitionend MSTransitionEnd webkitTransitionEnd oTransitionEnd",
        function() {
            $(this).removeClass("positive negative");
        
    });

    portfolio();

    var graphs = {};
  
    $("#settings,.cancel").click(function() {
        $(".form").toggle();
    });
    
    $("#currency").click(function(){

        if($(this).text()=='€'){

            $(this).text('Ƀ')
            $(".btcprice,.btctotal").css("display","block")
            $(".europrice,.eurototal").hide();
        
        }
        else{
            $(this).text('€')
           

            $(".btcprice,.btctotal").css("display","none")
            $(".europrice,.eurototal").css("display","block");

        }

    })


    $("#apiform").click(function(){

        $.post("editapi.php", {action: 'load'}, function(result){
           
            var r = $.parseJSON(result);
            $("input[name='bittrexapi']").val(r.api.bittrex.api)
            $("input[name='bittrexsecret']").val(r.api.bittrex.secret)

            $("input[name='krakenapi']").val(r.api.kraken.api)
            $("input[name='krakensecret']").val(r.api.kraken.secret)
            $("input[name='binanceapi']").val(r.api.binance.api)
            $("input[name='binancesecret']").val(r.api.binance.secret)
            $("input[name='poloniexapi']").val(r.api.poloniex.api)
            $("input[name='poloniexsecret']").val(r.api.poloniex.secret)
            $("input[name='investment']").val(r.investment.amount)
            
        });
        $(".apiform,.coinform").toggle()
    })
    
    $("#manualform").click(function(){
        $(".apiform,.coinform").toggle()
    })

    

    $(".item").on("click", function(event) {
        
        s = $(this).data("coin");
        $(".graph,.selectors,.info").remove();

        $(this).after("<tr class='info'>"+
            "<tr class='graph'><td colspan='5'><span id='graph'></span></td>" +
            "</tr><tr class='selectors'><td colspan='5'>" +
            "<button class='3month'>3M</button>" +
            "<button class='1month'>1M</button>" +
            "<button class='1week'>1W</button>" +
            "<button class='3d'>3D</button>" +
            "<button class='7h'>7H</button>" +
            "<button class='realtime'>REALTIME</button>" +
            "<button class='hide'>HIDE</button></tr>");

     
        $(".hide").on("click", function(event) {
            $(".graph,.selectors,.info").remove();
        });

        $(".realtime").on("click", function(event) {
            loadgraph(s, 'today', 90);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')
        });
        $(".1week").on("click", function(event) {
            loadgraph(s, 'hour', 168);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')

        });
        $(".1month").on("click", function(event) {
            loadgraph(s, 'history', 30);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')

        });
        $(".3d").on("click", function(event) {
            loadgraph(s, 'hour', 72);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')

        });
        $(".7h").on("click", function(event) {
            loadgraph(s, 'today', 420);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')

        });
        
        $(".3month").on("click", function(event) {
            loadgraph(s, 'history', 90);
            $(".selectors button").removeClass("active")
            $(this).addClass('active')

        });
       

        loadgraph(s, 'today', 90);
        $(".realtime").addClass('active')

    });



    function loadgraph(symbol, graphtype, limit) {
        
        clearInterval(graphrefresh);

        $("#graph").html("");
        var chart;        

        var dataurl = "graph.php?type=" + graphtype + "&symbol=" + symbol + "&limit=" + limit;
        var xhr = $.getJSON( dataurl, function(result) {})

        xhr.success(function(result){
        inputdata = result;
        
        var stats = inputdata[inputdata.length - 1]

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
                    value: ["CLOSE","VOLUME"]
                },
                xFormat: '%Y-%m-%d %H:%M:%S',
                types: {
                    CLOSE: 'area',
                    VOLUME: 'bar'
                },
                colors: {
                    CLOSE:'#00b0ff',
                    VOLUME:'#555'
                },
                axes:{
                    VOLUME: 'y2'
                }
            },
            
            axis: {
                x: {
                    type: 'timeseries',
                    tick: {
                        format: formatted

                    },
                     
                },
                y: {
                    tick: {
                        format: function(x) {
                            return "€ " + x.toFixed(2);
                        }
                    },
                    label:{
                        text: 'Price',
                        position: 'outer-middle'
                    }
                },
                y2:{

                    padding: {
                        top: 100,
                        bottom: 0
                    },
                    show: true,
                    label:{
                        text: 'Volume',
                        position: 'outer-middle'
                    }

                }
            },
            area: {
                zerobased: false
            },
            tooltip: {
                contents: function(d, defaultTitleFormat, defaultValueFormat, color) {
                       
                        return "<span class='label'>€ " + d[0].value + "</span><br/><span class='label'>V " + d[1].value + "</span>";
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
        
        var stats = inputdata[inputdata.length - 1]


            chart.load({
                json: inputdata,
                keys: {
                    x: 'TIMESTAMP',
                    value: ['CLOSE','VOLUME']
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
                            format: d3.format("€ ")
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
          
           var btceuro = json.RAW.BTC.EUR.PRICE
           var etheuro = json.RAW.ETH.EUR.PRICE
           var ltceuro = json.RAW.LTC.EUR.PRICE

           var out = json.RAW
           $("#euro").text(btceuro);
           $("#btceuro").text("BTC "+formatter(btceuro,'n','e'));
           $("#etheuro").text("ETH "+formatter(etheuro,'n','e'));
           $("#ltceuro").text("LTC "+formatter(ltceuro,'n','e'));

           Object.keys(out).forEach(function(key,index) {
                europrice = (out[key].BTC.PRICE*btceuro);
                btcprice = (out[key].BTC.PRICE);
                $("."+key+" .europrice").text(formatter(europrice,'n','e'))
                $("."+key+" .btcprice").text(formatter(btcprice,'n','b'))
                $("."+key+" .change").text(formatter(out[key].BTC.CHANGEPCT24HOUR,'p'))

            });


        },
        complete: function(json) {
            $("#holder").show()
            update();

        }
    });

}



function update() {
    var currentPrice = {};
    var streamUrl = "https://streamer.cryptocompare.com/";
    var socket = io(streamUrl);    
    
    var fsymarr = $(".item").map(function() {
        return $.map($(this).data(), function(v) {
            return "5~CCCAGG~"+v+"~BTC";
        });
    }).get();
    fsymarr.push("5~CCCAGG~BTC~EUR");

    var fsym = [];
    $.each(fsymarr, function(i, el){
        if($.inArray(el, fsym) === -1) fsym.push(el);
    });


    socket.emit('SubAdd', { subs: fsym });
    socket.on("m", function(message) {
            var messageType = message.substring(0, message.indexOf("~"));
            var res = {};
            if (messageType == CCC.STATIC.TYPE.CURRENTAGG) {
                res = CCC.CURRENT.unpack(message);

                dataUnpack(res);
            }
    });

    var swapcolor = function(from,what,keep){

        if(keep!=''){
        var cl = from.addClass(what+" "+keep)
        }
        else{
        var cl = from.addClass(what)
        }
        cl.delay(250).queue(function (next) { 
            $(this).removeClass(what); 
            next()
        })
    } 
    var dataUnpack = function(data) {
      
        var from = data['FROMSYMBOL'];
        var to = data['TOSYMBOL'];
        var fsym = CCC.STATIC.CURRENCY.getSymbol(from);
        var tsym = CCC.STATIC.CURRENCY.getSymbol(to);
        var pair = from + to;

       
        if (!currentPrice.hasOwnProperty(pair)) {
            currentPrice[pair] = {};
        }

        for (var key in data) {
            currentPrice[pair][key] = data[key];
        }
       
        if (currentPrice[pair]['LASTTRADEID']) {
            currentPrice[pair]['LASTTRADEID'] = parseInt(currentPrice[pair]['LASTTRADEID']).toFixed(0);
        }
        currentPrice[pair]['CHANGE24HOUR'] = CCC.convertValueToDisplay(tsym, (currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']));
        currentPrice[pair]['CHANGE24HOURPCT'] = ((currentPrice[pair]['PRICE'] - currentPrice[pair]['OPEN24HOUR']) / currentPrice[pair]['OPEN24HOUR'] * 100)
        
        displayData(currentPrice[pair], from, tsym, fsym);
    }
    

    var displayData = function(current, from, tsym, fsym) {
            
            for (var key in current) {
                
                if (key == 'CHANGE24HOURPCT') {
                    $('.' + from +" .change").text(formatter(current[key],'p'));
                  
                    var change = $('.' + from +" .change")

                    if (current['PRICE'] > current['OPEN24HOUR']) { swapcolor(change,'positive','cp') } 
                    else if (current['PRICE'] < current['OPEN24HOUR']) { swapcolor(change,'negative','cn') } 
                }
                
                if(key == 'TOSYMBOL' && current[key]== 'EUR'){
                    $("#euro,.BTC .europrice").text(current['PRICE'])
                    $("#btceuro").text(formatter(current['PRICE'],'n','e'))
                }
                if(key == 'PRICE') {

                    var priceDirection = current.FLAGS;
                    var euro = $("#euro").text().replace("€ ","")
                    var inbtc = current[key]*euro;
                    var totalvalue = 0;

                    $('.' + from +" .europrice").text(formatter(inbtc,'n','e'));
                    $('.BTC .europrice').text(formatter(euro,'n','e'))
                    $(".eurototal").each(function() {

                        var coinpriceinbtc = 0;
                        var totalcoins = 0
                        var coinvalue = 0
                        
                        i = $(this).index(".eurototal")

                        coinpriceineuro = $(".europrice").eq(i).text().replace("€ ","")
                        coinpriceinbtc = $(".btcprice").eq(i).text().replace("Ƀ ","")
                        totalcoins = $(".owned").eq(i).text();
                        coinvalue = (coinpriceineuro*totalcoins);
                        totalvalue +=coinvalue 
                        $(".eurototal").eq(i).text(formatter(coinvalue,'n','e'));
                        $(".btctotal").eq(i).text(formatter((coinpriceinbtc*totalcoins),'n','b'));
                        
                    })

                    var element = $('.' + from +" .europrice, ."+from+" .btcprice")
                   
                    if(priceDirection & 1){ swapcolor(element,'positive','') }
                    if(priceDirection & 2){ swapcolor(element,'negative','') }
                   
                }

                if($("#investment").length>0){
                 
                    var investment = $("#investment").text().replace("€ ","")
                    i = investment
                    valdiff = (totalvalue-investment)
                    plpct = formatter(((valdiff/investment))*100,'p')

                    $("#plpct").text(plpct)
                    $("#investment").text(formatter(i,'n','e'))
            }

            $("#totalValue").text(formatter(totalvalue,'n','e'))
           
            swapcolor($("#plpct:not(:contains('-'))"),'positive','cp')
            swapcolor($("#plpct:contains('-')"),'negative','cn')

    }

    
            
           
        

    $("#totals,#holder").show()

    

   };
}

function formatter(i, t, s) {
    c = parseFloat(i).toFixed(8)
    
    
    if (t == "p") {
        if (parseInt(c) > 0) {
            o = "+" + parseFloat(c).toFixed(2) + "%";
        } else {
            o = parseFloat(c).toFixed(2) + "%";
        }
    }

    if (t == "n") {

        if(s=='e'){
            var symbol = '€'
        }
        if(s=='b'){
            var symbol = 'Ƀ'
        }
        
        if (parseInt(c) > 0) {
            a = parseFloat(c).toFixed(2)
            o = symbol+" "+ a;
        } else {
            a = parseFloat(c).toFixed(8)
            o = symbol+" "+ a;
        }
    }

    return o;
}
