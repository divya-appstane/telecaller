<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\LMGEmployee;
use App\Models\UserAdmin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Session;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class indexController extends Controller
{
    public function viewProfile() {
        $user = Auth::guard('front')->user();
        $title = $user->designation == 1 ? 'Admin' : 'User';
        $title .= " | View Profile";
        return view('user.viewProfile', compact('title', 'user'));
    }

    public function editProfile() {
        $user = Auth::guard('front')->user();
        $title = $user->designation == 1 ? 'Admin' : 'User';
        $title .= " | Edit Profile";
        return view('user.editProfile', compact('title', 'user'));
    }

    public function updateProfile(Request $request) : JsonResponse
    {
        $request->validate(
            [
                "first_nm" => "required", 
                "last_nm" => "required", 
                "salute" => "required", 
                "photo" => "mimes:jpeg,png,jpg",
            ],
            [
                "first_nm.required" => "First name cannot be empty.",
                "last_nm.required" => "Last name cannot be empty.",
                "salute.required" => "Last name cannot be empty.",
                "photo.mimes" => "Only jpeg / jpg / png files are allowed",
            ]
        );
        $inputs = $request->all();
        $user = Auth::guard('front')->user();

        DB::beginTransaction();
        try{
            $filename = "";
            if($request->hasFile('photo')){
                $file = $request->file('photo');
                
                $filename = time().$file->hashName().".".$file->extension();
                $path = public_path('assets/images/empProfilePics/');
                $file->move($path,$filename);
            }

            $user->photo = $filename != "" ? $filename : $user->photo;
            $user->salute = $inputs['salute'];
            $user->first_nm = $inputs['first_nm'];
            $user->last_nm = $inputs['last_nm'];
            $user->update();
            DB::commit();
            $status = "success";
            $message = "Your user profile updated successfully.";
        } catch (Exception $err) {
            $status = "error";
            $message = "Unable to update the user profile as of now.";
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    public function changePassword(Request $request) {
        $request->validate(
            [
                "old" => "required", 
                "password" => "required|same:password_confirmation", 
                "password_confirmation" => "required", 
            ],
            [
                "old.required" => "First name cannot be empty.",
                "password.required" => "Last name cannot be empty.",
                "password_confirmation.required" => "Last name cannot be empty.",
            ]
        );
        $inputs = $request->all();
        $user = Auth::guard('front')->user();

        DB::beginTransaction();
        try{
            if($inputs['old'] == $user->emppass) {
                if($user->emppass == $inputs['password']){
                    $status = "success";
                    $message = "The password has been used in recent past please use other password";
                } else {
                    $user->emppass = $inputs['password'];
                    $user->password = Hash::make($inputs['password']);
                    $user->update();
                    $status = "success";
                    $message = "Profile password has been changed successfully";
                }
            } else {
                $status = "error";
                $message = "Old password did not matched";
            }
            DB::commit();
        } catch (Exception $err) {
            $status = "error";
            $message = "Unable to update the user profile password as of now.";
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    public function verifyOldPassword(Request $request) {
        $old_password = $request->old_password;
        $user = Auth::guard('front')->user();
        if($user->emppass == $old_password) {
            $status = "success";
            $message = "Old password matched";
        } else {
            $status = "error";
            $message = "Old password didnot match";
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }
    
    public function viewLoginPage() {
        $title = "LMG | User login";
        return view('login', compact('title'));
    }

    public function verifyLogin(Request $request)
    {
        // USER PANEL
        if (! $token = Auth::guard('front')->attempt(['empusrid' => $request->empusrid, 'password'=> $request->emppass])) {
            $status = "error";
            $message = "Invalid user credentials!";
            $icon = "error";
            $user = null;
        } else {
            $user = Auth::guard('front')->user();
            $validDesignationArray = [1,12,13,15,18,19,21,24,25];
            // var_dump($user->designation);
            // var_dump(in_array($user->designation, $validDesignationArray));
            // exit;
            if(in_array($user->designation, $validDesignationArray)){
                $this->generateSession();
                $status = "success";
                $message = "User Login successfully.";
                $icon = "success";
            } else {
                $status = "unauthorized";
                $message = "Unauthorized User Login.";
                $icon = "error";
                $user = null;
            }
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $icon,
            'load_dashboard' => session()->get('load_dashboard'),
            'user' => $user,
        ], 200);
        
    }

    public function generateSession()
    {
        // dd(Auth::guard('front')->user()->toArray());
        $load_dashboard = '';
        $user = Auth::guard('front')->user()->toArray();
        $designation = Designation::where('designation_id', $user['designation'])->select('designation_id', 'designation_title')->first();
        
        $login_id = $user['emp_id'];
        $empusrid = $user['empusrid'];
        $user_name = ucwords(strtolower($user['first_nm'] . ' ' . $user['last_nm']));
        $user_email = !empty($user['company_email']) ? $user['company_email'] : (!empty($user['emailid']) ? $user['emailid'] : $user['alternate_emailid']);
        if($designation->designation_title == 'Business Development Associate' || $designation->designation_title == 'Business Development Executive' || $designation->designation_title == 'Business Development Manager' || $designation->designation_title == "Tele-Sales")
        {
            $load_dashboard = 'telecaller';
        }
        else if($designation->designation_title == 'Business Development Associate (On Field)' || $designation->designation_title == 'Business Development Executive (On Field)' || $designation->designation_title == 'Business Development Manager (On Field)')
        {
            $load_dashboard = 'marketing';
        }
        else if($designation->designation_title == 'Customer Relationship Executive' || $designation->designation_title == 'Customer Relationship Manager')
        {
            $load_dashboard = "crm";
        }
        else if($designation->designation_title == 'Super Admin')
        {
            $load_dashboard = "admin";
        }
        
        $last_login_date = $user['loghistoryd'];
        $last_login_time = $user['loghistoryt'];

        DB::beginTransaction();
        try{
            session([
                'is_login' => 1,
                'login_id' => $login_id,
                'empusrid' => $empusrid,
                'user_name' => $user_name,
                'user_email' => $user_email,
                'load_dashboard' => $load_dashboard,
                'user_designation_id' => $user['designation'],
                'user_designation' => $designation->designation_title,
                'last_login' => date('Y-m-d H:i:s', strtotime($last_login_date." ".$last_login_time)),
            ]);
            DB::commit();
        } catch(Exception $e){
            DB::rollBack();
        }
    }

    public function toggleUserAccountStatus(Request $request) 
    {
        $user = LMGEmployee::where('emp_id', $request->id)->first();

        if(!is_null($user)) {
            DB::beginTransaction();
            try {
                $offinfo = $user->offinfo;
                $oldStatus = $offinfo == 2 ? 'Active' : 'Block';
                $newStatus = $oldStatus == 'Active' ? 'Block' : 'Active';
                $user->offinfo = 2 - $offinfo;
                $user->update();
                $status = "success";
                $message = "User account status has been changed from ".$oldStatus." to ".$newStatus." for ".$user->empusrid.".";
                DB::commit();
            } catch (Exception $e) {
                $status = "error";
                $message = "Internal server error. Try again later.";
                DB::rollBack();
            }
        } else {
            $status = "error";
            $message = "Unable to fetch the record at the moment.";
        }

        return response()->json([
            "status" => $status, 
            "message" => $message, 
            "icon" => $status,
        ]);
    }

    public function show($emp_id) {
        $emp_id = base64_decode($emp_id);
        $user = LMGEmployee::where('emp_id', $emp_id)->first();
        if(!is_null($user)) {
            $title = "Admin | View User Profile";
            return view('user.admin.viewUserProfile', compact('title', 'user'));
        } 
    }

    public function logout()
    {
        //
        session()->flush();
        Session::forget("timer");
        return redirect()->route('user.viewLoginPage');
    }

    public function getAllSystemUsers()
    {
        $title = "System Users";
        try{
            $allDesignations = [
                "Business Development Associate",
                "Business Development Associate (On Field)",
                "Business Development Executive",
                "Business Development Executive (On Field)",
                "Business Development Manager",
                "Business Development Manager (On Field)",
                "Customer Relationship Executive",
                "Customer Relationship Manager",
                "Sales Representative (On Field)",
                "Tele-Sales",
            ];
            $allUsers = Designation::whereIn('designation_title', $allDesignations)->with('getDesignationWiseEmp')->orderBy('designation_title')->get();
            // dd($allUsers->toArray());
            return view('user.admin.systemUsers', ["title" => $title, "allUsers" => $allUsers]);
        } catch(Exception $e) {
            dd($e->getMessage());
        }
    }
}
