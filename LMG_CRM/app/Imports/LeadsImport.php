<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\LeadsCategory;
use App\Models\UploadLeads;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Validators\Failure;
use Throwable;

class LeadsImport implements 
    ToCollection, 
    SkipsOnError, 
    WithHeadingRow, 
    WithValidation,
    SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;
    
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        // dd($rows);
        
        // if (!$rows->isEmpty()){
            foreach ($rows as $row) 
            {
                $uploadLeadsData = new UploadLeads();
                $uploadLeadsData["company_name"] = $row['shop_name'];
                $uploadLeadsData["contact_per_name"] = $row['contact_person_name'];
                $uploadLeadsData["contact_number"] = $row['contact_number'];
                $uploadLeadsData["email"] = $row['email'];
                $uploadLeadsData["designation"] = $row['designation'];
                $uploadLeadsData["address"] = $row['address'];
                $uploadLeadsData["state"] = $row['state'];
                $uploadLeadsData["city"] = $row['city'];
                $uploadLeadsData["pincode"] = $row['pincode'];
                $uploadLeadsData["gst_number"] = $row['gst_no'];
                $uploadLeadsData["area"] = $row['area'];
                $uploadLeadsData["remarks"] = $row['remarks'];
                $uploadLeadsData["lead_status"] = '1';
                $uploadLeadsData["added_by"] = session()->get('empusrid');
                $uploadLeadsData["added_for"] = session()->get('empusrid');
                $uploadLeadsData["call_date"] = date('Y-m-d');
                $uploadLeadsData["call_time"] = date('H:i');
                $uploadLeadsData["added_datetime"] = date('Y-m-d H:i:s');
                $uploadLeadsData->save();

                $all_cats = preg_replace('/\s*,\s*/', ',', preg_replace('/\s+/', ' ',$row['category']));
                $category_array = explode(',', $all_cats);

                foreach ($category_array as $category_name) {
                    $categoryData = Category::where('catname',$category_name)->select('cid')->first();
                    if(!is_null($categoryData)){
                        $leadCategory = new LeadsCategory();
                        $leadCategory["upload_lead_id"] = $uploadLeadsData->id;
                        $leadCategory["cat_id"] = $categoryData->cid;
                        $leadCategory["added_by"] = session()->get('empusrid');
                        $leadCategory["added_datetime"] = date('Y-m-d H:i:s');
                        $leadCategory->save();
                    }
                }
            }
        // } else {
        //     throw new Exception('No seller lead data found to be store on server.');
        // }
    }

    public function rules() : array {
        return [
            // "*.email" => ['email', 'unique:tbl_upload_leads,email'],
            "*.contact_number" => ['unique:tbl_upload_leads,contact_number']
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.email' => 'Duplicate :attribute.',
            '*.contact_number' => 'Duplicate Contact number.',
        ];
    }

    // public function onError(Throwable $err){

    // }

    // public function onFailure(Failure ...$failures)
    // {
        
    // }
}
