// Payments for a user
if (!$.fn.dataTable.isDataTable('#user_payments_table') && $("#user_payments_table").length > 0) {
    console.log("TABLE");
    console.log($("#user_payments_table"));
    console.log($("#debit_id").text());
    var url_string = "admin_user_info_data?credit_id=" + $("#debit_id").text();
    console.log(url_string);
    $('#user_payments_table').dataTable({
        "sPaginationType": "bootstrap",
        "ajax": {
            "url": url_string
        },
        "lengthMenu": [25, 50, 100],
        "deferRender": true,
        "aoColumns": [{
            "mData": "payment_key"
        }, {
            "mData": "payor_name"
        }, {
            "mData": "payor_total"
        }, {
            "mData": "created_on"
        }, {
            "mData": "payment_status"
        }, {
            "mData": null,
            "mRender": function() {
                return "<a class='cancel_button'>Cancel</a> <a class='refund_button'>Refund</a>";
            }
        }, ],
        "order": [
            [4, "desc"]
        ],
        "fnDrawCallback": function() {
            $(".cancel_button").click(function() {
                cancel_or_refund_payment();
            });
            $(".refund_button").click(function() {
                cancel_or_refund_payment();
            });
        }
    });
}

function cancel_or_refund_payment(payment_key) {
    var url_string = "refund_payment?payment_key=" + payment_key;

    $.ajax({
        url: url_string,
        method: "GET",
        success: function(arg) {
            window.alert(arg);
        }
    });
}
