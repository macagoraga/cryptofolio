$(function()
{
    $("#addcoin,#cancel").click(function()
    {
        $("#coinform").toggle()
    })

    // datewindow


    var loader = setInterval(portfolio, 30000);
    portfolio()
    


    $(".item").on("click", function(event){

        var symbol 
        symbol = $(this).attr("id")

        $("#graph,#selectors").remove()
        $(this).after("<tr id='graph'><td colspan='5'><span id='graphframe'></span></td></tr><tr id='selectors'><td colspan='5' ><button class='hide'>HIDE</button></tr>")

        $.ajax({
            type: "GET", 
            url: "graph.php?symbol="+symbol+"&limit=90", 
            dataType: "text",
            success: function(result)
            {
                g = new Dygraph(
                    document.getElementById("graphframe"),
                    result, {
                        title: symbol,
                   
                        gridLineColor: '#eeeeee',
                        customBars: true,
                        height: 350,
                        width:568,
                        labelsSeparateLines: false
                    
                    }
                );
                $(".hide").on("click", function(event){
                    $("#graph,#selectors").remove()
                })
            }
        })
    
    })
})


function portfolio()
{
  // screen detection for graph
    if (window.matchMedia('screen and (min-width: 600px)').matches) {
        var graphWidth = "200px"
        var graphHeight = "64px"
    }
    else{
        var graphWidth = "100px"
        var graphHeight = "40px"
    }
    
    $.ajax({
    type: "GET", 
    url: "portfolio.php", 
    dataType: "json",
    success: function(json)
    {

        for (var i in json) 
        {
            
            if(json[i].change>0){
                change = "+"+json[i].change
            }
            else{
                change = json[i].change
            }

            $("#"+json[i].symbol+" .change").text(change +"%");
            $("#"+json[i].symbol+" .price").text("€ "+json[i].price);
            $("#"+json[i].symbol+" .updown").text("€ "+json[i].updown);
            $("#"+json[i].symbol+" .imgholder").prop("src",json[i].image);
            
            // var graphinput = json[i].graphdata.split(",")

            // $("#"+json[i].symbol+" .graph").sparkline(graphinput,
            // {
            //     type: 'line',
            //     height: graphHeight,
            //     width: graphWidth,
            //     lineColor: '#c8c8c8',
            //     fillColor: '#e9e9e9',
            //     spotColor: '#ff0000',
            //     minSpotColor: '#ff0000',
            //     maxSpotColor: '#ff0000'
            // });
        }
    },
    complete: function(json){
        update()
    }
    })
}

function update(){

        var profits = 0;
        var profitpct = 0
        var profittmp= 0
        var sum = 0;
         
        $(".paid").each(function()
        {
            sum += parseFloat($(this).text().replace("€ ",""));
        });
        
        $(".updown").each(function()
        {
            profits += parseFloat($(this).text().replace("€ ",""));
        });
        
        $("#total").text("€ " + sum.toFixed(2))
        $("#profit").text("€ " + parseFloat(profits).toFixed(2))
        $("#currentvalue").text("€ " + parseFloat(sum + profits).toFixed(2))
       
        profitpct = ((profits/sum)*100).toFixed(2)
        document.title = "CRYPTOFOLIO  (€ "+profits.toFixed(2)+"  "+ profitpct +"%)";

        $(".updown:contains('-'),.change:contains('-'),#profit:contains('-')").removeClass('positive negative').addClass('negative') 
        $(".updown:not(:contains('-')),.change:not(:contains('-')),#profit:not(:contains('-'))").removeClass('positive negative').addClass('positive') 
      

}