<?php

namespace App\Http\Controllers;

use App\Models\AreaMaster;
use App\Models\Territory2;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "User | View all Area";
        $empusrid = session()->get('empusrid');
        $all_area = AreaMaster::get();
        return view('user.area.listAllArea', compact('title', 'all_area'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "User | Add New Area";
        $all_areas = AreaMaster::all();
        return view('user.area.addArea', compact('title','all_areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) :JsonResponse
    {
        $request->validate(
            [
                "area_name" => "required", 
                "isactive" => "required",
            ],
            [
                "area_name.required" => "Area name cannot be empty.",
                "isactive.required" => "You must select an active area status.",
            ]
        );
        $inputs = $request->all();

        DB::beginTransaction();
        try{
            $chkarea = AreaMaster::where('area_name',$inputs['area_name'])->exists();
            if(!$chkarea)
            {
                $areaData = new AreaMaster();
                $areaData->area_name = $inputs['area_name']; 
                if($request->surrounding_area_id)
                {
                    $areaData->surrounding_area_id = implode(',',$inputs['surrounding_area_id']);
                }
                else
                {
                    $areaData->surrounding_area_id = "";
                } 
                $areaData->isactive = $inputs['isactive'];  
                $areaData->cityID = 147; 
                $areaData->save();

                DB::commit();
                $status = "success";
                $message = "An area record has been added successfully.";
            }
            else
            {
                $status = "error";
                $message = "An area name already exist.";
            }
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to add the area record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $title = "User | edit Area";
        $area_id = base64_decode($id);
        $all_areas = AreaMaster::all();
        $singlearea = AreaMaster::whereId($area_id)->first();
        $surrounding_area_id = explode(',', $singlearea->surrounding_area_id);
        return view('user.area.editArea', compact(['title', 'all_areas','singlearea','surrounding_area_id']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) : JsonResponse
    {
        // $area_id = json_decode($id);
        $area_id = $request->id;
        DB::beginTransaction();
        try{
            AreaMaster::where('id',$area_id)->delete();
            // return redirect()->route('area.viewArea');
            DB::commit();
            $status = "success";
            $message = "Record deleted successfully.";

        }catch(Exception $err) {
            $status = "error";
            $message = "Could not delete record.";
            DB::rollback();
        }

        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }


    public function changeAreaStatus(Request $request) : JsonResponse
    {
        $rules = [
            'id' => "required",
        ];

        $message = [
            "id.required" => "Id cannot be null",
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if($validator->fails()) {
            $status = 'error';
            $message = $validator->errors()->first();
        } else {
            $id = $request->id;
            try {
                $areaData = AreaMaster::whereId($id)->first();
                DB::beginTransaction();
                if(!is_null($areaData)) {
                    $curStatus = $areaData->isactive;
                    $changeStatus = 1 - $curStatus;
                    $areaData->isactive = $changeStatus;
                    $areaData->update();
                    DB::commit();
                    $changeStatus_msg = $changeStatus == 1 ? "Active" : "In-active";
                    $status = "success";
                    $message = "Area status has been changed to ".$changeStatus_msg." successfully.";
                } else {
                    $status = 'error';
                    $message = "Invalid id provided";
                }
            } catch(Exception $err) {
                $status = "error";
                $message = "Internal Server error. Try again later.";
                DB::rollback();
            }
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }


    public function update(Request $request): JsonResponse
    {
        $request->validate(
            [
                "area_name" => "required", 
                "isactive" => "required",
            ],
            [
                "area_name.required" => "Area name cannot be empty.",
                "isactive.required" => "You must select an active area status.",
            ]
        );
        $inputs = $request->all();

        DB::beginTransaction();
        try {
            $areaid = $inputs['areaid'];
            $areaData = AreaMaster::whereId($areaid)->first();
            $areaData->area_name = $inputs['area_name']; 
            if($request->surrounding_area_id)
            {
                $areaData->surrounding_area_id = implode(',',$inputs['surrounding_area_id']);
            }
            else
            {
                $areaData->surrounding_area_id = "";
            } 
            $areaData->isactive = $inputs['isactive'];  
            $areaData->cityID = 147; 
            $areaData->update();

            DB::commit();
            $status = "success";
            $message = "An area record has been edited successfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to update the area record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    public function areawisebdf(Request $request)
    {
        $inputs = $request->all();
        $areaid = $inputs['area_id'];

        // $areadata = Territory2::select('agentcode')->join('tbl_area_master', 'tbl_area_master.id', '=', 'tblterritory2.area_id')->where('tbl_area_master.id', $areaid)->get();

        $areadata = Territory2::where('area_id', $areaid)->with('getEmployeeDetails')->get();

        $htmlText = "<div class='table-responsive'>
        <table class='table table-bordered'>
            <thead>
                <tr>
                    <th scope='col'>#</th>
                    <th scope='col'>Employee Name</th>
                    <th scope='col'>ID</th>
                    <th scope='col'>Employee Code</th>
                </tr>
            </thead>
            <tbody>";
        if(!is_null($areadata)){
            $count = 1;
            foreach ($areadata as $key => $area) {
                $htmlText .= "
                    <tr>
                        <th scope='row'>".$count."</th>
                        <td>".ucwords(strtolower($area->getEmployeeDetails->first_nm." ".$area->getEmployeeDetails->last_nm))."</td>
                        <td>".$area->getEmployeeDetails->office_id."</td>
                        <td>".$area->agentcode."</td>
                    </tr>
                ";

                $count++;
            }
        } else {
            $htmlText = "<tr>
                <td colspan='4' class='text-center'>No employee assigend to the area yet.</td>
            </tr>";
        }
        $htmlText .= "</tbody>
        </table>";
        return $htmlText;
    }

}
