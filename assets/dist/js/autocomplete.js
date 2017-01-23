$(document).ready(function(){
    var url = $('#partner_code').attr('data-url');
    $('#shipper_name').autocomplete({
      source: function( request, response ) {
            $.ajax({
                url : url,
                dataType: "json",
                data: {term: request.term,column:'shipper_name'},
                 success: function( data ) {
                 //alert(data);
                 response( $.map( data, function( item ) {
                  //alert(item.shipper_name);
                            return {
                                label: item.shipper_name,
                                value: item.shipper_name,
                                data : item
                            }
                        }));
                }
            });
      },
      minLength:0,
      width: 320,
      max: 10,
      appendTo: "#shipperdiv",    
    }).focus(function() {
        $(this).autocomplete("search", "");
    });
  $('#address').autocomplete({
    source: function( request, response ) {
        var shipper_name = $('#shipper_name').val();
        //console.log(request.term);
          $.ajax({
              url : url,
              dataType: "json",
              data: {shipper_name:shipper_name,term: request.term,column:'address'},
               success: function( data ) {
               
               response( $.map( data, function( item ) {
                          return {
                              label: item.address,
                              value: item.address,
                              data : item
                          }
                      }));
              }
          });
    },
    minLength:0,
    width: 320,
    max: 10,
    appendTo: "#addressdiv", 
  }).focus(function() {
      $(this).autocomplete("search", "");
  })
  $('#city').autocomplete({
    source: function( request, response ) {
        var shipper_name = $('#shipper_name').val();
        var address = $('#address').val();
        //alert(address);
          $.ajax({
              url : url,
              dataType: "json",
              data: {shipper_name:shipper_name,address:address,term: request.term,column:'city'},
               success: function( data ) {
               //console.log(data);
               response( $.map( data, function( item ) {
                          return {
                              label: item.city,
                              value: item.city,
                              data : item
                          }
                      }));
              }
          });
    },
    minLength:0,
    width: 320,
    max: 10,
    appendTo: "#citydiv",
  }).focus(function() {
      $(this).autocomplete("search", "");
  });
  $('#state').autocomplete({
    source: function( request, response ) {
        var shipper_name = $('#shipper_name').val();
        var address = $('#address').val();
        var city = $('#city').val();
          $.ajax({
              url : url,
              dataType: "json",
              data: {shipper_name:shipper_name,address:address,city:city,term: request.term,column:'state'},
               success: function( data ) {
               response( $.map( data, function( item ) {
                          return {
                              label: item.state,
                              value: item.state,
                              data : item
                          }
                      }));
              }
          });
    },
    minLength:0,
    width: 320,
    max: 10,
    appendTo: "#statediv",
    select: function (event, ui) {
         var partnerData = ui.item.data;
         $('#partner_code').val(partnerData.partner_code);
    }       
  }).focus(function() {
      $(this).autocomplete("search", "");
  });

$.datepicker._gotoToday = function (id) {
        var inst = this._getInst($(id)[0]),
          $dp = inst.dpDiv;
        this._base_gotoToday(id);
        var tp_inst = this._get(inst, 'timepicker');
        var offset = -5.0
        var clientDate = new Date();
        var utc = clientDate.getTime() + (clientDate.getTimezoneOffset() * 60000);
        //alert(utc)
        var now_est = new Date(utc + (3600000*offset));
        //var now = new Date();
        //var now_utc = new Date(now.getUTCFullYear(), now.getUTCMonth(), now.getUTCDate(), now.getUTCHours(), now.getUTCMinutes(), now.getUTCSeconds());
        this._setTime(inst, now_est);
        $('.ui-datepicker-today', $dp).click();
    };

var datePicker =  $('.datetimepicker').datetimepicker({
        lang:'en',
        timeFormat: 'HH:mm',
        dateFormat: 'dd-mm-yy',
        maxDate:0,
        maxTime:0,
        value:'',
     });


    
  //var datePicker = $('#datepicker').datepicker();

    /*$(".modal-body").scroll(function() {
       datePicker.datepicker('hide');
      //$('.datetimepicker').blur();  
    });*/
    /*$(window).scroll(function() {
       datePicker.datepicker('hide');
      //$('.datetimepicker').blur();  
    });*/
    
});