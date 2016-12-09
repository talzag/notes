function log(input) {
  console.log(input);
}

// Globals
var count = 0;
var time_type = "created";
var time_range = "month";
var current_model;
var today = today();
var start = oneMonthAgo();
var end = today;
var group_by = "m/d/y";


// Get Models initially on load
function getModels() {
  $.ajax({
    url: "models",
    success:function(response) {
      if(response["default"]) {
        current_model = response["default"];
      }
      else {
        current_model = response[0];
      }
      getModelGraphData(current_model,time_type,start,end,group_by);
      for (var model in response) {
        if(model !== "default") {
          $(".models ul").append("<li class='model_button'><button>"+response[model]+"</button></li>");
        }
      }
      addModelsClickEvents();
    },
    error:function(response) {
      log(response);
    }
  })
}


function getModelGraphData(models,time_type,start,end,group_by) {
  log(time_type);log(models);log(start);log(end);log(group_by);
  $.ajax({
    url: "model_stats",
    data: {
      time_type: time_type,
      models: models,
      date_range_start: start,
      date_range_end: end,
      date_format:group_by
    },
    success:function(response) {
      // get every model
      for (var model in response) {
        // SHOULD BE IT'S OWN FUNCTION PROBZ
        var data = [];
        // get each day in that model, if there's anything there
        if (Object.keys(response[model]).length > 0) {
          var days = response[model].days;
          for (var day in days) {
            data.push({
              day: day,
              models: days[day]
            });
          }
          // now that we have what we need, render that shit
          renderGraph(model,data);
          count++;

          // Render the non-graph extras
          $(".total_count").html(response[model].total);
          $(".today_count").html(response[model].today);
        } else {
          $(".total_count,.today_count").html("0");
        }
      }

    },
    error:function(a,b,c) {
      log({ a: a, b: b, c: c });
    }
  });
}


// Render Plottable Graph
function renderGraph(title,data) {
  log(data);
  var name = title;
  // if there's no SVG create one
  if($("#"+title).length === 0){
    $(".svgs").append('<h2>'+title+'</h2><svg width="100%" height="100%" id="'+title+'"></svg>');
  }
  var symbolSize = 10;

  var xScale = new Plottable.Scales.Category();
  var yScale = new Plottable.Scales.Linear();

  var xAxis = new Plottable.Axes.Category(xScale, "bottom");
  var yAxis = new Plottable.Axes.Numeric(yScale.domain([0]), "left");

  var linePlot = new Plottable.Plots.Line()
    .addDataset(new Plottable.Dataset(data))
    .x(function(d) { return d.day; }, xScale)
    .y(function(d) { return d.models; }, yScale)
    .attr("stroke-width", 3)
    .attr("stroke", "black")
    .addDataset(new Plottable.Dataset(data));

  var scatterPlot = new Plottable.Plots.Scatter()
    .addDataset(new Plottable.Dataset(data))
    .x(function(d) { return d.day; }, xScale)
    .y(function(d) { return d.models; }, yScale)
    .attr("opacity", 1)
    .attr("stroke-width", 3)
    .attr("stroke", "black")
    .attr("title", function(d) {return d.models})
    .size(symbolSize)
    .addDataset(new Plottable.Dataset(data));

  var plots = new Plottable.Components.Group([ linePlot, scatterPlot]);

  var table = new Plottable.Components.Table([
    [yAxis, plots],
    [null,  xAxis]
  ]);

  table.renderTo("svg#"+name);
  var svgHeight = 2 * $("svg#"+name).height();
  $("svg#"+name).attr('height', svgHeight);
  table.redraw();

  $('path').qtip({ // Grab some elements to apply the tooltip to
    style: {
      classes: "qtip-dark"
    },
    show: {
      delay: 0
    }
  })
}

//click events for created / updated
$(".time_type button").click(function() {
  var id = $(this).attr("id");
  if(time_type !== id) {
    time_type = id;
    $(".svgs").html("");
    getModelGraphData(current_model,time_type,start,end,group_by);
  }
});

$(".time_range button").click(function() {
  var id = $(this).attr("id");
  if(time_range !== id) {
    if(id === "month") {start = oneMonthAgo()}
    else if(id === "week") {start = oneWeekAgo()}
    else if(id === "year") {start = oneYearAgo()}
    else if(id === "forever"){start = '1900-12-05'}
    time_range = id;
    $(".svgs").html("");
    getModelGraphData(current_model,time_type,start,end,group_by);
  }
})

// DATE RANGE SELECTOR
$("#from").datepicker({dateFormat: 'yy-mm-dd',maxDate: new Date});
$("#to").datepicker({dateFormat: 'yy-mm-dd',maxDate: new Date});

// get date range
$('.date_range button').click(function() {
  var from = $("#from").val();
  var to = $("#to").val();
  // from end date to start date
  end = newDay(to);
  start = newDay(from);
  $(".svgs").html("");
  getModelGraphData(current_model,time_type,start,end,group_by);
});

$('.group_by button').click(function() {

  if($(this).attr("id") === "group_day") {
    group_by = "m/d/y";
  } else if($(this).attr("id") === "group_week") {
    group_by = "m/y";
  } else if($(this).attr("id") === "group_month") {
    group_by = "m/y";
  }

  $(".svgs").html("");
  getModelGraphData(current_model,time_type,start,end,group_by);
});

//add click events when models respond
function addModelsClickEvents() {
  $(".models ul li").click(function() {
    current_model = $(this).text();
    $(".svgs").html("");
    getModelGraphData(current_model,time_type,start,end,group_by);
  })
}

// TIME
function today() {
  var today = new Date();
  var today_string = today.getFullYear()+"-"+(today.getMonth()+1)+"-"+today.getDate();
  return today_string;
}

function oneWeekAgo() {
  var week = new Date();
  week.setDate(week.getDate() - 7);
  var week_string = week.getFullYear()+"-"+(week.getMonth()+1)+"-"+(week.getDate());
  return week_string;
}

function oneMonthAgo() {
  var month = new Date();
  var month_string = month.getFullYear()+"-"+month.getMonth()+"-"+month.getDate();
  return month_string;
}

function oneYearAgo() {
  var year = new Date();
  var year_string = (year.getFullYear()-1)+"-"+year.getMonth()+"-"+year.getDate();
  return year_string;
}

function newDay(str) {
  str = str.split('-');
  str[1]--;
  var time = Date.UTC.apply(null, str);
  var new_date = new Date(time);
  var date_string = new_date.getUTCFullYear()+"-"+(new_date.getUTCMonth()+1)+"-"+(new_date.getUTCDate()+1);
  return date_string;
}

// On load get all the models
getModels();
