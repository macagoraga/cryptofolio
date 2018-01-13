var graphrefresh;
var charturl
var symbolname
var currentchart
var timeformat
var updategraph
var chartsymbolname
var resizeId;
var inter  = false;
$(function() {


    $(window).on('hashchange', function() {
        var what = window.location.hash.substr(1);
        if (what == '') {
            $("#charthide").trigger('click')
            $("#chartcontainer").hide()
        }

    });

    portfolio();

    var graphs = {};

    $(".filter a").click(function(){
        
        var x = $(this).text()
        
        $(".filter a").removeClass('selected')        
        $(".filter a:contains("+x+")").addClass('selected')

        
        if(x=='ALL'){
            $(".item").show()
        }
        if(x=='PORTFOLIO'){
            $(".item").show()
            $(".item:has(span.watch)").hide()
        }
        if(x=='WATCH'){
            $(".item").hide()
            $(".item:has(span.watch)").show()

        } 
    })

    $("#settings").on("click", function() {
        if ($("#form").css("display") == 'block') {
            $("#form").slideUp("fast",function(){
                $(".apiform").hide()
           });

        } else {
            $("#form").load("settings.php",function(){
                $("#form").slideDown("fast");
            })
        }

    });

    $("#form").on('click', '.cancel', function() {
        $("#form").slideUp("fast",function(){
             $(".apiform").hide()

        });
    })



    $("#charthide").click(function() {
        $("#chartcontainer,#charthide").hide()
        $("#chart").html("")
        currentchart = ""

        $("svg").remove()
        
        $("#table").show()

    })

    $("#currency").click(function() {

        if ($(this).text() == '€') {

            $(this).text('Ƀ')
            $(".btcprice,.btctotal").css("display", "block")
            $(".europrice,.eurototal").hide();

        } else {
            $(this).text('€')


            $(".btcprice,.btctotal").css("display", "none")
            $(".europrice,.eurototal").css("display", "block");

        }

    })


    $("#form").on('click', '#apiform', function() {
        $(".apiform,.coinform").toggle()
    })

    $("#form").on("click", "#manualform", function() {
        $(".apiform").hide()
        $(".coinform").show()
    })

    $(".item").on("click", function(event) {

        var i = $('.item').index(this)
        window.location.hash = '#chart';

        $("#chartticker").html("")
        $("#table").hide()

        chartsymbolname = $(".symbolholder").eq(i).text()
        $("#chartsymbolname").text(chartsymbolname)

        currentchart = $(this).data("coin");

        $("#1d").trigger('click')

    });



    $("#1d").click(function() {
        $("nav>a.selected").removeClass("selected");
        $(this).addClass('selected')
        loadgraph(currentchart, 'minute', '1440');
    })



    $("#7d").click(function() {
        $("nav>a.selected").removeClass("selected");
        $(this).addClass('selected')

        loadgraph(currentchart, 'hour', '168');
    })

    $("#1m").click(function() {
        $("nav>a.selected").removeClass("selected");
        $(this).addClass('selected')

        loadgraph(currentchart, 'hour', '720');
    })

    $("#3m").click(function() {
        $("nav>a.selected").removeClass("selected");
        $(this).addClass('selected')

        loadgraph(currentchart, 'day', '90');
    })


})

function loadgraph(symbol, type, limit) {

    $(window).resize(function() {
        clearTimeout(resizeId);
        resizeId = setTimeout(doneResizing, 500);
    });
    
    $("#chart,#charttickereuro,#charttickerbtc,#charttickerchange").html("")
    $("#error,#load").hide()

    if (type == 'minutes') {
        timeformat = "%Y-%m-%d %H:%M:%S"
    }
    if (type == 'hours') {
        timeformat = "%Y-%m-%d %H:%M:%S"
    }
    if (type == 'days') {
        timeformat = "%Y-%m-%d"
    }

    $("#charttickereuro").text($("." + symbol + " .europrice:eq(0)").text())
    $("#charttickerbtc").text($("." + symbol + " .btcprice:eq(0)").text())
    $("#charttickerchange").text($("." + symbol + " .change:eq(0)").text())
    $("#charttickerchange:not(:contains('-'))").removeClass('pctp pctn').addClass('pctp')
    $("#charttickerchange:contains('-')").removeClass('pctp pctn').addClass('pctn')

   // charturl = "graph.php?type=" + type + "&limit=" + limit + "&symbol=" + symbol
    charturl = "https://min-api.cryptocompare.com/data/histo"+type+"?fsym="+symbol+"&tsym=EUR&limit="+limit+"&aggregate=3&e=CCCAGG"
    
    $("#chart").load("rendergraph.php?symbol=" + symbol)
    $("#twitter").prop("src", "https://www.twitter.com/search?src=typd&q=" + chartsymbolname).click(function() {
        window.open($(this).prop('src'));
    })
    $("#reddit").prop("src", "https://www.reddit.com/search?sort=new&q=" + chartsymbolname).click(function() {
        window.open($(this).prop('src'));
    })
    $("#chartcontainer").show()
    

};


function doneResizing() {
    $("nav>a.selected").trigger('click')
}

function portfolio() {

    var symbolarr = [];
    $(".symbolholdersmall").each(function() {
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

            var out = json.RAW
            $("#euro").text(btceuro);
            $("#btceuro").text("BTC " + formatter(btceuro, 'n', 'e'));
            $("#etheuro").text("ETH " + formatter(etheuro, 'n', 'e'));

            Object.keys(out).forEach(function(key, index) {

                europrice = (out[key].BTC.PRICE * btceuro);
                totalowned = parseFloat($("." + key + " .owned").text())

                btcprice = (out[key].BTC.PRICE);
                $("." + key + " .europrice").text(formatter(europrice, 'n', 'e'))
                $("." + key + " .btcprice").text(formatter(btcprice, 'n', 'b'))
                $("." + key + " .change").text(formatter(out[key].BTC.CHANGEPCT24HOUR, 'p'))

            });


        },
        complete: function(json) {


            $(".filter a:contains('PORTFOLIO')").trigger('click')
            update();

        }
    });

}



