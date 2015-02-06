// Table information for the payments view
$('#payments_table').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "load_payments"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "aoColumns": [{
        "mData": "payment_key"
    }, {
        "mData": "short_payment_key"
    }, {
        "mData": "primary_subscription"
    }, {
        "mData": "account_holder"
    }, {
        "mData": "payor_email"
    }, {
        "mData": "payor_address"
    }, {
        "mData": "formatted_total"
    }, {
        "mData": "formatted_date"
    }, {
        "mData": "credit_status"
    }, {
        "mData": "api_key"
    }, {
        "mData": "payee_description"
    }, {
        "mData": "payor_description"
    }, {
        "mData": "payor_phone"
    }, {
        "mData": "payment_id"
    }, {
        "mData": "credit_status",
        "mRender": function (data, type, full) {
            return render_actions(data, full);
        }
    }, ],
    "order": [
        [7, "desc"]
    ],
    "fnDrawCallback": function() {
        var table = $('#payments_table').DataTable();
        table.column(0).visible(false);
        table.column(9).visible(false);
        table.column(10).visible(false);
        table.column(11).visible(false);
        table.column(12).visible(false);
        table.column(13).visible(false);
        if (userPermission === "submerchant") {
            table.column(14).visible(false);
            add_payments_events(false);
        } else {
            add_payments_events(true);
        }
    }
});

function add_payments_events(actions) {
    if (actions) {
        $("a.action").click(function(event) {
            event.preventDefault();
            var table = $("#payments_table").DataTable();
            var row = table.row($(this).parents("tr"));
            var action = $(this).attr('class').split(' ')[1];

            $.ajax({
                url: "update_transaction",
                type: "POST",
                data: {
                    payment_id: row.data().payment_id,
                    payment_key: row.data().payment_key,
                    status: action
                },
                success: function(data, textStatus, jqXHR) {
                    table.ajax.reload();
                }
            });
        });
    }

    $("table#payments_table tbody tr td:not(:last-child)").click(function() {
        var table = $('#payments_table').DataTable();
        var tr = $(this).parents("tr");
        var row = table.row(tr);

        // There's definitely a better way to build html tags with jquery
        var dropdown = "<tr role='dropdown'><td colspan='10'>";
        dropdown = dropdown.concat("Transaction ID: " + row.data().payment_key + "<br>" + "API Key: " + row.data().api_key + "<br>");

        if (row.data().payee_description) {
            dropdown = dropdown.concat("Payee Description: " + row.data().payee_description + "<br>");
        }
        if (row.data().payor_description) {
            dropdown = dropdown.concat("Payor Description: " + row.data().payor_description + "<br>");
        }

        dropdown = dropdown.concat("Payor Phone: " + row.data().payor_phone + "<br>");

        dropdown = dropdown.concat("</td></tr>");

        if (tr.next("tr") && tr.next("tr").attr("role") != "dropdown") {
            tr.after(dropdown);
        } else {
            tr.next("tr").remove();
        }
    });

}

function render_actions(data, full) {
    var actions = "";
    switch (data) {
        case "queued":
            actions = "<a class='action businessDeferred'>Defer</a> <a class='action cancelled'>Cancel</a>";
            break;
        case "businessDeferred":
            actions = "<a class='action queued'>Queue</a> <a class='action cancelled'>Cancel</a>";
            break;
        default:
            break;
    }
    return actions;
}
