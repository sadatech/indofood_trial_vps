// $.ajax({
    //     type: "POST",
    //     url: base_url+"/api/CountTotalContact?key=ganteng",
    //     data: { "ba" : fields[1].value, "toko" : fields[2].value, "cabang" : fields[3].value, "kota" : fields[4].value, "startDate" : fields[5].value, "endDate" : fields[6].value},
    //     dataType: 'json',
    //     beforeSend : function() {
    //         $("#loading").html("<img width='30' style='margin: 0 auto;display:block;' src='"+base_url+"/assets/upload/loadings.gif' alt='Wait..' />");
    //     },
    //     success : function(data) {
    //         if (data == null) {
    //                     datas += "<tr>";
    //                     datas += "<td colspan='18'>Data Kosong</td>";
    //                     datas += "</tr>";
    //                 }
    //                 else{
    //         var datas = "";
    //         var aaa = "";
               
    //             datas += "";
    //             for (var i = data.length - 1; i >= 0; i--) {
    //                 // datas += "<tr><td>No</td>";
    //                     datas += "<tr>";
    //                     datas += "<td>"+data[i]['nama_cabang']+"</td>";
    //                     datas += "<td>"+data[i]['nama_user']+"</td>";
    //                     if (data[i]['stay_user'] == "Y") {
    //                         datas += "<td>Stay</td>";
    //                     }
    //                     if (data[i]['stay_user'] == "N") {
    //                         datas += "<td>Mobile</td>";
    //                     };
    //                     datas += "<td>"+data[i]['store_id']+"</td>";
    //                     datas += "<td>"+data[i]['nama_toko']+"</td>";

    //                     datas += "<td>"+data[i]['contact_count']+"</td>";
    //                     datas += "<td>"+data[i]['count_switching']+"</td>";
    //                     datas += "<td>"+data[i]['count_recruit']+"</td>";
                        
    // 				    // datas += "<td>"+data[i]['count_sampling']+"</td>";
    //                     if (data[i]['BC'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['BC']+"</td>";
    //                     }
    //                     if (data[i]['BTI'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['BTI']+"</td>";
    //                     }
    //                     if (data[i]['Rusk'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['Rusk']+"</td>";
    //                     }
    //                     if (data[i]['Pudding'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['Pudding']+"</td>";
    //                     }
    //                     if (data[i]['Others'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['Others']+"</td>";
    //                     }
    //                     // if (data[i]['strike_sampling'] == null) {
    //                     //     datas += "<td>0</td>";
    //                     // }
    //                     // else{
    //                     //     datas += "<td> "+data[i]['strike_sampling']+"</td>";
    //                     // }
    //                     if (data[i]['strike_sampling_bc'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['strike_sampling_bc']+"</td>";
    //                     }
    //                     if (data[i]['strike_sampling_bti'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['strike_sampling_bti']+"</td>";
    //                     }
    //                     if (data[i]['strike_sampling_rusk'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['strike_sampling_rusk']+"</td>";
    //                     }
    //                     if (data[i]['strike_sampling_pudding'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['strike_sampling_pudding']+"</td>";
    //                     }
    //                     if (data[i]['strike_sampling_others'] == null) {
    //                         datas += "<td>0</td>";
    //                     }
    //                     else{
    //                         datas += "<td> "+data[i]['strike_sampling_others']+"</td>";
    //                     }
    //                     // datas += "<td>"+data[i]['tipe']+"</td>";
    //                     // datas += "<td>"+data[i]['BTI']+"</td>";
    //                     // datas += "<td>"+data[i]['Rusk']+"</td>";
    //                     // // datas += "<td>"+data[i]['tipe']+"</td>";
    //                     // datas += "<td>"+data[i]['Pudding']+"</td>";
    //                     // datas += "<td>"+data[i]['Others']+"</td>";
                        
    //                     // datas += "<td>"+data[i]['strike_sampling']+"</td>";
    //                     // datas += "<td>"+data[i]['sampling']+"</td>";
    //                     // datas += "<td>"+data[i]['segmen']+"</td>";
    //                     // datas += "<td>"+data[i]['sada_kategori_label']+"</td></tr>";
    //                     aaa += "<td>"+data[i]['sada_kategori_label']+"</td>";
    //                 }
    //             $("#excelCDetail").attr('href',url);
    //             $("#dataContactTotal").html(datas);    
    //             // $("#sampling").append(aaa);    
    //             }
    //             $("#excelCDetail").attr('href',url);
    //             $("#dataContactTotal").html(datas); 
    //        }
    //     })