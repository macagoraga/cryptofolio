$(function()
{

    $("#addcoin").click(function()
    {
        $("#coinform").toggle()
    })

    $("#cancel").click(function()
    {
        $("#coinform").toggle()
    })

    var timeout = setInterval(portfolio, 20000);
    portfolio()

})


function portfolio()
{

    $('#holder').load('index.php #portfolio', function()
    {
        $(".frame").show();
        var sum = 0;

        $(".paid").each(function()
        {
            sum += parseFloat($(this).text().replace("€", ""));
            console.log(sum)
        });

        $("#total").text("€ " + sum.toFixed(2))

        var profits = 0;
        $(".updown").each(function()
        {
            profits += parseFloat($(this).text().replace("€", ""));
        });

        $("#profit").text("€ " + parseFloat(profits).toFixed(2))
        if($("#profit:contains('-')").length > 0){
            $("#profit").removeClass().addClass("negative")
            console.log("true")
        } 
        else{
            console.log("false")
          $("#profit").removeClass().addClass("positive")  
        }

        $("#currentvalue").text("€ " + parseFloat(sum + profits).toFixed(2))

        if (profits > 0)
        {

            $(".profit").removeClass('negative').addClass("positive")
        }
        else
        {
            $(".profit").removeClass('positive').addClass("negative")

        }

        $(".updown,.change").addClass('positive')
        $(".updown:contains('-'),.change:contains('-')").removeClass('positive').addClass('negative')


        // screen detection for graph
        if (window.matchMedia('screen and (min-width: 600px)').matches) {
            var graphWidth = "200px"
            var graphHeight = "64px"

        }
        else{
            var graphWidth = "100px"
            var graphHeight = "40px"

        }

        $(".graph").sparkline('html',
        {
            type: 'line',
            height: graphHeight,
            width: graphWidth,
            lineColor: '#c8c8c8',
            fillColor: '#e9e9e9',
            spotColor: '#ff0000',
            minSpotColor: '#ff0000',
            maxSpotColor: '#ff0000'
        });

    })
}