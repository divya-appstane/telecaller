<?php

namespace App\Http\Controllers;

use App\Models\Territory;
use App\Models\Territory2;
use App\Models\Territory3;
use App\Models\AreaMaster;
use App\Models\LMGEmployee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class TerritoryController extends Controller
{
      /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Territory | View all";
        $empusrid = session()->get('empusrid');
        $all_territory = Territory::join('tblemployee', 'tblemployee.empusrid', '=', 'tblterritory1.agentcode')
                        ->select('tblemployee.*')->get();
                        
        return view('user.territory.listAllTerritory', compact('title', 'all_territory'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "Territory | Add New";
        $all_territories = Territory::all();
        $emp = LMGEmployee::orderBy('empusrid', 'ASC')->get();
        $areas = AreaMaster::where('isactive',1)->get();
        return view('user.territory.addTerritory', compact('title','all_territories','emp','areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) :JsonResponse
    {
        $request->validate(
            [
                "tfile" => "required", 
                "txtfrm" => "required",
                "txtto" => "required",
                "txtpin" => "required",
                "send_to" => "required",
                "area_id" => "required",
            ],
            [
                "tfile.required" => "Image cannot be empty.",
                "txtfrm.required" => "Route source must be required",
                "txtto.required" => "Route destination must be required",
                "txtpin.required" => "Route source must be required",
                "send_to.required" => "Route source must be required",
                "area_id.required" => "Route source must be required",
            ]
        );
        $inputs = $request->all();

        // $inputs['tfile'] = time().'.'.$request->image->extension();

        $image = $request->file('tfile');
        $new_name = rand() . '.' . $image->getClientOriginalExtension();

        DB::beginTransaction();
        try {
            $territorydata = new Territory();
            $territorydata->filepath = $new_name;
            $territorydata->agentcode = $inputs['send_to']; 
            $territorydata->save();

            $image->move(public_path('assets/images'), $new_name);

            // $request->image->move(public_path('assets/images'), $inputs['tfile']);

            $n = $inputs['cnt'];

            for($i=0;$i<$n;$i++)
            {
                $territorydata1 = new Territory2();
                $territorydata1->tfrom = $inputs['txtfrm'][$i];
                $territorydata1->tto = $inputs['txtto'][$i]; 
                $territorydata1->area_id = $inputs['area_id']; 
                $territorydata1->agentcode = $inputs['send_to']; 
                $territorydata1->save();
            }
            $m = $inputs['cnt2'];

            for($j=0;$j<$m;$j++)
            {
                $territorydata3 = new Territory3();
                $territorydata3->tpin = $inputs['txtpin'][$j];
                $territorydata3->agentcode = $inputs['send_to'];
                $territorydata3->save();
            }

            DB::commit();
            $status = "success";
            $message = "A territory record has been added successfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to add the territory record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

    public function show($agentcode)
    {
        $title = "Territory | View Details";
        $agentcode = base64_decode($agentcode);

        $routemap = Territory::where('agentcode', $agentcode)->get();

        $route_by_area = DB::table('tblterritory2')
                        ->select('tblterritory2.*', 'tbl_area_master.area_name')
                        ->leftJoin('tbl_area_master','tbl_area_master.id','=','tblterritory2.area_id')
                        ->where('tblterritory2.agentcode','=',$agentcode)
                        ->get();
        $pincode_list = Territory3::where('agentcode', $agentcode)->get();
        return view('user.territory.viewDetails',compact('routemap','route_by_area','pincode_list'));
    }

}
