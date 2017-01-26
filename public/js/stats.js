let timeType = 'created';
let queryModels = '';
let start = '';
let end = '';
let groupBy = 'day';
let main = '';

function log(message) {
  console.log(message);
}

// TIME
function today() {
  const todayDate = new Date();
  const todayString = `${todayDate.getFullYear()}-${('0' + (todayDate.getMonth() + 1)).slice(-2)}-${('0' + todayDate.getDate()).slice(-2)}`;
  return todayString;
}

function oneMonthAgo() {
  const month = new Date();
  month.setMonth(month.getMonth() - 1);
  const monthString = `${month.getFullYear()}-${('0' + (month.getMonth() + 1)).slice(-2)}-${('0' + month.getDate()).slice(-2)}`;
  return monthString;
}

function prepareMainGraph() {
  main = new Keen.Dataviz()
    .el(document.getElementById('main-chart'))
    .chartType('line')
    .height(250)
    .colors(['#6ab975'])
    .chartOptions({
      data: {
        x: 'date',
      },
      axis: {
        x: {
          localtime: false,
          type: 'timeseries',
          tick: {
            format: '%m-%d',
          },
        },
      },
      tooltip: {
        format: {
          name: (name, ratio, id, index) => {
            if (index > 0) {
              const previousValue = main.dataset.matrix[index][1];
              const currentValue = main.dataset.matrix[index + 1][1];
              const growth = ((previousValue - currentValue) / previousValue) * -100;
              return `${growth}%`;
            }
            return 'N/A';
          },
        },
      },
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
  const totalToday = new Keen.Dataviz()
    .el('#metric-01')
    .title('Notes')
    .type('metric')
    .data({ result: data.today })
    .render();
  // Second Metric - using .prepare so I remember the structure
  const totalInRange = new Keen.Dataviz()
    .el('#metric-02')
    .title('Notes')
    .type('metric')
    .data({ result: data.total })
    .render();
  // Second Metric - using .prepare so I remember the structure
  const avgGrowthInRange = new Keen.Dataviz()
    .el('#metric-03')
    .title('% Growth')
    .type('metric')
    .data({ result: 0 })
    .render();
}


function addModels(models) {
  Object.keys(models).forEach((key) => {
    if (key !== 'default') {
      $('#models').append(`<option value='${models[key]}'>${models[key]}</option>`);
    }
  });
  if (models.default) {
    queryModels = models.default;
    $('#models').val(models.default);
  } else {
    queryModels = models[0];
  }
}

function getGraphData(type, modelsGraph, startDate, endDate, group) {
  prepareMainGraph();
  $.ajax({
    url: 'model_stats',
    data: {
      time_type: type,
      models: modelsGraph,
      date_range_start: startDate,
      date_range_end: endDate,
      date_format: group,
    },
    success: (response) => {
      log(response);
      const mainDataSet = new Dataset();
      const metricsDataSet = {};
      Object.keys(response).forEach((key) => {
        metricsDataSet.today = response[key].today;
        metricsDataSet.total = response[key].total;
        Keen.utils.each(response[key].dates, (d) => {
          const date = new Date(d.date).toISOString();
          mainDataSet.set([key, date], d.count);
        });
      });
      drawMainGraph(mainDataSet);
      drawMetrics(metricsDataSet);
    },
    error: (response) => {
      log(response);
    },
  });
}

function getModels() {
  $.ajax({
    url: 'models',
    success: (response) => {
      addModels(response);
      getGraphData(timeType, queryModels, start, end, groupBy);
    },
    error: (response) => {
      log(response);
    },
  });
}

function addClickEvents() {
  $('#refresh').click(() => {
    timeType = $('#time-type').val();
    queryModels = $('#models').val();
    start = $('#timeframe-start').val();
    end = $('#timeframe-end').val();
    groupBy = $('#group-by').val();
    getGraphData(timeType, queryModels, start, end, groupBy);
  });
}

$(document).ready(() => {
  getModels();
  // drawMainGraph(mainDataSet);
  end = today();
  $('#timeframe-end').val(today());
  start = oneMonthAgo();
  $('#timeframe-start').val(oneMonthAgo());
  addClickEvents();
});