function update() {
    var currentPrice = {};
    var streamUrl = "wss://streamer.cryptocompare.com/";
    var socket = io(streamUrl);
    socket.connect()


    var fsymarr = $(".item").map(function() {
        return $.map($(this).data(), function(v) {
            return "5~CCCAGG~" + v + "~BTC";
        });
    }).get();
    fsymarr.push("5~CCCAGG~BTC~EUR");

    var fsym = [];
    $.each(fsymarr, function(i, el) {
        if ($.inArray(el, fsym) === -1) fsym.push(el);
    });


    socket.emit('SubAdd', {
        subs: fsym
    });
    socket.on("m", function(message, error) {

        var messageType = message.substring(0, message.indexOf("~"));
        var res = {};
        if (messageType == CCC.STATIC.TYPE.CURRENTAGG) {
            res = CCC.CURRENT.unpack(message);
           
            dataUnpack(res);
        }
    });

    var swapcolor = function(from, what, keep) {

        if (keep != '') {
            var cl = from.removeClass('positive negative cn cp').addClass(what + " " + keep)
        } else {
            var cl = from.removeClass('positive negative cn cp').addClass(what)
        }
        cl.delay(250).queue(function(next) {
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

                var change = $('.' + from + " .change")
                change.text(formatter(current['CHANGE24HOURPCT'], 'p'));

                if (current['PRICE'] > current['OPEN24HOUR']) {
                     
                    swapcolor(change, 'positive', 'cp');
                    
                } else if (current['PRICE'] < current['OPEN24HOUR']) {
                    swapcolor(change, 'negative', 'cn')
                    
                }

            }

            if (key == 'TOSYMBOL' && current[key] == 'EUR') {
                $("#euro,.BTC .europrice").text(current['PRICE'])
                $("#btceuro").text("BTC " + formatter(current['PRICE'], 'n', 'e'))



            }
            if (key == 'PRICE') {

                var priceDirection = current.FLAGS;
                var euro = $("#euro").text().replace("€ ", "")
                var inbtc = current[key] * euro;
                var totalvalue = 0;



                $('.' + from + " .europrice").text(formatter(inbtc, 'n', 'e'));

                $('.BTC .europrice').text(formatter(euro, 'n', 'e'))
                $(".eurototal").each(function() {

                    var coinpriceinbtc = 0;
                    var totalcoins = 0
                    var coinvalue = 0

                    i = $(this).index(".eurototal")

                    coinpriceineuro = $(".europrice").eq(i).text().replace("€ ", "")
                    coinpriceinbtc = $(".btcprice").eq(i).text().replace("Ƀ ", "")
                    totalcoins = $(".owned").eq(i).text();
                    coinvalue = (coinpriceineuro * totalcoins);
                    totalvalue += coinvalue

                    if($(".walletname").eq(i).text() != 'watch'){
                        if (coinvalue < 7) {
                            $(".item").eq(i).remove()
                        }
                    }
                    $(".eurototal").eq(i).text(formatter(coinvalue, 'n', 'e'));
                    $(".btctotal").eq(i).text(formatter((coinpriceinbtc * totalcoins), 'n', 'b'));

                    if ($("#chartsymbolname:contains('" + from + "')").length > 0) {

                        $("#charttickereuro").text($("." + from + " .europrice:eq(0)").text())
                        $("#charttickerbtc").text($("." + from + " .btcprice:eq(0)").text())
                        $("#charttickerchange").text($("." + from + " .change:eq(0)").text())
                        $("#charttickerchange:not(:contains('-'))").removeClass('pctp pctn').addClass('pctp')
                        $("#charttickerchange:contains('-')").removeClass('pctp pctn').addClass('pctn')

                    }

                })

                var element = $('.' + from + " .europrice, ." + from + " .btcprice")

                if (priceDirection & 1) { swapcolor(element, 'positive', '') }
                if (priceDirection & 2) { swapcolor(element, 'negative', '') }
                if (priceDirection & 4) { swapcolor(element, 'neutral', '') }
                
            }

            if ($("#investment").length > 0) {

                var investment = $("#investment").text().replace("€ ", "")
                i = investment
                valdiff = (totalvalue - investment)
                plpct = formatter(((valdiff / investment)) * 100, 'p')

                $("#plpct").text(plpct)
                $("#investment").text(formatter(i, 'n', 'e'))
            }

            $("#totalValue").text(formatter(totalvalue, 'n', 'e'))
            $("#plpct:not(:contains('-'))").removeClass('pctp pctn').addClass('pctp')
            $("#plpct:contains('-')").removeClass('pctp pctn').addClass('pctn')

        }

    };

    $("#totals,#table").show()
    $(".walletname:contains('watch') .europrice").hide()

}

function formatter(i, t, s) {
    c = Number(i).toFixed(8)

    if (t == "p") {
        
        if (Number(c).toFixed(8) > 0) {
            o = "+" + Number(c).toFixed(2) + "%";
        } else {
            o = Number(c).toFixed(2) + "%";
        }
    }

    if (t == "n") {

        if (s == 'e') {
            var symbol = '€'
        }
        if (s == 'b') {
            var symbol = 'Ƀ'
        }

        if (parseInt(c) > 0) {
            a = Number(c).toFixed(2)
            o = symbol + " " + a;
        } else {
            a = Number(c).toFixed(8)
            o = symbol + " " + a;
        }
    }

    return o;
}