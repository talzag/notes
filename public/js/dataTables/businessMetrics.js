$('document').ready(function() {
// Initialize table to business metrics
    $('#business_metrics_table').dataTable({
        "sPaginationType": "bootstrap",
        "ajax": {
            "url": "load_business_metrics"
        },
        "lengthMenu": [25, 50, 100],
        "deferRender": true,
        "aoColumns": [{
            "mData": "full_name"
        }, {
            "mData": "unique_customers"
        }, {
            "mData": "unique_customer_accounts"
        }, {
            "mData": "transaction_count"
        }, {
            "mData": "average_transactions_per_customer"
        }, {
            "mData": "total"
        }, {
            "mData": "average_total_per_customer"
        } ],
        "order": [
            [0, "desc"]
        ]
    });

    date_regex = /^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/;

    $("input.metrics-date-range").on('keyup', function() {
        var table = $("#business_metrics_table").DataTable();
        var url = "load_business_metrics?";
        var export_button = $("a#export-metrics");

        if ($("input#to_date").val().match(date_regex) || $("input#from_date").val().match(date_regex)) {
            var to_date = $("input#to_date").val();
            var from_date = $("input#from_date").val();
            var query_array = [];

            if (from_date.match(date_regex)) {
                query_array.push("from_date=" + from_date);
            }

            if (to_date.match(date_regex)) {
                query_array.push("to_date=" + to_date);
            }

            url = url + query_array.join("&");
            export_button.attr("href", "export_business_metrics?" + query_array.join("&"));
            table.ajax.url(url).load();
        } else {
            export_button.attr("href", "export_business_metrics");
            table.ajax.url("load_business_metrics").load();
        }
    });
});
