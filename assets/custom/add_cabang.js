$(document).ready(function() {
    var max_fields      = 10;
    var wrapper         = $(".addpicdiv");
    var wrapper_email        = $(".addemaildiv");

    var wrapper_aspm        = $(".addaspmdiv");
    var wrapper_email_aspm        = $(".addemailaspmdiv");

    var add_button      = $(".add_pic");
    var add_button_aspm      = $(".add_aspm");

    var wrapper_test      = $(".addemailtestdiv");
    
    var x = 0;
    var x_2 = 1;
    $(add_button).click(function(e){
        e.preventDefault();
        // if(x < max_fields){
            x++; 
            $(wrapper).append('<div class="input-group"><span class="input-group-addon"><i class="fa fa-plus-square-o"></i></span><input id="pic" type="text" class="form-control" name="pic[]" placeholder="pic" style="width: 90%;"><a href="#" id="add_pic" class="btn btn-danger remove_pic"><span style="padding:10px;"><i class="glyphicon glyphicon-minus"></i></span></a>');
            $(wrapper_email).append('<div class="input-group"><span class="input-group-addon"><i class="fa fa-plus-square-o"></i></span><input id="pic" type="text" class="form-control" name="emailpic[]" placeholder="Email Pic" style="width: 90%;"><a href="#" id="add_pic" class="btn btn-danger remove_pic"><span style="padding:10px;"><i class="glyphicon glyphicon-minus"></i></span></a>');
            $(wrapper_aspm).append('<div class="input-group"><span class="input-group-addon"><i class="fa fa-plus-square-o"></i></span><input id="pic" type="text" class="form-control" name="aspm[]" placeholder="Nama ASPM" style="width: 90%;"><a href="#" id="add_pic" class="btn btn-danger remove_pic"><span style="padding:10px;"><i class="glyphicon glyphicon-minus"></i></span></a>');
            $(wrapper_email_aspm).append('<div class="input-group"><span class="input-group-addon"><i class="fa fa-plus-square-o"></i></span><input id="pic" type="text" class="form-control" name="emailaspm[]" placeholder="Email ASPM" style="width: 90%;"><a href="#" id="add_pic" class="btn btn-danger remove_pic"><span style="padding:10px;"><i class="glyphicon glyphicon-minus"></i></span></a>');
            $(wrapper_test).append('<div class="input-group"><span class="input-group-addon"><i class="fa fa-plus-square-o"></i></span><input id="pic" type="text" class="form-control" name="namapic[]" placeholder="Nama PIC" style="width: 90%;"><a href="#" id="add_pic" class="btn btn-danger remove_pic"><span style="padding:10px;"><i class="glyphicon glyphicon-minus"></i></span></a>');
        // }
    });
    
    $(wrapper).on("click",".remove_pic", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    $(wrapper_email).on("click",".remove_pic", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    $(wrapper_aspm).on("click",".remove_pic", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    $(wrapper_email_aspm).on("click",".remove_pic", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
    $(wrapper_test).on("click",".remove_pic", function(e){
        e.preventDefault(); $(this).parent('div').remove(); x--;
    })
});