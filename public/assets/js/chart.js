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
                    text: "Number of patients by patient type",
                    horizontalAlign: "left",
                    fontSize: 16,
                },
                data: [{
                    type: "doughnut",
                    startAngle: 60,
                    //innerRadius: 60,
                    indexLabelFontSize: 17,
                    indexLabel: "{y} {label} ",
                    toolTipContent: "<b> {y} {label}:</b> (#percent%)",
                    dataPoints: epat
                }]
            });
            chart2.render();
        },
        error: (e) => {

        }
    }).done(() => {

    });

// ---------------------------------------------------------------------------------
// Chart Nombre de Prise Par Type de Vaccin
// ---------------------------------------------------------------------------------
    $.ajax({
        url: "/praticien/chart/nb_prise_type_vacc",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            var nbTypeVAcc = [];
            $.each(data, function( index, value ) {
                nbTypeVAcc.push({label: value.label, y: value.y});
            });
            var dataSeries = {type : "doughnut", dataPoints : nbTypeVAcc};
            var dt = [];
            dt.push(dataSeries);

            var options = {
                animationEnabled : true,
                title : {
                    text : "Number of doses per type of vaccine",
                    horizontalAlign: "left",
                    fontSize: 16,
                },
                data : dt
            }

            var chart4 = new CanvasJS.Chart("chartNbPriseTypeVacc", options);

            chart4.render();
        },
        error: (e) => {

        }
    }).done(() => {

    });
// ---------------------------------------------------------------------------------

// ---------------------------------------------------------------------------------
// Chart Age Range
// ---------------------------------------------------------------------------------
    $.ajax({
        url: "/praticien/chart/age_range",
        type: 'get',
        dataType: 'json',
        success: (data) => {
            var ageRangeChart = new CanvasJS.Chart("chartContainerAgeRange",{
                animationEnabled : true,
                title : {
                    text : "Age range",
                    horizontalAlign: "left",
                    fontSize: 16,
                },
                axisX : {
                    title : "Age group by 10",
                    horizontalAlign : "right",
                    titleFontSize : 10
                },
                axisY : {
                    title : "Number of patients",
                    titleFontSize : 10
                },
                data : [{
                    type : "column",
                    dataPoints : data
                }]
            });
            ageRangeChart.render();
        },
        error: (e) => {

        }
    }).done(() => {

    });
// ---------------------------------------------------------------------------------



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
                    text: "Evolution of vaccinations per year",
                    fontSize: 18,
                },
                data: [{
                    yValueFormatString: "## Number of vaccinations",
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
                    text: "Users",
                    fontSize: 18,
                },
                data: [{
                    type: "pie",
                    startAngle: 240,
                    yValueFormatString: "##",
                    indexLabel: " {y} {label}",
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