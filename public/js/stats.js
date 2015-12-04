// Get data
function log(input) {
  // console.log(input);
}

// counter function, global
var count = 0;

$.ajax({
  url: "stats/date_data",
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
          temp.users = days[day];
          data.push(temp);
        }
        // now that we have what we need, render that shit
        renderGraph("example"+count,data);
        log(data);
        count++;
      }
    }
  },
  error:function(response) {
    log(response);
  }
});

function renderGraph(id,data) {
  var symbolSize = 10;

  var xScale = new Plottable.Scales.Category();
  var yScale = new Plottable.Scales.Linear();

  var xAxis = new Plottable.Axes.Category(xScale, "bottom");
  var yAxis = new Plottable.Axes.Numeric(yScale, "left");

  var linePlot = new Plottable.Plots.Line()
    .addDataset(new Plottable.Dataset(data))
    .x(function(d) { return d.day; }, xScale)
    .y(function(d) { return d.users; }, yScale)
    .attr("stroke-width", 3)
    .attr("stroke", "black")
    .addDataset(new Plottable.Dataset(data));

  var scatterPlot = new Plottable.Plots.Scatter()
    .addDataset(new Plottable.Dataset(data))
    .x(function(d) { return d.day; }, xScale)
    .y(function(d) { return d.users; }, yScale)
    .attr("opacity", 1)
    .attr("stroke-width", 3)
    .attr("stroke", "black")
    .size(symbolSize)
    .addDataset(new Plottable.Dataset(data));

  var bandPlot = new Plottable.Plots.Rectangle()
    .x(function(d) { return d.day; }, xScale)
    .y(0)
    .y2(function() { return bandPlot.height(); })
    .attr("fill", "white")
    .attr("opacity", 0.3)
    .addDataset(new Plottable.Dataset(data));

  var interaction = new Plottable.Interactions.Pointer();

  interaction.onPointerMove(function(point) {
      bandPlot.entities().forEach(function(entity) {
        entity.selection.attr("fill", "white");
      });
      var nearestEntity = bandPlot.entityNearest(point);
      nearestEntity.selection.attr("fill", "#7cb5ec");
      scatterPlot.size(function(datum) {
        return datum.day === nearestEntity.datum.day ? symbolSize * 2 : symbolSize;
      });
    })
  interaction.onPointerExit(function() {
    bandPlot.entities().forEach(function(entity) {
      entity.selection.attr("fill", "white");
    });
    scatterPlot.size(symbolSize);
  });

  interaction.attachTo(bandPlot);

  var plots = new Plottable.Components.Group([bandPlot, linePlot, scatterPlot]);

  var table = new Plottable.Components.Table([
    [yAxis, plots],
    [null,  xAxis]
  ]);

  table.renderTo("svg#"+id);
}
