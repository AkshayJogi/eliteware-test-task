<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use  App\Http\Requests\EmployeeRequest;
use App\Models\User;

class EmployeeController extends Controller
{

    /**
     * datatable listing of employee
     * @param type request
     * @return array
     */
    public function index(Request $request){
        if ($request->ajax()) {
            $data = User::select('*');

            if(!empty($request->start_date) || !empty($request->end_date)){
                $data->whereDate('joining_date','>=',$request->start_date)->whereDate('joining_date','<=',$request->end_date);
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('joining_date', function ($row) {
                    return $this->getFormatedDate($row->joining_date,'d-m-Y');
                 })
                ->editColumn('image', function ($row) {
                    $imgUrl = url('uploads').'/'.$row->image;
                    return "<img src='".$imgUrl."' height='50' width='100'>";
                 })
                ->rawColumns(['action','image'])
                ->make(true);
        }
        return view('home');        
    }

    /**
     * save employee details
     * @param type request
     * @return array
     */
    public function saveEmployee(EmployeeRequest $request){
        try {
            $user = new User();
            $user->employee_code = $this->employeeCode();            
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->joining_date = $this->getFormatedDate($request->joining_date,'Y-m-d');
            $fileResponse = $this->uploadFile($request->image);

            if($fileResponse['success']){
                $user->image = $fileResponse['fileName'];
            }

            $user->save();

            return response()->json(['success' => true,
                'message' => 'Employee details saved successfully.'
            ], 200);

        } catch(\Exception $e){
            echo $e->getLine().'=='.$e->getMessage();
            return response()->json(['success' => false,
                'message' => 'Opps! Something went wrong, please try again.'
            ], 200);
        }
    }

    /*
     * function for upload images.
     */
    public function uploadFile($image)
    {
        try {
            $destination = public_path() . '/uploads/';
            if (!is_dir($destination)) {
                File::makeDirectory($destination, $mode = 0777, true, true);
            }
            $imageName = time() . rand(11111, 99999) . '.' . $image->getClientOriginalExtension();

            $fileName = str_replace(" ", "-", $imageName);
            if (!file_exists($destination . '/' . $fileName)) {
                if ($image->move($destination, $fileName)) {
                    return ['success' => true, 'fileName' => $fileName];
                } else {
                    return ['success' => false, 'message' => "Error in uploading file."];
                }
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * convert date to database date format
     * @param type $string,$format
     * @return date
     */
    public function getFormatedDate($string, $format)
    {
        return Carbon::parse($string)->format($format);
    }

    /**
     * create employee code
     * @return string
     */
    public function employeeCode()
    {
        $userCount = User::count();
        $userCount += 1;

        $num = sprintf("%02d", $userCount);

        return 'EMP-'.$num;
    }    
}
