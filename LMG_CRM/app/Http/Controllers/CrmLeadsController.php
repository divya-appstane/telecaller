<?php

namespace App\Http\Controllers;

use App\Imports\LeadsImport;
use App\Models\AreaMaster;
use App\Models\Category;
use App\Models\LeadFollowUp;
use App\Models\LeadsCategory;
use App\Models\UploadLeads;
use App\Models\LeadsStatus;
use App\Models\LMGEmployee;
use App\Models\Territory2;
use App\Models\CRMFeedbackMaster;
use App\Models\FollowUpFeedback;
use Exception;
use Route;
use Session;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CrmLeadsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // dd(session()->all());
        $title = "CRM | View all Leads";
        $empusrid = session()->get('empusrid');
        $all_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [10,11,12,13,14,15,16,17,18,19,20])->orderby('id','DESC')->get();
        return view('user.crm.leads.listAllLeads', compact('title', 'all_leads'));
    }

    public function show($id)
    {
        //
        $lead_id = base64_decode($id);
        $title = "CRM | View Single Lead";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        // dd($single_lead->toArray());
        return view('user.crm.leads.viewSingle', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }

    /**
     * Display a listing of the resource.
     */
    public function getPendingLeads()
    {
        $title = "CRM | View Pending Leads";
        $empusrid = session()->get('empusrid');
        if(session()->exists('is_admin')){
            $pending_leads = UploadLeads::with('getLeadStatus')->get();
        } 
        else 
        {
            if(Route::getCurrentRoute()->getName() == "crm.leads.view.pendingLeads")
            {
                $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [10,12])->orderby('id','DESC')->get();
            }
            if(Route::getCurrentRoute()->getName() == "crm.leads.view.pendingLeadsFeedback2")
            {
                $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [13,14])->orderby('id','DESC')->get();
            }
            if(Route::getCurrentRoute()->getName() == "crm.leads.view.pendingLeadsFeedback3")
            {
                $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [15,16,17])->orderby('id','DESC')->get();
            }
            if(Route::getCurrentRoute()->getName() == "crm.leads.view.pendingLeadsRegister")
            {
                $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [19,20])->orderby('id','DESC')->get();
            }
            if(Route::getCurrentRoute()->getName() == "crm.leads.view.pendingLeadsNotIn")
            {
                $pending_leads = UploadLeads::with('getLeadStatus')->where('added_for', $empusrid)->whereIn('lead_status', [11,18])->orderby('id','DESC')->get();
            }
        }
        // dd($pending_leads->toArray());
        return view('user.crm.leads.listPendingLeads', compact('title', 'pending_leads'));
    }

    public function feedbackCall($id)
    {
        $lead_id = base64_decode($id);
        $title = "CRM | Feedback Call View";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $feedback_data = CRMFeedbackMaster::where('question_display', 'Feedback 1')->orderBy('question_no','ASC')->get();
        return view('user.crm.leads.feedbackCallView', compact('title', 'single_lead', 'all_categories', 'all_areas','feedback_data'));
    }

    public function feedbackCallStepTwo($id)
    {
        $lead_id = base64_decode($id);
        $title = "CRM | Feedback Call View Step Two";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $feedback_data_step_two = CRMFeedbackMaster::where('question_display', 'Feedback 2')->orderBy('question_no','ASC')->get();
        return view('user.crm.leads.feedbackCallViewStepTwo', compact('title', 'single_lead', 'all_categories', 'all_areas','feedback_data_step_two'));
    }

    public function feedbackCallStepThree($id)
    {
        $lead_id = base64_decode($id);
        $title = "CRM | Feedback Call View Step Three";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $feedback_data_step_three = CRMFeedbackMaster::where('question_display', 'Feedback 3')->orderBy('question_no','ASC')->get();
        return view('user.crm.leads.feedbackCallViewStepThree', compact('title', 'single_lead', 'all_categories', 'all_areas','feedback_data_step_three'));
    }

    public function feedbackCallNotIn($id)
    {
        $lead_id = base64_decode($id);
        $title = "CRM | Feedback Call View Not Intersted";
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $feedback_data_notin = CRMFeedbackMaster::where('question_display', 'Not Interested')->orderBy('question_no','ASC')->get();
        return view('user.crm.leads.feedbackCallViewNotIntersted', compact('title', 'single_lead', 'all_categories', 'all_areas','feedback_data_notin'));
    }

    public function feedbackCallRegister($id)
    {
        $lead_id = base64_decode($id);
        $title = "CRM | Feedback Call View Intersted";
        $all_categories = Category::get();
        $all_areas = AreaMaster::where('isactive', 1)->get();
        $single_lead = UploadLeads::with('getLeadCategory')->whereId($lead_id)->first();
        return view('user.crm.leads.feedbackCallViewRegister', compact('title', 'single_lead', 'all_categories', 'all_areas'));
    }


    public function getFollowupList($id)
    {
        //
        $lead_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "CRM | View Lead Follow-up";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('upload_lead_id', $lead_id)->with(['getLeadPreviousStatus', 'getLeadCurrentStatus'])->orderBy('followup_id', 'DESC')->get();
        // dd($leadFollowup->toArray());
        return view('user.crm.leads.viewFollowUp', compact('title', 'leadFollowup', 'lead_id'));
    }
    
    /**
     * Display a listing of the resource.
     */
    public function getSingleFollowupData($id)
    {
        //
        $followup_id = base64_decode($id);
        // echo $lead_id; die;
        $title = "CRM | View Lead Follow-up Details";
        // $empusrid = session()->get('empusrid');
        $leadFollowup = LeadFollowUp::where('followup_id', $followup_id)->first();
        // dd($leadFollowup->toArray());
        return view('user.crm.leads.viewSingleFollowup', compact('title', 'leadFollowup'));
    }


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
                "email" => "email", 
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

    public function store(Request $request): JsonResponse
    {
       
        $inputs = $request->all();

        DB::beginTransaction();
        try{
            $is_recall = $inputs['is_recall'];
            $recall_date = $inputs['recall_date'];
            $recall_time = $inputs['recall_time'];
            $last_remark = $inputs['last_remark'];
            $start_call_datetime = $inputs['start_call_datetime'];
            $end_call_datetime = $inputs['end_call_datetime'];
            $lead_id = $inputs['upload_lead_id'];

            $sql_lead = UploadLeads::whereId($lead_id)->first();

            $lead_area = $sql_lead->area;
            $lead_contact_number = $sql_lead->contact_number;
            

            $sql_previous_followup = LeadFollowUp::where('upload_lead_id',$lead_id )->orderBy('created_at','DESC')->first();

            
            $added_by = session()->get('empusrid');

            $message = "";

            if($is_recall == "")
            {
                $message = "Please select yes or no.";
            }

            if($message != "")
            {
                $d = array(
                    "status"=>"error",
                    "message"=>$message
                );
            }
            else
            {
                $previous_status = $sql_lead->lead_status;
                $status = 0;
                $salesperson_id = 0;
                $added_for = $added_by;

                if($recall_date == "")
                {
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                }
                else
                {
                    $recall_date = date("Y-m-d",strtotime($recall_date));
                }
                
                if($recall_time == "")
                {
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                }
                else
                {
                    $recall_time = date("H:i:s",strtotime($recall_time));
                }
                
                $datetime = date("Y-m-d H:i:s");

                $first_conf = $sql_previous_followup->is_person;
                $first_no_reason = $sql_previous_followup->first_no_reason;
                $chat_now = $sql_previous_followup->chat_now;
                $online_selling = $sql_previous_followup->is_sell_online;
                $protal_name = $sql_previous_followup->portal_name;
                $register = $sql_previous_followup->want_to_register;
                $appointment_date = $sql_previous_followup->appointment_date;
                $appointment_time = $sql_previous_followup->appointment_time;
                $help = $sql_previous_followup->want_help;
                $query_remark = $sql_previous_followup->query_remark;
                $last_remark = $sql_previous_followup->last_remark;

                if($is_recall == "Y")
                {
                    $status = 12;
                    $added_for = $added_by;
                    
                    $appointment_date = NULL;
                    $appointment_time = Null;
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();             
                }

                if($is_recall == "N")
                {
                    $status = 13;
                    $added_for = $added_by;
                    $appointment_date = NULL;
                    $appointment_time = NULL;
                    
                    $recall_date = date("Y-m-d",strtotime("+15 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));


                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();

                    if($insert_followup->id == NULL)
                    {
                        $followup_id = 0;
                    }
                    else
                    {
                        $followup_id = $insert_followup->id;
                    }


                    
                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();  

                    
                    $sql_feedback = CRMFeedbackMaster::where('question_display', 'Feedback 1')->orderBy('question_no','ASC')->get();
                    
                    
                    foreach($sql_feedback as $feedback)
                    {
                        $feedback_id = $feedback->feedback_id;
                        $question_display = $feedback->question_display;
                        
                        $fid = "question_".$feedback_id;
                        $ar = "question_remark_".$feedback_id;
                        $question = $feedback->question;
                        $answer = $request->$fid;
                        $answer_remark = $request->$ar;

                        
                        $sql_insert_feedback = new FollowUpFeedback();
                        $sql_insert_feedback->lead_id = $lead_id;
                        $sql_insert_feedback->followup_id =  $followup_id;
                        $sql_insert_feedback->feedback_id = $feedback_id;
                        $sql_insert_feedback->feedback_display = $question_display;
                        $sql_insert_feedback->question = $question;
                        $sql_insert_feedback->answer = $answer;
                        $sql_insert_feedback->remark = $answer_remark;
                        $sql_insert_feedback->feedback_added_by = $added_by;
                        $sql_insert_feedback->datetime = $datetime;
                        $sql_insert_feedback->save();
                    }
                }
            }
            Session::forget('timer');
            Session::forget("redirecturl");

            DB::commit();
           
            $status = "success";
            $message = "Followup completed successfully.";
            
        }catch (Exception $e) {
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

    public function storetwo(Request $request): JsonResponse
    {
       
        $inputs = $request->all();
        
        DB::beginTransaction();
        try{
            $is_recall = $inputs['is_recall'];
            $recall_date = $inputs['recall_date'];
            $recall_time = $inputs['recall_time'];
            $last_remark = $inputs['last_remark'];
            $start_call_datetime = $inputs['start_call_datetime'];
            $end_call_datetime = $inputs['end_call_datetime'];
            $lead_id = $inputs['upload_lead_id'];
    
            $sql_lead = UploadLeads::whereId($lead_id)->first();
    
            $lead_area = $sql_lead->area;
            $lead_contact_number = $sql_lead->contact_number;
            
    
            $sql_previous_followup = LeadFollowUp::where('upload_lead_id',$lead_id )->orderBy('created_at','DESC')->first();
    
            
            $added_by = session()->get('empusrid');
    
            $message = "";
    
            if($is_recall == "")
            {
                $message = "Please select yes or no.";
            }
    
            if($message != "")
            {
                $d = array(
                    "status"=>"error",
                    "message"=>$message
                );
            }
            else
            {
                $previous_status = $sql_lead->lead_status;
                $status = 0;
                $salesperson_id = 0;
                $added_for = $added_by;
    
                if($recall_date == "")
                {
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                }
                else
                {
                    $recall_date = date("Y-m-d",strtotime($recall_date));
                }
                
                if($recall_time == "")
                {
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                }
                else
                {
                    $recall_time = date("H:i:s",strtotime($recall_time));
                }
                
                $datetime = date("Y-m-d H:i:s");
    
                $first_conf = $sql_previous_followup->is_person;
                $first_no_reason = $sql_previous_followup->first_no_reason;
                $chat_now = $sql_previous_followup->chat_now;
                $online_selling = $sql_previous_followup->is_sell_online;
                $protal_name = $sql_previous_followup->portal_name;
                $register = $sql_previous_followup->want_to_register;
                $appointment_date = $sql_previous_followup->appointment_date;
                $appointment_time = $sql_previous_followup->appointment_time;
                $help = $sql_previous_followup->want_help;
                $query_remark = $sql_previous_followup->query_remark;
                $last_remark = $sql_previous_followup->last_remark;
    
                if($is_recall == "Y")
                {
                    $status = 14;
                    $added_for = $added_by;
                    
                    $appointment_date = NULL;
                    $appointment_time = Null;
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();
    
    
                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();             
                }
    
                if($is_recall == "N")
                {
                    $status = 15;
                    $added_for = $added_by;
                    $appointment_date = NULL;
                    $appointment_time = NULL;
                    
                    $recall_date = date("Y-m-d",strtotime("+15 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));

    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();
    
    
                    if($insert_followup->id == NULL)
                    {
                        $followup_id = 0;
                    }
                    else
                    {
                        $followup_id = $insert_followup->id;
                    }
    
                    
                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();  
    
                    
                    $sql_feedback = CRMFeedbackMaster::where('question_display', 'Feedback 2')->orderBy('question_no','ASC')->get();
                    
                    
                    foreach($sql_feedback as $feedback)
                    {
                       
                        $feedback_id = $feedback->feedback_id;
                        $question_display = $feedback->question_display;
                        
                        $fid = "question_".$feedback_id;
                        $ar = "question_remark_".$feedback_id;
                        $question = $feedback->question;
                        $answer = $request->$fid;
                        $answer_remark = $request->$ar;
    
                        
                        $sql_insert_feedback = new FollowUpFeedback();
                        $sql_insert_feedback->lead_id = $lead_id;
                        $sql_insert_feedback->followup_id = $followup_id;
                        $sql_insert_feedback->feedback_id = $feedback_id;
                        $sql_insert_feedback->feedback_display = $question_display;
                        $sql_insert_feedback->question = $question;
                        $sql_insert_feedback->answer = $answer;
                        $sql_insert_feedback->remark = $answer_remark;
                        $sql_insert_feedback->feedback_added_by = $added_by;
                        $sql_insert_feedback->datetime = $datetime;
                        $sql_insert_feedback->save();
                      
                    }
                }
            }
            Session::forget('timer');
            Session::forget("redirecturl"); 

            DB::commit();
            $status = "success";
            $message = "Followup completed successfully.";
        }catch (Exception $e) {
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

    public function storethree(Request $request): JsonResponse
    {
       
        $inputs = $request->all();

        DB::beginTransaction();
        try{

            $is_recall = $inputs['is_recall'];
            $recall_date = $inputs['recall_date'];
            $recall_time = $inputs['recall_time'];
            $last_remark = $inputs['last_remark'];
            $start_call_datetime = $inputs['start_call_datetime'];
            $end_call_datetime = $inputs['end_call_datetime'];
            $lead_id = $inputs['upload_lead_id'];

            $sql_lead = UploadLeads::whereId($lead_id)->first();

            $lead_area = $sql_lead->area;
            $lead_contact_number = $sql_lead->contact_number;
            

            $sql_previous_followup = LeadFollowUp::where('upload_lead_id',$lead_id )->orderBy('created_at','DESC')->first();

            
            $added_by = session()->get('empusrid');

            $message = "";

            if($is_recall == "")
            {
                $message = "Please select yes or no.";
            }

            if($message != "")
            {
                $d = array(
                    "status"=>"error",
                    "message"=>$message
                );
            }
            else
            {
                $previous_status = $sql_lead->lead_status;
                $status = 0;
                $salesperson_id = 0;
                $added_for = $added_by;

                if($recall_date == "")
                {
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                }
                else
                {
                    $recall_date = date("Y-m-d",strtotime($recall_date));
                }
                
                if($recall_time == "")
                {
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                }
                else
                {
                    $recall_time = date("H:i:s",strtotime($recall_time));
                }
            
                $datetime = date("Y-m-d H:i:s");

                $first_conf = $sql_previous_followup->is_person;
                $first_no_reason = $sql_previous_followup->first_no_reason;
                $chat_now = $sql_previous_followup->chat_now;
                $online_selling = $sql_previous_followup->is_sell_online;
                $protal_name = $sql_previous_followup->portal_name;
                $register = $sql_previous_followup->want_to_register;
                $appointment_date = $sql_previous_followup->appointment_date;
                $appointment_time = $sql_previous_followup->appointment_time;
                $help = $sql_previous_followup->want_help;
                $query_remark = $sql_previous_followup->query_remark;
                $last_remark = $sql_previous_followup->last_remark;

                if($is_recall == "Y")
                {
                    $status = 16;
                    $added_for = $added_by;
                    
                    $appointment_date = NULL;
                    $appointment_time = Null;
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();             
                }

                if($is_recall == "N")
                {
                    $status = 19;
                    $added_for = $added_by;
                    $appointment_date = NULL;
                    $appointment_time = NULL;

                    $sql_crm = LMGEmployee::select('empusrid')->where('designation', session()->get('user_designation_id'))->get();
                    
                    foreach($sql_crm as $sc)
                    {
                        $empid[] = $sc->empusrid;
                    }

                    
                    $sql_get_last_lead = UploadLeads::select('crm_id')->where(function ($query) {
                                        $query->where('crm_id','!=','')
                                            ->whereNotNull('crm_id');
                    })->orderBy('modified_datetime','desc')->limit(1)->get();
                
        


                    if(count($sql_get_last_lead) > 0)
                    {
                        $last_crm_id = $sql_get_last_lead[0]['crm_id'];
                    }
                    else
                    {
                        $last_crm_id = $empid[0];
                    }
                

                    $added_for = $this->get_next($empid,$last_crm_id);
                    $crm_id = $added_for;

                    $recall_date = date("Y-m-d",strtotime("+30 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));

                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    if($insert_followup->id == NULL)
                    {
                        $followup_id = 0;
                    }
                    else
                    {
                        $followup_id = $insert_followup->id;
                    }

                    
                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();  

                    
                    $sql_feedback = CRMFeedbackMaster::where('question_display', 'Feedback 3')->orderBy('question_no','ASC')->get();
                    
                    
                    foreach($sql_feedback as $feedback)
                    {
                    
                        $feedback_id = $feedback->feedback_id;
                        $question_display = $feedback->question_display;
                        
                        $fid = "question_".$feedback_id;
                        $ar = "question_remark_".$feedback_id;
                        $question = $feedback->question;
                        $answer = $request->$fid;
                        $answer_remark = $request->$ar;

                        
                        $sql_insert_feedback = new FollowUpFeedback();
                        $sql_insert_feedback->lead_id = $lead_id;
                        $sql_insert_feedback->followup_id = $followup_id;
                        $sql_insert_feedback->feedback_id = $feedback_id;
                        $sql_insert_feedback->feedback_display = $question_display;
                        $sql_insert_feedback->question = $question;
                        $sql_insert_feedback->answer = $answer;
                        $sql_insert_feedback->remark = $answer_remark;
                        $sql_insert_feedback->feedback_added_by = $added_by;
                        $sql_insert_feedback->datetime = $datetime;
                        $sql_insert_feedback->save();
                    
                    }
                } 
            }
            Session::forget('timer');
            Session::forget("redirecturl");

            DB::commit();
            $status = "success";
            $message = "Followup completed successfully.";
        }catch (Exception $e) {
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

    public function storenotin(Request $request): JsonResponse
    {
       
        $inputs = $request->all();

        DB::beginTransaction();
        try{
            $is_recall = $inputs['is_recall'];
            $recall_date = $inputs['recall_date'];
            $recall_time = $inputs['recall_time'];
            $selintrested = $inputs['selintrested'];
            $last_remark = $inputs['last_remark'];
            $start_call_datetime = $inputs['start_call_datetime'];
            $end_call_datetime = $inputs['end_call_datetime'];
            $lead_id = $inputs['upload_lead_id'];
            $appointment_date1 = $inputs['appointment_date'];
            $appointment_time = $inputs['appointment_time'];

            $appointment_date = date("Y-m-d", strtotime($appointment_date1));  

        

            $sql_lead = UploadLeads::whereId($lead_id)->first();

            $lead_area = $sql_lead->area;
            $lead_contact_number = $sql_lead->contact_number;
            $telecaller_id = $sql_lead->telecaller_id;
            $salesperson_id = $sql_lead->salesperson_id;
            

            $sql_previous_followup = LeadFollowUp::where('upload_lead_id',$lead_id )->orderBy('created_at','DESC')->first();

            
            $added_by = session()->get('empusrid');

            $message = "";

            if($is_recall == "")
            {
                $message = "Please select yes or no.";
            }

            if($message != "")
            {
                $d = array(
                    "status"=>"error",
                    "message"=>$message
                );
            }
            else
            {
                $previous_status = $sql_lead->lead_status;
                $status = 0;
                $salesperson_id = 0;
                $added_for = $added_by;

                if($recall_date == "")
                {
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                }
                else
                {
                    $recall_date = date("Y-m-d",strtotime($recall_date));
                }
                
                if($recall_time == "")
                {
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                }
                else
                {
                    $recall_time = date("H:i:s",strtotime($recall_time));
                }
                
                $datetime = date("Y-m-d H:i:s");

                $first_conf = $sql_previous_followup->is_person;
                $first_no_reason = $sql_previous_followup->first_no_reason;
                $chat_now = $sql_previous_followup->chat_now;
                $online_selling = $sql_previous_followup->is_sell_online;
                $protal_name = $sql_previous_followup->portal_name;
                $register = $sql_previous_followup->want_to_register;
                $help = $sql_previous_followup->want_help;
                $query_remark = $sql_previous_followup->query_remark;
                $last_remark = $sql_previous_followup->last_remark;

                if($is_recall == "Y")
                {

                    $status = 18;
                    $added_for = $added_by;
                    
                    $appointment_date = NULL;
                    $appointment_time = Null;
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();             
                }

                if($is_recall == "N" && $selintrested == "1")
                {

                    $getAllBDF = LMGEmployee::where([['designation',15],['offinfo', '2']])->select('empusrid')->get();

                    $empid = array_map(function($n) { 
                        return $n['empusrid']; 
                    }, $getAllBDF->toArray());

                    if(count($empid) > 1) 
                    {
                        $areaWiseEmpids = array();
                        foreach($empid as $emp) {
                            $getAreaWiseEmp = Territory2::where('agentcode', $emp)->first();
                            if(!is_null($getAreaWiseEmp))
                            {
                                $areaWiseEmpids[] = $getAreaWiseEmp->agentcode;
                            }
                        }
                        $getLastAssignedBDF = UploadLeads::whereNotNull('salesperson_id')->orderBy('added_datetime', 'DESC')->select('salesperson_id')->first();
                        if(!is_null($getLastAssignedBDF)){
                            $last_bdf_id = $getLastAssignedBDF->salesperson_id;
                            if(count($areaWiseEmpids) > 1) 
                            {
                                $added_for = $this->get_next($areaWiseEmpids, $last_bdf_id);
                            } 
                            else if(count($areaWiseEmpids) == 1) 
                            {
                                $added_for = $areaWiseEmpids[0];
                            } 
                            else 
                            {
                                $added_for = $this->get_next($empid,$last_bdf_id);
                            }
                        } 
                        else 
                        {
                            $added_for = ((count($areaWiseEmpids) > 1) ? $this->get_next($areaWiseEmpids, $areaWiseEmpids[0]) : ((count($areaWiseEmpids) == 1) ? $areaWiseEmpids[0] :  $this->get_next($empid,$empid[0])));
                        }
                    } 
                    elseif (count($empid) == 1) 
                    {
                        $added_for = $empid[0];
                    } 
                    else 
                    {
                        // NO Employee found having BDF designation THROW AN EXCEPTION HERE
                    }

                    $status = 6;
                    // $added_for = $salesperson_id;

                  
                    

                    $recall_date = date("Y-m-d",strtotime("+7 days"));
                    
                    $recall_time = date("H:i:s",strtotime("11:00:00"));



                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appointment_date;
                    $insert_followup->appointment_time = $appointment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    if($insert_followup->id == NULL)
                    {
                        $followup_id = 0;
                    }
                    else
                    {
                        $followup_id = $insert_followup->id;
                    }

                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appointment_date; 
                    $uploadLeadData->appointment_time = $appointment_time; 
                    $uploadLeadData->lead_status = $status;
                    $uploadLeadData->crm_id = NULL;
                    $uploadLeadData->salesperson_id = $added_for;
                    $uploadLeadData->added_for = $added_for;
                    $uploadLeadData->update();  

                    
                    $sql_feedback = CRMFeedbackMaster::where('question_display', 'Not Interested')->orderBy('question_no','ASC')->get();
                    
                    
                    foreach($sql_feedback as $feedback)
                    {
                    
                        $feedback_id = $feedback->feedback_id;
                        $question_display = $feedback->question_display;
                        
                        $fid = "question_".$feedback_id;
                        $ar = "question_remark_".$feedback_id;
                        $question = $feedback->question;
                        $answer = $request->$fid;
                        $answer_remark = $request->$ar;

                        
                        $sql_insert_feedback = new FollowUpFeedback();
                        $sql_insert_feedback->lead_id = $lead_id;
                        $sql_insert_feedback->followup_id = $followup_id;
                        $sql_insert_feedback->feedback_id = $feedback_id;
                        $sql_insert_feedback->feedback_display = $question_display;
                        $sql_insert_feedback->question = $question;
                        $sql_insert_feedback->answer = $answer;
                        $sql_insert_feedback->remark = $answer_remark;
                        $sql_insert_feedback->feedback_added_by = $added_by;
                        $sql_insert_feedback->datetime = $datetime;
                        $sql_insert_feedback->save();
                    
                    }
                }
                if($is_recall == "N" && $selintrested == "0")
                {
                    $status = 4;

                    $recall_date = date("Y-m-d",strtotime("+7 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                    $appoinment_date = NULL;
                    $appoinment_time = NULL;

                    $sql_crm = LMGEmployee::select('empusrid')->where('designation', 12)->orWhere('designation',13)->get();
                    
                    foreach($sql_crm as $sc)
                    {
                        $empid[] = $sc->empusrid;
                    }

                    
                    $sql_get_last_lead = UploadLeads::select('crm_id')->where(function ($query) {
                                        $query->where('crm_id','!=','')
                                            ->whereNotNull('crm_id');
                    })->orderBy('modified_datetime','desc')->limit(1)->get();
                
                

                    $added_for = $this->get_next($empid,$telecaller_id);
                    $crm_id = $added_for;

                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = $appoinment_date;
                    $insert_followup->appointment_time = $appoinment_time;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    if($insert_followup->id == NULL)
                    {
                        $followup_id = 0;
                    }
                    else
                    {
                        $followup_id = $insert_followup->id;
                    }

                    
                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = $appoinment_date; 
                    $uploadLeadData->appointment_time = $appoinment_time; 
                    $uploadLeadData->lead_status = $status;
                    $uploadLeadData->telecaller_id = $added_for; 
                    $uploadLeadData->crm_id = NULL;
                    $uploadLeadData->salesperson_id = NULL;
                    $uploadLeadData->added_for = $added_for;

                    $uploadLeadData->update();  

                    
                    $sql_feedback = CRMFeedbackMaster::where('question_display', 'Not Interested')->orderBy('question_no','ASC')->get();
                    
                    
                    foreach($sql_feedback as $feedback)
                    {
                    
                        $feedback_id = $feedback->feedback_id;
                        $question_display = $feedback->question_display;
                        
                        $fid = "question_".$feedback_id;
                        $ar = "question_remark_".$feedback_id;
                        $question = $feedback->question;
                        $answer = $request->$fid;
                        $answer_remark = $request->$ar;

                        
                        $sql_insert_feedback = new FollowUpFeedback();
                        $sql_insert_feedback->lead_id = $lead_id;
                        $sql_insert_feedback->followup_id = $followup_id;
                        $sql_insert_feedback->feedback_id = $feedback_id;
                        $sql_insert_feedback->feedback_display = $question_display;
                        $sql_insert_feedback->question = $question;
                        $sql_insert_feedback->answer = $answer;
                        $sql_insert_feedback->remark = $answer_remark;
                        $sql_insert_feedback->feedback_added_by = $added_by;
                        $sql_insert_feedback->datetime = $datetime;
                        $sql_insert_feedback->save();
                    
                    }
                }
            }
            Session::forget('timer');
            Session::forget("redirecturl"); 

            DB::commit();
            $status = "success";
            $message = "Followup completed successfully.";
            
        }catch (Exception $e) {
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

    public function storeregister(Request $request): JsonResponse
    {
       
        $inputs = $request->all();


        DB::beginTransaction();
        try{
            $is_recall = $inputs['is_recall'];
            $recall_date = $inputs['recall_date'];
            $recall_time = $inputs['recall_time'];
            $feedback_remarks = $inputs['feedback_remarks'];
            $last_remark = $inputs['last_remark'];
            $start_call_datetime = $inputs['start_call_datetime'];
            $end_call_datetime = $inputs['end_call_datetime'];
            $lead_id = $inputs['upload_lead_id'];


            $sql_lead = UploadLeads::whereId($lead_id)->first();

            $lead_area = $sql_lead->area;
            $lead_contact_number = $sql_lead->contact_number;
            $telecaller_id = $sql_lead->telecaller_id;
            $salesperson_id = $sql_lead->salesperson_id;
            

            $sql_previous_followup = LeadFollowUp::where('upload_lead_id',$lead_id )->orderBy('created_at','DESC')->first();

            
            $added_by = session()->get('empusrid');

            $message = "";

            if($is_recall == "")
            {
                $message = "Please select yes or no.";
            }

            if($message != "")
            {
                $d = array(
                    "status"=>"error",
                    "message"=>$message
                );
            }
            else
            {
                $previous_status = $sql_lead->lead_status;
                $status = 0;
                $salesperson_id = 0;
                $added_for = $added_by;

                if($recall_date == "")
                {
                    $recall_date = date("Y-m-d",strtotime("+1 days"));
                }
                else
                {
                    $recall_date = date("Y-m-d",strtotime($recall_date));
                }
                
                if($recall_time == "")
                {
                    $recall_time = date("H:i:s",strtotime("11:00:00"));
                }
                else
                {
                    $recall_time = date("H:i:s",strtotime($recall_time));
                }
                
                $datetime = date("Y-m-d H:i:s");

                $first_conf = $sql_previous_followup->is_person;
                $first_no_reason = $sql_previous_followup->first_no_reason;
                $chat_now = $sql_previous_followup->chat_now;
                $online_selling = $sql_previous_followup->is_sell_online;
                $protal_name = $sql_previous_followup->portal_name;
                $register = $sql_previous_followup->want_to_register;
                $help = $sql_previous_followup->want_help;
                $query_remark = $sql_previous_followup->query_remark;

                if($is_recall == "Y")
                {

                    $status = 20;
                    $added_for = $added_by;
                
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = NULL;
                    $insert_followup->appointment_time = NULL;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $query_remark;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = NULL; 
                    $uploadLeadData->appointment_time = NULL;
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->update();             
                }

                if($is_recall == "N")
                {
                    $status = 19;

                    $recall_date = date("Y-m-d",strtotime("+30 days"));
                    $recall_time = date("H:i:s",strtotime("11:00:00"));

                    $added_for = $added_by;


                    $getAllCRMs = LMGEmployee::where('designation', 18)->where('offinfo', 2)->select('empusrid')->orderBy('empusrid')->get();
                    $empid = array_map(function($n) { 
                        return $n['empusrid']; 
                    }, $getAllCRMs->toArray());
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
                    $crm_id = $added_for;
                    
                    $insert_followup = new LeadFollowUp();
                    $insert_followup->upload_lead_id = $lead_id;
                    $insert_followup->followup_added_by = $added_by;
                    $insert_followup->followup_added_for = $added_for;
                    $insert_followup->previous_status = $previous_status;
                    $insert_followup->current_status = $status;
                    $insert_followup->is_person = $first_conf;
                    $insert_followup->first_no_reason = $first_no_reason;
                    $insert_followup->chat_now = $chat_now;
                    $insert_followup->is_sell_online = $online_selling;
                    $insert_followup->portal_name = $protal_name;
                    $insert_followup->want_to_register = $register;
                    $insert_followup->appointment_date = NULL;
                    $insert_followup->appointment_time = NULL;
                    $insert_followup->want_help = $help;
                    $insert_followup->query_remark = $feedback_remarks;
                    $insert_followup->last_remark = $last_remark;
                    $insert_followup->recall_date = $recall_date;
                    $insert_followup->recall_time = $recall_time;
                    $insert_followup->call_start_time = $start_call_datetime;
                    $insert_followup->call_end_time = $end_call_datetime;
                    $insert_followup->created_at = $datetime;
                    $insert_followup->save();


                    $uploadLeadData = UploadLeads::whereId($lead_id)->first();
                    $uploadLeadData->call_date = $recall_date;
                    $uploadLeadData->call_time = $recall_time; 
                    $uploadLeadData->appointment_date = NULL; 
                    $uploadLeadData->appointment_time = NULL;
                    $uploadLeadData->lead_status = $status; 
                    $uploadLeadData->crm_id = $crm_id;
                    $uploadLeadData->added_for = $crm_id;
                    $uploadLeadData->update();   


                }
            }
            Session::forget('timer');
            Session::forget("redirecturl");
            
            DB::commit();
            $status = "success";
            $message = "Followup completed successfully.";
            
        }catch (Exception $e) {
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

      //user defined function
      function get_next($array, $key) {
	
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

    public function createSession(Request $request)
    {
        Session::put("timer", date('H:i:s'));
        Session::put("redirecturl", $request['sucurl']);
        return Session::get("timer");
    }
}
