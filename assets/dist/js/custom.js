 $(document).ready(function(){
    $.ajaxSetup({
          headers: {
              'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
          }
      });

    if($('.datetimepicker').length>0){
    
    $.datepicker._gotoToday = function (id) {
        var inst = this._getInst($(id)[0]),
          $dp = inst.dpDiv;
        this._base_gotoToday(id);
        var tp_inst = this._get(inst, 'timepicker');
        var offset = -5.0
        var clientDate = new Date();
        var utc = clientDate.getTime() + (clientDate.getTimezoneOffset() * 60000);
        var now_est = new Date(utc + (3600000*offset));
        //var now = new Date();
        //alert(now_est)
        //var now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
        this._setTime(inst, now_est);
        $('.ui-datepicker-today', $dp).click();
    };
    $('.datetimepicker').datetimepicker({
        lang:'en',
        timeFormat: 'HH:mm',
        dateFormat: 'dd-mm-yy',
        maxDate:0,
        maxTime:0,
        value:'',
     });
   }
     /*$('.datepicker').datetimepicker({
        timeFormat: 'HH:mm:ss',
        stepHour: 2,
        stepMinute: 10,
        stepSecond: 10
     });*/
    
    $('.capital-letter').focusout(function() {
        $(this).val($(this).val().toUpperCase());
    });

     $('#request_no_btn').click(function(){
        var date = $('#mail_received_time').val();
        var url = $('#mail_received_time').attr('data-url');

        if(date!=''){
            $.ajax({
                method: 'post',
                url: url,
                dataType: 'json',
                data: {date:date},
                success: function(data) {
                    var value = eval(data);
                    if(value.request_no){
                        $('#request_no').val(value.request_no);
                    }
                },
                beforeSend: function(){
                    $('#request_no_btn').attr('disabled',true).append('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>');
                    //$('#loaderDiv').show();

                },
                complete: function(){
                    //$('#request_no_btn').removeAttr('disabled');
                    $('#request_no_btn i').remove();
                }
            });
        }else{
            alert('Select Mail Received Datetime!');
        }
    });

    $('.parent').click(function(){
        var id = $(this).attr('id');
        $(".per-"+id).prop('checked', $(this).prop("checked"));
    });

    $('.submenu').click(function(){
        var id = $(this).attr('id');
        $(".sub-"+id).prop('checked', $(this).prop("checked"));
    });

    setNavigation(); 
    dateTimeClock(); 

});

$(document).on('click','.delete',function(index,value){
    var url = $(this).attr('data-url');
    $('#confirm').attr('action',url);
});

$(document).on('change','#request_id',function(){
    var request_name = $('#request_id').val().toLowerCase();
    requestType(request_name);
});
if($('#request_type_id').length>0){
  var request_name = $('#request_type_id').val().toLowerCase();
 
  requestType(request_name);  
}
 
$(document).on('change','#status_id',function(){
    var status = $('#status_id :selected').text().toLowerCase();
    //alert(status)
    if(status=='pending out'){
        $('#resolved_section').removeClass('hide');
        $('#rfi_section').addClass('hide');
        $('.mendatory').removeClass('required');
        //$("#rfi_type_id option[value='']").attr("selected", "selected");
       // $('#rfi_description').val('');
    }else if(status=='pending in'){
        $('#resolved_section').addClass('hide');
        $('#rfi_section').removeClass('hide');
        $('.mendatory').removeClass('required');
        $('#rfi_comment').val('');
    }else if(status=='in process'){
        $('#rfi_section').addClass('hide');
        $('#resolved_section').addClass('hide');
        $('.mendatory').removeClass('required');
        $('#rfi_comment').val('');
    }else{
        //alert(status)
        $('#rfi_section').addClass('hide');
        $('#resolved_section').addClass('hide');
        $('.mendatory').addClass('required');
       // $("#rfi_type_id option[value='']").attr("selected", "selected");
       // $('#rfi_description').val('');
        $('#rfi_comment').val('');
    }
    /*if(status=='pending in'){
       $('#rfi_section').removeClass('hide');
    }else{
      
    }*/
});

$(document).on('click','#is_error',function(){
    if($(this).is(':checked')){
         $('#correction_section').removeClass('hide');
    } else {
         $('#correction_section').addClass('hide')
    }
});

$(document).on('click','.oot',function(){
    var url = $(this).attr('data-url');
    var id = $(this).attr('id');
    $('#pq_id').val(id);
    $('#ootConfirm').attr('action',url);
    $('#ootConfirmModal').modal('show');
});

