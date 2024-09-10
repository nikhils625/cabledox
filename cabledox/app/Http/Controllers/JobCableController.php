<?php

namespace App\Http\Controllers;

use App\Models\JobCable;
use Illuminate\Http\Request;
use DataTables;
use Response;
use App\Models\CableType;
use App\Models\CableMaster;
use App\Models\JobLocation;
use App\Models\Job;

class JobCableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $jobId = null, $isEditable = false)
    {
        if(!auth()->user()->can('job-cables.list')) {
            abort(404);
        }

        $user = \Auth::user();

        if ($request->ajax()) {

            $jobCable = JobCable::with(['cableType', 'cableIdType', 'jobCableTo', 'jobCableFrom'])
            ->where('client_id', $user->client_id);

            if($jobId) {
                $jobCable->where('job_id', $jobId);
            }
            $jobCable = $jobCable->orderBy('id', 'DESC')->get();

            $isEditable = true;
            if(isset($request->is_editable) && $request->is_editable == 0) {
                $isEditable = false;
            }

            return Datatables::of($jobCable)
            ->addIndexColumn()
            ->addColumn('cable_id', function($jobCable) {
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
            ->addColumn('cable_type', function($jobCable) {
                return  @$jobCable->cableType->cable_name;
            })
            ->addColumn('cable_id_type', function($jobCable) {
                return  @$jobCable->cableIdType->cable_type_id;
            })
            ->addColumn('description', function($jobCable) {
                $description = @$jobCable->description;
                if(strlen($description) > 20) {
                    $description = substr(@$jobCable->description, 0, 15) . '....';
                }
                return $description;
            })
            ->addColumn('action', function($row) use($user, $isEditable) {

                $btn = '';
                if($user->can('job-cables.view')) {
                    $btn .= '<a href="javascript:void(0);" data-url= "'.route('job-cables.show', $row->id).'" data-id ="'.$row->id.'" data-toggle="modal" data-type="view" class="icons job-cables" title="View" data-target="#job-cables"><i class="fa fa-eye"></i></a> &nbsp; ';
                }

                if($user->can('job-cables.edit') && $isEditable) {
                    $btn .= '<a href="javascript:void(0);" data-url= "'.route('job-cables.edit', $row->id).'" data-type="edit" data-id ="'.$row->id.'" data-toggle="modal" class="icons job-cables" title="Edit" data-target="#job-cables"><i class="fa fa-edit"></i></a> &nbsp; ';
                }

                if($user->can('job-cables.delete') && $isEditable) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('job-cables.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }

                return $btn;
            }) 
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('jobs.job-cables.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if(!auth()->user()->can('job-cables.create')) {
            abort(404);
        }
        $authUser =\Auth::user();

        if ($request->ajax()) {

            $job = Job::find($request->job_id);
            if(!$job) {
                $result  = ['status' => 0, 'message' => 'Job not found', 'data' => array('content'=> '')];
                return Response::json($result);
            }

            $cableTypesId = CableType::pluck('cable_name', 'id')->toArray();
            $cableTypes   = CableMaster::where('client_id', $authUser->client_id)->pluck('cable_type_id', 'id')->toArray();

            $data['job'] = $job;
            $data['cableTypesId'] = $cableTypesId;
            $data['cableTypes']   = $cableTypes;

            $content = view('jobs.job-cables.add', $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content)];
            return Response::json($result);
        }
        /*return view('jobs.job-cables.add', compact('cableTypesId', 'cableTypes'));*/
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->except('_token');

        $clientId = \Auth::user()->client_id;

        $validator = (new JobCable)->validateJobCables($inputs, null, $clientId);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            /*$toLocation = JobLocation::find($request->to_location);*/
            $toLocation = JobLocation::where('location_name', $request->to)->first();
            if(!$toLocation) {
                $toLocation = new JobLocation;
                $toLocation->client_id = $clientId;
                $toLocation->created_by = \Auth::user()->id;
            }

            $toLocation->location_name = $request->to;
            $toLocation->save();

            /*$fromLocation = JobLocation::find($request->from_location);*/
            $fromLocation = JobLocation::where('location_name', $request->from)->first();
            if(!$fromLocation) {
                $fromLocation = new JobLocation;
                $fromLocation->client_id = $clientId;
                $fromLocation->created_by = \Auth::user()->id;
            }

            $fromLocation->location_name = $request->from;
            $fromLocation->save();

            $jobCable = new JobCable;
            $jobCable->user_id = \Auth::user()->id;
            $jobCable->client_id = $clientId;
            $jobCable->job_id    = $request->job_id;
            $jobCable->cable_type_id = $request->cable_id_type;
            $jobCable->custom_id = $request->custom_id;
            $jobCable->cable_id_type = $request->cable_type;
            // $jobCable->cores = $request->cores;
            $jobCable->size = $request->size;
            $jobCable->additional_information = $request->additional_information;
            $jobCable->cable_id = $request->cable_id;
            $jobCable->unique_code = $request->unique_code;
            /*$jobCable->to = $request->to;
            $jobCable->from = $request->from;*/
            $jobCable->description = $request->description;

            $jobCable->status =  1;
            $jobCable->save();

            if($jobCable) {
                $jobCableLocationData = [
                    [
                        'location_id' => $toLocation->id,
                        'location_type' => 0
                    ],
                    [
                        'location_id' => $fromLocation->id,
                        'location_type' => 1
                    ]
                ];
                $jobCable->jobCableLocations()->createMany($jobCableLocationData);
            }

            \DB::commit();
            return redirect()->back()->with('success', 'Job Cable added successfully');
        } catch (\PDOException $e) {
            \DB::rollBack();

            $message = 'Database Error: ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getLine();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JobCable  $jobCable
     * @return \Illuminate\Http\Response
     */
    public function show(JobCable $jobCable, Request $request)
    {
        if(!auth()->user()->can('job-cables.view')) {
            abort(404);
        }

        if(!$jobCable) {
            abort(404);
        }

        if ($request->ajax()) {

            $jobCablesId = $jobCable->id;

            if(!$jobCablesId) {
                $result  = ['status' => 0, 'message' => 'job cables id not found.'];
                return response()->json($result);
            }

            $jobCable = JobCable::find($jobCablesId);

            if(!$jobCable) {
                $result  = ['status' => 0, 'message' => 'job cables not found.'];
                return response()->json($result);
            }

            $data['jobCable'] = $jobCable;

            $content = view('jobs.job-cables.show', $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content)];
            return Response::json($result);
        }   
        //return view('jobs.job-cables.show', compact('jobCable'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JobCable  $jobCable
     * @return \Illuminate\Http\Response
     */
    public function edit(JobCable  $jobCable, Request $request)
    {
        if(!auth()->user()->can('job-cables.edit')) {
            abort(404);
        }

        if(!$jobCable) {
            abort(404);
        }

        $cableTypesId = CableType::pluck('cable_name', 'id')->toArray();
        $cableTypes   = CableMaster::pluck('cable_type_id', 'id')->toArray();

        if ($request->ajax()) {

            $jobCablesId = $jobCable->id;

            if(!$jobCablesId) {
                $result  = ['status' => 0, 'message' => 'job cables id not found.'];
                return response()->json($result);
            }

            $jobCable = JobCable::find($jobCablesId);

            if(!$jobCable) {
                $result  = ['status' => 0, 'message' => 'job cables not found.'];
                return response()->json($result);
            }

            $data['jobCable'] = $jobCable;

            $data['cableTypesId'] = $cableTypesId;

            $data['cableTypes'] = $cableTypes;

            $content = view('jobs.job-cables.edit', $data)->render();

            $result  = ['status' => 1, 'message' => '', 'data' => array('content'=> $content)];
            return Response::json($result);
        }   
        
        /*
        return view('jobs.job-cables.edit', compact('jobCable', 'cableTypesId', 'cableTypes'));*/
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobCable  $jobCable
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, JobCable $jobCable)
    {
        $inputs = $request->except('_token', '_method');

        $clientId = \Auth::user()->client_id;

        $validator = (new JobCable)->validateJobCables($inputs, $jobCable->id, $clientId);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput($inputs);
        }

        try {
            \DB::beginTransaction();


            /*$toLocation = JobLocation::find($request->to_location);*/
            $toLocation = JobLocation::where('location_name', $request->to)->first();
            if(!$toLocation) {
                $toLocation = new JobLocation;
                $toLocation->client_id = $clientId;
                $toLocation->created_by = \Auth::user()->id;
            }

            $toLocation->location_name = $request->to;
            $toLocation->save();

            /*$fromLocation = JobLocation::find($request->from_location);*/
            $fromLocation = JobLocation::where('location_name', $request->from)->first();
            if(!$fromLocation) {
                $fromLocation = new JobLocation;
                $fromLocation->client_id = $clientId;
                $fromLocation->created_by = \Auth::user()->id;
            }

            $fromLocation->location_name = $request->from;
            $fromLocation->save();

            // $jobCable->job_id    = $request->job_id;            
            $jobCable->cable_type_id = $request->cable_id_type;
            $jobCable->custom_id = $request->custom_id;
            $jobCable->cable_id_type = $request->cable_type;
            // $jobCable->cores = $request->cores;
            $jobCable->size = $request->size;
            $jobCable->additional_information = $request->additional_information;
            $jobCable->cable_id = $request->cable_id;
            $jobCable->unique_code = $request->unique_code;
            /*$jobCable->to = $request->to;
            $jobCable->from = $request->from;*/
            $jobCable->description = $request->description;

            $jobCable->status =  1;
            $jobCable->save();

            if($jobCable) {

                if(!$jobCable->jobCableLocations->isEmpty()) {
                    $jobCable->jobCableLocations()->delete();
                }

                $jobCableLocationData = [
                    [
                        'location_id'   => $toLocation->id,
                        'location_type' => 0
                    ],
                    [
                        'location_id'   => $fromLocation->id,
                        'location_type' => 1
                    ]
                ];

                $jobCable->jobCableLocations()->createMany($jobCableLocationData);
            }

            \DB::commit();
            
            return redirect()->back()->with('success', 'Job Cable added successfully');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JobCable  $jobCable
     * @return \Illuminate\Http\Response
     */
    public function destroy($id = null)
    {
        if(!auth()->user()->can('job-cables.delete')) {
            abort(404);
        }

        $jobCable  = JobCable::find($id);
        if(!$jobCable) {
            $message = 'Job Cable not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $jobCable->delete();

            \DB::commit();

            $message = 'Error in deleting job cable, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Job cable deleted successfully';
                $result  = ['status' => 1, 'message' => $message];
            }

            return response()->json($result);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    public function getAutoGeneratedCableId(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            $whereArr = [
                'client_id'     => $authUser->client_id,
                'cable_type_id' => $request->cable_type_id,
            ];

            $cableType = CableType::find($request->cable_type_id);

            $maxCableId = JobCable::where($whereArr)->max('cable_id');

            if(!$maxCableId) {
                $cableCode = $cableType->cable_identifier . '-001';
            } else {

                $maxCableIdLength  = strlen($maxCableId);
                $maxnCableIdNumber = substr($maxCableId, 2, $maxCableIdLength);

                $cableCode = generateAutoSerialNumber($cableType->cable_identifier, $maxnCableIdNumber);
            }

            $result  = ['status' => 1, 'cable_unique_code' => $cableCode];            
            return response()->json($result);
        } else {
            return redirect()->back()->with('error', 'Request not allowed.');
        }
    }

    /**
     * get the cable type details.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getCableTypeDetails(Request $request)
    {
        if ($request->ajax()) {
            $authUser = \Auth::user();

            $whereArr = [
                'client_id'     => $authUser->client_id
            ];

            $cableType = CableMaster::where($whereArr)->find($request->cable_id_type);
            if(!$cableType) {
                $result  = ['status' => 0, 'message' => 'cable not found.'];
                return response()->json($result);
            }

            $result  = ['status' => 1, 'cores' => $cableType->cores];
            return response()->json($result);
        } else {
            return redirect()->back()->with('error', 'Request not allowed.');
        }
    }
}