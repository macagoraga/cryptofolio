<!DOCTYPE html>
<meta charset="utf-8">
<style>

    #chart{font-size: 10px;}
    text {
        fill: #ccd1d5;
    }
    path.domain{
        stroke: #666;
    }
    text.symbol {
        fill: #ccd1d5;
    }

    path {
        fill: none;
        stroke-width: 1;
    }

    path.candle {
        stroke: #555;
    }

    path.candle.body {
        stroke-width: 0;
    }

    path.candle.up {
        fill: #00AA00;
        stroke: #00AA00;
    }

    path.candle.down {
        fill: #FF0000;
        stroke: #FF0000;
    }

    .close.annotation.up path {
        fill: #00AA00;
    }

    path.volume {
        fill: #DDDDDD;
    }

    .indicator-plot path.line {
        fill: none;
        stroke-width: 1;
    }

    .ma-0 path.line {
        stroke: #ccd1d5;
    }

    .ma-1 path.line {
        stroke: #aec7e8;
    }

    .ma-2 path.line {
        stroke: #ff7f0e;
    }

    button {
        position: absolute;
        right: 110px;
        top: 25px;
    }

    path.macd {
        stroke: #ccd1d5;
    }

    path.signal {
        stroke: #FF9999;
    }

    path.zero {
        stroke: #BBBBBB;
        stroke-dasharray: 0;
        stroke-opacity: 0.5;
    }

    path.difference {
        fill: #BBBBBB;
        opacity: 0.5;
    }

    path.rsi {
        stroke: #555555;
    }

    path.overbought, path.oversold {
        stroke: #FF9999;
        stroke-dasharray: 5, 5;
    }

    path.middle, path.zero {
        stroke: #BBBBBB;
        stroke-dasharray: 5, 5;
    }
    .tick line{
        stroke: #666;
    }
    .analysis path, .analysis circle {
        stroke: blue;
        stroke-width: 0.8;
    }

    .trendline circle {
        stroke-width: 0;
        display: none;
    }

    .mouseover .trendline path {
        stroke-width: 1.2;
    }

    .mouseover .trendline circle {
        stroke-width: 1;
        display: inline;
    }

    .dragging .trendline path, .dragging .trendline circle {
        stroke: darkblue;
    }

    .interaction path, .interaction circle {
        pointer-events: all;
    }

    .interaction .body {
        cursor: move;
    }

    .trendlines .interaction .start, .trendlines .interaction .end {
        cursor: nwse-resize;
    }

    .supstance path {
        stroke-dasharray: 2, 2;
    }

    .supstances .interaction path {
        pointer-events: all;
        cursor: ns-resize;
    }

    .mouseover .supstance path {
        stroke-width: 1.5;
    }

    .dragging .supstance path {
        stroke: darkblue;
    }

    .crosshair {
        cursor: crosshair;
    }

    .crosshair path.wire {
        stroke: #DDDDDD;
        stroke-dasharray: 1, 1;
    }

    .crosshair .axisannotation path {
        fill: #555;
    }

    .tradearrow path.tradearrow {
        stroke: none;
    }

    .tradearrow path.buy {
        fill: #0000FF;
    }

    .tradearrow path.sell {
        fill: #9900FF;
    }

    .tradearrow path.highlight {
        fill: none;
        stroke-width: 2;
    }

    .tradearrow path.highlight.buy {
        stroke: #0000FF;
    }

    .tradearrow path.highlight.sell {
        stroke: #9900FF;
    }

</style>
<body>
<script>
var data 
var intFrameWidth = window.innerWidth-40;

    var dim = {
        width: intFrameWidth, height: 500,
        margin: { top: 20, right: 50, bottom: 30, left: 50 },
        ohlc: { height: 305 },
        indicator: { height: 65, padding: 5 }
    };
    dim.plot = {
        width: dim.width - dim.margin.left - dim.margin.right,
        height: dim.height - dim.margin.top - dim.margin.bottom
    };
    dim.indicator.top = dim.ohlc.height+dim.indicator.padding;
    dim.indicator.bottom = dim.indicator.top+dim.indicator.height+dim.indicator.padding;

    var indicatorTop = d3.scaleLinear()
            .range([dim.indicator.top, dim.indicator.bottom]);

