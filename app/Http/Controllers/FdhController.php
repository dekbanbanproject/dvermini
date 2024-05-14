<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\support\Facades\Hash;
use Illuminate\support\Facades\Validator;
use App\Models\User;
use App\Models\Ot_one;
use PDF;
use setasign\Fpdi\Fpdi;
use App\Models\Budget_year;
use Illuminate\Support\Facades\File;
use DataTables;
use Intervention\Image\ImageManagerStatic as Image;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OtExport;
// use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Department;
use App\Models\Departmentsub;
use App\Models\Departmentsubsub;
use App\Models\Position;
use App\Models\Product_spyprice;
use App\Models\Products;
use App\Models\Products_type;
use App\Models\Product_group;
use App\Models\Product_unit;
use App\Models\Products_category;
use App\Models\Article;
use App\Models\Product_prop;
use App\Models\Product_decline;
use App\Models\Department_sub_sub;
use App\Models\Products_vendor;
use App\Models\Status;
use App\Models\Products_request;
use App\Models\Products_request_sub;
use App\Models\Leave_leader;
use App\Models\Leave_leader_sub;
use App\Models\Book_type;
use App\Models\Book_import_fam;
use App\Models\Book_signature;
use App\Models\Bookrep;
use App\Models\Book_objective;
use App\Models\Book_senddep;
use App\Models\Book_senddep_sub;
use App\Models\Book_send_person;
use App\Models\Book_sendteam;
use App\Models\Bookrepdelete;
use App\Models\Car_status;
use App\Models\Car_index;
use App\Models\Article_status;
use App\Models\Car_type;
use App\Models\Product_brand;
use App\Models\Product_color;
use App\Models\Land;
use App\Models\Building;
use App\Models\Product_budget;
use App\Models\Product_method;
use App\Models\Product_buy;
use App\Models\Users_prefix;
use App\Models\D_fdh_opd;
use App\Models\D_fdh_ipd;
use App\Models\D_fdh;
use App\Models\D_ins;
use App\Models\D_pat;
use App\Models\D_opd;
use App\Models\D_orf;
use App\Models\D_odx;
use App\Models\D_cht;
use App\Models\D_cha;
use App\Models\D_oop;
use App\Models\D_claim;
use App\Models\D_adp;
use App\Models\D_dru;
use App\Models\D_idx;
use App\Models\D_iop;
use App\Models\D_ipd;
use App\Models\D_aer;
use App\Models\D_irf;
use App\Models\D_ofc_401;
use App\Models\D_ucep24_main;
use App\Models\D_ucep24;
use App\Models\Acc_ucep24;
use App\Models\Fdh_ins;
use App\Models\Fdh_pat;
use App\Models\Fdh_opd;
use App\Models\Fdh_orf;
use App\Models\Fdh_odx;
use App\Models\Fdh_cht;
use App\Models\Fdh_cha;
use App\Models\Fdh_oop;
use App\Models\Fdh_adp;
use App\Models\Fdh_dru;
use App\Models\Fdh_idx;
use App\Models\Fdh_iop;
use App\Models\Fdh_ipd;
use App\Models\Fdh_aer;
use App\Models\Fdh_irf;
use App\Models\Fdh_lvd;
use App\Models\D_dru_out;
use App\Models\Fdh_mini_dataset;
use App\Models\Api_neweclaim;

use App\Imports\ImportAcc_stm_ti;
use App\Imports\ImportAcc_stm_tiexcel_import;
use App\Imports\ImportAcc_stm_ofcexcel_import;
use App\Imports\ImportAcc_stm_lgoexcel_import;
use App\Models\Acc_1102050101_217_stam;
use App\Models\Acc_opitemrece_stm;
use SplFileObject;
use PHPExcel;
use PHPExcel_IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\IOFactory;
use ZipArchive;
use Illuminate\Support\Facades\Redirect;
use PhpParser\Node\Stmt\If_;
use Stevebauman\Location\Facades\Location;

use Auth;
use Http;
use SoapClient;
use Arr;
use GuzzleHttp\Client;
use Illuminate\Filesystem\Filesystem;


use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;

