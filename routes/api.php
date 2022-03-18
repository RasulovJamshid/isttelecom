<?php

use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\UserController;
use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'users', 'middleware' => 'CORS'], function ($router) {
    Route::post('/register', [UserController::class, 'register'])->name('register.user');
    Route::post('/login', [UserController::class, 'login'])->name('login.user');
    Route::get('/view-profile', [UserController::class, 'viewProfile'])->name('profile.user');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout.user');
    });

Route::prefix("admin")->middleware(['auth:api','CORS','role:admin'])->group(function(){
    Route::apiResource('/company', CompanyController::class)->middleware('role:admin');
    Route::get('/paginate/employee',[EmployeeController::class,'admin_index']);
    Route::get('/paginate/company',[CompanyController::class,'index']);
});


Route::prefix("manager")->middleware(['auth:api','CORS','role:manager'])->group(function(){
    Route::get('/company/{company_id}',[CompanyController::class, 'single_company'])->whereNumber("company_id");
    Route::put('/company/{company_id}',[CompanyController::class, 'update'])->whereNumber("company_id");
    Route::apiResource('/employee', EmployeeController::class)->only(["index","show","update","destroy","store"]);
});
