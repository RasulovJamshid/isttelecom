<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Role;
use Illuminate\Http\Request;

use App\Models\User;
// use Auth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller{
//
protected $user;

public function __construct(){
    $this->middleware("auth:api")->except(["login","register"]);
    $this->user = new User;
}

public function register(Request $request){
    $validator = Validator::make($request->all(),[
        'name' => 'required|string',
        'email' => 'required|string|unique:users',
        'password' => 'required|min:6|confirmed',
        'company_name' => 'required|string',
        'company_owner' => 'required|string',
        'company_email' => 'required|email',
        'company_website' => 'nullable|url',
        'company_phone_number' => 'required|string',
        'address'=>'required|string'
    ]);

    if($validator->fails()){
        return response([
        'success' => false,
        'message' => $validator->messages()->toArray()
        ], 403);
    }
    $data = [
        "name" => $request->name,
        "email" => $request->email,
        "password" => Hash::make($request->password)
    ];
    $user = $this->user->create($data);

    //Creating company for given user
    $companyData = [
        "name" => $request->company_name,
        'owner' => $request->company_owner,
        'email' => $request->company_email,
        'website' => $request->company_website,
        'phone_number' => $request->company_phone_number,
        'address' => $request->address,
        'user_id' => $user->id
    ];
    $company = Company::create($companyData);

    //Attaching role to the user
    $manager_role = Role::where('slug', 'manager')->first();
    $user->roles()->attach($manager_role);


    $responseMessage = "Registration Successful";
    return response()->json([
        'success' => true,
        'message' => $responseMessage
    ], 200);

}

public function login(Request $request){

$validator = Validator::make($request->all(),[
'email' => 'required|string',
'password' => 'required|min:6',
]);
$role = "manager";



if($validator->fails()){
    return response()->json([
    'success' => false,
    'message' => $validator->messages()->toArray()
    ], 500);
}

$credentials = $request->only(["email","password"]);
$user = User::where('email',$credentials['email'])->first();
if($user->hasRole('admin')){
    $role = 'admin';
}
    if($user){
        if(!auth()->attempt($credentials)){
            $responseMessage = "Invalid username or password";
            return response()->json([
            "success" => false,
            "message" => $responseMessage,
            "error" => $responseMessage
            ], 422);
        }
        $accessToken = auth()->user()->createToken('authToken')->accessToken;
        $responseMessage = "Login Successful";
        return $this->respondWithToken($accessToken,$responseMessage,auth()->user(),$role);
    }
    else{
        $responseMessage = "Sorry, this user does not exist";
        return response()->json([
        "success" => false,
        "message" => $responseMessage,
        "error" => $responseMessage
        ], 422);
    }
}

public function viewProfile(){
    $responseMessage = "user profile";
    $data = Auth::guard("api")->user();
    return response()->json([
        "success" => true,
        "message" => $responseMessage,
        "data" => $data
    ], 200);
}

public function logout(){
    $user = Auth::guard("api")->user()->token();
    $user->revoke();
    $responseMessage = "successfully logged out";
    return response()->json([
    'success' => true,
    'message' => $responseMessage
    ], 200);
}

}
