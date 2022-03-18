<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{

    public function single_company(Request $request,$company_id){
        $current_user = auth('api')->id();
        $company = Company::find($company_id);

        if(is_null($company)){
            return response(['message'=>'no company with given id'],404);
        }

        if($company->user_id != $current_user){
            return response(['message'=>'You dont have permission to perform this action'],403);
        }else{
            return response($company,200);
        }
    }


    public function index(Request $request){
        $companies = Company::paginate(30);

        return $companies;

    }

    public function destroy($company_id){
        try {
            $success_state = Company::destroy($company_id);
        } catch (QueryException $error) {
            return response(["message"=>$error->errorInfo[0],"code"=>$error->errorInfo[1]],500);
        }

        if ($success_state) {
            return response(["message"=>"record succesfully deleted","code"=>$success_state],200);
        }else{
            return response(["message"=>"error while deleting record","code"=>$success_state],500);
        }
    }

    public function edit($company_id){
        $current_user = auth('api')->id();

        if(is_numeric($company_id)){
            $company = Company::find($company_id);

            if(is_null($company)){
                return response(['message'=>'No company with given id'],404);
            }

            if($company->user_id != $current_user){
                return response(['message'=>'You dont have permission to perform this action'],403);
            }else{
                return response($company,200);
            }
        }

        return response(['message'=>"Any company with given id"],404);
    }

    public function update(Request $request,$company_id){
        $current_user = auth('api')->id();


        $validation=Validator::make($request->all(),[
            'name'=>'string|nullable',
            'owner'=>'string|nullable',
            'address'=>'string|nullable',
            'email'=>'email|nullable',
            'website'=>'url|nullable',
            'phone_number'=>'string|nullable',
        ]);
        if($validation->fails()){
            return response(['message'=>'values not satisfies the needs'],400);
        }

        $company = Company::find($company_id);

        //check permissions
        if($company->user_id != $current_user){
            return response(['message'=>'You dont have permission to perform this action'],403);
        }

        $company->name = $request->input('name');
        $company->owner=$request->input('owner');
        $company->address=$request->input('address');
        $company->email=$request->input('email');
        $company->website=$request->input('website');
        $company->phone_number=$request->input('phone_number');

        if($company->save()){
            return response(['message'=>'record successfuly updated'],200);
        }else{
            return response(['message'=>'unable to update record'],400);
        }

    }


    //role creator
    public function role()
    {
		$dev_role = new Role();
		$dev_role->slug = 'admin';
		$dev_role->name = 'Main administrator';
		$dev_role->save();

		$manager_role = new Role();
		$manager_role->slug = 'manager';
		$manager_role->name = 'Company Manager';
		$manager_role->save();


		return redirect()->back();
    }


}
