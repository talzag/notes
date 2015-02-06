// payments for a business
if (!$.fn.dataTable.isDataTable('#business_payments_table') && $("#business_payments_table").length > 0) {
    console.log("BUSINESS PAYMENTS TABLE");
    var url_string = "";
    if ($("#credit_id").text().length > 0) {
        var url_string = "admin_business_info_data?credit_id=" + $("#credit_id").text();
    } else {
        var url_string = "admin_business_info_data?business_id=" + $("#business_id").text();
    }
    $('#business_payments_table').dataTable({
        "sPaginationType": "bootstrap",
        "ajax": {
            "url": url_string
        },
        "lengthMenu": [25, 50, 100],
        "deferRender": true,
        "aoColumns": [{
            "mData": "payment_key"
        }, {
            "mData": "payee_name"
        }, {
            "mData": "payee_total"
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
