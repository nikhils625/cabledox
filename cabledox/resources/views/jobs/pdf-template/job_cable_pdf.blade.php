<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
   <meta charset="utf-8">
   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>{{$pdfName}}</title>
	<style type="text/css">
      @media print
      {
         table {
            page-break-inside:avoid !important;
            page-break-after:auto !important;
         }
      }
      body{
        font-family: "Segoe UI",  sans-serif;
        margin: 0;
      }
      @page { margin: 0px; }
      table  tr td{
         /*text-align: center;*/
         font-weight: normal !important; 
      }
      table{
         width: 100%;
         font-size:16px;
         /*margin-bottom: 1rem; */
      }
      h4 {
         padding: 0 !important;
         margin: 0 !important;
      }
      h3 {
         padding:0 !important;
         /*margin-top: -8px;*/
         font-size: 26px; 
         /*background: #555;*/
         color: #fff;
         margin: 0 !important;
      }
      h4 {
         font-size: 22px;
         /*margin-bottom: 3px;*/
         /*text-align: left;*/
         background: #626569; /*#e4e4e4;*/
         color: #fff;
         font-weight: 900;
      }
      table tr td table tr:first-child {
         background: #333;
         color: #fff;
         /*padding: 0;*/
      }
      table tr td table tr td {
         padding:0px 0;
         border:#000 solid 1px;
      }
      .page-break {
         page-break-after: always;
      }
   </style>
</head>
<body>
	<div style="padding: 2rem;max-width: 100%;">
      <table class="table table-bordered" cellspacing="0">
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td align="center"><h3><strong>Cable Information Form</strong></h3></td>
                     <td><img src="{{$companyLogo}}" height="60" style="margin:15px;"></td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td>Cable ID</td>
                     <td>
                        {{$cable->cable_id}}
                        @if($cable->custom_id)
                           / {{$cable->custom_id}}
                        @endif
                        @if($cable->unique_code)
                           / {{$cable->unique_code}}
                        @endif
                     </td>
                  </tr>
                  <tr>
                     <td>Cable type</td>
                     <td>{{$cable->cableType->cable_name}}</td>
                  </tr>
                  <tr>
                     <td>Size</td>
                     <td>{{$cable->size}} (mm sq)</td>
                  </tr>
                  <tr>
                     <td>Cores</td>
                     <td>
                        @php
                           $cableCores = @$cable->cableIdType->cores;
                           if(@$cable->cableIdType->no_of_pair_triple_quad > 1) {
                              $cableCores = @$cable->cableIdType->cores . "x" . @$cable->cableIdType->no_of_pair_triple_quad; 
                           }
                        @endphp
                        {{$cableCores}}
                     </td>
                  </tr>
                  <tr>
                     <td>From</td>
                     <td>{{$cable->jobCableFrom->jobLocation->location_name}}</td>
                  </tr>
                  <tr>
                     <td>To</td>
                     <td>{{$cable->jobCableTo->jobLocation->location_name}}</td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td align="center" colspan="2"><h4><strong>"{{$cable->jobCableTo->jobLocation->location_name}}" Termination Details</strong></h4></td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <table class="table table-bordered" cellspacing="0">
                           <tr>
                              <td>Cores</td>
                              <td>Cores ID</td>
                              <td>Termination Location</td>
                           </tr>
                           @foreach($cable->cableIdType->cableMasterCoreDetails as $coreKey => $core)
                              @php
                                 $coreIdValue = $terminationLocationValue = null;
                                 if(isset($terminationTo->jobTerminationDetails[$coreKey])) {
                                    $coreIdValue = $terminationTo->jobTerminationDetails[$coreKey]->core_id; 
                                    $terminationLocationValue = $terminationTo->jobTerminationDetails[$coreKey]->termination_location;
                                 }
                              @endphp
                              <tr>
                                 <td>{{$core->core_name}}</td>
                                 <td>{{$coreIdValue}}</td>
                                 <td>{{$terminationLocationValue}}</td>
                              </tr>
                           @endforeach
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td align="center" colspan="2"><h4><strong>"{{$cable->jobCableFrom->jobLocation->location_name}}" Termination Details</strong></h4></td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <table class="table table-bordered" cellspacing="0">
                           <tr>
                              <td>Cores</td>
                              <td>Cores ID</td>
                              <td>Termination Location</td>
                           </tr>
                           @foreach($cable->cableIdType->cableMasterCoreDetails as $coreKey => $core)
                              @php
                                 $coreIdValue = $terminationLocationValue = null;
                                 if(isset($terminationFrom->jobTerminationDetails[$coreKey])) {
                                    $coreIdValue = $terminationFrom->jobTerminationDetails[$coreKey]->core_id; 
                                    $terminationLocationValue = $terminationFrom->jobTerminationDetails[$coreKey]->termination_location;
                                 }
                              @endphp
                              <tr>
                                 <td>{{$core->core_name}}</td>
                                 <td>{{$coreIdValue}}</td>
                                 <td>{{$terminationLocationValue}}</td>
                              </tr>
                           @endforeach
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td align="center" colspan="2"><h4><strong>Check list</strong></h4></td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <table class="table table-bordered" cellspacing="0">
                           <tr>
                              <td>Checklist Name</td>
                              <td>Name/Date</td>
                           </tr>
                           @foreach($checklistMaster as $key => $checklist)
                              <tr>
                                 <td>{{$checklist->checklist_name}}</td>
                                 <td>
                                    @php
                                       if(isset($jobChecklistDetails[$key]) && !empty($jobChecklistDetails[$key])) {
                                    @endphp
                                          {{$jobChecklistDetails[$key]->name}} / {{$jobChecklistDetails[$key]->submit_date}}
                                    @php
                                       }
                                    @endphp
                                 </td>
                              </tr>
                           @endforeach
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
         <tr>
            <td> 
               <table class="table table-bordered" cellspacing="0">
                  <tr>
                     <td align="center" colspan="2"><h4><strong>Test Results</strong></h4></td>
                  </tr>
                  <tr>
                     <td colspan="2">
                        <table class="table table-bordered" cellspacing="0">
                           <tr>
                              <td>Tests</td>
                              <td>Output</td>
                           </tr>
                           @if(!empty($getTestParameter))
                              @foreach($getTestParameter as $key => $testParameter)
                                 <tr>
                                    <td>{{$testParameter->parameter_name}}</td>
                                    <td>
                                       @if(isset($jobTestResult[$key]->id))
                                          {{$jobTestResult[$key]->output}}
                                       @else
                                        --
                                       @endif
                                    </td>
                                 </tr>
                              @endforeach
                           @endif
                        </table>
                     </td>
                  </tr>
               </table>
            </td>
         </tr>
      </table>
	</div>
</body>
</html>