class FdhController extends Controller
{ 
    public function fdh_checksit(Request $request)
    {
        $datestart = $request->datestart;
        $dateend = $request->dateend;
        $date = date('Y-m-d');

        $data_sitss = DB::connection('mysql')->select('SELECT vn,an,cid,vstdate,dchdate FROM d_fdh WHERE active = "N" AND subinscl IS NULL GROUP BY cid');

        $token_data = DB::connection('mysql2')->select('SELECT * FROM nhso_token ORDER BY update_datetime desc limit 1');
        foreach ($token_data as $key => $value) {
            $cid_    = $value->cid;
            $token_  = $value->token;
        }
        foreach ($data_sitss as $key => $item) {
            $pids = $item->cid;
            $vn   = $item->vn;
            $an   = $item->an;

            $client = new SoapClient(
                "http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?wsdl",
                array("uri" => 'http://ucws.nhso.go.th/ucwstokenp1/UCWSTokenP1?xsd=1', "trace" => 1, "exceptions" => 0, "cache_wsdl" => 0)
            );
            $params = array(
                'sequence' => array(
                    "user_person_id"   => "$cid_",
                    "smctoken"         => "$token_",
                    // "user_person_id" => "$value->cid",
                    // "smctoken"       => "$value->token",
                    "person_id"        => "$pids"
                )
            );
            $contents = $client->__soapCall('searchCurrentByPID', $params);
            foreach ($contents as $v) {
                @$status = $v->status;
                @$maininscl = $v->maininscl;
                @$startdate = $v->startdate;
                @$hmain = $v->hmain;
                @$subinscl = $v->subinscl;
                @$person_id_nhso = $v->person_id;

                @$hmain_op = $v->hmain_op;  //"10978"
                @$hmain_op_name = $v->hmain_op_name;  //"รพ.ภูเขียวเฉลิมพระเกียรติ"
                @$hsub = $v->hsub;    //"04047"
                @$hsub_name = $v->hsub_name;   //"รพ.สต.แดงสว่าง"
                @$subinscl_name = $v->subinscl_name; //"ช่วงอายุ 12-59 ปี"

                if (@$maininscl == "" || @$maininscl == null || @$status == "003") { #ถ้าเป็นค่าว่างไม่ต้อง insert
                    $date = date("Y-m-d");

                    D_fdh::where('cid', $pids)
                        ->update([
                            // 'status'         => 'จำหน่าย/เสียชีวิต',
                            // 'maininscl'      => @$maininscl,
                            // 'pttype_spsch'   => @$subinscl,
                            // 'hmain'          => @$hmain,
                            'subinscl'       => @$subinscl,
                        ]);
                } elseif (@$maininscl != "" || @$subinscl != "") {
                    D_fdh::where('cid', $pids)
                        ->update([
                            //    'status'         => @$status,
                            //    'maininscl'      => @$maininscl,
                            //    'pttype_spsch'   => @$subinscl,
                            //    'hmain'          => @$hmain,
                            'subinscl'       => @$subinscl,

                        ]);
                }
            }
        }

        return response()->json([

            'status'    => '200'
        ]);
    }
     

