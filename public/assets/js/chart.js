window.onload = function() {

    var chart = new CanvasJS.Chart("chartContainer", {
        animationEnabled: true,
        title: {
            text: "Evolution des vaccinations par an",
            fontSize: 18,
        },
        axisY: {
            title: "Units Sold",
            valueFormatString: "#0,,.",
            suffix: "mn",
            stripLines: [{
                value: 3366500,
                label: "Average"
            }]
        },
        data: [{
            yValueFormatString: "#,### Units",
            xValueFormatString: "YYYY",
            type: "spline",
            dataPoints: [
                { x: new Date(2002, 0), y: 2506000 },
                { x: new Date(2003, 0), y: 2798000 },
                { x: new Date(2004, 0), y: 3386000 },
                { x: new Date(2005, 0), y: 6944000 },
                { x: new Date(2006, 0), y: 6026000 },
                { x: new Date(2007, 0), y: 2394000 },
                { x: new Date(2008, 0), y: 1872000 },
                { x: new Date(2009, 0), y: 2140000 },
                { x: new Date(2010, 0), y: 7289000 },
                { x: new Date(2011, 0), y: 4830000 },
                { x: new Date(2012, 0), y: 2009000 },
                { x: new Date(2013, 0), y: 2840000 },
                { x: new Date(2014, 0), y: 2396000 },
                { x: new Date(2015, 0), y: 1613000 },
                { x: new Date(2016, 0), y: 2821000 },
                { x: new Date(2017, 0), y: 2000000 }
            ]
        }]
    });
    chart.render();

    // Chart 2

    var chart2 = new CanvasJS.Chart("chartContainer2", {
        animationEnabled: true,
        title: {
            text: "Email Categories",
            horizontalAlign: "left",
            fontSize: 18,
        },
        data: [{
            type: "doughnut",
            startAngle: 60,
            //innerRadius: 60,
            indexLabelFontSize: 17,
            indexLabel: "{label} - #percent%",
            toolTipContent: "<b>{label}:</b> {y} (#percent%)",
            dataPoints: [
                { y: 67, label: "Inbox" },
                { y: 28, label: "Archives" },
                { y: 10, label: "Labels" },
                { y: 7, label: "Drafts" },
                { y: 15, label: "Trash" },
                { y: 6, label: "Spam" }
            ]
        }]
    });
    chart2.render();

    // chart3

    var colorPalette = ['#00b04f', '#ffbf00'];
    var chart3 = new CanvasJS.Chart("chartContainer3", {
        animationEnabled: true,
        title: {
            text: "Utilisateurs",
            fontSize: 18,
        },
        data: [{
            type: "pie",
            startAngle: 240,
            yValueFormatString: "##",
            indexLabel: "{label} {y}",
            dataPoints: [
                { y: 2, label: "Patient", },
                { y: 1, label: "Praticien" },
            ]
        }],
        color: colorPalette,
    });
    chart3.render();

}