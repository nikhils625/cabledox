<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Client;
use DataTables;
use Mail; 
use Illuminate\Support\Facades\Password;
use Spatie\Permission\Models\Role;
 

class ClientController extends Controller
{
    /** 
     * Display a listing of the all clients.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!auth()->user()->can('clients.list')) {
            abort(404);
        }

        if ($request->ajax()){
            $data = Client::with(['users']);
            
            if($request->status == '0' || $request->status == '1'){
                $data->where('status',$request->status);
            }
            $data = $data->orderBy('id', 'DESC')->get();

            return Datatables::of($data)
            ->addIndexColumn()
            ->addColumn('contactPersonName', function($data){
                return $data->users->first_name .' '.$data->users->last_name;
            })
            ->addColumn('contactPersonEmail', function($data){
                return $data->users->email;
            })
            ->addColumn('contactPersonPhone', function($data){
                return $data->users->phone;
            })
            ->addColumn('status', function($data){
                return  $data->status;
            })
          
            ->addColumn('action', function($row) {
                $btn = '';
                if(auth()->user()->can('clients.view')) {
                    $btn .= '<a href="'.route('clients.show',$row->id).'" data-toggle="tooltip" title="View" class="icons"><i class="fa fa-eye"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('clients.edit')) {
                    $btn .= '<a href="'.route('clients.edit',$row->id).'" data-toggle="tooltip" title="Edit" class="icons"><i class="fa fa-edit"></i></a> &nbsp; ';
                }
                if(auth()->user()->can('clients.delete')) {
                    $btn .= '<a href="javascript:void(0);" class="delete icons" data-route="'. route('clients.destroy', $row->id).'" data-toggle="tooltip" title="Delete"><i class="fa fa-trash"> </i></a>';
                }
                return $btn;
            })
                      
            ->rawColumns(['action'])
            ->make(true);
        }       
        return view('clients.index');
    } 

    /**
     * Show the form for creating a new client.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!auth()->user()->can('clients.create')) {
            abort(404);
        }

        return view('clients.add');
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
        $validator = (new Client)->validateClients($inputs);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }
        try{
            \DB::beginTransaction();
            /*get first name & last name of Contact person */
            $contact_person_name = $request->contact_person_name;
            $user_name = explode(" " ,$contact_person_name);
            $user_first_name = current($user_name);
            if(count($user_name)  > 1){
                $user_last_name = end($user_name);
            }
            else{
                $user_last_name = ' ';
            }
            /*company logo*/
            if($request->hasFile('company_logo')){
                $dir = public_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR .   'company_logo' . DIRECTORY_SEPARATOR;
                $fileOzName = str_replace(' ', '', $request->file('company_logo')->            getClientOriginalName());
                $fileOzExtension = $request->file('company_logo')->getClientOriginalExtension();
                $fileName=time().'_'.pathinfo(strtolower($fileOzName), PATHINFO_FILENAME).'.'.$fileOzExtension;
                $request->file('company_logo')->move($dir, $fileName);
            }
            /*insert data into Client Table*/
            $add_client = new Client();
            $add_client->user_id = \Auth::user()->id;
            $add_client->company_name = $request->company_name;
            $add_client->company_email = $request->company_email;
            $add_client->company_phone = $request->company_phone;
            $add_client->company_logo =  $fileName;
            $add_client->no_of_jobs_allocated =  $request->no_of_jobs_allocated;
            $add_client->status =  0;
            $add_client->save();

            /*insert data into User Table*/
            $add_user = new User();
            $add_user->client_id = $add_client->id; 
            $add_user->first_name = $user_first_name;
            $add_user->last_name =  $user_last_name;
            $add_user->email = $request->email; 
            $add_user->phone = $request->phone;
            $add_user->role_id = 2;
            $add_user->status = 0;
            $add_user->created_by = \Auth::user()->id;
            $add_user->save();

            $add_user->syncRoles(2);

