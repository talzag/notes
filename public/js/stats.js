// Get data
function log(input) {
  console.log(input);
}

// counter function, global
var count = 0;

$.ajax({
  url: "stats/date_data",
  data: {
    time_type: "created"
  },
  success:function(response) {
    log(response);
    //get every model
    for(model in response) {
      var data = [];
      // get each day in that model, if there's anything there
      if(Object.keys(response[model]).length > 0) {
        var days = response[model];
        for(day in days) {
          var temp = {};
          temp.day = day;
          temp.models = days[day];
          data.push(temp);
        }
        // now that we have what we need, render that shit
        renderGraph(model,model,data);
        count++;
      }
    }
  },
  error:function(response) {
    log(response);
  }
});


function renderGraph(title,name,data) {
  // if there's no SVG create one
  if($("#"+model).length === 0){
    $(".svgs").append('<h2>'+title+'</h2><svg width="100%" height="100%" id="'+model+'"></svg>');
  }
  var symbolSize = 10;

  var xScale = new Plottable.Scales.Category();
  var yScale = new Plottable.Scales.Linear();

  var xAxis = new Plottable.Axes.Category(xScale, "bottom");
  var yAxis = new Plottable.Axes.Numeric(yScale, "left");

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

  $('path').qtip({ // Grab some elements to apply the tooltip to
    style: {
      classes: "qtip-dark"
    },
    show: {
      delay: 0
    }
  })
}
