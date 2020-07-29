window.onload = function() {

    // Chart 2
    $.ajax({
        url: "/admin/chart/evolutions_des_patiens",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            var epat = [];
            $.each(data, function( index, value ) {
                epat.push({label: value.label, y: value.y});
            });
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
                    indexLabel: "{label} - {y}",
                    toolTipContent: "<b>{label}:</b> {y} (#percent%)",
                    dataPoints: epat
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