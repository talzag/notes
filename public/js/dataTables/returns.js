//All Returns

$('#all_returen').dataTable({
    "sPaginationType": "bootstrap",
    "ajax": {
        "url": "load_returns"
    },
    "lengthMenu": [25, 50, 100],
    "deferRender": true,
    "aoColumns": [{
            "mData": "transaction_id"
        }, {
            "mData": "return_code_id"
        }, {
            "mData": "action"
        }, {
            "mData": "credit_status"
        }, {
            "mData": "debit_status"
        }, {
            "mData": "notes"
        }, {
            "mData": "refund_confirmation"
        }, {
            "mData": "formatted_date"
        }
    ],
    "order": [
        [7, "desc"]
    ],
    "fnDrawCallback": function() {
        console.log("success");
    }
});