//    var parseDate = d3.timeParse("%Y-%m-%d %I:%M:%S");
    var parseDate = d3.timeParse(timeformat);

    var zoom = d3.zoom()
            .on("zoom", zoomed);

    var x = techan.scale.financetime()
            .range([0, dim.plot.width]);

    var y = d3.scaleLinear()
            .range([dim.ohlc.height, 0]);


    var yPercent = y.copy();   // Same as y at this stage, will get a different domain later

    var yInit, yPercentInit, zoomableInit;

    var yVolume = d3.scaleLinear()
            .range([y(0), y(0.2)]);

    var candlestick = techan.plot.candlestick()
            .xScale(x)
            .yScale(y);

    var tradearrow = techan.plot.tradearrow()
            .xScale(x)
            .yScale(y)
            .y(function(d) {
                // Display the buy and sell arrows a bit above and below the price, so the price is still visible
                if(d.type === 'buy') return y(d.low)+5;
                if(d.type === 'sell') return y(d.high)-5;
                else return y(d.price);
            });

    var sma0 = techan.plot.sma()
            .xScale(x)
            .yScale(y);

    var sma1 = techan.plot.sma()
            .xScale(x)
            .yScale(y);

    var ema2 = techan.plot.ema()
            .xScale(x)
            .yScale(y);

    var volume = techan.plot.volume()
            .accessor(candlestick.accessor())   // Set the accessor to a ohlc accessor so we get highlighted bars
            .xScale(x)
            .yScale(yVolume);

    var trendline = techan.plot.trendline()
            .xScale(x)
            .yScale(y);

    var supstance = techan.plot.supstance()
            .xScale(x)
            .yScale(y);

    var xAxis = d3.axisBottom(x);

    var timeAnnotation = techan.plot.axisannotation()
            .axis(xAxis)
            .orient('bottom')
            .format(d3.timeFormat('%Y-%m-%d'))
            .width(65)
            .translate([0, dim.plot.height]);

    var yAxis = d3.axisRight(y);

    var ohlcAnnotation = techan.plot.axisannotation()
            .axis(yAxis)
            .orient('right')
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

    var closeAnnotation = techan.plot.axisannotation()
            .axis(yAxis)
            .orient('right')
            .accessor(candlestick.accessor())
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

    var percentAxis = d3.axisLeft(yPercent)
            .tickFormat(d3.format('+.1%'));

    var percentAnnotation = techan.plot.axisannotation()
            .axis(percentAxis)
            .orient('left');

    var volumeAxis = d3.axisRight(yVolume)
            .ticks(3)
            .tickFormat(d3.format(",.3s"));

    var volumeAnnotation = techan.plot.axisannotation()
            .axis(volumeAxis)
            .orient("right")
            .width(35);

    var macdScale = d3.scaleLinear()
            .range([indicatorTop(0)+dim.indicator.height, indicatorTop(0)]);

    var rsiScale = macdScale.copy()
            .range([indicatorTop(1)+dim.indicator.height, indicatorTop(1)]);

    var macd = techan.plot.macd()
            .xScale(x)
            .yScale(macdScale);

    var macdAxis = d3.axisRight(macdScale)
            .ticks(3);

    var macdAnnotation = techan.plot.axisannotation()
            .axis(macdAxis)
            .orient("right")
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

    var macdAxisLeft = d3.axisLeft(macdScale)
            .ticks(3);

    var macdAnnotationLeft = techan.plot.axisannotation()
            .axis(macdAxisLeft)
            .orient("left")
            .format(d3.format(',.2f'));

    var rsi = techan.plot.rsi()
            .xScale(x)
            .yScale(rsiScale);

    var rsiAxis = d3.axisRight(rsiScale)
            .ticks(3);

    var rsiAnnotation = techan.plot.axisannotation()
            .axis(rsiAxis)
            .orient("right")
            .format(d3.format(',.2f'))
            .translate([x(1), 0]);

    var rsiAxisLeft = d3.axisLeft(rsiScale)
            .ticks(3);

    var rsiAnnotationLeft = techan.plot.axisannotation()
            .axis(rsiAxisLeft)
            .orient("left")
            .format(d3.format(',.2f'));

    var ohlcCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(ohlcAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([ohlcAnnotation, percentAnnotation, volumeAnnotation])
            .verticalWireRange([0, dim.plot.height]);

    var macdCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(macdAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([macdAnnotation, macdAnnotationLeft])
            .verticalWireRange([0, dim.plot.height]);

    var rsiCrosshair = techan.plot.crosshair()
            .xScale(timeAnnotation.axis().scale())
            .yScale(rsiAnnotation.axis().scale())
            .xAnnotation(timeAnnotation)
            .yAnnotation([rsiAnnotation, rsiAnnotationLeft])
            .verticalWireRange([0, dim.plot.height]);

    var svg = d3.select("#chart").append("svg")
            .attr("width", dim.width)
            .attr("height", dim.height);

    var defs = svg.append("defs");

    defs.append("clipPath")
            .attr("id", "ohlcClip")
        .append("rect")
            .attr("x", 0)
            .attr("y", 0)
            .attr("width", dim.plot.width)
            .attr("height", dim.ohlc.height);

    defs.selectAll("indicatorClip").data([0, 1])
        .enter()
            .append("clipPath")
            .attr("id", function(d, i) { return "indicatorClip-" + i; })
        .append("rect")
            .attr("x", 0)
            .attr("y", function(d, i) { return indicatorTop(i); })
            .attr("width", dim.plot.width)
            .attr("height", dim.indicator.height);

    svg = svg.append("g")
            .attr("transform", "translate(" + dim.margin.left + "," + dim.margin.top + ")");

    

    svg.append("g")
            .attr("class", "x axis")
            .attr("transform", "translate(0," + dim.plot.height + ")");

    var ohlcSelection = svg.append("g")
            .attr("class", "ohlc")
            .attr("transform", "translate(0,0)");

    ohlcSelection.append("g")
            .attr("class", "axis")
            .attr("transform", "translate(" + x(1) + ",0)")
        .append("text")
            .attr("transform", "rotate(-90)")
            .attr("y", -12)
            .attr("dy", ".71em")
            .style("text-anchor", "end")
            .text("Price ($)");

    ohlcSelection.append("g")
            .attr("class", "close annotation up");

    ohlcSelection.append("g")
            .attr("class", "volume")
            .attr("clip-path", "url(#ohlcClip)");

    ohlcSelection.append("g")
            .attr("class", "candlestick")
            .attr("clip-path", "url(#ohlcClip)");

    ohlcSelection.append("g")
            .attr("class", "indicator sma ma-0")
            .attr("clip-path", "url(#ohlcClip)");

    ohlcSelection.append("g")
            .attr("class", "indicator sma ma-1")
            .attr("clip-path", "url(#ohlcClip)");

    ohlcSelection.append("g")
            .attr("class", "indicator ema ma-2")
            .attr("clip-path", "url(#ohlcClip)");

    ohlcSelection.append("g")
            .attr("class", "percent axis");

    ohlcSelection.append("g")
            .attr("class", "volume axis");

    var indicatorSelection = svg.selectAll("svg > g.indicator").data(["macd", "rsi"]).enter()
             .append("g")
                .attr("class", function(d) { return d + " indicator"; });

    indicatorSelection.append("g")
            .attr("class", "axis right")
            .attr("transform", "translate(" + x(1) + ",0)");

    indicatorSelection.append("g")
            .attr("class", "axis left")
            .attr("transform", "translate(" + x(0) + ",0)");

    indicatorSelection.append("g")
            .attr("class", "indicator-plot")
            .attr("clip-path", function(d, i) { return "url(#indicatorClip-" + i + ")"; });

    // Add trendlines and other interactions last to be above zoom pane
    svg.append('g')
            .attr("class", "crosshair ohlc");

    

    svg.append('g')
            .attr("class", "crosshair macd");

    svg.append('g')
            .attr("class", "crosshair rsi");

    d3.csv(charturl, function(error, data) {

            var accessor = candlestick.accessor(),
            indicatorPreRoll = 33;  // Don't show where indicators don't have data
         
        data = data.map(function(d) {
            return {
                date: parseDate(d.Date),
                open: +d.Open,
                high: +d.High,
                low: +d.Low,
                close: +d.Close,
                volume: +d.Volume
            };
        }).sort(function(a, b) { return d3.ascending(accessor.d(a), accessor.d(b)); });

        x.domain(techan.scale.plot.time(data).domain());
        y.domain(techan.scale.plot.ohlc(data.slice(indicatorPreRoll)).domain());
        yPercent.domain(techan.scale.plot.percent(y, accessor(data[indicatorPreRoll])).domain());
        yVolume.domain(techan.scale.plot.volume(data).domain());


        var macdData = techan.indicator.macd()(data);
        macdScale.domain(techan.scale.plot.macd(macdData).domain());
        var rsiData = techan.indicator.rsi()(data);
        rsiScale.domain(techan.scale.plot.rsi(rsiData).domain());

        svg.select("g.candlestick").datum(data).call(candlestick);
        svg.select("g.close.annotation").datum([data[data.length-1]]).call(closeAnnotation);
        svg.select("g.volume").datum(data).call(volume);
        svg.select("g.sma.ma-0").datum(techan.indicator.sma().period(10)(data)).call(sma0);
        svg.select("g.sma.ma-1").datum(techan.indicator.sma().period(20)(data)).call(sma1);
        svg.select("g.ema.ma-2").datum(techan.indicator.ema().period(50)(data)).call(ema2);
        svg.select("g.macd .indicator-plot").datum(macdData).call(macd);
        svg.select("g.rsi .indicator-plot").datum(rsiData).call(rsi);

        svg.select("g.crosshair.ohlc").call(ohlcCrosshair).call(zoom);
        svg.select("g.crosshair.macd").call(macdCrosshair).call(zoom);
        svg.select("g.crosshair.rsi").call(rsiCrosshair).call(zoom);
       
        // Stash for zooming
        zoomableInit = x.zoomable().domain([indicatorPreRoll, data.length]).copy(); // Zoom in a little to hide indicator preroll
        yInit = y.copy();
        yPercentInit = yPercent.copy();

        draw(data);
    });

    function reset() {
        zoom.scale(1);
        zoom.translate([0,0]);
        draw(data);
    }

    function zoomed() {
        x.zoomable().domain(d3.event.transform.rescaleX(zoomableInit).domain());
        y.domain(d3.event.transform.rescaleY(yInit).domain());
        yPercent.domain(d3.event.transform.rescaleY(yPercentInit).domain());

        draw(data);
    }

    function draw(data) {
        svg.select("g.x.axis").call(xAxis);
        svg.select("g.ohlc .axis").call(yAxis);
        svg.select("g.volume.axis").call(volumeAxis);
        svg.select("g.percent.axis").call(percentAxis);
        svg.select("g.macd .axis.right").call(macdAxis);
        svg.select("g.rsi .axis.right").call(rsiAxis);
        svg.select("g.macd .axis.left").call(macdAxisLeft);
        svg.select("g.rsi .axis.left").call(rsiAxisLeft);

        // We know the data does not change, a simple refresh that does not perform data joins will suffice.
        svg.select("g.candlestick").call(candlestick.refresh);
        svg.select("g.close.annotation").call(closeAnnotation.refresh);
        svg.select("g.volume").call(volume.refresh);
        svg.select("g .sma.ma-0").call(sma0.refresh);
        svg.select("g .sma.ma-1").call(sma1.refresh);
        svg.select("g .ema.ma-2").call(ema2.refresh);
        svg.select("g.macd .indicator-plot").call(macd.refresh);
        svg.select("g.rsi .indicator-plot").call(rsi.refresh);
        svg.select("g.crosshair.ohlc").call(ohlcCrosshair.refresh);
        svg.select("g.crosshair.macd").call(macdCrosshair.refresh);
        svg.select("g.crosshair.rsi").call(rsiCrosshair.refresh);
        
        //     updategraph =setTimeout(function() {
            

            
        //         var n = Math.round(new Date( (new Date()).getTime() - 1000 * 60 )/1000);
        //         update = "https://min-api.cryptocompare.com/data/histominute?fsym="+symbolname+"&tsym=BTC&limit=1&e=CCCAGG&"
        //         var epoch = d3.utcParse("%s");
        //         $.getJSON( update, function( out ) {
        //             var prices = []
        //             var euro = $("#euro").text().replace("€ ","");
        //             //console.log(data.Data[0]);

        //             prices.open = out.Data[0].open*euro
        //             prices.close = out.Data[0].close*euro
        //             prices.high = out.Data[0].high*euro
        //             prices.low = out.Data[0].low*euro
        //             prices.volume = out.Data[0].volume
        //             prices.timestamp = epoch(out.Data[0].time)
                   
        //             data.push(prices)
        //              console.log(data)
        //         })

        //         // Simulate intra day updates when no feed is left
        //         // var last = data[data.length-1];
        //         // //last.open = Math.round(((last.high - last.low)*Math.random())*10)/10+last.low;
        //         // // Last must be between high and low
        //         // last.close = Math.round(((last.high - last.low)*Math.random())*10)/10+last.low;

                
            

        //     draw(data);
        // }, 10000); // Randomly pick an interval to update the chart

    }
    $("#loader").hide()
$("#charthide").show()
</script>