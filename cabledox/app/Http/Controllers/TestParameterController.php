<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TestParameter;
use DataTables;


class TestParameterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('test-parameters.list')) {
            abort(404);
        }

        $user = \Auth::user();

        if ($request->ajax()){

            $data = TestParameter::where('client_id', $user->client_id)->orderBy('id', 'DESC')->get();

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(auth()->user()->can('test-parameters.view')) {
                    $btn .= '<a href="'.route('test-parameters.show', $row->id).'" data-toggle="tooltip" title="View" class="icons"><i class="fa fa-eye"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('test-parameters.edit')) {
                    $btn .= '<a href="'.route('test-parameters.edit', $row->id).'" data-toggle="tooltip" title="Edit" class="icons"><i class="fa fa-edit"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('test-parameters.delete')) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('test-parameters.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }       
        return view('test-parameters.index'); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('test-parameters.create')) {
            abort(404);
        }

        return view('test-parameters.add');
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
      
        $validator = (new TestParameter)->validateTestParameter($inputs);

        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }
        try{
            \DB::beginTransaction();

            /*Insert data into TestParameter Table*/
            $add = new TestParameter();
            $add->user_id = \Auth::user()->id;
            $add->client_id = \Auth::user()->client_id;
            $add->parameter_name = $request->parameter_name;
            $add->status =  1;
            $add->save();
               
            \DB::commit();
            return redirect()->back()->with('success','Test Parameters details saved successfully');
        } 
        catch(\Exception $e) {
            \DB::rollBack();
            $message = 'Internal Server Error - ' . $e->getMessage();
            return redirect()->back()->withError($message)->withInput($inputs);

        }              
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if(!auth()->user()->can('test-parameters.view')) {
            abort(404);
        }

        $data = TestParameter::find($id);
        return view('test-parameters.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('test-parameters.edit')) {
            abort(404);
        }

        $data = TestParameter::find($id);
        return view('test-parameters.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_token');
        $validator = (new TestParameter)->validateTestParameter($inputs);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }
        try{
            \DB::beginTransaction();

            /*update data into TestParameter Table*/
            TestParameter::whereId($id)->update(['parameter_name' => $request->parameter_name]);

            \DB::commit();
            return redirect()->back()->with('success','Test Parameters details update successfully');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('test-parameters.delete')) {
            abort(404);
        }

        $data = TestParameter::find($id);
        if(!$data) {
            $message = 'Parameter not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $data->delete();
            

            \DB::commit();

            $message = 'Error in deleting Parameter, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Parameter deleted successfully';
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
}