            /*send email to company*/
            \Mail::send('emails/sendMailToCompany', array(                                
                'contact_person_name' => $request->contact_person_name,
                'company_name' => $request->company_name, 
            ), function($message) use ($request){   
                $message->to($request->company_email);
                $message->from(env('MAIL_FROM_ADDRESS'),
                    env('MAIL_FROM_NAME'))
                ->subject("Company Register");
            });
            /*send email to contact person*/
            $user =  $add_user;
            $contact_person_email = $add_user->email;
            $token = Password::getRepository()->create($user);
            $reset_password_link = url('/password/reset/'.$token.'?email='.$contact_person_email.'');          
            \Mail::send('emails/sendMailToContactPerson', array(                     
                'contact_person_name' => $request->contact_person_name,
                'contact_person_email' => $request->email,
                'company_name' => $request->company_name,
                'reset_password_link' =>  $reset_password_link,

            ), function($message) use ($request){   
                $message->to($request->email);
                $message->from(env('MAIL_FROM_ADDRESS'),
                    env('MAIL_FROM_NAME'))
                ->subject("Reset Password");
            });
            
            \DB::commit();
            return redirect()->back()->with('success','Client details saved successfully');
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
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        if(!auth()->user()->can('clients.view')) {
            abort(404);
        }

        $data = Client::with(['users'])->find($id);
        return view('clients.view',compact('data')); 
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!auth()->user()->can('clients.edit')) {
            abort(404);
        }

        $data = Client::with(['users'])->find($id);
        return view('clients.edit',compact('data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inputs = $request->except('_token');
        $validator = (new Client)->validateClients($inputs, $id);
        if ($validator->fails()) {
            return redirect()->back()
            ->withErrors($validator)
            ->withInput($inputs);
        }
        try{
            \DB::beginTransaction();
            /*get first name & last name of Contact person */
            $contact_person_name = $request->contact_person_name;
            $user_name = explode(" " ,$contact_person_name);
            $user_first_name = current($user_name);
            if(count($user_name)  > 1){
                $user_last_name = end($user_name);
            }
            else{
                $user_last_name = ' ';
            }
            /*get details of clients*/
            $get_company_detail = Client::find($id);
            $fileName = $request->company_old_logo;
            /*company logo*/
            if($request->hasFile('company_logo')){
                $dir = public_path() . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR .   'company_logo' . DIRECTORY_SEPARATOR;
                if(!empty($get_company_detail->company_logo) && file_exists($dir . $get_company_detail->company_logo)) {
                    unlink($dir . $get_company_detail->company_logo);
                }
                $fileOzName = str_replace(' ', '', $request->file('company_logo')->            getClientOriginalName());
                $fileOzExtension = $request->file('company_logo')->getClientOriginalExtension();
                $fileName=time().'_'.pathinfo(strtolower($fileOzName), PATHINFO_FILENAME).'.'.$fileOzExtension;
                $request->file('company_logo')->move($dir, $fileName);
            }
            /*client status*/
            if($request->status){
                $status = $request->status; 
            }
            else{
                $status = 0;
            }
            /*update data into Client Table*/
            Client::whereId($id)->update(['company_name' => $request->company_name,'company_email' =>$request->company_email,
                'company_phone' =>$request->company_phone,'no_of_jobs_allocated' =>$request->no_of_jobs_allocated,'company_logo' => $fileName,'status' => $status]);

            /*update data into User Table*/
            User::where('id',$request->user_id)->update(['first_name' => $user_first_name,'last_name' =>$user_last_name,
                'email' =>$request->email,'phone' =>$request->phone]); 

            \DB::commit();
            return redirect()->back()->with('success','Client details update successfully');
        } 
        catch(\Exception $e) {
            \DB::rollBack();
            $message = $e->getMessage().' - Internal Server Error';
            return redirect()->back()->withError($message)->withInput($inputs);

        }               
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->user()->can('clients.delete')) {
            abort(404);
        }

        $clients = Client::with(['users'])->find($id);
        if(!$clients) {
            $message = 'Client not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $res = $clients->delete();
            $clients->users->delete();

            \DB::commit();

            $message = 'Error in deleting client, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Client deleted successfully';
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

    /** change active/inactive status on list client**/
    public function changeStatus(Request $request)
    {             
        $client = Client::find($request->client_id);
        if(!$client) {
            $message = 'Client not found.';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }

        try {
            \DB::beginTransaction();

            $client->status = $request->status;
            $res = $client->save();

            \DB::commit();

            $message = 'Error in changing status, please try again later.';
            $result  = ['status' => 0, 'message' => $message];
            if($res) {
                $message = 'Client status changed successfully.';
                $result  = ['status' => 1, 'message' => $message];
            }            
            return response()->json($result);
        } catch(\Exception $e) {
            $message = $e->getMessage().' - Internal Server Error';
            $result  = ['status' => 0, 'message' => $message];
            return response()->json($result);
        }
    }
}
