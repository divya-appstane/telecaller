<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\UploadLeads;
use App\Models\Category;
use App\Models\AreaMaster;
use App\Models\LeadsCategory;
use App\Models\LeadFollowUp;
use App\Models\LMGEmployee;
use App\Imports\AdminLeadImport;
use Maatwebsite\Excel\Validators\ValidationException;

class AdminLeadsController extends Controller
{
    public function index()
    {
        $title = "Admin | View all Leads";
        $empusrid = session()->get('empusrid');
        // $all_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->get();
        $all_leads = UploadLeads::with('getLeadStatus')->get();
        return view('user.admin.leads.listAllLeads', compact('title', 'all_leads'));
    }

     /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        //
        $title = "Admin | Add Single Lead";
        $empusrid = session()->get('empusrid');
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $emp = LMGEmployee::where('offinfo', 2)->whereIn('designation',[12, 13, 24])->orderBy('empusrid', 'ASC')->get();
        return view('user.admin.leads.addLead', compact('title', 'empusrid', 'all_categories', 'all_areas','emp'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        //
        // dd($request->all());
        $request->validate(
            [
                "company_name" => "required", 
                "contact_number" => "required|max:10|min:10", 
                "cat_id" => "required",
                "area" => "required", 
                "email" => "nullable|email", 
            ],
            [
                "company_name.required" => "Company name cannot be empty.",
                "contact_number.required" => "Contact number field is required.",
                "contact_number.max" => "Contact numbers can only be {0} digit long",
                "contact_number.min" => "Contact numbers can only be {0} digit long",
                "cat_id.required" => "You must select at least 1 category.",
                "area.required" => "You must select an area.",
                "email.email" => "Please enter a valid email address.",
            ]
        );
        $inputs = $request->all();

        DB::beginTransaction();
        try {

            if(!(UploadLeads::where('contact_number',$inputs['contact_number'])->exists()))
            {
                $uploadLeadData = new UploadLeads();
                $uploadLeadData->company_name = $inputs['company_name']; 
                $uploadLeadData->contact_per_name = $inputs['contact_per_name'];	 
                $uploadLeadData->contact_number = $inputs['contact_number']; 
                $uploadLeadData->lead_status ='1';   // NEW LEAD STATUS 
                $uploadLeadData->email = $inputs['email']; 
                $uploadLeadData->designation = $inputs['designation']; 
                $uploadLeadData->address = $inputs['address']; 
                $uploadLeadData->state = $inputs['state']; 
                $uploadLeadData->city = $inputs['city']; 
                $uploadLeadData->pincode = $inputs['pincode']; 
                $uploadLeadData->gst_number = $inputs['gst_number']; 
                $uploadLeadData->area = $inputs['area'];
                $uploadLeadData->remarks = $inputs['remarks'];
                $uploadLeadData->added_datetime = date('Y-m-d H:i:s'); 
                $uploadLeadData->added_by = session()->get('empusrid');
                $uploadLeadData->added_for = $inputs['send_to'];
                $uploadLeadData->call_date = date('Y-m-d');
                $uploadLeadData->call_time = date('H:i');
                $uploadLeadData->save();

                if(count($inputs['cat_id'])){
                    foreach ($inputs['cat_id'] as $catItem) {
                        $leadAdd = new LeadsCategory();
                        $leadAdd->upload_lead_id = $uploadLeadData->id;
                        $leadAdd->cat_id = $catItem;
                        $leadAdd->added_by = session()->get('empusrid');
                        $leadAdd->save();
                    }
                }
                DB::commit();
                $status = "success";
                $message = "A lead record has been added successfully.";
            }
            else
            {
                $status = "error";
                $message = "A lead record has been already generated.";
            }
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to add the lead record as of now.".$e->getMessage();
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
        //
        $lead_id = base64_decode($id);
        $title = "Admin | View Single Lead";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        return view('user.admin.leads.viewSingle', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }

     /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate(
            [
                "company_name" => "required", 
                "contact_number" => "required|max:10|min:10", 
                "cat_id" => "required",
                "area" => "required", 
                "email" => "nullable|email", 
            ],
            [
                "company_name.required" => "Company name cannot be empty.",
                "contact_number.required" => "Contact number field is required.",
                "contact_number.max" => "Contact numbers can only be {0} digit long",
                "contact_number.min" => "Contact numbers can only be {0} digit long",
                "cat_id.required" => "You must select at least 1 category.",
                "area.required" => "You must select an area.",
                "email.email" => "Please enter a valid email address.",
            ]
        );
        $inputs = $request->all();

        DB::beginTransaction();
        try {
            if(!(UploadLeads::where('id', "!=" ,$inputs['lid'])->where('contact_number',$inputs['contact_number'])->exists()))
            {
                $lead_id = $inputs['lid'];
                $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                $uploadLeadData->company_name = $inputs['company_name']; 
                $uploadLeadData->contact_per_name = $inputs['contact_per_name'];	 
                $uploadLeadData->contact_number = $inputs['contact_number']; 
                $uploadLeadData->email = $inputs['email']; 
                $uploadLeadData->designation = $inputs['designation']; 
                $uploadLeadData->address = $inputs['address']; 
                $uploadLeadData->state = $inputs['state']; 
                $uploadLeadData->city = $inputs['city']; 
                $uploadLeadData->pincode = $inputs['pincode']; 
                $uploadLeadData->gst_number = $inputs['gst_number']; 
                $uploadLeadData->area = $inputs['area'];
                $uploadLeadData->remarks = $inputs['remarks'];
                $uploadLeadData->modified_datetime = date('Y-m-d H:i:s'); 
                $uploadLeadData->update();

                // DELETING EXISTING LEAD CATEGORIES
                LeadsCategory::where('upload_lead_id', $lead_id)->delete();

                if(count($inputs['cat_id'])){
                    foreach ($inputs['cat_id'] as $catItem) {
                        $leadAdd = new LeadsCategory();
                        $leadAdd->upload_lead_id = $lead_id;
                        $leadAdd->cat_id = $catItem;
                        $leadAdd->added_by = session()->get('empusrid');
                        $leadAdd->save();
                    }
                }
                DB::commit();
                $status = "success";
                $message = "A lead record has been edited successfully.";
            }
            else
            {
                $status = "error";
                $message = "A lead record has been already generated.";
            }
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to update the lead record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }

     /**
     * Display a listing of the resource.
     */
    public function getFollowupList($id)
    {
        $lead_id = base64_decode($id);
        $title = "Admin | View Lead Follow-up";
        $leadFollowup = LeadFollowUp::where('upload_lead_id', $lead_id)->with(['getLeadPreviousStatus', 'getLeadCurrentStatus'])->orderBy('followup_id', 'DESC')->get();
        return view('user.admin.leads.viewFollowUp', compact('title', 'leadFollowup', 'lead_id'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getSingleFollowupData($id)
    {
        $followup_id = base64_decode($id);
        $title = "Admin | View Lead Follow-up Details";
        $leadFollowup = LeadFollowUp::where('followup_id', $followup_id)->first();
        return view('user.admin.leads.viewSingleFollowup', compact('title', 'leadFollowup'));
    }

    public function createBulkLead(): View
    {
        $title = "Admin | Add Bulk Leads";
        $empusrid = session()->get('empusrid');
        $emp = LMGEmployee::where('offinfo', 2)->whereIn('designation',[12, 13, 24])->orderBy('empusrid', 'ASC')->get();
        return view('user.admin.leads.addBulkLead', compact('title', 'empusrid','emp'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeBulkLead(Request $request): JsonResponse
    {
        if($request->hasFile('upload_lead_file')) {
            $file = $request->file('upload_lead_file')->store('import');
            try {
                Excel::import(new AdminLeadImport($request->send_to), $file);
                $status = "success";
                $message = "Seller lead data has been stored successfully.";

            } catch (Exception $e) {
                $status = 'error';
                $message = "Error occured: ".$e->getMessage();
            }
        } else {
            $status = "error";
            $message = "The upload file cannot be empty";
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }
   
}

