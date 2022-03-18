<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    protected $current_user;

    public function __construct()
    {
        $this->current_user = auth('api')->id();
    }

    public function single_employe(Request $request,$employe_id){

        if(is_numeric($employe_id)){
            $employe = Employee::find($employe_id);
            return $employe;
        }

        return response("Any company with given id",404);
    }

    public function admin_index(Request $request){
        $companies = Employee::paginate(30);

        return $companies;

    }

    public function index(Request $request){

        $company = Company::where("user_id",$this->current_user)->firstOrFail();

        $companies = Employee::where("company_id",$company->id)->paginate(30);

        return $companies;

    }

    public function show(Request $request,$employeeId){
        $company = Company::where("user_id",$this->current_user)->firstOrFail();
        $employee = Employee::find($employeeId);

        if($company->id != $employee->company_id){
            return response(['message'=>'You dont have permission to perform this action'],403);
        }

        if(is_null($employee)){
            return response(['message'=>'no employee with given id'],404);
        }else{
            return response($employee,200);
        }
    }

    public function store(Request $request){
        $company = Company::where("user_id",$this->current_user)->firstOrFail();

        $validated=Validator::make($request->all(),[
            'name'=>'required|string',
            'passport'=>'required|regex:/^[a-zA-Z]{2}-?[0-9]{7}$/',
            'surname'=>'required|string',
            'middlename'=>'required|string',
            'job_title'=>'required|string',
            'phone_number'=>'required|regex:/^\+998-?[0-9]{9}$/',
            'address'=>'required|string',
        ]);

        if($validated->fails()){
            return response([
                "message"=>"cannot create new employee, validation failed",
            ],400);
        }
        $employee=new Employee([
            'name'=>$request->input('name'),
            'passport'=>$request->input('passport'),
            'surname'=>$request->input('surname'),
            'middlename'=>$request->input('middlename'),
            'job_title'=>$request->input('job_title'),
            'phone_number'=>$request->input('phone_number'),
            'address'=>$request->input('address'),
            'company_id'=>$company->id
        ]);

        //checks proccess of storing to the database
        if($employee->save()){
            return response(["message"=>"success"],200);
        }else{
            return response(["message"=>"cannot save data to the database"],500);
        }

    }

    //destroy employee by id
    public function destroy(Request $request,$employeeId){

        $company = Company::where("user_id",$this->current_user)->firstOrFail();
        $employee = Employee::find($employeeId);

        if($company->id != $employee->company_id){
            return response(['message'=>'You dont have permission to perform this action'],403);
        }


        try {
            $success_state = Employee::destroy($employeeId);
        } catch (QueryException $error) {
            return response(["message"=>$error->errorInfo[0],"code"=>$error->errorInfo[1]],500);
        }

        if ($success_state) {
            return response(["message"=>"record succesfully deleted","code"=>$success_state],200);
        }else{
            return response(["message"=>"error while deleting record","code"=>$success_state],500);
        }
    }


    public function update(Request $request,$employeeId){
        // $this->authorize('update');

        $company = Company::where("user_id",$this->current_user)->firstOrFail();
        $employee = Employee::find($employeeId);

        if($company->id != $employee->company_id){
            return response(['message'=>'You dont have permission to perform this action'],403);
        }


        $validation=Validator::make($request->all(),[
            'name'=>'string|nullable',
            'passport'=>'nullable|regex:/^[a-zA-Z]{2}-?[0-9]{7}$/',
            'surname'=>'string|nullable',
            'middlename'=>'string|nullable',
            'job_title'=>'string|nullable',
            'phone_number'=>'nullable|regex:/^\+998-?[0-9]{9}$/',
            'address'=>'string|nullable'
        ]);
        if($validation->fails()){
            return response(['message'=>'values not satisfies the needs'],400);
        }

        $employee = Employee::find($employeeId);

        $employee->name = $request->input('name');
        $employee->passport=$request->input('passport');
        $employee->surname=$request->input('surname');
        $employee->middlename=$request->input('middlename');
        $employee->job_title=$request->input('job_title');
        $employee->phone_number=$request->input('phone_number');
        $employee->address=$request->input('address');


        if($employee->save()){
            return response(['message'=>'record successfuly updated'],200);
        }else{
            return response(['message'=>'unable to update record'],400);
        }

    }
}


// "name":"B3",
// "passport":"AA4444444",
// "surname":"R",
// "middlename":"T",
// "job_title":"DEV",
// "phone_number":"+9988888",
// "address":"SOMEWHERE",
