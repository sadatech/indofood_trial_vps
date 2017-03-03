$("#form_detail_contact").validate({
    rules: {
            startDate: "required",
            endDate: "required",
        },
    messages: {
            startDate: "Please enter your startDate",
            endDate: "Please enter your endDate",
            
        },
    submitHandler: function(form) {
            console.log("yes");
            var fields = $(":input").serializeArray();
    if($.fn.dataTable.isDataTable('#contact_detail_report')){
         $('#contact_detail_report').DataTable().clear();
         $('#contact_detail_report').DataTable().destroy();
    }
    var arr = {"ba" : fields[1].value, "toko" : fields[2].value, "cabang" : fields[3].value, "kota" : fields[4].value, "startDate" : fields[5].value, "endDate" : fields[6].value};
    var oTable = $('#contact_detail_report').DataTable({
    "processing": true,
    dom: 'Bfrtip',
    scrollX : true,
    buttons : [
           {
             extend: 'excel',
             text: 'Download Excel',
             className :'btn green-soft'
           },
           {
             extend: 'pdf',
             text: 'Download Pdf',
             className :'btn red-sunglo'
           },
           {
             extend: 'print',
             text: 'Print',
             className :'btn purple-plum'
           }
         ],
    // "serverSide": true,
    "ajax": {
        "url": "http://ba.promina.co.id/detailcontact.jsp",
        "type": "POST",
        "data" : arr
    },
        "initComplete": function() {
            var $searchInput = $('div.dataTables_filter input');

            $searchInput.unbind();

            $searchInput.bind('keyup', function(e) {
                if(e.keyCode == 13) {
                    oTable.search( this.value ).draw();
                }
            });
        }
    });
    }
})
