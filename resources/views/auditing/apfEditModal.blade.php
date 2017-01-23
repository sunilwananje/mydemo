<?php 
$indexed_tat = App\Helpers\timeRemaining($auditing->indexing->indexing_tat);
//$statusData = \App\Model\Status::where('status_type','apf')->whereNotIn('status_name',['sent to customer','sent to inside sales'])->get();
if($auditing->action=='rfi'){
  $statusData = \App\Model\Status::where('status_type','apf')->where('status_name','=','pending out')->get();
}else{
  $statusData = \App\Model\Status::where('status_type','apf')->whereNotIn('status_name',['sent to customer','sent to inside sales','pending out'])->get();
}
?>

<div class="modal fade" id="apfEditModal" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Audit Process Form</h4>
      </div>

      <form class="form-horizontal" role="form" method="POST" action="{{ route('auditing.update',$auditing->id) }}" id="apfForm1" autocomplete="off" onsubmit="return false;">
        <div class="modal-body">
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="box-body">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Request Details</h3>
              </div>
              <div class="box-body">
                  <div class="form-group">
                      <label for="mail_received_time" class="col-md-3 control-label">Mail Received Time <span style="color:#FF0000">*</span></label>
                      <div class="input-group date col-md-3">
                          <input type='text' class="form-control" name="mail_received_time" id="mail_received_time" value="{{$auditing->indexing->mail_received_time}}" readonly />
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>

                          <span class="help-block" id="mail_received_time_err"></span>

                          <input type='hidden' name="indexing_id" value="{{$auditing->indexing->id}}"/>
                          <input type='hidden' name="publishing_id" value="{{$auditing->publishing->id}}"/>
                          <input type='hidden' name="auditing_id" value="{{$auditing->id}}"/>
                      </div>

                      <label for="account_name" class="col-md-3 control-label">Account Name <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="name" type="text" class="form-control capital-letter" name="account_name" value="{{ $auditing->indexing->customer_name }}" style="text-transform: uppercase;" readonly>
                          <span class="help-block" id="account_name_err"></span>
                      </div>
                  </div>
                  <?php //echo '<pre>';print_r($auditing);echo '</pre>';?>
                  <div class="form-group">
                      <label for="request_id" class="col-md-3 control-label">Request Type <span style="color:#FF0000">*</span></label>
                      
                      <div class="col-md-3">
                      <?php /*/?>
                          <select name="request_type_id" id="request_id" class="form-control">
                            <option value="">Select</option>
                            @foreach(\App\Model\RequestType::all() as $request)
                              <option value="{{$request->id}}" {{(($request->id==$auditing->indexing->request_type_id)?'selected':'')}}>{{strtoupper($request->name)}}</option>
                            @endforeach
                              <!-- <option value="Standard">Standard</option>
                              <option value="Urgent">Urgent</option> -->
                          </select>
                       <?php /*/?>
                       <input id="request_type_id" type="text" class="form-control" name="request_type_id" value="{{ $auditing->indexing->requestType->name }}" style="text-transform: uppercase;" readonly>

                          <span class="help-block" id="request_type_id_err"></span>
                      </div>
                      <label for="user_name" class="col-md-3 control-label">Auditor Name <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                      
                        @if(strtolower(Session::get('user_role'))=='admin')
                         <select name="user_name" id="name" class="form-control">
                            <option value="">Select</option>
                          @foreach(App\Model\User::all() as $user)
                            <option value="{{$user->id}}" {{(($user->id==$auditing->audit_by)?'selected':'')}}>{{$user->name}}</option>
                          @endforeach
                        </select>
                        @else
                         <input id="name" type="text" class="form-control capital-letter" name="user_name" value="{{ Session::get('ldap_name') }}" style="text-transform: uppercase;" readonly>
                        @endif

                          
                          <span class="help-block" id="user_name_err"></span>
                      </div>  
                  </div>

                  <div class="form-group">
                    <label for="publisher_name" class="col-md-3 control-label">Publish By <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="publisher_name" type="text" class="form-control capital-letter" name="publisher_name" value="{{ $auditing->publisher_name }}" style="text-transform: uppercase;" readonly>
                          <span class="help-block" id="publisher_name_err"></span>
                      </div>  

                    <label for="audit_status_id" class="col-md-3 control-label">Status <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('audit_status_id') ? ' has-error' : '' }}">
                          <select name="audit_status_id" id="status_id" class="form-control">
                            <option value="">Select</option>
                            @foreach($statusData as $status)
                              <option value="{{$status->id}}" {{(($status->id==$auditing->audit_status_id)?'selected':'')}}>{{strtoupper($status->status_name)}}</option>
                            @endforeach
                              <!-- <option value="Standard">Standard</option>
                              <option value="Urgent">Urgent</option> -->
                          </select>

                          <span class="help-block" id="audit_status_id_err"></span>
                      </div>
                     
                  </div>
                  <div class="form-group">
                     
                     <label for="total_lane" class="col-md-3 control-label">Total Lane <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('total_lane') ? ' has-error' : '' }}">
                          <input id="total_lane" type="number" class="form-control required" name="total_lane" value="{{ $auditing->publishing->total_lane or old('total_lane') }}" min="0">

                          <span class="help-block" id="total_lane_err"></span>
                      </div>
                     <label for="no_of_inlands" class="col-md-3 control-label">No Of Inlands <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="no_of_inlands" type="number" class="form-control required" name="no_of_inlands" value="{{ $auditing->publishing->no_of_inlands or old('no_of_inlands') }}" min="0">

                          <span class="help-block" id="no_of_inlands_err"></span>
                      </div>
                    </div>
                    <div class="form-group">
                     <label for="modes" class="col-md-3 control-label">Modes <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('modes') ? ' has-error' : '' }}" >
                        <div class="row">
                        <?php 
                          if(!empty($auditing->publishing->mode_id)){
                               if(strpos($auditing->publishing->mode_id, ',')){
                                  $mode_ids = explode(',',$auditing->publishing->mode_id);
                              }else{
                                  $mode_ids=array($auditing->publishing->mode_id);
                              }
                              
                          }else{
                              $mode_ids=array();
                          }
                        ?>
                            @foreach(\App\Model\Mode::all() as $k=>$mode)
                              <div class="col-md-2" id="modes_check">
                                <input type="checkbox" name="modes[]" id="modes{{$mode->id}}" class="required" value="{{ $mode->id }}" {{ ((in_array($mode->id,$mode_ids))?"checked":"" ) }}>
                              </div>
                              <label for="mode_id{{$mode->id}}" class="col-md-4 control-label">{{$mode->name}}</label>
                              
                            @endforeach
                        </div>
                          <span class="help-block" id="modes_err"></span>
                      </div>

                      <?php /*/ ?> 
                     <label for="price_area" class="col-md-3 control-label">Pricing Area <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('price_area') ? ' has-error' : '' }}" >
                          <div class="row">
                            <?php 
                             if(!empty($auditing->publishing->pricing_area)){
                                  if(strpos($auditing->publishing->pricing_area, ',')){
                                      $pricing_areas = explode(',',$auditing->publishing->mode_id);
                                  }else{
                                      $pricing_areas = array($auditing->publishing->pricing_area);
                                  }
                              
                              }else{
                                  $pricing_areas = array();
                              }
                            ?>
                            @foreach(\App\Model\PricingArea::all() as $pricing)
                            <?php //echo ((in_array($pricing->id,old('price_area')))?'checked':'');?>
                            <div class="col-md-3" id="pricing_area_check">
                                <input type="checkbox" name="price_area[]" class="required" id="price_area{{$pricing->id}}" value="{{ $pricing->id }}" {{ ((in_array($pricing->id,$pricing_areas))?"checked":"" ) }}>
                            </div>
                            <label for="price_area{{$pricing->id}}" class="col-md-3 control-label">{{$pricing->name}}</label>
                            @endforeach
                         </div>
                           @if ($errors->has('price_area'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('price_area') }}</strong>
                              </span>
                            @endif
                      </div>
                      <?php /*/ ?>
                    </div>

                   <div class="form-group">
                     <div id="resolved_section" class="{{((($auditing->status->status_name)=='pending out')?'':'hide')}}">
                         <label for="audit_rfi_comment" class="col-md-3 control-label">RFI Resolved Comment</label>
                          <div class="col-md-3">
                          <textarea name="audit_rfi_comment" class="form-control">{{$auditing->audit_rfi_comment or old('audit_rfi_comment')}}</textarea>

                          <span class="help-block" id="audit_rfi_comment_err"></span>

                         </div>

                          <label for="rfi_etd" class="col-md-3 control-label">RFI Resolved Date Time</label>
                          <div class="col-md-3">
                           <input type='text' class="form-control datetimepicker" name="rfi_etd" id="rfi_etd" value="@if($auditing->audit_rfi_end_date!='0000-00-00 00:00:00' && !empty($auditing->audit_rfi_end_date)){{date('d-m-Y H:i',strtotime($auditing->audit_rfi_end_date))}} @endif" autocomplete="off" />
                            <span class="help-block" id="rfi_etd_err"></span>
                              
                         </div>
                          

                         <input type="hidden" name="rfi_id" value="{{ $auditing->rfi_id or '' }}">
                         
                     </div>

                    </div>
                    <div class="form-group">
                     <div class="col-md-3"></div>
                     <div class="col-md-9">
                          <input type="checkbox" id="is_error" name="is_error" value="1" @if($auditing->is_error==1) checked @endif>
                          <label for="is_error" class="control-label">Marked as error</label>
                      </div>
                      
                    </div>
                  </div>
                </div><!--End Section Request Details-->

                @if(strtolower($auditing->request_type_name)!='rfi' && $auditing->status->status_name!='pending in')
                 <?php $rficlass ='hide';?>
                @else
                 <?php $rficlass ='';?> 
                @endif

                <div id="rfi_section" class="{{$rficlass}}">
                   <div class="box box-default">
                      <div class="box-header with-border">
                        <h3 class="box-title">RFI Details</h3>
                      </div>
                      <div class="box-body">
                        <div class="form-group">
                          <label for="audit_rfi_type_id" class="col-md-3 control-label">RFI Type <span style="color:#FF0000">*</span></label>
                          <div class="col-md-3">
                              <select name="audit_rfi_type_id" id="audit_rfi_type_id" class="form-control">
                               <option value="">Select</option>
                                @foreach(\App\Model\RfiType::all() as $rfi)
                                  <option value="{{$rfi->id}}" {{(($rfi->id==$auditing->audit_rfi_type_id)?'selected':'')}}>{{strtoupper($rfi->rfi_type_name)}}</option>
                                @endforeach
                              </select>

                             <span class="help-block" id="audit_rfi_type_id_err"></span>
                          </div>
                          <label for="audit_rfi_description" class="col-md-3 control-label">RFI Description <span style="color:#FF0000">*</span></label>
                          <div class="col-md-3 {{ $errors->has('audit_rfi_description') ? ' has-error' : '' }}">
                               <textarea id="audit_rfi_description" class="form-control" name="audit_rfi_description">{{ $auditing->audit_rfi_description or old('audit_rfi_description') }}</textarea>

                              <span class="help-block" id="audit_rfi_description_err"></span>
                          </div>

                       </div>
                       <div class="form-group">
                           <label for="audit_isr_initiated" class="col-md-3 control-label">ISR Intiated</label>
                              <div class="col-md-3">
                              <input type="text" name="audit_isr_initiated" id="audit_isr_initiated" class="form-control" value="{{$auditing->audit_isr_initiated}}">
                               <span class="help-block" id="audit_isr_initiated_err"></span>
                             </div>
                        </div>
                      </div>
                  </div>
                </div><!--End Section RFI Details-->
                
                <div id="correction_section" class="{{((strtolower($auditing->request_type_name)=='cor' || $auditing->is_error==1)?'':'hide')}}">
                <div class="box box-default">
                  <div class="box-header with-border">
                    <h3 class="box-title">Correction Details </h3>
                  </div>
                  <div class="box-body">
                    <div class="form-group">
                      <label for="audit_error_cat_id" class="col-md-3 control-label">Error Category <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('audit_error_cat_id') ? ' has-error' : '' }}">
                          <select name="audit_error_cat_id" id="audit_error_cat_id" class="form-control cor-drop">
                           <option value="">Select</option>
                            @foreach(\App\Model\ErrorCat::all() as $category)
                              <option value="{{$category->id}}" {{(($category->id==$auditing->audit_error_cat_id)?'selected':'')}}>{{strtoupper($category->name)}}</option>
                            @endforeach
                          </select>

                          <span class="help-block" id="audit_error_cat_id_err"></span>

                      </div>
                      <label for="audit_error_type_id" class="col-md-3 control-label">Error Type <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <select name="audit_error_type_id" id="audit_error_type_id" class="form-control cor-drop">
                           <option value="">Select</option>
                            @foreach(\App\Model\ErrorType::all() as $type)
                              <option value="{{$type->id}}" {{(($type->id==$auditing->audit_error_type_id)?'selected':'')}}>{{strtoupper($type->name)}}</option>
                            @endforeach
                          </select>

                          <span class="help-block" id="audit_error_type_id_err"></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="audit_error_description" class="col-md-3 control-label">Error Description <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('audit_error_description') ? ' has-error' : '' }}">
                          <textarea id="audit_error_description" class="form-control" name="audit_error_description">{{ $auditing->audit_error_description or old('audit_error_description') }}</textarea>

                          <span class="help-block" id="audit_error_description_err"></span>
                      </div>
                     <label for="audit_root_cause" class="col-md-3 control-label">Root Cause Analysis <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="root_cause" type="text" class="form-control" name="audit_root_cause" value="{{ $auditing->audit_root_cause or old('root_cause') }}" >

                          <span class="help-block" id="audit_root_cause_err"></span>
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="audit_correction" class="col-md-3 control-label">Correction <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="audit_correction" type="text" class="form-control" name="audit_correction" value="{{ $auditing->audit_correction or old('audit_correction') }}">

                          <span class="help-block" id="audit_correction_err"></span>
                      </div>
                      <label for="audit_corrective_action" class="col-md-3 control-label">Corrective Action*</label>
                      <div class="col-md-3">
                          <input id="audit_corrective_action" type="text" class="form-control" name="audit_corrective_action" value="{{ $auditing->audit_corrective_action or old('audit_corrective_action') }}">

                          <span class="help-block" id="audit_corrective_action_err"></span>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="audit_preventive_action" class="col-md-3 control-label">Preventive Action <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="audit_preventive_action" type="text" class="form-control" name="audit_preventive_action" value="{{ $auditing->audit_preventive_action or old('audit_preventive_action') }}">

                          <span class="help-block" id="audit_preventive_action_err"></span>
                      </div>
                      
                      <label for="audit_proposed_comp_date" class="col-md-3 control-label">Proposed Completion Date <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input type='text' class="form-control datetimepicker" name="audit_proposed_comp_date" id="audit_proposed_comp_date" value="@if($auditing->audit_proposed_comp_date!='0000-00-00 00:00:00' && !empty($auditing->audit_proposed_comp_date)){{date('m/d/Y H:i:s',strtotime($auditing->audit_proposed_comp_date))}} @endif"/>
                          
                          <span class="help-block" id="audit_proposed_comp_date_err"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="audit_proposed_act_date" class="col-md-3 control-label">Actual Completion Date <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input type='text' class="form-control datetimepicker" name="audit_proposed_act_date" id="audit_proposed_act_date" value="@if($auditing->audit_proposed_act_date!='0000-00-00 00:00:00' && !empty($auditing->audit_proposed_act_date)){{date('m/d/Y H:i:s',strtotime($auditing->audit_proposed_act_date))}} @endif" />

                          <span class="help-block" id="audit_proposed_act_date_err"></span>
                      </div>
                      <label for="audit_error_done_by" class="col-md-3 control-label">Error Done By <span style="color:#FF0000">*</span></label>
                       <div class="col-md-3">
                          <select name="audit_error_done_by" id="audit_error_done_by" class="form-control cor-drop">
                              <option value="">Select</option>
                            @foreach(App\Model\User::all() as $user)
                              <option value="{{$user->id}}" {{(($user->id==$auditing->audit_error_done_by)?'selected':'')}}>{{$user->name}}</option>
                            @endforeach
                          </select>

                         <span class="help-block" id="audit_error_done_by_err"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!--End Section Correction Details-->


              <div class="form-group">
                  <label for="office" class="col-md-3 control-label">OOT Remark</label>
                  <div class="col-md-3">
                      <textarea name="oot_remark" class="form-control" @if($auditing->oot==1) required @endif>{{$auditing->oot_remark or old('oot_remark')}}</textarea>
                  </div>
                  <label for="office" class="col-md-3 control-label">Comments</label>
                  <div class="col-md-3">
                      <textarea name="comments" class="form-control">{{$auditing->comments or old('comments')}}</textarea>
                      <input type="hidden" name="tat_comp" value="{{$indexed_tat}}">
                  </div>
              </div>
          </div>
        </div> 
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">
          <i class="fa fa-btn fa-floppy-o"></i> Save</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
   </div>
  </div>
 
 <script src="{{ asset('/assets/dist/js/jquery.validate.js') }}"></script>
 <script src="{{ asset('/assets/dist/js/validation.js') }}"></script>
 <script type="text/javascript">
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

     $('#rfi_etd').datetimepicker({
        lang:'en',
        timeFormat: 'HH:mm',
        dateFormat: 'dd-mm-yy',
        minDate:'-{{ date("Y/m/d",strtotime($auditing->audit_rfi_start_date)) }}',
        //minTime:'{{ date("H:i",strtotime($auditing->audit_rfi_start_date)) }}',
        maxDate:0,
        maxTime:0,
        value:'',
     });

    $(".modal-body").scroll(function() {
       datePicker.datepicker('hide');
      //$('.datetimepicker').blur();  
    });
 </script>
