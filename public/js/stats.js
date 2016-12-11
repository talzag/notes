function log (message) {
  // console.log(message)
}

var timeType = 'created'
var queryModels
var start
var end
var groupBy = 'day'
var main

$(document).ready(function () {
  getModels()
  // drawMainGraph(mainDataSet);
  end = today()
  $('#timeframe-end').val(today())
  start = oneMonthAgo()
  $('#timeframe-start').val(oneMonthAgo())
  addClickEvents()
})

function getModels () {
  $.ajax({
    url: 'models',
    success: function (response) {
      addModels(response)
      getGraphData(timeType, queryModels, start, end, groupBy)
    },
    error: function (response) {
      log(response)
    }
  })
}

function getGraphData (timeType, models, start, end, groupBy) {
  prepareMainGraph()
  $.ajax({
    url: 'model_stats',
    data: {
      time_type: timeType,
      models: models,
      date_range_start: start,
      date_range_end: end,
      date_format: groupBy
    },
    success: function (response) {
      log(response)
      var mainDataSet = new Dataset()
      for (model in response) {
        var metricsDataSet = {}
        metricsDataSet['today'] = response[model]['today']
        metricsDataSet['total'] = response[model]['total']
        Keen.utils.each(response[model]['dates'], function (d, i) {
          var date = new Date(d['date']).toISOString()
          mainDataSet.set([model, date], d['count'])
        })
      }
      drawMainGraph(mainDataSet)
      drawMetrics(metricsDataSet)
    },
    error: function (response) {
      log(response)
    }
  })
}

function addModels (models) {
  for (model in models) {
    if (model !== 'default') {
      $('#models').append('<option value="' + models[model] + '">' + models[model] + '</option>')
    }
  }
  if (models['default']) {
    queryModels = models['default']
    $('#models').val(models['default'])
  } else {
    queryModels = models[0]
  }
}

function prepareMainGraph () {
  main = new Keen.Dataviz()
    .el(document.getElementById('main-chart'))
    .chartType('line')
    .height(250)
    .colors(['#6ab975'])
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
      },
      tooltip: {
        format: {
          name: function (name, ratio, id, index) {
            if (index > 0) {
              var previousValue = main['dataset']['matrix'][index][1]
              var currentValue = main['dataset']['matrix'][index + 1][1]
              var growth = ((previousValue - currentValue) / previousValue) * -100
              return '' + growth + '%'
            } else {
              return 'N/A'
            }
          }
        }
      }
    })
  .prepare()
}

function drawMainGraph (data) {
  log(data)
  main
    .data(data)
    .render()
}

function drawMetrics (data) {

  // First Metric
  var totalToday = new Keen.Dataviz()
    .el('#metric-01')
    .title('Notes')
    .type('metric')
    .data({ result: data['today'] })
    .render()

  // Second Metric - using .prepare so I remember the structure
  var totalInRange = new Keen.Dataviz()
    .el('#metric-02')
    .title('Notes')
    .type('metric')
    .data({ result: data['total'] })
    .render()

  // Second Metric - using .prepare so I remember the structure
  var avgGrowthInRange = new Keen.Dataviz()
    .el('#metric-03')
    .title('% Growth')
    .type('metric')
    .data({ result: 0 })
    .render()
}

// TIME
function today () {
  var today = new Date()
  var todayString = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + ('0' + today.getDate()).slice(-2)
  return todayString
}

function oneMonthAgo() {
  var month = new Date()
  var monthString = month.getFullYear() + '-' + month.getMonth() + '-' + ('0' + month.getDate()).slice(-2)
  return monthString
}

function addClickEvents () {
  $('#refresh').click(function () {
    timeType = $('#time-type').val()
    queryModels = $('#models').val()
    start = $('#timeframe-start').val()
    end = $('#timeframe-end').val()
    groupBy = $('#group-by').val()
    getGraphData(timeType, queryModels, start, end, groupBy)
  })
}
