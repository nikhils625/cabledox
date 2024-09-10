<?php

namespace App\Http\Controllers;

use App\Models\CableMaster;
use App\Models\CableType;
use App\Models\CableMasterCoreDetails;
use Illuminate\Http\Request;
use DataTables;

class CableMasterController extends Controller
{
    /** 
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    { 
        if(!auth()->user()->can('cable-master.list')) {
            abort(404);
        }

        $user = \Auth::user();

        if ($request->ajax()) {
            $cableMasters = CableMaster::with(['cableType'])->where('client_id', $user->client_id)->orderBy('id', 'DESC')->get();

            return Datatables::of($cableMasters)
            ->addIndexColumn()
            ->addColumn('cable_type', function($cableMasters){
                /*return $cableMasters->cableType->cable_name;*/
                return $cableMasters->cable_type_id;
            })
            ->addColumn('cores', function($cableMasters) {
                $cores = null;
                if($cableMasters->no_of_pair_triple_quad > 1) {
                    $cores = $cableMasters->cores . ' * ' . $cableMasters->no_of_pair_triple_quad;
                } else {
                    $cores = $cableMasters->cores;
                }
                return $cores;
            })
            ->addColumn('status', function($cableMasters){
                return  $cableMasters->status;
            })
            ->addColumn('action', function($row){
                $btn = '<a href="'.route('cable-masters.show', $row->id).'" data-toggle="tooltip" class="icons" title="View"><i class="fa fa-eye"></i></a> &nbsp; <a href="'.route('cable-masters.edit', $row->id).'" data-toggle="tooltip" class="icons" title="Edit"><i class="fa fa-edit"></i></a> &nbsp; <a href="javascript:void(0);" class="delete icons" data-route="'. route('cable-masters.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('cable-masters.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('cable-master.create')) {
            abort(404);
        }

        $cableTypes = CableType::pluck('cable_name', 'id')->toArray();
        return view('cable-masters.add', compact('cableTypes'));
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
        $validator = (new CableMaster)->validateCableMaster($inputs);
        
        if ($validator->fails()) {
            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            $cableMaster = new CableMaster;
            $cableMaster->user_id                   = \Auth::user()->id;
            $cableMaster->client_id                 = \Auth::user()->client_id;
            $cableMaster->cable_type_id             = $request->cable_type_id;
            $cableMaster->cores                     = $request->cores;
            /*$cableMaster->cable_is_pair_triple_quad = $request->cable_is_pair_triple_quad;*/
            $cableMaster->no_of_pair_triple_quad    = $request->no_of_pair_triple_quad ?? 1;
            $cableMaster->status                    = 1;
            $cableMaster->save();

            if(!empty($request->core_name) && count($request->core_name) > 0) {
                $coreName = array_filter($request->core_name);

                if(!empty($coreName) && count($coreName) > 0) {
                    $coreDetails = [];
                    foreach ($coreName as $key => $names) {
                        foreach ($names as $k => $name) {
                            $coreDetails[] = [
                                'core_name'  => $name,
                                'core_index' => $key,
                                'wire_index' => $k,
                            ];
                        }
                    }
                    $cableMaster->cableMasterCoreDetails()->createMany($coreDetails);
                }
            }

            \DB::commit();
            
            return redirect()->back()->with('success', 'Cable Master saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CableMaster  $cableMaster
     * @return \Illuminate\Http\Response
     */
    public function show(CableMaster $cableMaster)
    {
        if(!auth()->user()->can('cable-master.view')) {
            abort(404);
        }

        if(!$cableMaster) {
            abort(404);
        }
        
        $cableTypes = CableType::pluck('cable_name', 'id')->toArray();
        return view('cable-masters.show', compact('cableMaster', 'cableTypes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CableMaster  $cableMaster
     * @return \Illuminate\Http\Response
     */
    public function edit(CableMaster $cableMaster)
    {
        if(!auth()->user()->can('cable-master.edit')) {
            abort(404);
        }

        if(!$cableMaster) {
            abort(404);
        }
        
        $cableTypes = CableType::pluck('cable_name', 'id')->toArray();
        return view('cable-masters.edit', compact('cableMaster', 'cableTypes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CableMaster  $cableMaster
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CableMaster $cableMaster)
    {
        $inputs = $request->except('_token', '_method');

        if(!$cableMaster) {
            abort(404);
        }

        $validator = (new CableMaster)->validateCableMaster($inputs, $cableMaster->id);
        if ($validator->fails()) {

            return redirect()->back()
                   ->withErrors($validator)
                   ->withInput($inputs);
        }

        try {
            \DB::beginTransaction();

            // $cableMaster
            $cableMaster->cable_type_id             = $request->cable_type_id;
            $cableMaster->cores                     = $request->cores;
            /*$cableMaster->cable_is_pair_triple_quad = $request->cable_is_pair_triple_quad;*/
            $cableMaster->no_of_pair_triple_quad    = $request->no_of_pair_triple_quad??1;
            $cableMaster->status                    = 1;
            $cableMaster->save();



            if(!empty($request->core_name) && count($request->core_name) > 0) {
                $coreName = array_filter($request->core_name);

                if(!empty($coreName) && count($coreName) > 0) {
                    $coreDetails = [];
                    foreach ($coreName as $key => $names) {
                        foreach ($names as $k => $name) {
                            $coreDetails[] = [
                                'core_name'  => $name,
                                'core_index' => $key,
                                'wire_index' => $k,
                            ];
                        }
                    }

                    if(!$cableMaster->cableMasterCoreDetails->isEmpty()) {
                        $cableMaster->cableMasterCoreDetails()->delete();
                    }

                    $cableMaster->cableMasterCoreDetails()->createMany($coreDetails);
                }
            }

            \DB::commit();
            
            return redirect()->back()->with('success', 'Cable Master saved successfully.');
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = null)
    {
        if(!auth()->user()->can('cable-master.delete')) {
            $message = 'Request not allowed.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        $cableMaster = CableMaster::find($request->id);
        if(!$cableMaster) {
            $message = 'Cable Master not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $cableMaster->delete();

            \DB::commit();

            $message = 'Error in deleting user, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Cable Master deleted successfully';
                $result  = ['status' => 1, 'message' => $message];
            }
            
            return response()->json($result);
        } catch (\PDOException $e) {
            \DB::rollBack();

            $message = 'Database Error:' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        } catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     *
    ** change active/inactive status on list client**/
    public function changeStatus(Request $request)
    {
        $cableMaster = CableMaster::find($request->id);

        if(!$cableMaster) {
            $message = 'Cable Master not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $cableMaster->status = $request->status;
            $res = $cableMaster->save();

            \DB::commit();

            $message = 'Error in changing status, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Cable Master status changed successfully.';
                $result  = ['status' => 1, 'message' => $message];
            }            
            return response()->json($result);
        } catch(\Exception $e) {
            $message = 'Internal Server Error - ' . $e->getMessage();
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }
}