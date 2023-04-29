<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Imports\AdminLeadImport;
use App\Models\AreaMaster;
use App\Models\Category;
use App\Models\LeadFollowUp;
use App\Models\LeadsCategory;
use App\Models\UploadLeads;
use App\Models\LeadsStatus;
use App\Models\LMGEmployee;
use App\Models\Territory2;
use Exception;
use Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TelecallerLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $title = "Telecaller | View all Leads";
        $empusrid = session()->get('empusrid');
        $all_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [1,2,4,5,6,7,8,9])->get();
        return view('user.telecaller.leads.listAllLeads', compact('title', 'all_leads'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getPendingLeads()
    {
        $title = "Telecaller | View Pending Leads";
        $empusrid = session()->get('empusrid');
        if(session()->exists('is_admin')){
            $pending_leads = UploadLeads::with('getLeadStatus')->get();
        } else {
            $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [1])->get();
        }
        return view('user.telecaller.leads.listPendingLeads', compact('title', 'pending_leads'));
    }

    
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        //
        $title = "Telecaller | Add Single Lead";
        $empusrid = session()->get('empusrid');
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        return view('user.telecaller.leads.addLead', compact('title', 'empusrid', 'all_categories', 'all_areas'));
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
                $uploadLeadData->lead_status = 1;   // NEW LEAD STATUS 
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
                $uploadLeadData->added_for = session()->get('empusrid');
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
     * Show the form for creating a new resource.
     */
    public function createBulkLead(): View
    {
        //
        $title = "Telecaller | Add Bulk Leads";
        $empusrid = session()->get('empusrid');
        // $all_categories = Category::get();
        // $all_areas = AreaMaster::where('isactive', 1)->get();
        return view('user.telecaller.leads.addBulkLead', compact('title', 'empusrid'));
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function storeBulkLead(Request $request): JsonResponse
    {
        //
        if($request->hasFile('upload_lead_file')) {
            $file = $request->file('upload_lead_file')->store('import');
            try {
                Excel::import(new LeadsImport($request->send_to), $file);
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
    
    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $lead_id = base64_decode($id);
        $title = "Telecaller | View Single Lead";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.telecaller.leads.viewSingle', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function getFollowupList($id)
    {
        //
        $lead_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "Telecaller | View Lead Follow-up";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('upload_lead_id', $lead_id)->with(['getLeadPreviousStatus', 'getLeadCurrentStatus'])->orderBy('followup_id', 'DESC')->get();
        // dd($leadFollowup->toArray());

        return view('user.telecaller.leads.viewFollowUp', compact('title', 'leadFollowup', 'lead_id'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function getSingleFollowupData($id)
    {
        //
        $followup_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "Telecaller | View Lead Follow-up Details";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('followup_id', $followup_id)->first();
        // dd($leadFollowup->toArray());
        return view('user.telecaller.leads.viewSingleFollowup', compact('title', 'leadFollowup'));
    }

    /**
     * Display the specified resource.
     */
    public function getLeadCalledPage($id)
    {
        $lead_id = base64_decode($id);
        $title = "Telecaller | Lead call Engagements";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.telecaller.leads.telecallerCall', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }


    /**
     * Display the specified resource.
     */
    public function getLeadReCalledPage($id)
    {
        //
        $lead_id = base64_decode($id);
        $title = "Telecaller | Lead call Re-Engagements";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $followup_lead_data = LeadFollowUp::where('upload_lead_id', $lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.telecaller.leads.telecallerReCall', compact('title', 'single_lead', 'followup_lead_data', 'all_categories', 'all_areas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function saveLeadCallEngagementData(Request $request)
    {
        //
        // print_r($request->all());
        
        $first_conf = isset($_POST['first_conf']) && !empty($_POST['first_conf']) ? trim($_POST['first_conf']) : '';
        $first_no_reason = isset($_POST['first_no_reason']) && !empty($_POST['first_no_reason']) ? trim($_POST['first_no_reason']) : '';
        $name_change = isset($_POST['name_change']) && !empty($_POST['name_change']) ? trim($_POST['name_change']) : '';
        $number_change = isset($_POST['number_change']) && !empty($_POST['number_change']) ? trim($_POST['number_change']) : '';
        $chat_now = isset($_POST['chat_now']) && !empty($_POST['chat_now']) ? trim($_POST['chat_now']) : '';
        $online_selling = isset($_POST['online_selling']) && !empty($_POST['online_selling']) ? trim($_POST['online_selling']) : '';
        $portal_name = isset($_POST['protal_name']) && !empty($_POST['protal_name']) ? trim($_POST['protal_name']) : '';
        $register = isset($_POST['register']) && !empty($_POST['register']) ? trim($_POST['register']) : '';
        $appointment_date = isset($_POST['appoinment_date']) && !empty($_POST['appoinment_date']) ? trim($_POST['appoinment_date']) : null;
        $appointment_time = isset($_POST['appoinment_time']) && !empty($_POST['appoinment_time']) ? trim($_POST['appoinment_time']) : null;
        $followup_area = isset($_POST['followup_area']) && !empty($_POST['followup_area']) ? trim($_POST['followup_area']) : '';
        $help = isset($_POST['help']) && !empty($_POST['help']) ? trim($_POST['help']) : '';
        $query_remark = isset($_POST['query_remark']) && !empty($_POST['query_remark']) ? trim($_POST['query_remark']) : '';
        $recall_date = isset($_POST['recall_date']) && !empty($_POST['recall_date']) ? trim($_POST['recall_date']) : null;
        $recall_time = isset($_POST['recall_time']) && !empty($_POST['recall_time']) ? trim($_POST['recall_time']) : null;
        $last_remark = isset($_POST['last_remark']) && !empty($_POST['last_remark']) ? trim($_POST['last_remark']) : '';
        $start_call_datetime = isset($_POST['start_call_datetime']) && !empty($_POST['start_call_datetime']) ? trim($_POST['start_call_datetime']) : '';
        $end_call_datetime = isset($_POST['end_call_datetime']) && !empty($_POST['end_call_datetime']) ? trim($_POST['end_call_datetime']) : '';
        $upload_lead_id = isset($_POST['upload_lead_id']) && !empty($_POST['upload_lead_id']) ? trim($_POST['upload_lead_id']) : '';
    

        DB::beginTransaction();
        try {
            
            $leadData = UploadLeads::whereId($upload_lead_id)->first();
    
            if(!is_null($leadData)){
                // VALID LEAD DATA FOUND
                $lead_area = $leadData->area;
                $lead_contact_number = $leadData->contact_number;
                $previous_status = $leadData->lead_status;
    
                $added_by = session()->get('empusrid');
    
                $message = "";
    
                if($first_conf == ""){
                    $message = "Please select the first question.";
                }else if($first_conf == "N" && $first_no_reason == ""){
                    $message = "Please select reason.";
                }else if($first_conf == "N" && $first_no_reason == "Contact Number Change" && $name_change == ""){
                    $message = "Please enter contact name.";
                }else if($first_conf == "N" && $first_no_reason == "Contact Number Change" && $number_change == ""){
                    $message = "Please enter new contact number.";
                }else if($first_conf == "N" && $first_no_reason == "Contact Number Change" && $number_change == $lead_contact_number){
                    $message = "Please enter new contact number.";
                }else if($first_conf == "Y" && $chat_now == ""){
                    $message = "Please select kya ye sahi waqt hai aapke sath baat karne ka.";
                }else if($first_conf == "Y" && $chat_now == "Y" && $online_selling == ""){
                    $message = "Please select online selling option.";
                }else if($first_conf == "Y" && $chat_now == "Y" && $online_selling == "Y" && $portal_name == ""){
                    $message = "Please enter portal name.";
                }else if($first_conf == "Y" && $chat_now == "Y" && $online_selling != "" && $register == ""){
                    $message = "Please select if the seller wants to register or not.";
                }else if($first_conf == "Y" && $chat_now == "Y" && $online_selling != "" && $register == "Y" && $followup_area == ""){
                    $message = "Please select area.";
                }
    
                if(!empty($message)) {
                    // Validation failed need to send message
                    $status = "error";
                    $message = $message;
                } else {
                    // Validation passed
    
                    /* status 
                        
                        1	New Call	BD
                        2	Recall	BD
                        3	Reject	BD
                        4	Shuffle	BD
                        5	Reattempt	BD
                        6	Meet Assign	BDF
                        7	Meet Confirm	BDF
                        8	Meet Followup	BDF
                        9	Meet Recall	BDF
                        10	Seller Registered	CRM
                        11	Seller Not Interested	CRM
                    
                    */
                    $status = 0;
                    $salesperson_id = 0;
                    $added_for = $added_by;
                    
                    if($recall_date == ""){
                        $recall_date = date("Y-m-d",strtotime("+7 days"));
                    }else{
                        $recall_date = date("Y-m-d",strtotime($recall_date));
                    }
                    
                    if($recall_time == ""){
                        $recall_time = date("H:i:s",strtotime("11:00:00"));
                    }else{
                        $recall_time = date("H:i:s",strtotime($recall_time));
                    }
                    
                    if($first_conf == "N" && $first_no_reason == "Recall"){
                        $status = 2;
                        $added_for = $added_by;
                        $appoinment_date = "";
                        $appoinment_time = "";
                    }
                    if($first_conf == "N" && $first_no_reason == "Wrong Number"){
                        $status = 3;
                        $added_for = $added_by;
                        $appoinment_date = "";
                        $appoinment_time = "";
                        $recall_date = "";
                        $recall_time = "";
                    }
                    if($first_conf == "N" && $first_no_reason == "Not Available"){
                        $status = 2;
                        $added_for = $added_by;
                        $appoinment_date = "";
                        $appoinment_time = "";
                    }
                    if($first_conf == "N" && $first_no_reason == "Contact Number Change"){
    
                        $status = 2;
                        
                        $last_remark = "Contact number updated from $lead_contact_number to $number_change.";
                        // UPDATE CONTACT PERSON NAME AND NUMBER
                        $leadData->contact_per_name = $name_change;
                        $leadData->contact_number = $number_change;
                        $leadData->update();
                        $added_for = $added_by;
                        
                        $appoinment_date = "";
                        $appoinment_time = "";
                        
                    }
                    if($first_conf == "N" && $first_no_reason == "Business Closed"){
                        $status = 3;
                        $added_for = $added_by;
                        $appoinment_date = "";
                        $appoinment_time = "";
                        $recall_date = "";
                        $recall_time = "";
                    }
                    if($first_conf == "Y" && $chat_now == "N"){
                        $status = 2;
                        $added_for = $added_by;
                        $appoinment_date = "";
                        $appoinment_time = "";
                    }
                    if($first_conf == "Y" && $chat_now == "Y" && $register == "N"){
                        $status = 4;
                        $getAllBDs = LMGEmployee::where('designation', 12)->orWhere('designation', 13)->where('offinfo', 2)->select('empusrid')->orderBy('empusrid')->get();
                        $getAllBDs = array_map(function($n) { 
                            return $n['empusrid']; 
                        }, $getAllBDs->toArray());
                        
                        
                        $added_for = $this->get_next($getAllBDs,$added_by);
                        $leadData->telecaller_id = $added_for;
                        $leadData->update();
                        
                        $appoinment_date = "";
                        $appoinment_time = "";

                        
                    }
                    if($first_conf == "Y" && $chat_now == "Y" && $register == "Y"){
                        $status = 6;
                        // $update_lead = "update tbl_upload_leads set area ='$followup_area' where id='$upload_lead_id'";
                        // mysqli_query($objdb->link_id,$update_lead);
                        $leadData->area = $followup_area;
                        $leadData->update();
                        // $sql_get_area = mysqli_query($objdb->link_id,"select id from tbl_area_master where area_name = '$followup_area'");
                        // $fetch_area = mysqli_fetch_assoc($sql_get_area);
                        // $area_id = $fetch_area['id'];
                        
                        $fetch_area = AreaMaster::where('area_name', $followup_area)->select('id')->first();
                        $area_id = $fetch_area->id;
                        // $sql_bdf = "select e.empusrid from tblemployee as e left join tblterritory2 as t on e.empusrid=t.agentcode where e.designation='15' and t.area_id='$area_id' order by e.empusrid";
                        // $get_bdf = mysqli_query($objdb->link_id,$sql_bdf);
                        // while($fetch_bdf = mysqli_fetch_assoc($get_bdf)){
                            //     $empid[] = $fetch_bdf['empusrid'];
                            // }
                        $getAllBDF = LMGEmployee::where([['designation',15],['offinfo', '2']])->select('empusrid')->get();
                        $empid = array_map(function($n) { 
                            return $n['empusrid']; 
                        }, $getAllBDF->toArray());
                        if(count($empid) > 1) {
                            // $sql_last_bdf = "select salesperson_id from tbl_upload_leads where salesperson_id is not null and salesperson_id != '0' order by added_datetime desc limit 1";
                            // $get_last_bdf = mysqli_query($objdb->link_id,$sql_last_bdf);
                            // if(mysqli_num_rows($get_last_bdf) > 0){
                            //     $fetch_last_bdf = mysqli_fetch_assoc($get_last_bdf);
                            //     $last_bdf_id = $fetch_last_bdf['salesperson_id'];
                            //     $added_for = get_next($empid,$last_bdf_id);
                            // }else{
                            //     $added_for = get_next($empid,$empid[0]);
                            // }
                            // IDENTIFY AREA WISE EMPIDS IF ANY
                            $areaWiseEmpids = array();
                            foreach($empid as $emp) {
                                $getAreaWiseEmp = Territory2::where([['agentcode', $emp],['area_id', $area_id]])->first();
                                if(!is_null($getAreaWiseEmp)){
                                    $areaWiseEmpids[] = $getAreaWiseEmp->agentcode;
                                }
                            }
                            $getLastAssignedBDF = UploadLeads::whereNotNull('salesperson_id')->orderBy('added_datetime', 'DESC')->select('salesperson_id')->first();
                            if(!is_null($getLastAssignedBDF)){
                                $last_bdf_id = $getLastAssignedBDF->salesperson_id;
                                if(count($areaWiseEmpids) > 1) {
                                    $added_for = $this->get_next($areaWiseEmpids, $last_bdf_id);
                                } else if(count($areaWiseEmpids) == 1) {
                                    $added_for = $areaWiseEmpids[0];
                                } else {
                                    $added_for = $this->get_next($empid,$last_bdf_id);
                                }
                            } else {
                                $added_for = ((count($areaWiseEmpids) > 1) ? $this->get_next($areaWiseEmpids, $areaWiseEmpids[0]) : ((count($areaWiseEmpids) == 1) ? $areaWiseEmpids[0] :  $this->get_next($empid,$empid[0])));
                            }
                        } elseif (count($empid) == 1) {
                            $added_for = $empid[0];
                        } else {
                            // NO Employee found having BDF designation THROW AN EXCEPTION HERE
                        }
                        
                        if($appointment_date == ""){
                            $appointment_date = date("Y-m-d",strtotime("+2 days"));
                            if(date("l",strtotime($appointment_date)) == "Sunday"){
                                $appointment_date = date("Y-m-d",strtotime("+3 days"));
                            }
                        }else{
                            $appointment_date = date("Y-m-d",strtotime($appointment_date));
                        }

                        
                        if($appointment_time == ""){
                            $appointment_time = date("H:i:s",strtotime("11:00:00"));
                        }else{
                            $appointment_time = date("H:i:s",strtotime($appointment_time));
                        }
                        
                        $recall_date = date("Y-m-d",strtotime($appointment_date));
                        $recall_time = date("H:i:s",strtotime($appointment_time." - 30 minutes"));
                        
                        $salesperson_id = $added_for;
                        
                    }
                    
                    $datetime = date("Y-m-d H:i:s");
                    // $appointment_date = $this->validateDate($appoinment_date);
    
                    $upload_lead_id = isset($upload_lead_id) && !empty($upload_lead_id) ? $upload_lead_id : null; 
                    $added_by = isset($added_by) && !empty($added_by) ? $added_by : null; 
                    $added_for = isset($added_for) && !empty($added_for) ? $added_for : null; 
                    $previous_status = isset($previous_status) && !empty($previous_status) ? $previous_status : null; 
                    $status = isset($status) && !empty($status) ? $status : null; 
                    $first_conf = isset($first_conf) && !empty($first_conf) ? $first_conf : null; 
                    $first_no_reason = isset($first_no_reason) && !empty($first_no_reason) ? $first_no_reason : null; 
                    $chat_now = isset($chat_now) && !empty($chat_now) ? $chat_now : null; 
                    $online_selling = isset($online_selling) && !empty($online_selling) ? $online_selling : null; 
                    $portal_name = isset($portal_name) && !empty($portal_name) ? $portal_name : null; 
                    $register = isset($register) && !empty($register) ? $register : null; 
                    $appointment_date = isset($appointment_date) && !empty($appointment_date) ? $appointment_date : null; 
                    $appointment_time = isset($appointment_time) && !empty($appointment_time) ? $appointment_time : null; 
                    $help = isset($help) && !empty($help) ? $help : null; 
                    $query_remark = isset($query_remark) && !empty($query_remark) ? $query_remark : null; 
                    $last_remark = isset($last_remark) && !empty($last_remark) ? $last_remark : null; 
                    $recall_date = isset($recall_date) && !empty($recall_date) ? $recall_date : null; 
                    $recall_time = isset($recall_time) && !empty($recall_time) ? $recall_time : null; 
                    $start_call_datetime = isset($start_call_datetime) && !empty($start_call_datetime) ? $start_call_datetime : null; 
                    $end_call_datetime = isset($end_call_datetime) && !empty($end_call_datetime) ? $end_call_datetime : null; 
                    $datetime = isset($datetime) && !empty($datetime) ? $datetime : null; 
                    

                    // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`created_at`) values ('$upload_lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$portal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$start_call_datetime','$end_call_datetime','$datetime')";
                    // echo $insert_followup; die;
                    // mysqli_query($objdb->link_id,$insert_followup);
                    
                    $leadFollowUpData = new LeadFollowUp();
                    $leadFollowUpData->upload_lead_id = $upload_lead_id;
                    $leadFollowUpData->followup_added_by = $added_by;
                    $leadFollowUpData->followup_added_for = $added_for;
                    $leadFollowUpData->previous_status = $previous_status;
                    $leadFollowUpData->current_status = $status;
                    $leadFollowUpData->is_person = $first_conf;
                    $leadFollowUpData->first_no_reason = $first_no_reason;
                    $leadFollowUpData->chat_now = $chat_now;
                    $leadFollowUpData->is_sell_online = $online_selling;
                    $leadFollowUpData->portal_name = $portal_name;
                    $leadFollowUpData->want_to_register = $register;
                    $leadFollowUpData->appointment_date = $appointment_date;
                    $leadFollowUpData->appointment_time = $appointment_time;
                    $leadFollowUpData->want_help = $help;
                    $leadFollowUpData->query_remark = $query_remark;
                    $leadFollowUpData->last_remark = $last_remark;
                    $leadFollowUpData->recall_date = $recall_date;
                    $leadFollowUpData->recall_time = $recall_time;
                    $leadFollowUpData->call_start_time = $start_call_datetime;
                    $leadFollowUpData->call_end_time = $end_call_datetime;
                    $leadFollowUpData->created_at = $datetime;
                    $leadFollowUpData->save();
                    
                    if($status == 6){
                        // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', ='$recall_time', salesperson_id='$salesperson_id', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status' where id='$upload_lead_id'";
                        $leadData->call_date = $recall_date;
                        $leadData->call_time = $recall_time;
                        $leadData->salesperson_id = $salesperson_id;
                        $leadData->appointment_date = $appointment_date;
                        $leadData->appointment_time = $appointment_time;
                        $leadData->lead_status = $status;
                        $leadData->update();
                    }else{
                        // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', salesperson_id='$salesperson_id', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status',added_for='$added_for' where id='$upload_lead_id'";
                        $leadData->call_date = $recall_date;
                        $leadData->call_time = $recall_time;
                        $leadData->salesperson_id = $salesperson_id;
                        $leadData->appointment_date = $appointment_date;
                        $leadData->appointment_time = $appointment_time;
                        $leadData->lead_status = $status;
                        $leadData->added_for = $added_for;
                        $leadData->update();
                    }
                    // echo $update_lead; die;
                    // mysqli_query($objdb->link_id,$update_lead);

                    Session::forget('timer');
                    Session::forget("redirecturl");
                    
                    $status = "success";
                    $message = "Call engagement recods has been saved successfully.";
                }
                DB::commit();
            } else {
                // Unable to find lead with provided lead id
                $status = "error";
                $message = "No matching lead data not found the provided lead id.";
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
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        //
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request): JsonResponse
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function get_next($array, $key) {
	
        if (in_array($key, $array)){
            $currentKey = current($array);
            while ($currentKey !== null && $currentKey != $key) {
               next($array);
               $currentKey = current($array);
            }
            if(next($array) == ""){
                return $array[0];
            }else{
                return current($array);
            }
        }else{
          return $array[0];
        }
        
    }

    public function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date ? $date : '';
    }
    

    public function createSession(Request $request)
    {
        Session::put("timer", date('H:i:s'));
        Session::put("redirecturl", $request['sucurl']);
        return Session::get("timer");
    }
}
