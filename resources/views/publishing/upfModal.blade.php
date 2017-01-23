<?php 
$indexed_tat = App\Helpers\timeRemaining($indexing->indexing_tat);
//$statusData = \App\Model\Status::where('status_type','upf')->get();
if($indexing->action=='rfi'){
  $statusData = \App\Model\Status::where('status_type','upf')->where('status_name','=','pending out')->get();
}else{
  $statusData = \App\Model\Status::where('status_type','upf')->where('status_name','!=','pending out')->get();
}
?>

<div class="modal fade" id="upfModal" role="dialog" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">User Process Form</h4>
      </div>
      <form class="form-horizontal" role="form" method="POST" action="{{ route('publishing.update',$indexing->publishing_id) }}" id="upfForm" autocomplete="off" onsubmit="return false;">
       <div class="modal-body">
        
          {{ csrf_field() }}
          {{ method_field('PUT') }}
          <div class="box-body">
            <div class="box box-default">
              <div class="box-header with-border">
                <h3 class="box-title">Request Details </h3>
              </div>
              <div class="box-body">
                  <div class="form-group">
                      <label for="mail_received_time" class="col-md-3 control-label">Mail Received Time <span style="color:#FF0000">*</span> </label>
                      <div class="input-group date col-md-3">
                          <input type='text' class="form-control" name="mail_received_time" id="mail_received_time" value="{{$indexing->mail_received_time}}" readonly />
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                          <span class="help-block" id="mail_received_time_err"></span>
                          <input type='hidden' name="indexing_id" value="{{$indexing->id}}"/>
                      </div>
                      <label for="user_name" class="col-md-3 control-label">User Name <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                        @if(strtolower(Session::get('user_role'))=='admin')
                         <select name="user_name" id="name" class="form-control">
                            <option value="">Select</option>
                          @foreach(App\Model\User::all() as $user)
                            <option value="{{$user->id}}" {{(($user->id==$indexing->publishings->publish_by)?'selected':'')}}>{{$user->name}}</option>
                          @endforeach
                        </select>
                        @else
                          <input id="name" type="text" class="form-control capital-letter" name="user_name" value="{{ Session::get('ldap_name') }}" style="text-transform: uppercase;" readonly>
                        @endif
                          <span class="help-block" id="user_name_err"></span>
                      </div>
                  </div>

                  <div class="form-group">
                      
                      <label for="region_name" class="col-md-3 control-label">Region <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('region_name') ? ' has-error' : '' }}">
                       <input id="region_name" type="text" class="form-control" name="region_name" value="{{ $indexing->region->name }}" style="text-transform: uppercase;" readonly>

                          @if ($errors->has('region_name'))
                              <span class="help-block">
                                  <strong>{{ $errors->first('region_name') }}</strong>
                              </span>
                          @endif
                      </div>
                      <label for="request_id" class="col-md-3 control-label">Request Type <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                      
                       <input id="request_type_id" type="text" class="form-control" name="request_type_id" value="{{ $indexing->requestType->name }}" style="text-transform: uppercase;" readonly>

                          <span class="help-block" id="request_type_id_err"></span>
                      </div>
                      
                  </div>

                  <div class="form-group">
                    <label for="sq_no" class="col-md-3 control-label">Sq No. <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="sq_no" type="text" class="form-control mendatory" name="sq_no" value="{{ $indexing->publishings->sq_no or old('sq_no') }}">

                          <span class="help-block" id="sq_no_err"></span>
                      </div>
                    <label for="status_id" class="col-md-3 control-label">Status <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">

                          <select name="status_id" id="status_id" class="form-control">
                            <option value="">Select</option>
                            @foreach($statusData as $status)
                              <option value="{{$status->id}}" {{(($status->id==$indexing->publishings->status_id)?'selected':'')}}>{{strtoupper($status->status_name)}}</option>
                            @endforeach
                              <!-- <option value="Standard">Standard</option>
                              <option value="Urgent">Urgent</option> -->
                          </select>

                          <span class="help-block" id="status_id_err"></span>
                      </div>
                      
                     
                  </div>
                  <div class="form-group">
                    <div id="resolved_section" class="{{((($indexing->publishings->status->status_name)=='pending out')?'':'hide')}}">
                       <label for="rfi_comment" class="col-md-3 control-label">RFI Resolved Comment</label>
                        <div class="col-md-3">
                        <textarea name="rfi_comment" id="rfi_comment" class="form-control">{{$indexing->publishings->rfi_comment}}</textarea>

                        <span class="help-block" id="rfi_comment_err"></span>
                       </div>

                       <label for="rfi_etd" class="col-md-3 control-label">RFI Resolved Date Time</label>
                        <div class="col-md-3 {{ $errors->has('rfi_etd') ? ' has-error' : '' }}">
                         <input type='text' class="form-control datetimepicker" name="rfi_etd" id="rfi_etd" value="@if($indexing->publishings->rfi_end_date!='0000-00-00 00:00:00' && !empty($indexing->publishings->rfi_end_date)){{date('d-m-Y H:i',strtotime($indexing->publishings->rfi_end_date))}} @endif" autocomplete="off"/>
                         <span class="help-block" id="rfi_etd_err"></span>
                       </div>
                       <input type="hidden" name="rfi_id" value="{{ $indexing->rfi_id or '' }}">
                   </div>
                  </div>
                   <div class="form-group">
                     
                     <label for="total_lane" class="col-md-3 control-label">Total Lane <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('total_lane') ? ' has-error' : '' }}">
                          <input id="total_lane" type="number" class="form-control" name="total_lane" value="{{ $indexing->publishings->total_lane or old('total_lane') }}" min="0">

                          <span class="help-block" id="total_lane_err"></span>
                      </div>
                     <label for="no_of_inlands" class="col-md-3 control-label">No Of Inlands <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="no_of_inlands" type="number" class="form-control" name="no_of_inlands" value="{{ $indexing->publishings->no_of_inlands or old('no_of_inlands') }}" min="0">

                          <span class="help-block" id="no_of_inlands_err"></span>
                      </div>
                    </div>
                    <div class="form-group">
                     <label for="modes" class="col-md-3 control-label">Modes <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3" >
                        <div class="row">
                        <?php 
                          if(!empty($indexing->publishings->mode_id)){
                               if(strpos($indexing->publishings->mode_id, ',')){
                                  $mode_ids = explode(',',$indexing->publishings->mode_id);
                              }else{
                                  $mode_ids=array($indexing->publishings->mode_id);
                              }
                              
                          }else{
                              $mode_ids=array();
                          }
                        ?>
                            @foreach(\App\Model\Mode::all() as $k=>$mode)
                              <div class="col-md-2" id="modes_check">
                                <input type="checkbox" name="modes[]" id="modes{{$mode->id}}" class="mendatory" value="{{ $mode->id }}" {{ ((in_array($mode->id,$mode_ids))?"checked":"" ) }}>
                              </div>
                              <label for="mode_id{{$mode->id}}" class="col-md-4 control-label">{{$mode->name}}</label>
                              
                            @endforeach
                        </div>

                          <span class="help-block" id="modes_err"></span>
                      </div>
                     <label for="price_area" class="col-md-3 control-label">Pricing Area <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3" >
                          <div class="row">
                            <?php 
                             if(!empty($indexing->publishings->pricing_area)){
                                  if(strpos($indexing->publishings->pricing_area, ',')){
                                      $pricing_areas = explode(',',$indexing->publishings->mode_id);
                                  }else{
                                      $pricing_areas=array($indexing->publishings->pricing_area);
                                  }
                              
                              }else{
                                  $pricing_areas=array();
                              }
                            ?>
                            @foreach(\App\Model\PricingArea::all() as $pricing)
                            <?php //echo ((in_array($pricing->id,old('price_area')))?'checked':'');?>
                            <div class="col-md-3" id="pricing_area_check">
                                <input type="checkbox" name="price_area[]" class="mendatory" id="price_area{{$pricing->id}}" value="{{ $pricing->id }}" {{ ((in_array($pricing->id,$pricing_areas))?"checked":"" ) }}>
                            </div>
                            <label for="price_area{{$pricing->id}}" class="col-md-3 control-label">{{$pricing->name}}</label>
                            @endforeach
                         </div>
                          <span class="help-block" id="price_area_err"></span>
                      </div>
                    </div>
                    <div class="form-group">
                     <div class="col-md-3"></div>
                     <div class="col-md-9">
                          <input id="is_error" type="checkbox" name="is_error" value="1" @if($indexing->publishings->is_error==1) checked @endif >
                          <label for="is_error" class="control-label">Marked as error</label>
                      </div>
                      
                    </div>  
                  </div>
                </div><!--End Section Request Details-->
                @if(strtolower($indexing->requestType->name)!='rfi' && $indexing->publishings->status->status_name!='pending in')
                 <?php $class ='hide'?>
                @else
                 <?php $class =''?> 
                @endif
                <div id="rfi_section" class="{{$class}}">
                   <div class="box box-default">
                      <div class="box-header with-border">
                        <h3 class="box-title">RFI Details</h3>
                      </div>
                      <div class="box-body">
                        <div class="form-group">
                          <label for="rfi_type_id" class="col-md-3 control-label">RFI Type <span style="color:#FF0000">*</span></label>
                          <div class="col-md-3">
                              <select name="rfi_type_id" id="rfi_type_id" class="form-control">
                               <option value="">Select</option>
                                @foreach(\App\Model\RfiType::all() as $rfi)
                                  <option value="{{$rfi->id}}" {{(($rfi->id==$indexing->publishings->rfi_type_id)?'selected':'')}}>{{strtoupper($rfi->rfi_type_name)}}</option>
                                @endforeach
                              </select>

                              <span class="help-block" id="rfi_type_id_err"></span>
                          </div>

                          <label for="rfi_description" class="col-md-3 control-label">RFI Description <span style="color:#FF0000">*</span></label>
                          <div class="col-md-3 {{ $errors->has('rfi_description') ? ' has-error' : '' }}">
                               <textarea id="rfi_description" class="form-control" name="rfi_description">{{ $indexing->publishings->rfi_description or old('rfi_description') }}</textarea>

                              <span class="help-block" id="rfi_description_err"></span>
                          </div>
                       </div>
                       
                        <div class="form-group">
                           <label for="isr_initiated" class="col-md-3 control-label">ISR Intiated</label>
                              <div class="col-md-3">
                              <input type="text" name="isr_initiated" id="isr_initiated" class="form-control" value="{{$indexing->publishings->isr_initiated}}">
                               <span class="help-block" id="isr_initiated_err"></span>
                             </div>
                        </div>
                       
                      </div>
                  </div>
                </div><!--End Section RFI Details-->
                


                <div id="correction_section" class="{{(((strtolower($indexing->requestType->name)=='cor') || $indexing->publishings->is_error=='1')?'':'hide')}}">
                <div class="box box-default">
                  <div class="box-header with-border">
                    <h3 class="box-title">Correction Details</h3>
                  </div>
                  <div class="box-body">
                    <div class="form-group">
                      <label for="error_cat_id" class="col-md-3 control-label">Error Category <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3 {{ $errors->has('error_cat_id') ? ' has-error' : '' }}">
                          <select name="error_cat_id" id="error_cat_id" class="form-control cor-drop">
                           <option value="">Select</option>
                            @foreach(\App\Model\ErrorCat::all() as $category)
                              <option value="{{$category->id}}" {{(($category->id==$indexing->publishings->error_cat_id)?'selected':'')}}>{{strtoupper($category->name)}}</option>
                            @endforeach
                          </select>

                          <span class="help-block" id="error_cat_id_err"></span>
                      </div>
                      <label for="error_type_id" class="col-md-3 control-label">Error Type <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <select name="error_type_id" id="error_type_id" class="form-control cor-drop">
                           <option value="">Select</option>
                            @foreach(\App\Model\ErrorType::all() as $type)
                              <option value="{{$type->id}}" {{(($type->id==$indexing->publishings->error_type_id)?'selected':'')}}>{{strtoupper($type->name)}}</option>
                            @endforeach
                          </select>

                          <span class="help-block" id="error_type_id_err"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="error_description" class="col-md-3 control-label">Error Description <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <textarea id="error_description" class="form-control" name="error_description">{{ $indexing->publishings->error_description or old('error_description') }}</textarea>

                         <span class="help-block" id="error_description_err"></span>
                      </div>

                     <label for="root_cause" class="col-md-3 control-label">Root Cause Analysis <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="root_cause" type="text" class="form-control" name="root_cause" value="{{ $indexing->publishings->root_cause }}" >

                          <span class="help-block" id="root_cause_err"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="correction" class="col-md-3 control-label">Correction <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="correction" type="text" class="form-control" name="correction" value="{{ $indexing->publishings->correction }}">

                          <span class="help-block" id="correction_err"></span>
                      </div>

                      <label for="corrective_action" class="col-md-3 control-label">Corrective Action <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="corrective_action" type="text" class="form-control" name="corrective_action" value="{{ $indexing->publishings->corrective_action or old('corrective_action') }}">

                          <span class="help-block" id="corrective_action_err"></span>
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="preventive_action" class="col-md-3 control-label">Preventive Action <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="preventive_action" type="text" class="form-control" name="preventive_action" value="{{ $indexing->publishings->preventive_action }}">

                          <span class="help-block" id="preventive_action_err"></span>
                      </div>

                      <label for="proposed_comp_date" class="col-md-3 control-label">Proposed Completion Date <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input type='text' class="form-control datetimepicker" name="proposed_comp_date" id="proposed_comp_date" value="@if($indexing->publishings->proposed_comp_date!='0000-00-00 00:00:00' &&!empty($indexing->publishings->proposed_comp_date)){{ date('m/d/Y H:i:s',strtotime($indexing->publishings->proposed_comp_date)) }} @endif"/>
                          
                          <span class="help-block" id="proposed_comp_date_err"></span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="proposed_act_date" class="col-md-3 control-label">Actual Completion Date <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input type='text' class="form-control datetimepicker" name="proposed_act_date" id="proposed_act_date" value="@if($indexing->publishings->proposed_act_date!='0000-00-00 00:00:00' &&!empty($indexing->publishings->proposed_act_date)){{date('m/d/Y H:i:s',strtotime($indexing->publishings->proposed_act_date))}} @endif" />

                          <span class="help-block" id="proposed_act_date_err"></span>
                      </div>

                      <label for="error_done_by" class="col-md-3 control-label">Error Done By <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <select name="error_done_by" id="error_done_by" class="form-control cor-drop">
                              <option value="">Select</option>
                            @foreach(App\Model\User::all() as $user)
                              <option value="{{$user->id}}" {{(($user->id==$indexing->publishings->error_done_by)?'selected':'')}}>{{$user->name}}</option>
                            @endforeach
                          </select>

                         <span class="help-block" id="error_done_by_err"></span>
                      </div>
                    </div>
                  </div>
                </div>
              </div><!--End Section Correction Details-->

              <div class="box box-default">
                  <div class="box-header with-border">
                    <h3 class="box-title">Shipper Details</h3>
                  </div>
                  <div class="box-body">
                    <div class="form-group">
                      <label for="shipper_name" class="col-md-3 control-label">Shipper Name <span style="color:#FF0000">*</span></label>
                      <div id="shipperdiv" class="col-md-3">
                          <input id="shipper_name" type="text" class="form-control mendatory" name="shipper_name" value="{{ $indexing->publishings->partnerCodeDb->shipper_name or old('shipper_name') }}">
                          <span class="help-block" id="shipper_name_err"></span>
                      </div>
                     
                     <label for="address" class="col-md-3 control-label">Address<span style="color:#FF0000">*</span> </label>
                      <div id="addressdiv" class="col-md-3">
                          <textarea id="address" class="form-control mendatory" name="address">{{ $indexing->publishings->partnerCodeDb->address or old('address') }}</textarea>
                          <!-- <input type="text" id="address" class="form-control mendatory" name="address" value=""> -->
                          <span class="help-block" id="address_err"></span>
                      </div>
                      
                   </div>

                   <div class="form-group">
                     <label for="city" class="col-md-3 control-label">City <span style="color:#FF0000">*</span></label>
                      <div id="citydiv" class="col-md-3">
                          <input id="city" type="text" class="form-control mendatory" name="city" value="{{ $indexing->publishings->partnerCodeDb->city or old('city') }}">
                         <span class="help-block" id="city_err"></span>
                      </div>

                      <label for="state" class="col-md-3 control-label">State <span style="color:#FF0000">*</span></label>
                      <div id="statediv" class="col-md-3">
                          <input id="state" type="text" class="form-control mendatory" name="state" value="{{ $indexing->publishings->partnerCodeDb->state or old('state') }}">
                         <span class="help-block" id="state_err"></span>
                      </div>
                      
                    </div>

                   <div class="form-group">
                     <label for="partner_code" class="col-md-3 control-label">Partner Code <span style="color:#FF0000">*</span></label>
                      <div class="col-md-3">
                          <input id="partner_code" type="text" class="form-control mendatory" name="partner_code" value="{{ $indexing->publishings->partnerCodeDb->partner_code or old('partner_code') }}" data-url="{{url('/partnerData')}}">

                          <span class="help-block" id="partner_code_err"></span>
                      </div>
                      
                    </div>
                  </div>
                </div><!--End Section Shipper Details-->

              <div class="form-group">
                  <label for="office" class="col-md-3 control-label">OOT Remark</label>
                  <div class="col-md-3">
                      <textarea name="oot_remark" class="form-control" @if($indexing->publishings->oot==1) required @endif>{{$indexing->publishings->oot_remark or old('oot_remark')}}</textarea>

                      <span class="help-block" id="oot_remark_err"></span>
                  </div>
                  <label for="office" class="col-md-3 control-label">Comments</label>
                  <div class="col-md-3">
                      <textarea name="comments" class="form-control">{{$indexing->publishings->comments or old('comments')}}</textarea>
                      <input type="hidden" name="tat_comp" value="{{ $indexed_tat }}">
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
   <script src="{{ asset('/assets/dist/js/jquery.validate.js') }}"></script>
   <script src="{{ asset('/assets/dist/js/validation.js') }}"></script>
   <script src="{{ asset('/assets/dist/js/autocomplete.js') }}"></script>
  </div>
 

 <!-- <script>
   $(document).ready(function(){
        
  });
 </script> -->