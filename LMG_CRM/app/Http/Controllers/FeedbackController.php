<?php

namespace App\Http\Controllers;

use App\Models\FeedbackMaster;
use App\Models\FollowUpFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Exception;

class FeedbackController extends Controller
{
    public function index()
    {
        $title = "User | View all CRM Feedbacks";
        $empusrid = session()->get('empusrid');
        $all_feedback = FeedbackMaster::get();
        return view('user.feedback.listAllFeedback', compact('title', 'all_feedback'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $title = "User | Add New CRM Feedback";
        $all_feedback = FeedbackMaster::all();
        return view('user.feedback.addFeedback', compact('title','all_feedback'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) :JsonResponse
    {
        $request->validate(
            [
                "question" => "required", 
                "question_type" => "required",
                "question_display" => "required",
                "question_order" => "required"
            ],
            [
                "question.required" => "Area name cannot be empty.",
                "question_type.required" => "You must select an active area status.",
                "question_display.required" => "You must select an active area status.",
                "question_order.required" => "You must select an active area status.",
            ]
        );
        $inputs = $request->all();

        DB::beginTransaction();
        try{
            $chkfeedback = FeedbackMaster::where('question_no',$inputs['question_order'])->where('question_display',$inputs['question_display'])->exists();
            if(!$chkfeedback)
            {
                $feedbackData = new FeedbackMaster();
                $feedbackData->question = $inputs['question'];
                $feedbackData->question_type = $inputs['question_type'];
                $feedbackData->question_display = $inputs['question_display'];
                $feedbackData->question_no = $inputs['question_order'];
                $feedbackData->create_datetime = date("Y-m-d H:i:s");
                $feedbackData->save();

                DB::commit();
                $status = "success";
                $message = "An feedback record has been added successfully.";
            }
            else
            {
                $status = "error";
                $message = "Question already exist.";
            }
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to add the feedback record as of now.".$e->getMessage();
            DB::rollback();
        }
        return response()->json([
            'status' => $status,
            'message' => $message,
            'icon' => $status,
        ]);
    }


    public function changeOrder(Request $request) : JsonResponse
    {
        $rules = [
            'question_display' => "required",
        ];

        $message = [
            "question_display.required" => "Question Display cannot be null",
        ];
        $validator = Validator::make($request->all(), $rules, $message);
        if($validator->fails()) {
            $status = 'error';
            $message = $validator->errors()->first();
        } else {
            $question_display = $request->question_display;
            try {
                $feedbackdata = FeedbackMaster::where('question_display', $question_display)->max('question_no');
                DB::beginTransaction();
                if(!is_null($feedbackdata)) {
                    DB::commit();
                    if($feedbackdata == null || $feedbackdata == "" || $feedbackdata == 0){
                        $feedbackdata = 1;
                    }else{
                        $feedbackdata++;
                    }
                    
                    echo $feedbackdata;
                    exit;
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



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request) : JsonResponse
    {
        // $area_id = json_decode($id);
        $feedback_id = $request->id;
        DB::beginTransaction();
        try{
            FeedbackMaster::where('feedback_id',$feedback_id)->delete();
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


     /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $title = "User | Edit CRM Feedback";
        $feedback_id = base64_decode($id);
        $all_feedback = FeedbackMaster::all();
        $singlefeedback = FeedbackMaster::where('feedback_id',$feedback_id)->first();
        return view('user.feedback.editFeedback', compact(['title', 'all_feedback','singlefeedback']));
    }


    public function update(Request $request): JsonResponse
    {
        $request->validate(
            [
                "question" => "required", 
                "question_type" => "required",
                "question_display" => "required",
                "question_order" => "required"
            ],
            [
                "question.required" => "Area name cannot be empty.",
                "question_type.required" => "You must select an active area status.",
                "question_display.required" => "You must select an active area status.",
                "question_order.required" => "You must select an active area status.",
            ]
        );

        $inputs = $request->all();

        DB::beginTransaction();
        try {
            $feedbackid = $inputs['feedbackid'];
            $feedbackData = FeedbackMaster::where('feedback_id',$feedbackid)->first();
            $feedbackData->question = $inputs['question'];
            $feedbackData->question_type = $inputs['question_type'];
            $feedbackData->question_display = $inputs['question_display'];
            $feedbackData->question_no = $inputs['question_order'];
            $feedbackData->update_datetime = date("Y-m-d H:i:s");
            $feedbackData->update();

            DB::commit();
            $status = "success";
            $message = "An feedback record has been edited successfully.";
        } catch (Exception $e) {
            $status = "error";
            $message = "Unable to update the feedback record as of now.".$e->getMessage();
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
    public function followupFeedback($id)
    {
        $feedback_id = base64_decode($id);
        $title = "User | View Feedback Follow-up";
        $feedbackFollowup = FollowUpFeedback::where('feedback_id', $feedback_id)->orderBy('id','DESC')->get();

        return view('user.feedback.viewFollowUpFeedback', compact('title', 'feedbackFollowup', 'feedback_id'));
    }
}