//var name = $('#request_id :selected').text().toLowerCase();
    // /requestType(name);
if($('#is_error').is(':checked')){
    $('#correction_section').removeClass('hide');
}

/*var name = $('#request_id :selected').text().toLowerCase();
if($('#is_error').is(':checked') || name=='cor'){
    $('#correction_section').removeClass('hide');
}else{
    
    requestType(name);
}*/ 

function requestType(request_name){
   if(request_name=='cor'){
        $('#correction_section').removeClass('hide');
        $('#rfi_description').val('');
        $('#rfi_type_id option[value=""]').attr("selected","selected");
        $('#rfi_section').addClass('hide');
    }else if(request_name=='rfi'){
        if(!($('#is_error').is(':checked'))){
            $('.cor-drop option[value=""]').attr("selected","selected");
            $('#correction_section input[type="text"]').val('');
            $('#error_description').val('');
            $('#correction_section').addClass('hide')
        }
        $('#rfi_section').removeClass('hide');
    }else{
        $('#correction_section').addClass('hide');
        $('#rfi_section').addClass('hide');
    }
}


/*$(document).on('focusout','#partner_code',function(){
    var partner_code = $(this).val();
    var path = $(this).attr('data-url');
    $.ajax({
        url:path,
        method:'post',
        dataType: 'json',
        data:{partner_code:partner_code},
        success:function(data){
            if(data!=0){
                $('#shipper_name').val(data.shipper_name);
                $('#city').val(data.city);
                $('#state').val(data.state);
                $('#address').val(data.address);
            }else{
                $('#shipper_name').val('');
                $('#city').val('');
                $('#state').val('');
                $('#address').val('');
            }
           
        }
    })
});*/
function setNavigation() {  //sidebar selected menu
    var path = window.location.href;
    //alert(path);
    path = path.replace(/\/$/, '');
    path = decodeURIComponent(path);
    
    $('.child-item, .no-child a').each(function () {
        var href = $(this).attr('href');
        //alert(path.substring(0, href.length)+'----'+href)
        if (path.substring(0, href.length) === href) {
            $(this).closest('li').addClass('active');
            $(this).closest('li').parent().parent().addClass('active');
        }
    });
   
}
function toTimeZone() {
    var format = 'YYYY/MM/DD HH:mm:ss ZZ';
    var nowLocal = moment();

// get the current time in the US Eastern time zone
   var nowEastern = moment.tz("America/New_York");
   return nowEastern;
   // return moment(new Date()).tz('America/New_York').format(format);
}
function dateSuffix(n){ //date suffix like eg. 21st,22nd,23rd,24th
    if (n >= 11 && n <= 13) {
        return "th";
    }
    switch (n % 10) {
        case 1:  return "st";
        case 2:  return "nd";
        case 3:  return "rd";
        default: return "th";
    }
}
function dateTimeClock() //datetime clock
{
   // console.log(toTimeZone());
    var offset = -5.0
    var clientDate = new Date();
    var utc = clientDate.getTime() + (clientDate.getTimezoneOffset() * 60000);

    var date = new Date((utc + (3600000*offset)));
    //date = toTimeZone();
        //console.log(utc+' --  '+clientDate+'  '+date.toLocaleString())
        //date = new Date(date); // Clone date
        //date.setHours(date.getHours() - 4); // set EST to be 4 hour earlier
        year = date.getFullYear();
        month = date.getMonth();
        months = new Array('January', 'February', 'March', 'April', 'May', 'June', 'Jully', 'August', 'September', 'October', 'November', 'December');
        d = date.getDate();
        day = date.getDay();
        days = new Array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        h = date.getHours();
        if(h > 12){
            ext = 'PM';
            h = (h - 12);

            if(h < 10){
                h = "0" + h;
            }else if(h == 12){
                h = "00";
                ext = 'AM';
            }
        }else if(h < 12){
            h = ((h < 10) ? "0" + h : h);
            ext = 'AM';
        }else if(h == 12){
            ext = 'PM';
        }

        /*if(h<10){
                h = "0"+h;
        }*/
        m = date.getMinutes();
        if(m<10){
                m = "0"+m;
        }
        s = date.getSeconds();
        if(s<10){
                s = "0"+s;
        }
        result = ''+days[day]+', '+d+dateSuffix(d)+' '+months[month]+' '+year+', '+h+':'+m+':'+s+' '+ext+' EST';
        $('#date_time_clock').html('<h4 class="logo-lg"><b>'+result+'</b></h4>');
        setTimeout(dateTimeClock,500);
        return true;
}
