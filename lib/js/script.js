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
        });

        $("#total").text("Investment: € " + sum.toFixed(2))

        var profits = 0;
        $(".updown").each(function()
        {
            profits += parseFloat($(this).text().replace("€", ""));
        });

        $("#profit").html("P/L: <span class='profit'>€ " + parseFloat(profits).toFixed(2) + "</span>")
        $("#currentvalue").text("Total: € " + parseFloat(sum + profits).toFixed(2))

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

        $(".graph").sparkline('html',
        {
            type: 'line',
            height: '40px',
            width: '100px',
            lineColor: '#c8c8c8',
            fillColor: '#e9e9e9',
            spotColor: '#ff0000',
            minSpotColor: '#ff0000',
            maxSpotColor: '#ff0000'
        });

    })
}