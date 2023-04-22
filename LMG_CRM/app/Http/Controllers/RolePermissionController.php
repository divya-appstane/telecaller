<?php

namespace App\Http\Controllers;

use App\Models\Designation;
use App\Models\Permission;
use App\Models\PermissionAction;
use App\Models\Module;
use App\Models\ModulePermission;
use App\Models\RolePermission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class RolePermissionController extends Controller
{

    private $mdoules = array();
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Role | View all";
        $matchingDesignations = [
            "business-development-associate", 
            "business-development-executive", //telecaller
            "business-development-manager",
            "business-development-associate-on-field",
            "business-development-executive-on-field", //marketing on field
            "business-development-manager-on-field",
            "customer-relationship-executive",
            "customer-relationship-manager", //crm
            "super-admin" //admin
        ];
        $all_designations = Designation::whereIn('slug', $matchingDesignations)->get();

        return view('user.admin.role-permission.allRole', compact('title','all_designations'));
    }

   
    // public function create(): View
    // {
    //     $title = "Role Permission | Add New";
    //     $all_roles = Designation::orderBy('designation_title', 'ASC')->get();
    //     $all_permission = Permission::orderBy('name', 'ASC')->get();
    //     return view('user.admin.role-permission.createPermission', compact('title', 'all_roles','all_permission'));
    // }

   
    public function store(Request $request) :JsonResponse
    {
        $inputs = $request->all();

        $designation_designation_id = $inputs['designation_designation_id'];

        $permission_id = $inputs['permission_id'];



        DB::beginTransaction();
        try{
            for ($i = 0; $i < count($permission_id); $i++) 
            {
                $chkpermission = RolePermission::where('designation_designation_id',$designation_designation_id)->where('permission_id',$permission_id[$i])->exists();
                if($chkpermission)
                {
                    $allpermissions = RolePermission::where('designation_designation_id',$designation_designation_id)->get();

                    foreach($allpermissions as $permission)
                    {
                        // echo $permission->permission_id."||"; echo $permission_id[$i]."<br>";

                        if($permission->permission_id != $permission_id[$i])
                        {
                            RolePermission::where('id',$permission->id)->delete();
                        }
                    }
                }
                else
                {
                    $roleperData = new RolePermission();
                    $roleperData->designation_designation_id = $designation_designation_id;
                    $roleperData->permission_id = $permission_id[$i]; 
                    $roleperData->save();
                }
            }
            DB::commit();
            $status = "success";
            $message = "Permission applied successfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to add the permission record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    public function show($id)
    {
        $roleid = base64_decode($id);

        $role = Designation::where('designation_id',$roleid)->first();

        $title = "Role Permission | View Role Permission";
        
        $all_actions = PermissionAction::where('isactive',1)->get();

        $all_module = Module::with(['modulePermissions'])->get();

        foreach($all_module as $module)
        {
            foreach($module->modulePermissions as $permission)
            {
                $actionid[] = $permission->action_id;
            }
        }

        //display permissions

        $chkpermission = RolePermission::all();

        $all_permissions = PermissionAction::with(['permissionActions'])->whereIn('id',$actionid)->get();

        return view('user.admin.role-permission.allPermission', compact('title','all_actions','all_module','all_permissions','role','chkpermission'));

    }


    // public function update(Request $request): JsonResponse
    // {
    //     $request->validate(
    //         [
    //             "designation_id" => "required", 
    //             "permission_id" => "required",
    //         ],
    //         [
    //             "designation_id.required" => "This field is required.",
    //             "permission_id.required" => "This field is required.",
    //         ]
    //     );
    //     $inputs = $request->all();

    //     DB::beginTransaction();
    //     try {
    //         $chkrole = RolePermission::where('designation_designation_id',$inputs['designation_id'])->where('permission_id',$inputs['permission_id'])->exists();
    //         if(!$chkrole)
    //         {
    //             $perid = $inputs['perid'];
    //             $perData = RolePermission::whereId($perid)->first();
    //             $perData->designation_designation_id = $inputs['designation_id']; 
    //             $perData->permission_id = $inputs['permission_id']; 
    //             $perData->update();
    //         }
    //         else
    //         {
    //             $status = "error";
    //             $message = "Permission already applied.";
    //         }
    //         DB::commit();
    //         $status = "success";
    //         $message = "Permission edited successfully.";
    //     } catch (Exception $e) {
    //         $status = "error";
    //         $message = "Unable to update the permission record as of now.".$e->getMessage();
    //         DB::rollback();
    //     }
    //     return response()->json([
    //         'status' => $status,
    //         'message' => $message,
    //         'icon' => $status,
    //     ]);
    // }

}