    // **********************************************************
    public function fdh_mini_dataset(Request $request)
    {
        $startdate = $request->startdate;
        $enddate = $request->enddate;

        $date = date('Y-m-d');
        $y = date('Y') + 543;
        $newweek = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี

        $data_auth = DB::connection('mysql')->select('
            SELECT *
            FROM api_neweclaim
        ');
        return view('fdh.fdh_mini_dataset', [
            'startdate'        => $startdate,
            'enddate'          => $enddate,
            'data_auth'        => $data_auth,
        ]);
    }
 

    public function fdh_mini_dataset_pull(Request $request)
    {
        $startdate   = $request->startdate;
        $enddate     = $request->enddate;
        if ($startdate == '') {
        } else {
            $date = date('Y-m-d');
          
            $datashow_ = DB::connection('mysql2')->select(
                'SELECT v.vstdate,o.vsttime
                    ,Time_format(o.vsttime ,"%H:%i") vsttime2
                    ,v.cid,"10978" as hcode
                    ,rd.total_amount as total_amout
                    ,rd.finance_number as invoice_number
                    ,v.vn,concat(pt.pname,pt.fname," ",pt.lname) as ptname,v.hn,v.pttype
                    FROM vn_stat v 
                    LEFT OUTER JOIN ovst o ON v.vn = o.vn 
                    LEFT OUTER JOIN patient pt on pt.hn = v.hn
                    LEFT OUTER JOIN pttype ptt ON v.pttype=ptt.pttype   
                    LEFT OUTER JOIN rcpt_debt rd ON v.vn = rd.vn 
                WHERE o.vstdate BETWEEN "' . $startdate . '" and "' . $enddate . '"  
                AND ptt.hipdata_code ="UCS" AND v.income > 0
                GROUP BY o.vn 
            '
            );
            // AND v.pttype NOT IN("M1","M2","M3","M4","M5") 
            foreach ($datashow_ as $key => $value) {
                $check_opd = Fdh_mini_dataset::where('vn', $value->vn)->count();
                if ($check_opd > 0) {
                    Fdh_mini_dataset::where('vn', $value->vn)->update([   
                        'pttype'              => $value->pttype,
                        'total_amout'         => $value->total_amout,
                        'invoice_number'      => $value->invoice_number, 
                    ]);
                } else {
                    Fdh_mini_dataset::insert([
                        'service_date_time'   => $value->vstdate . ' ' . $value->vsttime,
                        'cid'                 => $value->cid,
                        'hcode'               => $value->hcode,
                        'total_amout'         => $value->total_amout,
                        'invoice_number'      => $value->invoice_number,
                        'vn'                  => $value->vn,
                        'pttype'              => $value->pttype,
                        'ptname'              => $value->ptname,
                        'hn'                  => $value->hn,
                        'vstdate'             => $value->vstdate,
                        'vsttime'             => $value->vsttime,
                        'datesave'            => $date,
                         
                    ]);
                }
            }
        }
        $data['fdh_mini_dataset']    = DB::connection('mysql')->select('SELECT * from fdh_mini_dataset WHERE active ="N" ORDER BY total_amout DESC');

        return view('fdh.fdh_mini_dataset_pull',$data, [
            'startdate'        => $startdate,
            'enddate'          => $enddate,
        ]);
    }

    public function fdh_mini_dataset_pullnoinv(Request $request)
    {
        $startdate   = $request->startdate;
        $enddate     = $request->enddate;
        if ($startdate == '') {
            # code...
        } else {
            $date = date('Y-m-d');
        
            $datashow_ = DB::connection('mysql10')->select(
                'SELECT v.vstdate,o.vsttime
                    ,Time_format(o.vsttime ,"%H:%i") vsttime2
                    ,v.cid,"10978" as hcode
                    ,IFNULL(rd.total_amount,v.income) as total_amout
                    ,IFNULL(rd.finance_number,v.vn) as invoice_number
                    ,v.vn,concat(pt.pname,pt.fname," ",pt.lname) as ptname,v.hn,v.pttype
                    FROM vn_stat v 
                    LEFT OUTER JOIN ovst o ON v.vn = o.vn 
                    LEFT OUTER JOIN patient pt on pt.hn = v.hn
                    LEFT OUTER JOIN pttype ptt ON v.pttype = ptt.pttype 
                    LEFT OUTER JOIN rcpt_debt rd ON v.vn = rd.vn 
                WHERE o.vstdate BETWEEN "' . $startdate . '" and "' . $enddate . '"  
                AND ptt.hipdata_code ="UCS" AND v.income > 0 and rd.finance_number IS NULL 
                GROUP BY o.vn 
            '
            );
            
            foreach ($datashow_ as $key => $value) {
                $check_opd = Fdh_mini_dataset::where('vn', $value->vn)->count();
                if ($check_opd > 0) {
                    Fdh_mini_dataset::where('vn', $value->vn)->update([  
                        'pttype'              => $value->pttype, 
                        'total_amout'         => $value->total_amout,
                        'invoice_number'      => $value->invoice_number, 
                    ]);
                } else {
                    Fdh_mini_dataset::insert([
                        'service_date_time'   => $value->vstdate . ' ' . $value->vsttime,
                        'cid'                 => $value->cid,
                        'hcode'               => $value->hcode,
                        'total_amout'         => $value->total_amout,
                        'invoice_number'      => $value->invoice_number,
                        'vn'                  => $value->vn,
                        'pttype'              => $value->pttype,
                        'ptname'              => $value->ptname,
                        'hn'                  => $value->hn,
                        'vstdate'             => $value->vstdate,
                        'vsttime'             => $value->vsttime,
                        'datesave'            => $date, 
                    ]);
                }
            }
        }
        $data['fdh_mini_dataset']    = DB::connection('mysql')->select('SELECT * from fdh_mini_dataset WHERE active ="N" ORDER BY total_amout DESC');
        return response()->json([
            'status'     => '200'
        ]);
    }
    public function fdh_mini_dataset_api(Request $request)
    {
        $ip = $request->ip();
        $username = $request->username;
        $password = $request->password;

        if ($ip == '::1') {
            $username        = $request->username;
            $password        = $request->password;
            $password_hash   = strtoupper(hash_hmac('sha256', $password, '$jwt@moph#'));

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fdh.moph.go.th/token?Action=get_moph_access_token&user=' . $username . '&password_hash=' . $password_hash . '&hospital_code=10978',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: __cfruid=bedad7ad2fc9095d4827bc7be4f52f209543768f-1714445470'
                ),
            ));
            $token = curl_exec($curl);
            // dd($token); 
            curl_close($curl);

