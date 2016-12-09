function log(message) {
  console.log(message);
}

// var intervalData = [
//   { name: 'www.site1.com', upload: 200, download: 200, total: 400 },
//   { name: 'www.site2.com', upload: 100, download: 300, total: 400 },
//   { name: 'www.site3.com', upload: 300, download: 200, total: 500 },
//   { name: 'www.site4.com', upload: 400, download: 100, total: 500 }
// ];
//
// var mainDataSet = new Dataset();
// Keen.utils.each(intervalData, function(record, i) {
//   mainDataSet.set(["Upload",record.name],record.upload);
//   mainDataSet.set([ 'Download', record.name ], record.download);
// });

var time_type = "created";
var query_models;
var start;
var end;
var group_by = "m/d/y";
var main;

$(document).ready(function() {
  getModels();
  // drawMainGraph(mainDataSet);
  end = today();
  $("#timeframe-end").val(today());
  start = oneMonthAgo();
  $("#timeframe-start").val(oneMonthAgo());
  addClickEvents();
});


function getModels() {
  $.ajax({
    url: "models",
    success:function(response) {
      addModels(response);
      getGraphData(time_type,query_models,start,end,group_by);
    },
    error:function(response) {
      log(response);
    }
  });
}

function getGraphData(time_type,models,start,end,group_by) {
  prepareMainGraph();
  $.ajax({
    url: "model_stats",
    data: {
      time_type: time_type,
      models: models,
      date_range_start: start,
      date_range_end: end,
      date_format:group_by
    },
    success: function (response) {
      log(response);

      var mainDataSet = new Dataset();
      for(model in response) {
        Keen.utils.each(response[model]["days"], function(d, i) {
          var date = new Date(i).toISOString();
          log(i);
          mainDataSet.set([model,date],d);
        });
      }

      drawMainGraph(mainDataSet);
    },
    error: function(response) {
      log(response);
    }
  });
}

function addModels(models) {
  for(model in models) {
    if (model !== "default") {
      $("#models").append('<option value="'+models[model]+'">'+models[model]+'</option>');
    }
  }
  if(models["default"]) {
    query_models = models["default"];
    $("#models").val(models["default"]);
  } else {
    query_models = models[0];
  }
}

function prepareMainGraph() {
  main = new Keen.Dataviz()
    .el(document.getElementById('main-chart'))
    .chartType("line")
    .dateFormat('%m/%d')
    .height(250)
    .colors(["#6ab975"])
    .chartOptions({
      data: {
        x: 'date'
      },
      axis: {
        x: {
            type: 'timeseries',
            tick: {
                format: '%m-%d'
            }
        }
      }
    })
    .prepare();
}

function drawMainGraph(data) {
    main
      .data(data)
      .render();

    // First Metric
    var totalToday = new Keen.Dataviz()
      .el('#metric-01')
      .title('Notes')
      .type('metric')
      .data({ result: 621 })
      .render();

    // Second Metric - using .prepare so I remember the structure
    var totalInRange = new Keen.Dataviz()
        .el('#metric-02')
        .title('Notes')
        .type('metric')
        .data({ result: 1799 })
        .render();


}

// TIME
function today() {
  var today = new Date();
  var today_string = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+("0" + today.getDate()).slice(-2)
;
  return today_string;
}

function oneMonthAgo() {
  var month = new Date();
  var month_string = month.getFullYear()+"-"+month.getMonth()+"-"+("0" + month.getDate()).slice(-2);
  return month_string;
}

function addClickEvents() {
  $("#refresh").click(function () {
    time_type = $("#time-type").val();
    query_models = $("#models").val();
    start = $("#timeframe-start").val();
    end = $("#timeframe-end").val();
    group_by = $("#group-by").val();
    getGraphData(time_type,query_models,start,end,group_by);
  });
}
