<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\StudentDetail;
use App\Models\User;
use App\Traits\ApiResponsable;
use Illuminate\Http\Request;
use Validator;

class StudentController extends Controller
{
    use ApiResponsable;

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:3,200',
            'email' => 'required|email|unique:App\Models\User',
            'mobile' => 'required|numeric|unique:users|digits_between:10,11',
            'gender' => 'required|string|in:male,female',
            'state' => 'required|string'
        ],[
            'mobile.digits_between' => 'Invalid mobile number'
        ]);

        if ($validator->fails()) {
            return  $this->apiResponse(400, 'fail', 'Validation failed', $validator->errors());
        }

        \DB::transaction(function() use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'is_student' => true,
                'active' => true
            ]);
            StudentDetail::insert([
                [
                    'student_id' => $user->id,
                    'key' => 'gender',
                    'value' => $request->gender
                ],
                [
                    'student_id' => $user->id,
                    'key' => 'state',
                    'value' => $request->state
                ]
            ]);
            $user->assignRole(2); // 2 is customer role
        });

        $user = User::where('mobile', $request->mobile)->first();
        return  $this->apiResponse(200, 'success', 'Registered Successfully!!', ['token' => $user->createToken($request->mobile)->plainTextToken]);
        // monika toke: "1|aL5gOb2E7KYC1OO3NxA5t56I4gYzlq1dLyeRV8Ap"
        // "5|DyLoYKUqmGyiqtaYPj2jQjEL9pLhYS8te51GytVP"
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|numeric|digits_between:10,11',
        ],[
            'mobile.digits_between' => 'Invalid mobile number'
        ]);

        if ($validator->fails()) {
            return  $this->apiResponse(400, 'fail', 'Validation failed', $validator->errors());
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (! $user ) {
            return  $this->apiResponse(404, 'fail', 'Not Found', ['mobile' => 'Mobile not Registered.']);
        }

        return $this->apiResponse(200, 'success', 'Login Successfully!!', ['token' => $user->createToken($request->mobile)->plainTextToken]);
        // return $this->apiResponse(200, 'success', 'Login Successfully!!', ['token' => $user->createToken($request->mobile)->plainTextToken]);
    }

    public function logout(Request $request)
    {
        $user = User::where('mobile', $request->mobile)->first();
        $user->tokens()->delete();
        return $this->apiResponse(200, 'success', 'Logout Successfully!!');
    }


}
