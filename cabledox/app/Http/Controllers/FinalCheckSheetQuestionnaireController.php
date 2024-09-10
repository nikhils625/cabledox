<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FinalCheckSheetQuestionnaire;
use DataTables;

class FinalCheckSheetQuestionnaireController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('final-check-sheet.list')) {
            abort(404);
        }

        $user = \Auth::user();

        if ($request->ajax()){

            $data = FinalCheckSheetQuestionnaire::where('client_id', $user->client_id)->orderBy('id', 'DESC')->get(); 

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('action', function($row) {
                $btn = '';
                if(auth()->user()->can('final-check-sheet.view')) {
                    $btn .= '<a href="'.route('final-check-sheet.show',$row->id).'" data-toggle="tooltip" title="View" class="icons"><i class="fa fa-eye"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('final-check-sheet.edit')) {
                    $btn .= '<a href="'.route('final-check-sheet.edit',$row->id).'" data-toggle="tooltip" title="Edit" class="icons"><i class="fa fa-edit"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('final-check-sheet.delete')) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('final-check-sheet.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }       
        return view('final-check-sheet.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('final-check-sheet.create')) {
            abort(404);
        }

        return view('final-check-sheet.add');
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

        $validator = (new FinalCheckSheetQuestionnaire)->validateFinalCheckSheet($inputs);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }

        try{
            \DB::beginTransaction();

            /*Insert data into FinalCheckSheetQuestionnaire Table*/
            $add = new FinalCheckSheetQuestionnaire();
            $add->user_id = \Auth::user()->id;
            $add->client_id = \Auth::user()->client_id;
            $add->question_name = $request->question_name;
            $add->status =  1;
            $add->save();
               
            \DB::commit();
            return redirect()->back()->with('success','Final Check Sheet details saved successfully');
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
        if(!auth()->user()->can('final-check-sheet.view')) {
            abort(404);
        }

        $data = FinalCheckSheetQuestionnaire::find($id);
        return view('final-check-sheet.view',compact('data'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('final-check-sheet.edit')) {
            abort(404);
        }

        $data = FinalCheckSheetQuestionnaire::find($id);
        return view('final-check-sheet.edit',compact('data'));
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
        $validator = (new FinalCheckSheetQuestionnaire)->validateFinalCheckSheet($inputs);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }
        try{
            \DB::beginTransaction();

            /*update data into FinalCheckSheetQuestionnaire Table*/
            FinalCheckSheetQuestionnaire::whereId($id)->update(['question_name' => $request->question_name]);

            \DB::commit();
            return redirect()->back()->with('success','Final Check Sheet details update successfully');
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
        if(!auth()->user()->can('final-check-sheet.delete')) {
            abort(404);
        }

        $data = FinalCheckSheetQuestionnaire::find($id);
        if(!$data) {
            $message = 'Question not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $data->delete();
            

            \DB::commit();

            $message = 'Error in deleting question, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Question deleted successfully';
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
