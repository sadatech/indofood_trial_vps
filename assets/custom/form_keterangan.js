$("#form_keterangan").validate({
    // var base_url = window.location.origin;
    rules: {
        keterangan: "required",
    },
    messages: {
        keterangan: "Please enter your description",
        
    },
    submitHandler: function(form) {
        console.log("yes");

        var base_url = window.location.origin;
        var fields = $(":input").serializeArray();
        var arr = {"store_id" : fields[1].value, "kota" : fields[2].value, "toko" : fields[3].value};
        $.ajax({
            "url": base_url+"/keterangan/form_keterangan.jsp",
            "type": "POST",
            "data" : arr,
            "dataType" : 'json',
            success : function(data) {
                window.location.href = window.location.origin+'/keterangan_oos.jsp';
            }
        });
    }
});