            $check = Api_neweclaim::where('api_neweclaim_user', $username)->where('api_neweclaim_pass', $password)->count();
            if ($check > 0) {
                Api_neweclaim::where('api_neweclaim_user', $username)->update([
                    'api_neweclaim_token'       => $token,
                    // 'user_id'                   => Auth::user()->id,
                    'password_hash'             => $password_hash,
                    'hospital_code'             => '50178',
                ]);
            } else {
                Api_neweclaim::insert([
                    'api_neweclaim_user'        => $username,
                    'api_neweclaim_pass'        => $password,
                    'api_neweclaim_token'       => $token,
                    'password_hash'             => $password_hash,
                    'hospital_code'             => '50178',
                    // 'user_id'                   => Auth::user()->id,
                ]);
            }
        } else {

            $username        = $request->username;
            $password        = $request->password;
            $password_hash   = strtoupper(hash_hmac('sha256', $password, '$jwt@moph#'));

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://fdh.moph.go.th/token?Action=get_moph_access_token&user=' . $username . '&password_hash=' . $password_hash . '&hospital_code=10978',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: __cfruid=bedad7ad2fc9095d4827bc7be4f52f209543768f-1714445470'
                ),
            ));
            $token = curl_exec($curl);
            // dd($token); 
            curl_close($curl);
     
            $check = Api_neweclaim::where('api_neweclaim_user', $username)->where('api_neweclaim_pass', $password)->count();
            if ($check > 0) {
                Api_neweclaim::where('api_neweclaim_user', $username)->update([
                    'api_neweclaim_token'       => $token,
                    // 'user_id'                   => Auth::user()->id,
                    'password_hash'             => $password_hash,
                    'hospital_code'             => '50178',
                ]);
            } else {
                Api_neweclaim::insert([
                    'api_neweclaim_user'        => $username,
                    'api_neweclaim_pass'        => $password,
                    'api_neweclaim_token'       => $token,
                    'password_hash'             => $password_hash,
                    'hospital_code'             => '50178',
                    // 'user_id'                   => Auth::user()->id,
                ]);
            }
        }

        return response()->json([
            'status'     => '200'
        ]);
    }

    // ************************** จองเคลม **************
    public function fdh_mini_dataset_apicliam(Request $request)
    {
        $id = $request->ids; 
        $data_vn_1 = Fdh_mini_dataset::whereIn('fdh_mini_dataset_id', explode(",", $id))->get();
        // $data_token_ = DB::connection('mysql')->select(' SELECT * FROM api_neweclaim WHERE user_id = "' . $iduser . '"');
        $data_token_ = DB::connection('mysql')->select(' SELECT * FROM api_neweclaim');
        foreach ($data_token_ as $key => $val_to) {
            $token_   = $val_to->api_neweclaim_token;
        }
        $token = $token_;

        $startcount = 1;
        $data_claim = array();
        foreach ($data_vn_1 as $key => $val) {
            $service_date_time_      = $val->service_date_time;

            $service_date_time    = substr($service_date_time_,0,16);
            $cid                  = $val->cid;
            $hcode                = $val->hcode;
            $total_amout          = $val->total_amout;
            $invoice_number       = $val->invoice_number;
            $vn                   = $val->vn;

       
        $curl = curl_init();
        $postData_send = [ 
            "service_date_time"  => $service_date_time,
            "cid"                => $cid,
            "hcode"              => $hcode,
            "total_amout"        => $total_amout,
            "invoice_number"     => $invoice_number,
            "vn"                 => $vn
            
        ];
            curl_setopt($curl, CURLOPT_URL,"https://fdh.moph.go.th/api/v1/reservation");
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($postData_send, JSON_UNESCAPED_SLASHES));
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Authorization: Bearer '.$token,
                'Cookie: __cfruid=bedad7ad2fc9095d4827bc7be4f52f209543768f-1714445470'
            ));
  
            $server_output     = curl_exec ($curl);
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            
            $content = $server_output;
            $result = json_decode($content, true);
            #echo "<BR>";
            @$status = $result['status'];
            #echo "<BR>";
            @$message = $result['message'];
            @$data = $result['data'];
            @$uid = $data['transaction_uid'];
            #echo "<BR>";
            if (@$message == 'success') {
                    Fdh_mini_dataset::where('vn', $vn)
                    ->update([
                        'transaction_uid' =>  @$uid,
                        'active'          => 'Y'
                    ]); 
            } elseif ($status == '400') {
                    Fdh_mini_dataset::where('vn', $vn)
                        ->update([
                            'transaction_uid' =>  @$uid,
                            'active'          => 'Y'
                        ]);
            } else {
                # code...
            }
        }
            // dd($result);
            return response()->json([
                'status'    => '200'
            ]);
              

    }

    public function fdh_mini_dataset_rep(Request $request)
    {
        $startdate   = $request->startdate;
        $enddate     = $request->enddate;
        $date        = date('Y-m-d');
        $y           = date('Y') + 543;
        $newdays     = date('Y-m-d', strtotime($date . ' -1 days')); //ย้อนหลัง 1 วัน
        $newweek     = date('Y-m-d', strtotime($date . ' -1 week')); //ย้อนหลัง 1 สัปดาห์
        $newDate     = date('Y-m-d', strtotime($date . ' -5 months')); //ย้อนหลัง 5 เดือน
        $newyear     = date('Y-m-d', strtotime($date . ' -1 year')); //ย้อนหลัง 1 ปี
 
        if ($startdate == '') {
            $data['fdh_mini_dataset']    = DB::connection('mysql')->select('SELECT * from fdh_mini_dataset WHERE vstdate BETWEEN "'.$newdays.'" AND "'.$date.'" AND transaction_uid IS NOT NULL ORDER BY vstdate DESC');
        } else {
            $data['fdh_mini_dataset']    = DB::connection('mysql')->select('SELECT * from fdh_mini_dataset WHERE vstdate BETWEEN "'.$startdate.'" AND "'.$enddate.'" AND transaction_uid IS NOT NULL ORDER BY vstdate DESC');            
        }
        

        return view('fdh.fdh_mini_dataset_rep',$data, [
            'startdate'        => $startdate,
            'enddate'          => $enddate, 
        ]);
    }
    public function fdh_mini_dataset_pulljong(Request $request)
    {
        $id = $request->ids;
      
        $data_vn_1 = Fdh_mini_dataset::whereIn('fdh_mini_dataset_id', explode(",", $id))->get();
        $data_token_ = DB::connection('mysql')->select(' SELECT * FROM api_neweclaim WHERE user_id = "' . $iduser . '"');
        foreach ($data_token_ as $key => $val_to) {
            $token_   = $val_to->api_neweclaim_token;
        }
        $token = $token_;
 
            foreach ($data_vn_1 as $key => $val) { 
                $transaction_uid      = $val->transaction_uid;
                $hcode                = $val->hcode; 

                    $curl = curl_init(); 
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://fdh.moph.go.th/api/v1/reservation',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'GET',
                        CURLOPT_POSTFIELDS => '{
                            "transaction_uid": "'.$transaction_uid.'", 
                            "hcode"          : "'.$hcode.'" 
                        }',
                        CURLOPT_HTTPHEADER => array(
                            'Content-Type: application/json',
                            'Authorization: Bearer '.$token,
                            'Cookie: __cfruid=bedad7ad2fc9095d4827bc7be4f52f209543768f-1714445470'
                        ),
                    ));
                    $response = curl_exec($curl);
                    // dd($response); 
                    $result            = json_decode($response, true); 
                    @$status           = $result['status']; 
                    @$message          = $result['message'];
                    @$data             = $result['data'];
                    @$uidrep           = $data['transaction_uid'];
                    @$id_booking       = $data['id_booking'];
                    @$uuid_booking     = $data['uuid_booking']; 
                    if (@$message == 'success') {
                            Fdh_mini_dataset::where('transaction_uid', $uidrep)
                            ->update([
                                'id_booking'     => @$id_booking,
                                'uuid_booking'   => @$uuid_booking
                            ]);  
                    } elseif ($status == '400') {
                            Fdh_mini_dataset::where('transaction_uid', $uidrep)
                                ->update([
                                    'id_booking'     => @$id_booking,
                                    'uuid_booking'   => @$uuid_booking
                                ]);
                    } else {
                        # code...
                    }
            }
            // dd($result);
            return response()->json([
                'status'    => '200'
            ]);
           
    }

     
     
}
