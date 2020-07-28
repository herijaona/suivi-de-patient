window.onload = function() {

    // Chart 2
    $.ajax({
        url: "/admin/chart/evolutions_des_vaccinations",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            var chart2 = new CanvasJS.Chart("chartContainer2", {
                animationEnabled: true,
                title: {
                    text: "Email Categories",
                    horizontalAlign: "left",
                    fontSize: 16,
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
        },
        error: (e) => {

        }
    }).done(() => {

    });

    $.ajax({
        url: "/admin/chart/evolutions_des_vaccinations",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            var evacc = [];
            $.each(data, function( index, value ) {
                evacc.push({x: new Date(value.x), y: value.y});
            });

            console.log(new Date())
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title: {
                    text: "Evolution des vaccinations par an",
                    fontSize: 18,
                },
                data: [{
                    yValueFormatString: "## Nombre de vaccination",
                    xValueFormatString: "MM YYYY",
                    type: "spline",
                    dataPoints: evacc
                }]
            });
            chart.render();
        },
        error: (e) => {

        }
    }).done(() => {

    });




    // chart3

    var colorPalette = ['#00b04f', '#ffbf00'];

    $.ajax({
        url: "/admin/chart/evolutions_des_patients_praticiens",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            patientPraticien = data;
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
                        { y: data.patient, label: "Patient", },
                        { y: data.praticien, label: "Praticien" },
                    ]
                }],
                color: colorPalette,
            });
            chart3.render();
        },
        error: (e) => {

        }
    }).done(() => {

    });



}