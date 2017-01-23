$( document ).ready( function () {

	/*User Process Form Validattion*/
	$( "#upfForm" ).validate( {
		rules: {
			mail_received_time: "required",
			/*sq_no: "required",
			request_type_id: "required",*/
			status_id: "required",
			total_lane:{
				required: true,
				number: true,
			},
			no_of_inlands: {
				required: true,
				number: true,
			},
			/*'modes[]': "required",
			'price_area[]': "required",*/
			rfi_type_id: "required",
			rfi_description: "required",
			isr_initiated: "required",
			rfi_etd: "required",
			rfi_comment: "required",
			error_cat_id: "required",
			error_type_id: "required",
			error_description: "required",
			root_cause: "required",
			correction: "required",
			corrective_action: "required",
			preventive_action: "required",
			proposed_comp_date: "required",
			proposed_act_date: "required",
			error_done_by: "required",
			/*shipper_name: "required",
			partner_code: "required",
			state: "required",
			city: "required",
			address: "required",*/
		},
		messages: {
			mail_received_time: "Enter mail received datetime",
			//sq_no: "Enter SQ number",
			//request_type_id: "Select request type",
			status_id: "Select status",
			total_lane:{
				required: "Enter number of lanes",
				number: "Only numbers",
			},
			no_of_inlands: {
				required: "Enter number of inlands",
				number: "Only numbers",
			},
			/*'modes[]': "Select atleast one mode",
			'price_area[]': "Select atleast one pricing area",*/
			rfi_type_id: "Select RFI type",
			rfi_description: "Enter RFI description",
			isr_initiated: "Enter ISR Initiated",
			rfi_etd: "Enter RFI resolved date",
			rfi_comment: "Enter RFI resolved comment",
			error_cat_id: "Select error category",
			error_type_id: "Select error type",
			error_description: "Enter error description",
			root_cause: "Enter root cause",
			correction: "Enter correction",
			corrective_action: "Enter corrective action",
			preventive_action: "Enter preventive action",
			proposed_comp_date: "Enter proposed completion date",
			proposed_act_date: "Enter proposed actual date",
			error_done_by: "Select error done by",
			/*shipper_name: "Enter shipper name",
			partner_code: "Enter partner code",
			state: "Enter state",
			city: "Enter City",
			address: "Enter address",*/
			
		},
	    errorPlacement: function ( error, element ) {
			//element.parent().addClass( "has-error" );
			/*if ($("#modes_check").has(element).size() > 0) {
             error.insertAfter($("#modes_check"));
	        } else {
	            error.insertAfter(element);
	        }*/
	        if (element.attr("type") == "checkbox") {
               error.insertAfter($(element).parent().parent());
           }else {
	            error.insertAfter(element);
	        }

	        /*if ($("#price_area_check").has(element).size() > 0) {
             error.insertAfter($("#price_area_check"));
	        } else {
	            error.insertAfter(element);
	        }*/
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parent().addClass( 'has-error' );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parent().removeClass( 'has-error' );
		},

		submitHandler: function (form) {
		  //form.submit();
		  var formData = new FormData(form);
		  var url = $('#upfForm').attr('action');
	      $.ajax({
	            url:url,
	            method : 'POST',
	            dataType:'json',
	            data: formData,
	            contentType: false,
	            processData: false,
	            success:function(data){
	            	//alert(data);
	              var response = eval(data);

	              if(response.success == 'done'){
	                window.location.href = response.url;
	              }else{
	              	  $.each(response, function (index, value) {
	                      $('#' + index).html('');
	                      
		              });
		              $.each(response, function (index, value) {

		              	  $('#' + index).parent().addClass('has-error');
		                  $('#' + index).html('<label class="error">'+value+'</label>');
		                  index = index.replace('_err','');
		                  $('#'+index).focus();
		              });
	              }
	              

	            },
	            beforeSend: function(){
			        $('#loaderDiv').show();
                },
                error: function (xhr, status, err) {
                   if(xhr.status == 401){
                   	  alert("Your session/token is expired!");
		        	   //window.location.href = location.pathname;
                   }     
				},
			    complete: function(xhr, status){
			    	$('#loaderDiv').hide(); 
			    }
	          });
		}
	} );

/*Audit Process Form Validattion*/
	$( "#apfForm1" ).validate( {
		rules: {
			mail_received_time: "required",
			request_type_id: "required",
			audit_status_id: "required",
			audit_rfi_type_id: "required",
			audit_rfi_description: "required",
			audit_isr_initiated: "required",
			audit_rfi_end_date: "required",
			rfi_etd: "required",
			audit_rfi_comment: "required",
			audit_error_cat_id: "required",
			audit_error_type_id: "required",
			audit_error_description: "required",
			audit_root_cause: "required",
			audit_correction: "required",
			audit_corrective_action: "required",
			audit_preventive_action: "required",
			audit_proposed_comp_date: "required",
			audit_proposed_act_date: "required",
			audit_error_done_by: "required",		
		},
		messages: {
			mail_received_time: "Enter mail received datetime",
			request_type_id: "Select request type",
			audit_status_id: "Select status",
			audit_rfi_type_id: "Select RFI type",
			audit_rfi_description: "Enter RFI description",
			audit_isr_initiated: "Enter ISR Initiated",
			audit_rfi_end_date: "Enter RFI resolved date",
			rfi_etd: "Enter RFI resolved date",
			audit_rfi_comment: "Enter RFI resolved comment",
			audit_error_cat_id: "Select error category",
			audit_error_type_id: "Select error type",
			audit_error_description: "Enter error description",
			audit_root_cause: "Enter root cause",
			audit_correction: "Enter correction",
			audit_corrective_action: "Enter corrective action",
			audit_preventive_action: "Enter preventive action",
			audit_proposed_comp_date: "Enter proposed completion date",
			audit_proposed_act_date: "Enter proposed actual date",
			audit_error_done_by: "Select error done by",
			
		},
	    errorPlacement: function ( error, element ) {
            if (element.attr("type") == "checkbox") {
               error.insertAfter($(element).parent().parent());
            }else {
	            error.insertAfter(element);
	        }

		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parent().addClass( 'has-error' );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parent().removeClass( 'has-error' );
		},

		submitHandler: function (form) {
		  var formData = new FormData(form);
		  var url = $('#apfForm1').attr('action');
	      $.ajax({
	            url:url,
	            method : 'POST',
	            dataType:'json',
	            data: formData,
	            contentType: false,
	            processData: false,
	            success:function(data){
	            	//alert(data);
	              var response = eval(data);

	              if(response.success == 'done'){
	                window.location.href = response.url;
	              }else{
	              	  $.each(response, function (index, value) {
	                      $('#' + index).html('');
		              });
		              $.each(response, function (index, value) {
		              	  $('#' + index).parent().addClass('has-error');
		                  $('#' + index).html('<label class="error">'+value+'</label>');
		                  index = index.replace('_err','');
		                  $('#'+index).focus();
		              });
	              }
	              

	            },
	            beforeSend: function(){
			        $('#loaderDiv').show();
                },
                error: function (xhr, status, err) {

                       if(xhr.status == 401){
                       	  alert("Your session/token is expired!");
			        	   //window.location.href = location.pathname;
                       }
                       
				},
			    complete: function(xhr, status){
			    	$('#loaderDiv').hide();
			        /*if(status == 'parsererror'){
			        	alert("Your session/token is expired!");
			        	window.location.href = location.pathname;
			        }else{
			        	$('#loaderDiv').hide();
			        }*/
			    }
	          });
		}
	} );
    
   /*Pricer Form Validattion*/ 
    $( "#pricerForm" ).validate( {
		rules: {
			pol_port: "required",
			pol_region: "required",
			pod_port: "required",
			pod_region: "required",
			pricer_name: "required",		
		},
		messages: {
			pol_port: "Enter POL Port",
			pol_region: "Enter POL Region",
			pod_port: "Enter POD Port",
			pol_region: "Enter POD Region",
			pricer_name: "Enter Pricer Name",
			
		},
	    errorPlacement: function ( error, element ) {
            error.insertAfter(element);
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parent().addClass( 'has-error' );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parent().removeClass( 'has-error' );
		},

		submitHandler: function (form) {
		  form.submit();

		}
	} );

	$( "#indexingForm" ).validate( {
		rules: {
			mail_received_time: "required",
			customer_name: "required",
			priority_id: "required",
			region_id: "required",
			request_type_id: "required",		
			office_id: "required",		
			indexed_by: "required",		
		},
		messages: {
			mail_received_time: "Enter mail received datetime",
			customer_name: "Enter customer name",
			priority_id: "Select Priority",
			region_id: "Select Region",
			request_type_id: "Select request type",
			office_id: "Select office",
			indexed_by: "Indexed by is required",
			
		},
	    errorPlacement: function ( error, element ) {
            if($( element ).parent().hasClass('input-group')){
            	error.insertAfter('.input-group').css('margin-left', '205px');
            }else{
                error.insertAfter(element);
            }
            
		},
		highlight: function ( element, errorClass, validClass ) {
			$( element ).parent().addClass( 'has-error' );
		},
		unhighlight: function (element, errorClass, validClass) {
			$( element ).parent().removeClass( 'has-error' );
		},

		submitHandler: function (form) {
		  form.submit();

		}
	} );
	
} );