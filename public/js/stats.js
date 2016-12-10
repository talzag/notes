function log(message) {
  //console.log(message);
}

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
        var metricsDataSet = {};
        metricsDataSet["today"] = response[model]["today"];
        metricsDataSet["total"] = response[model]["total"];
        Keen.utils.each(response[model]["dates"], function(d,i) {
          var date = new Date(d["date"]).toISOString();
          mainDataSet.set([model,date],d["count"]);
        });
      }

      drawMainGraph(mainDataSet);
      drawMetrics(metricsDataSet);
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
           localtime: false,
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
  log(data);
    main
      .data(data)
      .render();
}

function drawMetrics(data) {

      // First Metric
      var totalToday = new Keen.Dataviz()
        .el('#metric-01')
        .title('Notes')
        .type('metric')
        .data({ result: data["today"] })
        .render();

      // Second Metric - using .prepare so I remember the structure
      var totalInRange = new Keen.Dataviz()
          .el('#metric-02')
          .title('Notes')
          .type('metric')
          .data({ result: data["total"] })
          .render();

      // Second Metric - using .prepare so I remember the structure
      var avgGrowthInRange = new Keen.Dataviz()
          .el('#metric-03')
          .title('% Growth')
          .type('metric')
          .data({ result: 0 })
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
