<?php
 
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Job;
use App\Models\JobCable;
use App\Models\JobUser;
use App\Models\JobDrawing;
use App\Models\CableType;
use App\Models\CableMaster;
use App\Models\JobTermination;
use App\Models\JobTerminationDetail;
use App\Models\JobCableLocation;
use App\Models\TestParameter;
use App\Models\JobTestResultDetail;
use App\Models\JobFinalCheckSheet;
use App\Models\JobFinalCheckSheetDetails;
use App\Models\FinalCheckSheetQuestionnaire;
use App\Models\JobAreaOfWorkDetail;
use App\Models\JobLocation;
use App\Models\ReportedIssue;
use App\Models\ReportedIssueDetail;
use App\Models\ChecklistMaster;
use App\Models\JobChecklistDetail;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Builder;
use DataTables;
use Response;
use Carbon\Carbon;
use PDF;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('jobs.list')) {
            abort(404);
        }

        $user = \Auth::user();

        $role_id = 0;

        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
            $role_id = @$user->roles[0]->id;
        }

        //$userRole = User::with(['roles'])->pluck('role_id')->toArray();

        if ($request->ajax()){

            $data = Job::with(['jobUsers'])->where('client_id', $user->client_id);

            if($role_id == '3' ||  $role_id == '4' ||  $role_id == '5'){

                $data->whereHas('jobUsers', function($q) use ($user){
                    $q->where('user_id', '=', $user->id);                        
                }); 
            }

            if($request->status == '0' || $request->status == '1' || $request->status == '2'){
                $data->where('status',$request->status);
            } 

            $data = $data->orderBy('id', 'DESC')->get();

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('status',function($row){
                return $row->status;
            })
            ->addColumn('action', function($row) {
                $btn = '';
                if(auth()->user()->can('jobs.view')) {
                    $btn .= '<a href="'.route('jobs.show',$row->id).'" data-toggle="tooltip" title="View" class="icons"><i class="fa fa-eye"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('jobs.edit') && $row->status != 2) {
                    $btn .= '<a href="'.route('jobs.edit',$row->id).'" data-toggle="tooltip" title="Edit" class="icons"><i class="fa fa-edit"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('jobs.delete')) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('jobs.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }       
        return view('jobs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        if(!auth()->user()->can('jobs.create')) {
            abort(404);
        }

        $authUser = \Auth::user();

        $role_name = 0;

        if(!@$authUser->roles->isEmpty() && @$authUser->roles->count() == 1) {
            $role_name = @$authUser->roles[0]->name;
        }
        
        /*get electrician record*/
        $electrician =  User::whereHas('roles', function($q){
                          $q->where('id', '=', 3); 
                        })->where('client_id', $authUser->client_id)
                        ->where('status', 1)->get();                        

        /*get manager record*/
        $manager = User::whereHas('roles', function($q){
                      $q->where('id', '=', 4); 
                    })->where('client_id', $authUser->client_id)
                    ->where('status', 1)->get();

        /*get superVisor record*/
        $superVisor = User::whereHas('roles', function($q){
                        $q->where('id', '=', 5); 
                    })->where('client_id', $authUser->client_id)
                    ->where('status', 1)->get();

        return view('jobs.add' ,compact('electrician','manager','superVisor','role_name'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs   = $request->except('_token');        
        $clientId = \Auth::user()->client_id;

        $validator = (new Job)->validateJob($inputs, null, $clientId);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }

        try{
            \DB::beginTransaction();
            
            /*insert data into jobs Table*/
            $add_job = new Job();
            $add_job->user_id = \Auth::user()->id;
            $add_job->client_id = \Auth::user()->client_id;
            $add_job->job_number = $request->job_number;
            $add_job->site_name =  $request->site_name;
            $add_job->post_code =  $request->post_code;
            $add_job->address =   $request->address;
            $add_job->status =  0;
            $add_job->save();

            /*insert data into Job Users Table*/
            $data =[];
            foreach($request['user_id'] as $key => $value){
                    $data[] =array(
                    'job_id'=>$add_job->id,'user_id'=>$value
                );
            }

            JobUser::insert($data);
            
            \DB::commit();
            return redirect()->back()->with('success','Job details saved successfully');
        } 
        catch(\Exception $e) {
            \DB::rollBack();
            $message = $e->getMessage().' - Internal Server Error';
            return redirect()->back()->withError($message)->withInput($inputs);

        }       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can('jobs.view')) {
            abort(404);
        }
        
        $job = Job::with(['jobUsers'])->find($id); 
        if(!$job) {
            abort(404);
        }

        $user = \Auth::user();

        $roleName = 0;
        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
            $roleName = @$user->roles[0]->name;
        }

        $managers = User::with(['roles'])->where('role_id', 4)->where('client_id', $user->client_id)->where('status',1)->get();

        /*get electrician record*/
        $electricians =  User::whereHas('roles', function($q){
                          $q->where('id', '=', 3); 
                        })->where('client_id', $user->client_id)
                        ->where('status', 1)->get();
        /*get superVisor record*/
        $superVisors = User::whereHas('roles', function($q){
                        $q->where('id', '=', 5); 
                    })->where('client_id', $user->client_id)
                    ->where('status', 1)->get();

        /*get data for jobdrawing table*/
        $getJobDrawing = JobDrawing::where('client_id', $user->client_id)->where('job_id', $id)->get();

        /*get data from jobcable table */
        $jobCableOptions= JobCable::where('job_id', $id)->where('client_id', $user->client_id)->get()->toArray();

        $cableOptions = JobCable::where(['job_id' => $id, 'client_id' => $user->client_id])->pluck('id')->toArray();

        /*get all location with cable */
        $jobCableLocations = JobCableLocation::whereHas('jobLocation', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->whereIn('job_cable_id', $cableOptions)->get();

        $jobCableLocations = collect($jobCableLocations)->unique('location_id');

        /*get from location  to job cable */
        $jobLocationTo = JobCableLocation::whereHas('jobLocation', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->whereIn('job_cable_id', $cableOptions)->where('location_type', '0')->get();

        $jobLocationTo = collect($jobLocationTo)->unique('location_id');

        /*get from location  from job cable */
        $jobLocationFrom = JobCableLocation::whereHas('jobLocation', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->whereIn('job_cable_id', $cableOptions)->where('location_type', '1')->get();

        $jobLocationFrom = collect($jobLocationFrom)->unique('location_id');

        return view('jobs.show',compact('managers', 'job', 'getJobDrawing', 'jobCableOptions', 'jobCableLocations', 'jobLocationTo', 'jobLocationFrom', 'cableOptions', 'roleName', 'electricians', 'superVisors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if(!auth()->user()->can('jobs.edit')) {
            abort(404);
        }

        $job = Job::with(['jobUsers'])->find($id);
        if(!$job) {
            abort(404);
        }

        $authUser = \Auth::user();

        //userRole = User::with(['roles'])->pluck('role_id')->toArray();
        $role_name = 0;
        if(!@$authUser->roles->isEmpty() && @$authUser->roles->count() == 1) {
            $role_name = @$authUser->roles[0]->name;
        }
        
        /*get electrician record*/
        $electrician =  User::whereHas('roles', function($q){
                          $q->where('id', '=', 3); 
                        })->where('client_id', $authUser->client_id)
                        ->where('status', 1)->get();                        

        /*get manager record*/
        $manager = User::whereHas('roles', function($q){
                      $q->where('id', '=', 4); 
                    })->where('client_id', $authUser->client_id)
                    ->where('status', 1)->get();

        /*get superVisor record*/
        $superVisor = User::whereHas('roles', function($q){
                        $q->where('id', '=', 5); 
                    })->where('client_id', $authUser->client_id)
                    ->where('status', 1)->get();

        /*get data for jobdrawing table*/
        $getJobDrawing = JobDrawing::where('client_id', $authUser->client_id)->where('job_id', $id)->get();

        /*get data from jobcable table */
        $jobCableOptions= JobCable::where('job_id', $id)->where('client_id', $authUser->client_id)->get()->toArray();
        
        /*get data from job cable table*/
        $jobCable = JobCable::with(['jobCableLocations'])->where('client_id', $authUser->client_id)->where('job_id', $id)->get();

        $cableOptions = JobCable::where(['job_id' => $id, 'client_id' => $authUser->client_id])->pluck('id')->toArray();

        /*get all location with cable */
        $jobCableLocations = JobCableLocation::whereHas('jobLocation', function ($q) use ($authUser) {
            $q->where('client_id', $authUser->client_id);
        })->whereIn('job_cable_id', $cableOptions)->get();

        $jobCableLocations = collect($jobCableLocations)->unique('location_id');

        /*get from location  to job cable */
        $jobLocationTo = JobCableLocation::whereHas('jobLocation', function ($q) use ($authUser) {
            $q->where('client_id', $authUser->client_id);
        })->whereIn('job_cable_id', $cableOptions)->where('location_type', '0')->get();

        $jobLocationTo = collect($jobLocationTo)->unique('location_id');

        /*get from location  from job cable */
        $jobLocationFrom = JobCableLocation::whereHas('jobLocation', function ($q) use ($authUser) {
            $q->where('client_id', $authUser->client_id);
        })->whereIn('job_cable_id', $cableOptions)->where('location_type', '1')->get();

        $jobLocationFrom = collect($jobLocationFrom)->unique('location_id');
       
        /*get CableType and CableMaster record  for add job cable*/
        $cableTypesId = CableType::pluck('cable_name', 'id')->toArray();
        $cableTypes   = CableMaster::where('client_id',$authUser->client_id)->pluck('cable_type_id', 'id')->toArray(); 

        return view('jobs.edit',compact('manager','job','getJobDrawing','jobCableOptions','jobCable','cableTypesId','cableTypes','jobLocationTo','jobLocationFrom','jobCableLocations','electrician','superVisor','role_name'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $inputs = $request->except('_token');
  
        $client_id = \Auth::user()->client_id;

        $validator = (new Job)->validateJob($inputs, $id, $client_id);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }

        try{
            \DB::beginTransaction();        
           
            /*update data into Job Table*/
            Job::whereId($id)->update(['job_number' => $request->job_number,'site_name' =>$request->site_name,
                'post_code' =>$request->post_code,'address' =>$request->address]);

           /*Delete & insert data into JobUser Table*/
            $get_user = JobUser::where('job_id',$id)->delete();

            $data =[];
            foreach($request['user_id'] as $key => $value){
                    $data[] =array(
                    'job_id'=>$request->job_id,'user_id'=>$value
                );
            }
                           
            JobUser::insert($data);

            \DB::commit();
            return redirect()->back()->with('success','Job details update successfully');
        } catch (\PDOException $e) {
            \DB::rollBack();

            $message = 'Database Error: ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
        catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }               
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('jobs.delete')) {
            abort(404);
        }

        $job = Job::with('jobUsers')->find($id);

        if(!$job) {
            $message = 'Job not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $job->delete();
            if(!$job->jobUsers->isEmpty() && $job->jobUsers->count() > 0) {
                $job->jobUsers->each->delete();
            }

            \DB::commit();

            $message = 'Error in deleting job, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Job deleted successfully';
                $result  = ['status' => 1, 'message' => $message];
            }
            return response()->json($result);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = $e->getMessage().'-  -'.' Internal Server Error';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    } 

    /**
     * Display a listing of the drawing images.
     *
     * @return \Illuminate\Http\Response
     */
    public function insertJobDrawing(Request $request, $jobId = null)
    {
        $inputs = $request->except('_token'); 

        if(!$jobId) {
            return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }  

        $job = Job::find($jobId);

        if(!$jobId) {
            return redirect()->back()->withError('Job not found.')->withInput($inputs);
        }  

        $validator = (new JobDrawing)->validateJobDrawing($inputs);

        if ($validator->fails()) {

            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        //Location to where you want to created upload image

        $jobsDir = \Config::get('constants.uploadPaths.jobs');

        $singleLinePath   = \Config::get('constants.uploadPaths.singleLineDrawing');

        $schematicPath  = \Config::get('constants.uploadPaths.schematicDrawing');

        $locationPath   = \Config::get('constants.uploadPaths.locationDrawing');

        $jobNumber = $job->job_number;

        $singleLineDir   = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$singleLinePath;

        $schematicDir   = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$schematicPath;

        $locationDir   = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$locationPath;

        try {

            \DB::beginTransaction();
            
            $jobDrawing =[];

            foreach ($request['drawing_name']  as $key => $value){ 

                if(isset($request['drawing_name'][$key])  && !empty($request['drawing_name'][$key])){

                    if(!\File::isDirectory($singleLineDir)){
                       \File::makeDirectory($singleLineDir, 0775, true);
                    }
 
                    $fileOzName = str_replace(' ', '', $request['drawing_name'][$key]->getClientOriginalName());
                    $fileOzExtension = $request['drawing_name'][$key]->getClientOriginalExtension();
                    $fileName = time().'_'.pathinfo(strtolower($fileOzName), PATHINFO_FILENAME).'.'.$fileOzExtension;

                    if($request->drawing_type == '1'){

                       $request['drawing_name'][$key]->move($singleLineDir, $fileName);

                    }
                    elseif($request->drawing_type == '2'){

                        $request['drawing_name'][$key]->move($schematicDir, $fileName);

                    }
                    elseif($request->drawing_type == '3'){

                         $request['drawing_name'][$key]->move($locationDir, $fileName);

                    }                   

                }   

                $jobDrawing[] =array(

                    'user_id' => \Auth::user()->id,
                    'client_id' => \Auth::user()->client_id,
                    'job_id'=> $jobId,
                    'drawing_name' =>$fileName,
                    'drawing_type' =>$request->drawing_type,
                    'status' => 1
                );              
                
            }
            JobDrawing::insert($jobDrawing);

            \DB::commit();
            
            return redirect()->back()->with('success', 'Drawing saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }          

    /**
     * Remove the drawing images.
     * @return \Illuminate\Http\Response
    */
    public function removeJobDrawing(Request $request)
    {
        $dir = \Config::get('constants.uploadPaths.uploadDrawingImage');

        $data = JobDrawing::find($request->imgId);

        if(!$data) {
            $message = 'Job Drawing  not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            /* if(!empty($data->drawing_name) && file_exists($dir . $data->drawing_name)) {
                    unlink($dir . $data->drawing_name);
                }*/

            $res = $data->delete();

            \DB::commit();

            $message = 'Error in deleting job drawing, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Drawing deleted successfully';
                $result  = ['status' => 1, 'message' => $message];
            }
            
            return response()->json($result);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = $e->getMessage().'-  -'.' Internal Server Error';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

     /**
     * download the drawing images.
     *
     * @param $file path to the file
     * @param $fileName incase want to rename it.
     */

    public function downloadDrawingImage($encodedFilePath, $fileName = null) 
    {
        $file = null;
        try {
            $file = base64_decode($encodedFilePath);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
        
        $fileExtension = \File::extension($file);

        $headers = [
            'Content-Type: application/'.$fileExtension
        ];

        $filePath = public_path($file);

        if(file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            return redirect()->back()->with('error', 'Document Not Exist!');
        }
    }   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Job  $job
     * @return \Illuminate\Http\Response
     */
    public function addTerminationdetails(Request $request, $jobId = null) 
    {
        if(!auth()->user()->can('termination-details.add')) {
            abort(404);
        }
        // jobTerminationdetails
        $job = Job::with(['jobUsers'])->find($jobId);
        if(!$job) {
            abort(404);
        }

        $jobOptions = Job::select(\DB::raw('CONCAT(job_number, " + ", site_name) AS job_name'), 'id')->pluck('job_name', 'id')->toArray();
        $jobCableOptions = JobCable::where('job_id', $jobId)->get()->toArray();
        return view('jobs.termination-details.add', compact('job', 'jobCableOptions', 'jobOptions'));
    }

    /**
     * get the job cable location details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Job  $jobId
     * @return \Illuminate\Http\Response
     */
    public function getJobCableLocations(Request $request, $jobId = null) 
    {
        if ($request->ajax()) {
            $user = \Auth::user();

            $whereArr = [
                'client_id'     => $user->client_id
            ];

            $jobCable = JobCable::with('jobCableLocations')->where($whereArr)->find($request->job_cable_id);

            if(!$jobCable) {
                $result  = ['status' => 0, 'message' => 'job cable not found.'];
                return response()->json($result);
            }

            $jobCableLocations = JobCableLocation::with('jobLocation')->where('job_cable_id', $jobCable->id)->get();

            $jobCableLocations = collect($jobCableLocations)->unique('location_id');

            $result  = ['status' => 1, 'locations' => $jobCableLocations];

            return response()->json($result);
        } else {
            return redirect()->back()->with('error', 'Request not allowed.');
        }
    }

    /**
     * get the termination details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTerminationDetails(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            $jobId = $request->job_id;
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job id not found.'];
                return response()->json($result);
            }

            $job = Job::find($jobId);
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job not found.'];
                return response()->json($result);
            }

            $data['job'] = $job;

            $whereArr = [
                'client_id'    => $authUser->client_id, 
                'job_id'       => $jobId, 
                'cable_id'     => $request->job_cable_id, 
                'location_id'  => $request->location_id, 
            ];

            $jobTermination = JobTermination::where($whereArr)->first();
            if(!empty($jobTermination)) {
                $data['jobTermination'] = $jobTermination;
            }

            $whereArr = [
                'client_id' => $authUser->client_id,
                'job_id'    => $jobId,
                'id'        => $request->job_cable_id,
            ];

            $jobCable = JobCable::where($whereArr)->first();

            $data['jobCable'] = $jobCable; 

            $data['isEditable'] = 1;
            if(isset($request->is_editable) && $request->is_editable == 0) {
                $data['isEditable'] = 0;
            }
            // $coreDetails = $jobCable->cableIdType->cableMasterCoreDetails;
            $content = view('jobs.termination-details.cable_cores', $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content)];
            return Response::json($result);
        }
    }

    /**
     * Store a termination resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveTerminationdetails(Request $request) 
    {
        $inputs   = $request->except('_token');        
        $clientId = \Auth::user()->client_id;

        $validator = (new JobTermination)->validateJobTerminationdetails($inputs);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        $authUser = \Auth::user();

        try{
            \DB::beginTransaction();

            /*insert data into job termination Table*/
            if(isset($request->termination_id) && !empty($request->termination_id)) {
                $jobTermination = JobTermination::find($request->termination_id);
            } else {
                $jobTermination = new JobTermination;
                $jobTermination->user_id     = $authUser->id;
                $jobTermination->client_id   = $authUser->client_id;
            }
            $jobTermination->job_id      = 1;
            $jobTermination->cable_id    = $request->cable_id;
            $jobTermination->location_id = $request->location_id;
            $jobTermination->status      = 1;
            $jobTermination->save();

            /*insert data into Job termination details Table*/
            if(!empty($request->termination_detail) && count($request->termination_detail) > 0) {
                $terminationDetails   = $request->termination_detail;
                $cableMasterDetailIds = $terminationDetails['cable_master_detail_id'];
                $terminationArr       = [];

                foreach ($cableMasterDetailIds as $key => $cableMasterId) {
                    $terminationDetailArr = []; 
                    if($cableMasterId != '') {

                        $terminationDetailArr = new JobTerminationDetail;
                        // $terminationDetailArr['id'] = 0;
                        if(isset($terminationDetails['id'][$key]) && !empty($terminationDetails['id'][$key])) {
                            $terminationDetailId  = $terminationDetails['id'][$key];
                            $terminationDetailArr = JobTerminationDetail::find($terminationDetailId);
                        }

                        $terminationDetailArr['cable_master_detail_id'] = $cableMasterId;

                        $terminationDetailArr['core_id'] = null;
                        if(isset($terminationDetails['core_id'][$key])) {
                            $terminationDetailArr['core_id'] = $terminationDetails['core_id'][$key];
                        }

                        $terminationDetailArr['termination_location'] = null;
                        if(isset($terminationDetails['termination_location'][$key])) {
                            $terminationDetailArr['termination_location'] = $terminationDetails['termination_location'][$key];
                        }
                    }
                    $terminationArr[] = $terminationDetailArr;
                }
                $jobTermination->jobTerminationDetails()->saveMany($terminationArr);
            }
            \DB::commit();
            return redirect()->back()->with('success','Job termination details saved successfully');
        }
        catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * get the termination details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function finalCheckSheet(Request $request, $jobId = null)
    {
        if(!auth()->user()->can('job.add-final-check-sheet')) {
            abort(404);
        }

        $user = \Auth::user();

        $role_name = 0;

        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
            $role_name = @$user->roles[0]->name;
        }      

        $job = Job::with(['jobUsers'])->find($jobId);
        if(!$job) {
            abort(404);
        }

        $jobOptions = Job::where('client_id', $user->client_id)->select(\DB::raw('CONCAT(job_number, " + ", site_name) AS job_name'), 'id')->pluck('job_name', 'id')->toArray();

        $jobCableOption = JobCable::where(['job_id' => $jobId, 'client_id' => $user->client_id])->pluck('id')->toArray();

        $jobCableLocations = JobCableLocation::whereHas('jobLocation', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->whereIn('job_cable_id', $jobCableOption)->get();

        $jobCableLocations = collect($jobCableLocations)->unique('location_id');

        $jobCableOptions= JobCable::where('job_id', $jobId)->where('client_id', $user->client_id)->get()->toArray();

        return view('jobs.final-check-sheet.add', compact('job', 'jobCableLocations', 'jobOptions','role_name','jobCableOptions'));
    }

    /**
     * get the termination details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function viewFinalCheckSheet(Request $request, $jobId = null)
    {
        $user = \Auth::user();

        $job = Job::with(['jobUsers'])->find($jobId);
        if(!$job) {
            abort(404);
        }

        $roleName = 0;
        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
            $roleName = @$user->roles[0]->name;
        }

        $jobOptions = Job::where('client_id', $user->client_id)->select(\DB::raw('CONCAT(job_number, " + ", site_name) AS job_name'), 'id')->pluck('job_name', 'id')->toArray();

        /*get data from jobcable table */
        $jobCableOptions= JobCable::where('job_id', $jobId)->where('client_id', $user->client_id)->get()->toArray();

        $cableOptions = JobCable::where(['job_id' => $jobId, 'client_id' => $user->client_id])->pluck('id')->toArray();

        $jobCableLocations = JobCableLocation::whereHas('jobLocation', function ($q) use ($user) {
            $q->where('client_id', $user->client_id);
        })->whereIn('job_cable_id', $cableOptions)->get();

        $jobCableLocations = collect($jobCableLocations)->unique('location_id');

        return view('jobs.final-check-sheet.view', compact('job', 'jobCableLocations', 'jobOptions', 'jobCableOptions', 'roleName'));
    }

    /**
     * get the job cable details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCableDetails(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            if(!$request->job_id) {
                $result  = ['status' => 0, 'message' => 'job id not found.'];
                return response()->json($result);
            }

            $whereArr = [
                'client_id' => $authUser->client_id,
                'job_id'    => $request->job_id,
            ];

            $cableLocationId = $request->cable_location_id;

            $jobCables = JobCable::whereHas('jobCableLocations', function(Builder $query) use($cableLocationId) {
                $query->where('id', $cableLocationId);  
            })->where($whereArr)->get();

            if(!$jobCables) {
                $result  = ['status' => 0, 'message' => 'job cable not found.'];
                return response()->json($result);
            }

            $result  = ['status' => 1, 'job_cables' => $jobCables];
            return response()->json($result);
        } else {
            return redirect()->back()->with('error', 'Request not allowed.');
        }
    }

    /**
     * get the final check sheet details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCheckSheetDetails(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            $jobId    = $request->job_id;
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job id not found.'];
                return response()->json($result);
            }

            $job = Job::find($jobId);
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job not found.'];
                return response()->json($result);
            }

            $data['job'] = $job;

            $whereArr = [
                'client_id'         => $authUser->client_id, 
                'job_id'            => $jobId, 
                'cable_location_id' => $request->area_inspected, 
                'cable_id'          => $request->cable_id, 
            ];

            $jobFinalCheckSheet = JobFinalCheckSheet::where($whereArr)->first();
            if(!empty($jobFinalCheckSheet)) {
                $data['jobFinalCheckSheet'] = $jobFinalCheckSheet;
            }

            $whereArr = [
                'client_id' => $authUser->client_id,
                'user_id'   => $authUser->id,
            ];

            $questionnaire = FinalCheckSheetQuestionnaire::where($whereArr)->get();

            $data['questionnaire'] = $questionnaire;

            $viewName = "jobs.final-check-sheet.add_questionnaire";
            if(isset($request->is_editable) && $request->is_editable == 0) {    
                $viewName = "jobs.final-check-sheet.view_questionnaire";
            }

            $content = view($viewName, $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content' => $content)];
            return Response::json($result);
        }
    }

    /**
     * Store a final check sheet resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveFinalCheckSheet(Request $request, $jobId = null)
    {
        $inputs   = $request->except('_token');

        $jobId    = $request->job_id;
        if(!$jobId) {
            return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }

        $job = Job::find($jobId);
        if(!$jobId) {
            return redirect()->back()->withError('Job not found.')->withInput($inputs);
        }

        $clientId = \Auth::user()->client_id;

        $validator = (new JobFinalCheckSheet)->validateJobFinalCheckSheet($inputs);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        $authUser = \Auth::user();

        //Location to where you want to created sign image
        $jobsDir         = \Config::get('constants.uploadPaths.jobs');
        $inspectorPath   = \Config::get('constants.uploadPaths.inspectorSignatureImage');
        $pcInspectorPath = \Config::get('constants.uploadPaths.pcInspectorSignatureImage');

        $finalCheckSeetDocumentPath = \Config::get('constants.uploadPaths.finalCheckSeetDocument');

        $jobNumber = $job->job_number;

        $inspectorDir   = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$inspectorPath;
        $pcInspectorDir = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$pcInspectorPath;

        $finalCheckSeetDocumentDir = $jobsDir . $jobNumber.DIRECTORY_SEPARATOR.$finalCheckSeetDocumentPath;

        $fileNameArr = [];

        try {
            \DB::beginTransaction();

            /*insert data into job final check sheet Table*/
            if(isset($request->job_final_check_sheet_id) && !empty($request->job_final_check_sheet_id)) {
                $jobFinalCheckSheet = JobFinalCheckSheet::find($request->job_final_check_sheet_id);
            } else {
                $jobFinalCheckSheet = new JobFinalCheckSheet;
                $jobFinalCheckSheet->user_id     = $authUser->id;
                $jobFinalCheckSheet->client_id   = $authUser->client_id;
            }

            $jobFinalCheckSheet->job_id              = 1;
            $jobFinalCheckSheet->cable_location_id   = $request->area_inspected;
            $jobFinalCheckSheet->cable_id            = $request->cable_id;
            $jobFinalCheckSheet->inspector_name      = $request->inspector_name;

            if($request->has('inspector_signature') && !empty($request->inspector_signature)) {
                if(!empty($jobFinalCheckSheet->inspector_signature) && file_exists($inspectorDir . $jobFinalCheckSheet->inspector_signature)) {
                    unlink($inspectorDir . $jobFinalCheckSheet->inspector_signature);
                }

                if(!\File::isDirectory($inspectorDir)){
                    \File::makeDirectory($inspectorDir, 0775, true);
                }

                /*$inspectorSig = $request->inspector_signature;*/
                // or the better way, using PHP filters
                $inspectorSig = filter_input(INPUT_POST, 'inspector_signature', FILTER_UNSAFE_RAW);
                $imageDataJson = createImage($inspectorSig);
                $imageData     = json_decode($imageDataJson);
                if($imageData->status != 'success') {
                    $message = "Error in saving inspector signature.";
                    return redirect()->back()->withError($message)->withInput($inputs);
                }
                $signature = preg_replace('#^data:image/\w+;base64,#i', '', $imageData->signature);
                $imageDecoded = base64_decode($signature);
                $fileName     = time() . '_'. 'inspector_signature.png';
                $filePath     = $inspectorDir . $fileName;

                file_put_contents($filePath, $imageDecoded);
                $jobFinalCheckSheet->inspector_signature = $fileName;

                $fileNameArr['file'][] = $filePath;
            }

            $jobFinalCheckSheet->inspector_signature_date    = $request->inspector_signature_date;
            $jobFinalCheckSheet->pc_inspector_name           = $request->pc_inspector_name;

            if($request->has('pc_inspector_signature') && !empty($request->pc_inspector_signature)) {

                if(!empty($jobFinalCheckSheet->pc_inspector_signature) && file_exists($pcInspectorDir . $jobFinalCheckSheet->pc_inspector_signature)) {
                    unlink($pcInspectorDir . $jobFinalCheckSheet->pc_inspector_signature);
                }

                if(!\File::isDirectory($pcInspectorDir)){
                    \File::makeDirectory($pcInspectorDir, 0775, true);
                }

                /*$pcInspectorSig = $request->inspector_signature;*/
                // or the better way, using PHP filters
                $pcInspectorSig = filter_input(INPUT_POST, 'pc_inspector_signature', FILTER_UNSAFE_RAW);

                $imageDataJson = createImage($pcInspectorSig);
                $imageData     = json_decode($imageDataJson);
                if($imageData->status != 'success') {
                    $message = "Error in saving principle contractor inspector signature.";
                    return redirect()->back()->withError($message)->withInput($inputs);
                }
                $signature = preg_replace('#^data:image/\w+;base64,#i', '', $imageData->signature);
                $imageDecoded = base64_decode($signature);
                $fileName     = time() . '_'. 'pc_inspector_signature.png';
                $filePath     = $pcInspectorDir . $fileName;

                file_put_contents($filePath, $imageDecoded);
                $jobFinalCheckSheet->pc_inspector_signature = $fileName;

                $fileNameArr['file'][] = $filePath;
            }

            /*jobFinalCheckSheet document*/
            if($request->hasFile('upload_image')){

                if(!empty($jobFinalCheckSheet->upload_image) && file_exists($finalCheckSeetDocumentDir . $jobFinalCheckSheet->upload_image)) {
                    unlink($finalCheckSeetDocumentDir . $jobFinalCheckSheet->upload_image);
                }

                if(!\File::isDirectory($finalCheckSeetDocumentDir)){
                    \File::makeDirectory($finalCheckSeetDocumentDir, 0775, true);
                }

                $fileOzName = str_replace(' ', '', $request->file('upload_image')->            getClientOriginalName());
                $fileOzExtension = $request->file('upload_image')->getClientOriginalExtension();
                $fileName   = time().'_'.pathinfo(strtolower($fileOzName), PATHINFO_FILENAME).'.'.$fileOzExtension;

                $request->file('upload_image')->move($finalCheckSeetDocumentDir, $fileName);
                $jobFinalCheckSheet->upload_image = $fileName;

                $fileNameArr['file'][] = $finalCheckSeetDocumentDir. $fileName;
            }

            $jobFinalCheckSheet->pc_inspector_signature_date = $request->pc_inspector_signature_date;
            $jobFinalCheckSheet->save();

            /*insert data into Job final check sheet details Table*/
            if(!empty($request->questionnaire) && count($request->questionnaire) > 0) {
                $finalCheckSheetDetails  = $request->questionnaire;
                $fcsQuestionnaireIds     = $finalCheckSheetDetails['fcs_questionnaire_id'];
                $finalCheckSheetArr      = [];

                foreach ($fcsQuestionnaireIds as $key => $fcsQuestionnaireId) {
                    $finalCheckSheetDetailArr = []; 
                    if($fcsQuestionnaireId != '') {

                        $finalCheckSheetDetailArr = new JobFinalCheckSheetDetails;
                        // $finalCheckSheetDetailArr['id'] = 0;
                        if(isset($finalCheckSheetDetails['id'][$key]) && !empty($finalCheckSheetDetails['id'][$key])) {
                            $finalCheckSheetDetailId  = $finalCheckSheetDetails['id'][$key];
                            $finalCheckSheetDetailArr = JobFinalCheckSheetDetails::find($finalCheckSheetDetailId);
                        }

                        $finalCheckSheetDetailArr['fcs_questionnaire_id'] = $fcsQuestionnaireId;

                        $finalCheckSheetDetailArr['completed'] = null;
                        if(isset($finalCheckSheetDetails['completed'][$key])) {
                            $finalCheckSheetDetailArr['completed'] = $finalCheckSheetDetails['completed'][$key];
                        }

                        $finalCheckSheetDetailArr['comment'] = null;
                        if(isset($finalCheckSheetDetails['comment'][$key])) {
                            $finalCheckSheetDetailArr['comment'] = $finalCheckSheetDetails['comment'][$key];
                        }
                    }
                    $finalCheckSheetArr[] = $finalCheckSheetDetailArr;
                }
                $jobFinalCheckSheet->jobFinalCheckSheetDetails()->saveMany($finalCheckSheetArr);
            }

            \DB::commit();
            return redirect()->back()->with('success','Job final details saved successfully.'); 
        } catch (\PDOException $e) {
            \DB::rollBack();

            if(isset($fileNameArr['file']) && !empty($fileNameArr['file']) && count($fileNameArr['file']) > 0) {
                $this->removeFiles($fileNameArr['file']);
            }

            $message = 'Database Error: ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        } catch(\Exception $e) {
            \DB::rollBack();

            if(isset($fileNameArr['file']) && !empty($fileNameArr['file']) && count($fileNameArr['file']) > 0) {
                $this->removeFiles($fileNameArr['file']);
            }
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * download a final check sheet resource in pdf.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    /*public function downloadFinalCheckSheetPdf(Request $request)
    {
        if(!$request->job_final_check_sheet_id) {
            $message = 'Job final check sheet id not found.';
            return redirect()->back()->withError($message);
        }

        try {

            $authUser = \Auth::user();

            $whereArr = [
                'client_id' => $authUser->client_id
            ];

            $jobFinalCheckSheet = JobFinalCheckSheet::where($whereArr)->find($request->job_final_check_sheet_id);

            $jobNumber         = $jobFinalCheckSheet->job->job_number;
            $cableName         = $jobFinalCheckSheet->jobCable->cable_id;
            $cableLocationName = $jobFinalCheckSheet->jobCableLocations->location;

            $companyLogo = asset(\Config::get('constants.static.staticProfileImage'));
            if(isset($jobFinalCheckSheet->job->jobCompany->company_logo) && !empty($jobFinalCheckSheet->job->jobCompany->company_logo)) {
                $companyLogoPath = \Config::get('constants.uploadPaths.viewCompanyLogo');

                $companyLogo = public_path($companyLogoPath . $jobFinalCheckSheet->job->jobCompany->company_logo);
            }

            $companyLogo   = $companyLogo;
            $fileExtension = \File::extension($companyLogo);
            $companyLogo   = 'data:image/'.$fileExtension.';base64,'.base64_encode(file_get_contents($companyLogo));

            $pdfName = 'FinalCheckSheet_'.$jobNumber.'_'.$cableName.'_'.$cableLocationName.'.pdf';
            $pdf = PDF::loadView('pdf.final_check_sheet_pdf', compact('jobFinalCheckSheet', 'companyLogo', 'pdfName'));
            // download PDF file with download method
            return $pdf->download($pdfName);
        } catch (\Exception $e) {
            abort(404);
        }
    }*/

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAreaOfWork(Request $request, $jobId = null)
    {
        $user = \Auth::user();

        if ($request->ajax()) {

            $jobCable = JobCable::with(['cableIdType', 'jobCableTo', 'jobCableFrom','areaOfWorkDetails'])->where('client_id', $user->client_id);

            if($jobId) {
                $jobCable->where('job_id', $jobId);
            }

            if($request->to_location && $request->from_location){
                $jobCable->whereHas('jobCableTo', function ($q) use ($request){
                    $q->where('id', $request->to_location);               
                });
                $jobCable->whereHas('jobCableFrom', function ($q) use ($request){
                    $q->where('id', $request->from_location);                    
                });
            }
            elseif($request->to_location){
                 $jobCable->whereHas('jobCableTo', function ($q) use ($request){
                    $q->where('id', $request->to_location);                 
                });
            }
            elseif($request->from_location){
                $jobCable->whereHas('jobCableFrom', function ($q) use ($request){
                    $q->where('id', $request->from_location);                
                });
            }
            
            if(($request->status == '1' || $request->status == '0') && ($request->type == 'installed')){
                $jobCable->whereHas('areaOfWorkDetails', function ($q) use ($request,$user){
                    $q->where('installed', $request->status);                  
                });
            }
            elseif(($request->status == '1' || $request->status == '0') && ($request->type == 'checklist')){
                $jobCable->whereHas('areaOfWorkDetails', function ($q) use ($request,$user){
                    $q->where('checklist', $request->status);                      
                });
            }
            elseif(($request->status == '1' || $request->status == '0') && ($request->type == 'test-result')){
                $jobCable->whereHas('areaOfWorkDetails', function ($q) use ($request,$user){
                    $q->where('test_result', $request->status);                  
                });
            }

            $jobCable = $jobCable->orderBy('id', 'DESC')->get();
            return Datatables::of($jobCable)
            ->addIndexColumn()
            ->addColumn('cableId', function($jobCable) {
                return @$jobCable->cable_id;
            })
            ->addColumn('to', function($jobCable) {
                return @$jobCable->jobCableTo->jobLocation->location_name;

            })->addColumn('from', function($jobCable) {
                return @$jobCable->jobCableFrom->jobLocation->location_name;
            })
            ->addColumn('size', function($jobCable) {
                return  @$jobCable->size;
            })
            ->addColumn('cores', function($jobCable) {
                $cableCores = @$jobCable->cableIdType->cores;
                if(@$jobCable->cableIdType->no_of_pair_triple_quad > 1) {
                    $cableCores = @$jobCable->cableIdType->cores . "x" . @$jobCable->cableIdType->no_of_pair_triple_quad; 
                }
                return $cableCores;
            })
            ->addColumn('installedStatus', function($jobCable) {
                return  @$jobCable->areaOfWorkDetails->installed;
            })
            ->addColumn('checklistStatus', function($jobCable) {
                return  @$jobCable->areaOfWorkDetails->checklist;
            })
            ->addColumn('testResultStatus', function($jobCable) {
                return  @$jobCable->areaOfWorkDetails->test_result;
            })
            ->make(true);
        }

        return view('jobs.area-of-work.index');
    }

    /** change status on job_area_of_work_details**/
    public function changeStatusAreaOfWork(Request $request)
    {
        $user_by = \Auth::user()->id;
        $user_at = Carbon::now();

        if($request->data_type == 'installed') {
           $update = ['installed' => $request->status,'installed_by' =>$user_by,'installed_at'=>$user_at];

           $insert = ['job_cable_id'=>$request->job_cable_id,'installed' => $request->status,'installed_by' =>$user_by,'installed_at'=>$user_at];

           $installedStatus = $request->status;

           if($installedStatus == 0) {
                $update += ['checklist' => 0, 'test_result' => 0, 'checklist_at' => $user_at, 'test_result_at' => $user_at,'checklist_by' =>$user_by, 'test_result_by' => $user_by];
           }
        }
        elseif($request->data_type == 'checklist') {
           $update = ['checklist' => $request->status,'checklist_by' =>$user_by,'checklist_at'=>$user_at];

           $insert = ['job_cable_id'=>$request->job_cable_id,'checklist' => $request->status,'checklist_by' =>$user_by,'checklist_at'=>$user_at];

        }
        elseif($request->data_type == 'test_result') {
           $update = ['test_result' => $request->status,'test_result_by' =>$user_by,'test_result_at'=>$user_at];

           $insert = ['job_cable_id'=>$request->job_cable_id,'test_result' => $request->status,'test_result_by' =>$user_by,'test_result_at'=>$user_at];

        }      

        $jobArea = JobAreaOfWorkDetail::where('job_cable_id',$request->job_cable_id)->first();

        try {
            \DB::beginTransaction();

            if(isset($jobArea) && !empty($jobArea) && $jobArea->count() > 0 ) {             
                $res = JobAreaOfWorkDetail::where('job_cable_id',$request->job_cable_id)->update($update);               
            } else {
                $res = JobAreaOfWorkDetail::insert($insert);
            }
          
            $jobOfArea = JobAreaOfWorkDetail::where('job_cable_id',$request->job_cable_id)->first();


            \DB::commit();

            $message = 'Error in changing status, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Status changed successfully.';
                $result  = ['status' => 1, 'message' => $message, 'data' =>$jobOfArea];
            }            
            return response()->json($result);
        } catch(\Exception $e) {
            $message = $e->getMessage().' - Internal Server Error';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    /** add  test results
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     /** add  test results**/
    public function addTestResults(Request $request, $jobId = null)
    {
       $authUser = \Auth::user();
       $job = Job::with(['jobUsers'])->find($jobId);
       $jobCableOptions = JobCable::where('job_id', $jobId)->get()->toArray();      
       return view('jobs.edit',compact('jobCableOptions','job'));
    }

    /**
     * get the termination detail details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getTestResults(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            $jobId = $request->job_id;
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job id not found.'];
                return response()->json($result);
            }

            $job = Job::find($jobId);
            

            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job not found.'];
                return response()->json($result);
            }

            $whereArr = [
                'job_id'       => $jobId, 
                'job_cable_id'     => $request->job_cable_id,
            ];

            $jobTestResult = JobTestResultDetail::where($whereArr)->get();

            if(!empty($jobTestResult)) {
                $data['jobTestResult'] = $jobTestResult;
            }

            $getTestParameter = TestParameter::where('client_id', $authUser->client_id)->get()->toArray();

            $data['getTestParameter'] = $getTestParameter;

            $viewName = "jobs.test-results.test_parameter";
            if(isset($request->is_editable) && $request->is_editable == 0) {    
                $viewName = "jobs.test-results.view_test_parameter";
            }
            $content = view($viewName, $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content)];
            return Response::json($result);
        }
    }

    /**
     * Remove upload files etc.
     *
     * @param  array fileArray []
     * unlink file from server
    */
    public function removeFiles($fileArr = [])
    {
        foreach ($fileArr as $key => $file) {
            if(file_exists($file)) {
                unlink($file);
            }
        }
    }//end of function

    /**
     * Store a test result resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveTestResults(Request $request, $jobId = null) 
    {
        $inputs   = $request->except('_token'); 

        $authUser = \Auth::user();       

        $validator = (new JobTestResultDetail)->validateJobTestResult($inputs);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }
      
        $jobCableId = $request->job_cable_id;

        if(!$jobId) {
                return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }

        try{
            \DB::beginTransaction();

            /*insert data into Job test result Table*/
            if(!empty($request->test_parameter_id ) && count($request->test_parameter_id ) > 0) {

                foreach ($request->test_parameter_id  as $key => $testParameterId) {
                            
                    if($testParameterId != '') {

                        $testResult = new JobTestResultDetail;
                     
                        if(isset($request['test_result_id'][$key]) && !empty($request['test_result_id'][$key])) {
                            $testResultId  = $request['test_result_id'][$key];
                            $testResult = JobTestResultDetail::find($testResultId);
                        }

                        $testResult->job_id = $jobId;
                        $testResult->job_cable_id = $jobCableId;
                        $testResult->test_parameter_id = $testParameterId;
                        $testResult->output = $request['output'][$key];

                        $testResult->save();
                    }
                }
            }

            \DB::commit();
            return redirect()->back()->with('success','Final Test details saved successfully');
        }
        catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }     

    /**
     * Store a test result resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveReportIssue(Request $request, $jobId = null) 
    {
        $inputs   = $request->except('_token');

        $authUser = \Auth::user();

        $validator = (new ReportedIssue)->validateReportIssue($inputs);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        if(!$jobId) {
            return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }

       try{
            \DB::beginTransaction();
           
            /*insert data into Reported Issues Table*/
            $add = new ReportedIssue();
            $add->client_id = $authUser->client_id;
            $add->job_id = $jobId;
            $add->location_id = $request->location_id;
            $add->priority = $request->priority;
            $add->description =  $request->description;
            $add->created_by =  $authUser->id;
            $add->created_date =  Carbon::now();
            $add->status =  1;
            $add->save();
            
            \DB::commit();
            return redirect()->back()->with('success','Report an Issue saved successfully');
        } 
        catch(\Exception $e) {
            \DB::rollBack();
            $message = $e->getMessage().' - Internal Server Error';
            return redirect()->back()->withError($message)->withInput($inputs);

        }
    }

    /**
     * get the Report Issues.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getReportIssue(Request $request, $job_id = null)
    {
        $authUser = \Auth::user();

        if ($request->ajax()) {

            $getReportIssue = ReportedIssue::where('client_id', $authUser->client_id)->where('job_id', $job_id)->orderBy('id', 'DESC')->get();

            return Datatables::of($getReportIssue)
            ->addIndexColumn()
            ->addColumn('description', function($getReportIssue) {
                return @$getReportIssue->description;
            })
            ->addColumn('priority', function($getReportIssue) {
                if($getReportIssue->priority == 0){
                    $priority = 'Low';
                }
                elseif ($getReportIssue->priority == 1) {
                    $priority = 'Normal';
                }
                elseif ($getReportIssue->priority == 2) {
                    $priority = 'Medium';
                }
                else{
                    $priority = 'High';
                }
                return @$priority;
            }) 
            ->addColumn('status', function($getReportIssue){
                return  $getReportIssue->status;
            })          
            ->addColumn('comment', function($row) {
                $btn = '<a href="javascript:void(0);" data-url= "'.route('report-issue.get_comment', $row->id).'" data-id ="'.$row->id.'"  data-toggle="modal" class="icons report-issue-comment" title="View" data-target="#report-issue-comment"><i class="fa fa-comment"></i></a>';
                return $btn;
            }) 
            ->rawColumns(['comment'])
            ->make(true);
        }
        return view('jobs.report-issue.list');    
    }  

    /**
     * get comment on the Reported Issues.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCommentReportedIssue(Request $request)
    {
        if ($request->ajax()) {

            $reportIssueId = $request->report_issue_id;

            if(!$reportIssueId) {
                $result  = ['status' => 0, 'message' => 'Reported issue id not found.'];
                return response()->json($result);
            }

            $reportedIssue = ReportedIssue::find($reportIssueId);

            $reportedIssueComment = ReportedIssueDetail::where('reported_issue_id',$reportIssueId)->orderBy('id')->get(); 

            if(!$reportedIssue) {
                $result  = ['status' => 0, 'message' => 'Reported issue not found.'];
                return response()->json($result);
            }

            $data['reportedIssue'] = $reportedIssue;

            $data['reportedIssueComment'] = $reportedIssueComment;

            $data['isEditable'] = 1;
            if(isset($request->is_editable) && $request->is_editable == 0) {
                $data['isEditable'] = 0;
            }

            $content = view('jobs.reported-issue.add_comment', $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content), 'page' => $request->page];
            return Response::json($result);
        }
    }

    /**
     * save comment on  the Reported Issues.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveCommentReportedIssue(Request $request)
    {
     
        if ($request->ajax()) {

           $authUser = \Auth::user();

           $reportedIssueId = $request->reported_issue_id;
          
            if(!$reportedIssueId) {
                $result  = ['status' => 0, 'message' => 'Reported issue id not found.'];
                return response()->json($result);
            }
            
            /*insert data into  reported issue details Table*/
            $add = new ReportedIssueDetail();
            $add->reported_issue_id = $reportedIssueId;
            $add->user_id = $authUser->id;
            $add->comments = $request->comment_val;
            $add->status =  1;
            $add->save();

            $result  = ['status' => 1, 'comment' => $add];
            
            return response()->json($result);
        }

        else {
            return redirect()->back()->with('error', 'Request not allowed.');
        } 
    }


    /** change Report Status on list client**/
    public function changeReportStatus(Request $request)
    {
        $data = ReportedIssue::find($request->report_issue_id);
        if(!$data) {
            $message = 'Reported issue not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $data->status = $request->status;
            $res = $data->save();

            \DB::commit();

            $message = 'Error in changing status, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Reported issue status changed successfully.';
                $result  = ['status' => 1, 'message' => $message];
            }            
            return response()->json($result);
        } catch(\Exception $e) {
            $message = $e->getMessage().' - Internal Server Error';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    /**
     * download the asset from job.
     *
     * @param $file path to the file
     * @param $fileName incase want to rename it.
     */
    public function downloadJobAsset($encodedFilePath, $fileName = null) 
    {
        // $file= public_path(). "/uploads/document/". $document;
        $file = null;
        try {
            $file = base64_decode($encodedFilePath);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
        
        $fileExtension = \File::extension($file);

        $headers = [
            'Content-Type: application/'.$fileExtension
        ];

        $filePath = public_path($file);

        if(file_exists($filePath)) {
            return Response::download($filePath);
        } else {
            return redirect()->back()->with('error', 'Document Not Exist!');
        }
    }

    /**
     * get the Job Locations.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getJobLocation(Request $request)
    {
        $user = \Auth::user();

        if ($request->ajax()) {

            $seach = trim($request->term);

            $list  = JobLocation::where('client_id', $user->client_id)
                    ->where('location_name', 'LIKE', '%'. $seach . '%')
                    ->pluck('location_name', 'id')->toArray();

            $locations = [];
            if(!empty($list)) {
                foreach ($list as $locatioinId => $locationName) {
                    $locations[] = [
                        'id' => $locatioinId,
                        'label' => $locationName,
                        'value' => $locationName,
                    ];
                }
            }
            $result  = $locations;
            return Response::json($result);
        } else {
            return redirect()->back()->withError('Method not allowed');
        }
    }

    /**
     * get the Job Checklist Details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getJobChecklistDetails(Request $request)
    {
        if ($request->ajax()) {

            $user = \Auth::user();

            $jobId    = $request->job_id;
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job id not found.'];
                return response()->json($result);
            }

            $job = Job::find($jobId);
            if(!$jobId) {
                $result  = ['status' => 0, 'message' => 'job not found.'];
                return response()->json($result);
            }

            $data['job'] = $job;
            $whereArr = [
                'job_id'        => $jobId, 
                'job_cable_id'  => $request->cable_id,
            ];
            $jobChecklistDetails = JobChecklistDetail::where($whereArr)->get();
            if(!empty($jobChecklistDetails)) {
                $data['jobChecklistDetails'] = $jobChecklistDetails;
            }

            $whereArr = [
                'client_id' => $user->client_id,
                'user_id'   => $user->id,
            ];

            $checklistMaster = ChecklistMaster::where($whereArr)->get();

            $data['checklistMaster'] = $checklistMaster; 

            $viewName = "jobs.checklist.add_checklist_detail";
            if(isset($request->is_editable) && $request->is_editable == 0) {    
                $viewName = "jobs.checklist.view_checklist_detail";
            }

            $content = view($viewName, $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content' => $content)];
            return Response::json($result);
        } else {
            return redirect()->back()->withError('Method not allowed');
        }
    }

    /**
     * add the Job Checklist Details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveJobChecklist(Request $request, $jobId = null)
    {
        $inputs   = $request->except('_token');

        if(!$jobId) {
            return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }

        $job = Job::find($jobId);
        if(!$jobId) {
            return redirect()->back()->withError('Job not found.')->withInput($inputs);
        }

        $clientId = \Auth::user()->client_id;

        $validator = (new JobChecklistDetail)->validateJobChecklistDetail($inputs);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            if(!empty($request->checklist)) {
                $checklistArr = $request->checklist;

                foreach ($checklistArr as $key => $listArr) {

                    $checkList = new JobChecklistDetail;
                    if(isset($listArr['id']) && !empty($listArr['id'])) {
                        $checkList = JobChecklistDetail::find($listArr['id']);
                    }

                    $checkList->job_id       = $jobId;
                    $checkList->job_cable_id = $request->cable;

                    $checkList->checklist_master_id = $listArr['checklist_master_id'];
                    $checkList->name = $listArr['name'];
                    $checkList->submit_date = $listArr['date'];

                    $checkList->save();
                }
            }

            \DB::commit();
            return redirect()->back()->with('success','Job checklist saved successfully.'); 
        } catch (\PDOException $e) {
            \DB::rollBack();

            $message = 'Database Error: '.$e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        } catch(\Exception $e) {
            \DB::rollBack();

            $message = 'Internal Server Error - '.$e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    public function closeJob(Request $request, $jobId = null)
    {
        $inputs   = $request->except('_token');

        if(!$jobId) {
            return redirect()->back()->withError('Job id not found.')->withInput($inputs);
        }

        $job = Job::find($jobId);
        if(!$jobId) {
            return redirect()->back()->withError('Job not found.')->withInput($inputs);
        }

        $user = \Auth::user();

        $validatedData = $request->validate([
            'checklist'           => 'required',
            'final_check_sheet'   => 'required',
            'termination_details' => 'required',
        ]);

        try {
            \DB::beginTransaction();

            $customMessage=", Please fill all the required information to close this job";

            if($job->jobCables->count() == 0) {
                $message = 'Job cable details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }
            if($job->jobSingleLineDrawings->count() == 0) {
                $message = 'Job single line drawings are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }
            if($job->jobSchematicLineDrawings->count() == 0) {
                $message = 'Job schematic drawings are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }
            if($job->jobLocationLineDrawings->count() == 0) {
                $message = 'Job location drawings are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $joCableIds = $job->jobCables->pluck('id')->toArray();
            $jobTermination = JobTermination::whereIn('cable_id', $joCableIds)->count();

            if($jobTermination == 0) {
                $message = 'Job termination details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $jobAreaOfWorkDetail = JobAreaOfWorkDetail::whereIn('job_cable_id', $joCableIds)->count();

            if($jobAreaOfWorkDetail == 0) {
                $message = 'Job area of work details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $jobChecklistDetail = JobChecklistDetail::whereIn('job_cable_id', $joCableIds)->count();

            if($jobChecklistDetail == 0) {
                $message = 'Job checklist details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $jobFinalCheckSheet = JobFinalCheckSheet::whereIn('cable_id', $joCableIds)->count();

            if($jobFinalCheckSheet == 0) {
                $message = 'Job final check sheet details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $jobTestResultDetail = JobTestResultDetail::whereIn('job_cable_id', $joCableIds)->count();

            if($jobTestResultDetail == 0) {
                $message = 'Job test results details are missing'.$customMessage;
                return redirect()->back()->withError($message);
            }

            $job->status    = 2;
            $job->closed_by = $user->id;
            $job->closed_at = date('Y-m-d H:i');

            $job->save();

            \DB::commit();
            return redirect()->route('jobs.index')->with('success', 'Job closed successfully.'); 
        } catch (\PDOException $e) {
            \DB::rollBack();

            $message = 'Database Error: '.$e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        } catch(\Exception $e) {
            \DB::rollBack();

            $message = 'Internal Server Error - '.$e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }
}