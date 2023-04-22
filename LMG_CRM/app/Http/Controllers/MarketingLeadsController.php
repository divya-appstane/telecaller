<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Models\LeadFollowUp;
use App\Models\UploadLeads;
use App\Models\Category;
use App\Models\AreaMaster;
use App\Models\LeadsCategory;
use App\Models\LMGEmployee;
use App\Models\SellerCategory;
use App\Models\Sellers;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Validator;

class MarketingLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $title = "Marketing | View all Leads";
        $empusrid = session()->get('empusrid');
        $all_leads = UploadLeads::with('getLeadStatus')->where('salesperson_id', $empusrid)->whereIn('lead_status', [6,7,8,9])->get();
        // dd($all_leads->toArray());
        return view('user.marketing.leads.listAllLeads', compact('title', 'all_leads'));
    }

    /**
    * Display a listing of the resource.
    */
   public function getPendingLeads()
   {
       //
       $title = "Marketing | View Pending Leads";
       $empusrid = session()->get('empusrid');
       if(session()->exists('is_admin')){
           $pending_leads = UploadLeads::with('getLeadStatus')->get();
       } else {
           $pending_leads = UploadLeads::with('getLeadStatus')->where('salesperson_id', $empusrid)->whereIn('lead_status', [6])->get();
       }
       
       return view('user.marketing.leads.listPendingLeads', compact('title', 'pending_leads'));
   }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $lead_id = base64_decode($id);
        $title = "BDE (On Field) | View Single Lead";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.marketing.leads.viewSingle', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getFollowupList($id)
    {
        //
        $lead_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "BDE (On Field) | View Lead Follow-up";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('upload_lead_id', $lead_id)->with(['getLeadPreviousStatus', 'getLeadCurrentStatus'])->orderBy('followup_id', 'DESC')->get();
        // dd($leadFollowup->toArray());
        return view('user.marketing.leads.viewFollowUp', compact('title', 'leadFollowup', 'lead_id'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getSingleFollowupData($id)
    {
        //
        $followup_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "BDE (On Field) | View Lead Follow-up Details";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('followup_id', $followup_id)->first();
        // dd($leadFollowup->toArray());
        return view('user.marketing.leads.viewSingleFollowup', compact('title', 'leadFollowup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
    public function editMeeting(string $id)
    {
        //
        $lead_id = base64_decode($id);
        $title = "BDE (On Field) | Sales Meeting Update";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.marketing.leads.salesMeeting', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateMeeting(Request $request): JsonResponse
    {
        $rules = [
            "sellername" => "required", 
            "contnum" => "required|max:10|min:10", 
            "cat_id" => "required",
            // "area" => "required", 
            "email" => "nullable|email",
            "photo" => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            "selreg" => "required",
            "selreason" => "nullable|required_if:selreg,0",
            "sappoinment_date" => "nullable|required_if:selreason,newfollowup",
            "sappoinment_time" => "nullable|required_if:selreason,newfollowup",
            "reasondata" => "nullable|required_if:selreason,notintrested",
        ];
        $messages = [
            "sellername.required" => "Company name cannot be empty.",
            "contnum.required" => "Contact number field is required.",
            "contnum.max" => "Contact numbers can only be {0} digit long",
            "contnum.min" => "Contact numbers can only be {0} digit long",
            "cat_id.required" => "You must select at least 1 category.",
            // "area.required" => "You must select an area.",
            "email.email" => "Please enter a valid email address.",
            "selreg" => "Please select seller registration status",
            "selreason" => "Please select reason for why seller is not registering",
            "sappoinment_date" => "Please select new appointment date",
            "sappoinment_time" => "Please select new appointment time",
            "reasondata" => "Please explain the reason for seller not registering with LMG in a brief",
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {

            if($request->ajax())
            {
                return response()->json(array(
                    'success' => false,
                    'message' => 'There are incorect values in the form!',
                    'errors' => $validator->getMessageBag()->toArray()
                ), 200);
            }

            $this->throwValidationException(

                $request, $validator

            );

        }
        $inputs = $request->all();

        DB::beginTransaction();
        try {
            // FILE UPLOAD
            if($request->hasFile("photo")) {
                $image = session()->get('empusrid').'_'.time().'.'.$request->photo->extension();
                $request->photo->move(public_path('uploads/sales/shop_images/'), $image);
                // $image = Image::create(["imageName" => $image]);
                // return back()->with('success','Success! image uploaded');
                $sellername = $inputs['sellername'];
                $contperson = $inputs['contperson'];
                $contnum = $inputs['contnum'];
                $email = $inputs['email'];
                $designation = $inputs['designation'];
                $address = $inputs['address'];
                $city = $inputs['city'];
                $state = $inputs['state'];
                $pincode = $inputs['pincode'];
                $remarks = $inputs['remarks'];
                $subcategory = isset($inputs['subcategory']) && !empty($inputs['subcategory']) ? $inputs['subcategory'] : '';
                $latitude = isset($inputs['latitude']) && !empty($inputs['latitude']) ? $inputs['latitude'] : '';
                $longitude = isset($inputs['longitude']) && !empty($inputs['longitude']) ? $inputs['longitude'] : '';
                $gst_number = isset($inputs['gst_number']) && !empty($inputs['gst_number']) ? $inputs['gst_number'] : '';
                $selreg = isset($inputs['selreg']) && !empty($inputs['selreg']) ? $inputs['selreg'] : 0;
                $selreason = isset($inputs['selreason']) && !empty($inputs['selreason']) ? $inputs['selreason'] : null;
                $appoinment_date = isset($inputs['sappoinment_date']) && !empty($inputs['sappoinment_date']) ? $inputs['sappoinment_date'] : null;
                $appoinment_time = isset($inputs['sappoinment_time']) && !empty($inputs['sappoinment_time']) ? $inputs['sappoinment_time'] : null;
                $reasondata = isset($inputs['reasondata']) && !empty($inputs['reasondata']) ? htmlentities($inputs['reasondata'], ENT_QUOTES) : '';
                $lead_id = $inputs['lid'];
                $cat_id = $inputs['cat_id'];
                
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
                $lead_info = UploadLeads::whereId($lead_id)->first();
                if(!is_null($lead_info)) {
                    $lead_contact_number = $lead_info->contact_number;
                    $previous_status = $lead_info->lead_status;
    
                    $lead_previous_followup_info = LeadFollowUp::where('upload_lead_id', $lead_id)->orderBy('created_at', "DESC")->first();
                    // $sql_previous_followup = "select * from tbl_lead_followup where upload_lead_id = '".$lead_id."' order by created_at DESC limit 1";
                    // $row_previous_followup = mysqli_query($objdb->link_id,$sql_previous_followup);
                    // $fetch = mysqli_fetch_object($row_previous_followup);
            
                    $added_by = session()->get('empusrid');
            
                    $message = "";

                    $status = 0;
                    $salesperson_id = null;
                    $added_for = $added_by = session()->get('empusrid');
                    
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                    
                    $datetime = date("Y-m-d H:i:s");
                    
                    $first_conf = $lead_previous_followup_info->is_person;
                    $first_no_reason = $lead_previous_followup_info->first_no_reason;
                    $chat_now = $lead_previous_followup_info->chat_now;
                    $online_selling = $lead_previous_followup_info->is_sell_online;
                    $protal_name = $lead_previous_followup_info->portal_name;
                    $register = $lead_previous_followup_info->want_to_register;
                    $appointment_date = $lead_previous_followup_info->appointment_date;
                    $appointment_time = $lead_previous_followup_info->appointment_time;
                    $help = $lead_previous_followup_info->want_help;
                    $query_remark = $lead_previous_followup_info->query_remark;
                    $last_remark = $lead_previous_followup_info->last_remark;
                    
                    if($selreg == "1"){
                        $status = 10;
                        
                        $recall_date = date("Y-m-d");
                        $recall_time = date("H:i:s");

                        $getAllCRMs = LMGEmployee::where('designation', 18)->where('offinfo', 2)->select('empusrid')->orderBy('empusrid')->get();
                        $empid = array_map(function($n) { 
                            return $n['empusrid']; 
                        }, $getAllCRMs->toArray());
                        // print_r($empid); die;
                        // $sql_crm = "select empusrid from tblemployee where designation='18'";
                        // $get_crm = mysqli_query($objdb->link_id,$sql_crm);
                        // while($fetch_crm = mysqli_fetch_assoc($get_crm)){
                        //     $empid[] = $fetch_crm['empusrid'];
                        // }
                        if(count($empid) > 1) {
                            $getLastAssignedCRM = UploadLeads::whereNotNull('crm_id')->orderBy('added_datetime', 'DESC')->select('crm_id')->first();
                            if(!is_null($getLastAssignedCRM)){
                                $last_crm_id = $getLastAssignedCRM->crm_id;
                                $added_for = $this->get_next($empid,$last_crm_id);
                            } else {
                                $added_for = $this->get_next($empid,$empid[0]);
                            }
                        } elseif (count($empid) == 1) {
                            $added_for = $empid[0];
                        } else {
                            // NO Employee found having BDF designation THROW AN EXCEPTION HERE
                        }
                        // $sql_get_last_lead = mysqli_query($objdb->link_id,"select crm_id from tbl_upload_leads where (crm_id != '' && crm_id is not null) order by modified_datetime desc limit 1");
                        // if(mysqli_num_rows($sql_get_last_lead) > 0){
                        //     $fetch_last_lead = mysqli_fetch_assoc($sql_get_last_lead);
                        //     $last_crm_id = $fetch_last_lead['crm_id'];
                        // }else{
                        //     $last_crm_id = $empid[0];
                        // }
                        
                        // $added_for = get_next($empid,$last_crm_id);
                        $crm_id = $added_for;

                        // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`latitude`,`longitude`,`photo`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$datetime','$datetime','$txt1','$txt2','$final1','$datetime')";

                        $leadFollowup = new LeadFollowUp();
                        $leadFollowup->upload_lead_id = $lead_id;
                        $leadFollowup->followup_added_by = $added_by;
                        $leadFollowup->followup_added_for = $added_for;
                        $leadFollowup->previous_status = $previous_status;
                        $leadFollowup->current_status = $status;
                        $leadFollowup->is_person = $first_conf;
                        $leadFollowup->first_no_reason = $first_no_reason;
                        $leadFollowup->chat_now = $chat_now;
                        $leadFollowup->is_sell_online = $online_selling;
                        $leadFollowup->portal_name = $protal_name;
                        $leadFollowup->want_to_register = $register;
                        $leadFollowup->appointment_date = $appointment_date;
                        $leadFollowup->appointment_time = $appointment_time;
                        $leadFollowup->want_help = $help;
                        $leadFollowup->query_remark = $query_remark;
                        $leadFollowup->last_remark = $last_remark;
                        $leadFollowup->recall_date = $recall_date;
                        $leadFollowup->recall_time = $recall_time;
                        $leadFollowup->call_start_time = $datetime;
                        $leadFollowup->call_end_time = $datetime;
                        $leadFollowup->latitude = $latitude;
                        $leadFollowup->longitude = $longitude;
                        $leadFollowup->photo = $image;
                        $leadFollowup->created_at = $datetime;
                        $leadFollowup->save();

                        
                        // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status',added_for='$added_for',crm_id='$crm_id' where id='$lead_id'";
                        
                        $lead_info->call_date = date('Y-m-d', strtotime($datetime));
                        $lead_info->call_time = date('H:i:s', strtotime($datetime));
                        $lead_info->lead_status = $status;
                        $lead_info->crm_id = $crm_id;
                        $lead_info->added_for = $added_for;
                        $lead_info->update();
                        
                        // $insert_seller = "INSERT INTO `tblseller` (`sellername`, `contperson`, `contnum`, `address`, `city`, `state`, `pincode`, `subcategory`, `empid`, `remarks`, `latitude`, `longitude`, `photo`, `visitdate`, `visittime`, `status`, `gst_number`, `designation`, `email`) VALUES ('$txtcmp','$txtcontpr','$txtcontno','$txtcompadd','$txtcity','$txtstate','$txtpin','$txtcat','$added_by','$txtrem','$txt1','$txt2','$final1','$ndate','$ndate',0,'$gst_number','$designation','$email');";
                        // mysqli_query($objdb->link_id,$insert_seller);
                        // $seller_id = mysqli_insert_id($objdb->link_id);
                        
                        $addSeller = new Sellers();
                        $addSeller->sellername = $sellername;
                        $addSeller->contperson = $contperson;
                        $addSeller->contnum = $contnum;
                        $addSeller->address = $address;
                        $addSeller->city = $city;
                        $addSeller->state = $state;
                        $addSeller->pincode = $pincode;
                        $addSeller->subcategory = $subcategory;
                        $addSeller->empid = session()->get('empusrid');
                        $addSeller->remarks = $remarks;
                        $addSeller->latitude = $latitude;
                        $addSeller->longitude = $longitude;
                        $addSeller->photo = $image;
                        $addSeller->visitdate = date('Y-m-d',strtotime($datetime));
                        $addSeller->visittime = date('H:i:s',strtotime($datetime));
                        $addSeller->status = $status;
                        $addSeller->gst_number = $gst_number;
                        $addSeller->designation = $designation;
                        $addSeller->email = $email;
                        $addSeller->user_type = 'employee';
                        $addSeller->save();
                        
                        $seller_id = $addSeller->sid;
                        
                        for ($i = 0; $i < count($cat_id); $i++) {
                            $addSellerCat = new SellerCategory();
                            $addSellerCat->scatid = $cat_id[$i];
                            $addSellerCat->empcode = $added_by;
                            $addSellerCat->sellerid = $seller_id;
                            $addSellerCat->created_by = session()->get('login_id');
                            $addSellerCat->save();
                            // mysqli_query($objdb->link_id,"insert into tblsellercat values (null,'" . $txttype[$i] . "','" . $added_by . "','".$seller_id."')");
                        }
                    }
                    if($selreg == "0" && $selreason == "notintrested"){
                        
                        $status = 11;
                        $getAllCRMs = LMGEmployee::where('designation', 18)->where('offinfo', 2)->select('empusrid')->orderBy('empusrid')->get();
                        $empid = array_map(function($n) { 
                            return $n['empusrid']; 
                        }, $getAllCRMs->toArray());
                        
                        // $sql_crm = "select empusrid from tblemployee where designation='18'";
                        // $get_crm = mysqli_query($objdb->link_id,$sql_crm);
                        // while($fetch_crm = mysqli_fetch_assoc($get_crm)){
                        //     $empid[] = $fetch_crm['empusrid'];
                        // }
                        if(count($empid) > 1) {
                            $getLastAssignedCRM = UploadLeads::whereNotNull('crm_id')->orderBy('added_datetime', 'DESC')->select('crm_id')->first();
                            if(!is_null($getLastAssignedCRM)){
                                $last_crm_id = $getLastAssignedCRM->crm_id;
                                $added_for = $this->get_next($empid,$last_crm_id);
                            } else {
                                $added_for = $this->get_next($empid,$empid[0]);
                            }
                        } elseif (count($empid) == 1) {
                            $added_for = $empid[0];
                        } else {
                            // NO Employee found having BDF designation THROW AN EXCEPTION HERE
                        }
                        
                        $query_remark = $reasondata;
                        $crm_id = $added_for;
                        
                        $appointment_date = $appointment_time = null;
                    
                        // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`latitude`,`longitude`,`photo`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$datetime','$datetime','$txt1','$txt2','$final1','$datetime')";

                        $leadFollowup = new LeadFollowUp();
                        $leadFollowup->upload_lead_id = $lead_id; 
                        $leadFollowup->followup_added_by = $added_by; 
                        $leadFollowup->followup_added_for = $added_for; 
                        $leadFollowup->previous_status = $previous_status; 
                        $leadFollowup->current_status = $status; 
                        $leadFollowup->is_person = $first_conf; 
                        $leadFollowup->first_no_reason = $first_no_reason; 
                        $leadFollowup->chat_now = $chat_now; 
                        $leadFollowup->is_sell_online = $online_selling; 
                        $leadFollowup->portal_name = $protal_name; 
                        $leadFollowup->want_to_register = $register; 
                        $leadFollowup->appointment_date = $appointment_date; 
                        $leadFollowup->appointment_time = $appointment_time; 
                        $leadFollowup->want_help = $help; 
                        $leadFollowup->query_remark = $query_remark; 
                        $leadFollowup->last_remark = $last_remark; 
                        $leadFollowup->recall_date = $recall_date; 
                        $leadFollowup->recall_time = $recall_time; 
                        $leadFollowup->call_start_time = $datetime; 
                        $leadFollowup->call_end_time = $datetime; 
                        $leadFollowup->latitude = $latitude; 
                        $leadFollowup->longitude = $longitude; 
                        $leadFollowup->photo = $image; 
                        $leadFollowup->created_at = $datetime; 
                        $leadFollowup->save();
                        
                        // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status',added_for='$added_for',crm_id='$crm_id' where id='$lead_id'";

                        $lead_info->call_date = $recall_date;
                        $lead_info->call_time = $recall_time;
                        $lead_info->salesperson_id = $salesperson_id;
                        $lead_info->appointment_date = $appointment_date;
                        $lead_info->appointment_time = $appointment_time;
                        $lead_info->lead_status = $status;
                        $lead_info->added_for = $added_for;
                        $lead_info->crm_id = $crm_id;
                        $lead_info->update();
                        
                    }
                    if($selreg == "0" && $selreason == "newfollowup"){
                        $status = 8;
                        $added_for = $added_by;
                        
                        if($appoinment_date == ""){
                            $appointment_date = date("Y-m-d",strtotime("+3 days"));
                        }else{
                            $appointment_date = date("Y-m-d",strtotime($appoinment_date));
                        }
                        
                        if($appoinment_time == ""){
                            $appointment_time = date("H:i:s",strtotime("11:00:00"));
                        }else{
                            $appointment_time = date("H:i:s",strtotime($appoinment_time));
                        }
                        
                        $recall_date = date("Y-m-d",strtotime($appointment_date));
                        $recall_time = date("H:i:s",strtotime($appointment_time." - 30 minutes"));
                        
                        // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`latitude`,`longitude`,`photo`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$datetime','$datetime','$txt1','$txt2','$final1','$datetime')";

                        $leadFollowup = new LeadFollowUp();
                        $leadFollowup->upload_lead_id = $lead_id;
                        $leadFollowup->followup_added_by = $added_by;
                        $leadFollowup->followup_added_for = $added_for;
                        $leadFollowup->previous_status = $previous_status;
                        $leadFollowup->current_status = $status;
                        $leadFollowup->is_person = $first_conf;
                        $leadFollowup->first_no_reason = $first_no_reason;
                        $leadFollowup->chat_now = $chat_now;
                        $leadFollowup->is_sell_online = $online_selling;
                        $leadFollowup->portal_name = $protal_name;
                        $leadFollowup->want_to_register = $register;
                        $leadFollowup->appointment_date = $appointment_date;
                        $leadFollowup->appointment_time = $appointment_time;
                        $leadFollowup->want_help = $help;
                        $leadFollowup->query_remark = $query_remark;
                        $leadFollowup->last_remark = $last_remark;
                        $leadFollowup->recall_date = $recall_date;
                        $leadFollowup->recall_time = $recall_time;
                        $leadFollowup->call_start_time = date('H:i:s', strtotime($datetime)); 
                        $leadFollowup->call_end_time = date('H:i:s', strtotime($datetime)); 
                        $leadFollowup->latitude = $latitude;
                        $leadFollowup->longitude = $longitude;
                        $leadFollowup->photo = $image;
                        $leadFollowup->created_at = $datetime;
                        
                        // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status' where id='$lead_id'";

                        $lead_info->call_date = $recall_date;
                        $lead_info->call_time = $recall_time;
                        $lead_info->salesperson_id = $salesperson_id;
                        $lead_info->appointment_date = $appointment_date;
                        $lead_info->appointment_time = $appointment_time;
                        $lead_info->lead_status = $status;
                        $lead_info->update();
                        
                    }
                    $status = "success";
                    $message = "A Sales Followup record has been saved successfully.";
                    DB::commit();
                } else {
                    // LEAD DATA NOT FOUND
                    $status = "error";
                    $message = "No matching lead data not found the provided lead id.";
                }
            } else {
                $status = "error";
                $message = "Please upload the shop photo";
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

    /**
     * Save call staus from Marketing panel.
     */
    public function saveCallStatus(Request $request): JsonResponse
    {
        // dd($request->all());
        $assignStatus = isset($request->assignStatus) && !empty($request->assignStatus) ? trim($request->assignStatus) : '0';
        $appoitmentstatus = isset($request->appoitmentstatus) && !empty($request->appoitmentstatus) ? trim($request->appoitmentstatus) : '';
        $appoinment_date = isset($request->appoinment_date) && !empty($request->appoinment_date) ? trim($request->appoinment_date) : '';
        $appoinment_time = isset($request->appoinment_time) && !empty($request->appoinment_time) ? trim($request->appoinment_time) : '';
        $recall_date = isset($request->recall_date) && !empty($request->recall_date) ? trim($request->recall_date) : '';
        $recall_time = isset($request->recall_time) && !empty($request->recall_time) ? trim($request->recall_time) : '';
        $lead_id = isset($request->assignModal_lead_id) && !empty($request->assignModal_lead_id) ? trim($request->assignModal_lead_id) : '';

        DB::beginTransaction();
        try {
            if(!empty($lead_id)) 
            {
                $lead_info = UploadLeads::whereId($lead_id)->first();
                if(!is_null($lead_info)) {
    
                    $lead_area = $lead_info->area;
                    $lead_contact_number = $lead_info->contact_number;
    
                    $lead_previous_followup_info = LeadFollowUp::where('upload_lead_id', $lead_id)->orderBy('created_at', "DESC")->first();
                    // $sql_previous_followup = "select * from tbl_lead_followup where upload_lead_id = '".$lead_id."' order by created_at DESC limit 1";
                    // $row_previous_followup = mysqli_query($objdb->link_id,$sql_previous_followup);
                    // $fetch = mysqli_fetch_object($row_previous_followup);
            
                    $added_by = session()->get('empusrid');
            
                    $message = "";
            
                    if($assignStatus == "0" && $appoitmentstatus == ""){
                        $message = "Please select reason.";
                    }
            
                    if($message != ""){
                        $status = "success";
                        $message = $message;
                    }else{
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
                        
                        $previous_status = $lead_info->lead_status;
                        $status = 0;
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
                        
                        $datetime = date("Y-m-d H:i:s");
                        
                        $first_conf = $lead_previous_followup_info->is_person;
                        $first_no_reason = $lead_previous_followup_info->first_no_reason;
                        $chat_now = $lead_previous_followup_info->chat_now;
                        $online_selling = $lead_previous_followup_info->is_sell_online;
                        $protal_name = $lead_previous_followup_info->portal_name;
                        $register = $lead_previous_followup_info->want_to_register;
                        $appointment_date = $lead_previous_followup_info->appointment_date;
                        $appointment_time = $lead_previous_followup_info->appointment_time;
                        $help = $lead_previous_followup_info->want_help;
                        $query_remark = $lead_previous_followup_info->query_remark;
                        $last_remark = $lead_previous_followup_info->last_remark;
                        // $start_call_datetime = $lead_previous_followup_info->is_person;
                        // $end_call_datetime = $lead_previous_followup_info->is_person;
                        
                        if($assignStatus == "1"){

                            $status = 7;
                            $added_for = $added_by;
                            
                            // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$appointment_date','$appointment_time','$datetime','$datetime','$datetime')";
                            $insertFollowUp = new LeadFollowUp();
                            $insertFollowUp->upload_lead_id =  $lead_id;
                            $insertFollowUp->followup_added_by =  $added_by;
                            $insertFollowUp->followup_added_for =  $added_for;
                            $insertFollowUp->previous_status =  $previous_status;
                            $insertFollowUp->current_status =  $status;
                            $insertFollowUp->is_person =  $first_conf;
                            $insertFollowUp->first_no_reason =  $first_no_reason;
                            $insertFollowUp->chat_now =  $chat_now;
                            $insertFollowUp->is_sell_online =  $online_selling;
                            $insertFollowUp->portal_name =  $protal_name;
                            $insertFollowUp->want_to_register =  $register;
                            $insertFollowUp->appointment_date =  $appointment_date;
                            $insertFollowUp->appointment_time =  $appointment_time;
                            $insertFollowUp->want_help =  $help;
                            $insertFollowUp->query_remark =  $query_remark;
                            $insertFollowUp->last_remark =  $last_remark;
                            $insertFollowUp->recall_date =  $recall_date;
                            $insertFollowUp->recall_time =  $recall_time;
                            $insertFollowUp->call_start_time =  $datetime;
                            $insertFollowUp->call_end_time =  $datetime;
                            $insertFollowUp->created_at =  $datetime;
                            $insertFollowUp->save();

                           
                            $lead_info->call_date = date('Y-m-d', strtotime($datetime));
                            $lead_info->call_time = date('H:i:s', strtotime($datetime));
                            $lead_info->appointment_date = $appointment_date;
                            $lead_info->appointment_time = $appointment_time;
                            $lead_info->lead_status = $status;
                            $lead_info->update();

                          
                        }
                        if($assignStatus == "0" && $appoitmentstatus == "cancel"){
                            $status = 5;
                            $salesperson_id = null;
                            // $sql_bd = "select empusrid from tblemployee where designation='12' or designation='13' order by empusrid";
                            // $get_bd = mysqli_query($objdb->link_id,$sql_bd);
                            // while($fetch_bd = mysqli_fetch_assoc($get_bd)){
                            //     $empid[] = $fetch_bd['empusrid'];
                            // }
                            // $all_employees = implode("','",$empid);
                            // $sql_get_last_assigned_bd = mysqli_query($objdb->link_id,"select telecaller_id from tbl_upload_leads where id = '$lead_id'");
                            // $fetch_last_lead = mysqli_fetch_assoc($sql_get_last_assigned_bd);
                            // $last_emp_id = $fetch_last_lead['telecaller_id'];
                            /* $added_for = get_next($empid,$last_emp_id); */
                            if($lead_info->telecaller_id !== null)
                            {
                                $added_for = $lead_info->telecaller_id;
                            }
                            else
                            {
                                $added_for = "";
                            }

                            $appointment_date = null;
                            $appointment_time = null;
                        
                            // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$datetime','$datetime','$datetime')";
                            
                            $insertFollowUp = new LeadFollowUp();
                            $insertFollowUp->upload_lead_id = $lead_id;
                            $insertFollowUp->followup_added_by = $added_by;
                            $insertFollowUp->followup_added_for = $added_for;
                            $insertFollowUp->previous_status = $previous_status;
                            $insertFollowUp->current_status = $status;
                            $insertFollowUp->is_person = $first_conf;
                            $insertFollowUp->first_no_reason = $first_no_reason;
                            $insertFollowUp->chat_now = $chat_now;
                            $insertFollowUp->is_sell_online = $online_selling;
                            $insertFollowUp->portal_name = $protal_name;
                            $insertFollowUp->want_to_register = $register;
                            $insertFollowUp->appointment_date = $appointment_date;
                            $insertFollowUp->appointment_time = $appointment_time;
                            $insertFollowUp->want_help = $help;
                            $insertFollowUp->query_remark = $query_remark;
                            $insertFollowUp->last_remark = $last_remark;
                            $insertFollowUp->recall_date = $recall_date;
                            $insertFollowUp->recall_time = $recall_time;
                            $insertFollowUp->call_start_time = $datetime;
                            $insertFollowUp->call_end_time = $datetime;
                            $insertFollowUp->created_at = $datetime;
                            $insertFollowUp->save();

                            // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', salesperson_id='0', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status',added_for='$added_for' where id='$lead_id'";

                            $lead_info->call_date = date('Y-m-d', strtotime($datetime));
                            $lead_info->call_time = date('H:i:s', strtotime($datetime));
                            $lead_info->salesperson_id = $salesperson_id;
                            $lead_info->appointment_date = $appointment_date;
                            $lead_info->appointment_time = $appointment_time;
                            $lead_info->lead_status = $status;
                            $lead_info->added_for = $added_for;
                            $lead_info->update();
                        }
                        if($assignStatus == "0" && $appoitmentstatus == "new"){
                            $status = 8;
                            $added_for = $added_by;
                            
                            if($appoinment_date == ""){
                                $appointment_date = date("Y-m-d",strtotime("+3 days"));
                            }else{
                                $appointment_date = date("Y-m-d",strtotime($appoinment_date));
                            }
                            
                            if($appoinment_time == ""){
                                $appointment_time = date("H:i:s",strtotime("11:00:00"));
                            }else{
                                $appointment_time = date("H:i:s",strtotime($appoinment_time));
                            }
                            
                            $recall_date = date("Y-m-d",strtotime($appointment_date));
                            $recall_time = date("H:i:s",strtotime($appointment_time." - 30 minutes"));

                            
                            // $insert_followup = "insert into tbl_lead_followup (`upload_lead_id`,`followup_added_by`,`followup_added_for`,`previous_status`,`current_status`,`is_person`,`first_no_reason`,`chat_now`,`is_sell_online`,`portal_name`,`want_to_register`,`appointment_date`,`appointment_time`,`want_help`,`query_remark`,`last_remark`,`recall_date`,`recall_time`,`call_start_time`,`call_end_time`,`created_at`) values ('$lead_id','$added_by','$added_for','$previous_status','$status','$first_conf','$first_no_reason','$chat_now','$online_selling','$protal_name','$register','$appointment_date','$appointment_time','$help','$query_remark','$last_remark','$recall_date','$recall_time','$datetime','$datetime','$datetime')";
                            $insertFollowUp = new LeadFollowUp();
                            $insertFollowUp->upload_lead_id = $lead_id;
                            $insertFollowUp->followup_added_by = $added_by;
                            $insertFollowUp->followup_added_for = $added_for;
                            $insertFollowUp->previous_status = $previous_status;
                            $insertFollowUp->current_status = $status;
                            $insertFollowUp->is_person = $first_conf;
                            $insertFollowUp->first_no_reason = $first_no_reason;
                            $insertFollowUp->chat_now = $chat_now;
                            $insertFollowUp->is_sell_online = $online_selling;
                            $insertFollowUp->portal_name = $protal_name;
                            $insertFollowUp->want_to_register = $register;
                            $insertFollowUp->appointment_date = $appointment_date;
                            $insertFollowUp->appointment_time = $appointment_time;
                            $insertFollowUp->want_help = $help;
                            $insertFollowUp->query_remark = $query_remark;
                            $insertFollowUp->last_remark = $last_remark;
                            $insertFollowUp->recall_date = $recall_date;
                            $insertFollowUp->recall_time = $recall_time;
                            $insertFollowUp->call_start_time = $datetime;
                            $insertFollowUp->call_end_time = $datetime;
                            $insertFollowUp->created_at = $datetime;
                            $insertFollowUp->save();
                            
                            // $update_lead = "update tbl_upload_leads set call_date ='$recall_date', call_time='$recall_time', appointment_date='$appointment_date', appointment_time='$appointment_time',lead_status='$status' where id='$lead_id'";

                            // echo "<br/>".$insert_followup;
                            // echo "<br/>".$update_lead;
                            // die;
                            $lead_info->call_date = $recall_date;
                            $lead_info->call_time = $recall_time;
                            $lead_info->appointment_date = $appointment_date;
                            $lead_info->appointment_time = $appointment_time;
                            $lead_info->lead_status = $status;
                            $lead_info->update();
                            
                        }
                        
                        $status = "success";
                        $message = "Marketing Lead meeting status changes has been completed successfully.";
                    }
                    DB::commit();
                } else {
                    // LEAD DATA NOT FOUND
                    $status = "error";
                    $message = "No matching lead data not found the provided lead id.";
                }
            }
            else 
            {
                // LEAD ID CANNOT BE NULL
                $status = "error";
                $message = "Lead id cannot be null";
            }
            DB::commit();
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to update the lead record as of now. MESSAGE: ".$e->getMessage();
            DB::rollback();
        }
        tag: 
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
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
}
