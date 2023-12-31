<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lid;
use App\Models\User;
use App\Models\Log;

use DB;

class TierController extends Controller
{
    public function getLogUserDates(Request $request)
    {
        $res = [];
        $data = $request->all();
        $datefrom =  $data['datefrom'] . " 00:00:00";
        $dateto =  $data['dateto'] . " 23:59:59";
        $user_id =  $data['user_id'];
        $res['logs'] =  Log::select('status_id')->selectRaw('Date(created_at) as created')->where('user_id', $user_id)->whereBetween('created_at', array($datefrom, $dateto))->orderBy('created_at', 'DESC')->get();

        return $res;
    }

    public function getTiersDates(Request $request)
    {
        $data = $request->all();
        $datefrom =  $data['dateFrom'] . " 00:00:00";
        $dateto =  $data['dateTo'] . " 23:59:59";
        $res = [];

        $sql = "SELECT user_id,date(created_at) created FROM `tierdoc` td WHERE td.`created_at` BETWEEN '" . $datefrom . "' AND '" . $dateto . "' ORDER BY created DESC";
        // $sql = "SELECT COUNT(*) hm_docs FROM `tierdoc` td WHERE  td.`created_at` BETWEEN '" . $datefrom . "' AND '" . $dateto . "' ";
        $res['docs_user_ids'] = DB::select($sql);

        $sql = "SELECT `id`,`name`,`order`,`color` FROM statuses ";
        $res['statuses'] = DB::select($sql);

        $logs = Log::select('user_id')->whereBetween('created_at', array($datefrom, $dateto))->groupBy('user_id')->get();
        $res['users'] = User::select('id', 'name', 'office_id', 'group_id')->where('tier', 1)->whereIn('id', $logs)->get();

        $logs = Log::select('lid_id')->whereBetween('created_at', array($datefrom, $dateto))->groupBy('lid_id');
        $res['leads'] = Lid::select('status_id', 'user_id', 'docs_compl', 'id')->whereIn('id', $logs)->orderBy('created_at', 'DESC')->get();

        return $res;
    }


    public function changeUserTiers(Request $request)
    {
        $data = $request->all();

        $office_id = User::where('id', $data['user_id'])->value('office_id');
        $res = 0;
        foreach ($data['lids'] as $lid) {
            $a_lid = [
                'user_id' => $data['user_id'],
                'office_id' => $office_id,
                'updated_at' => Now()
            ];

            $res =  DB::table('lids')->where('id', $lid['id'])->update($a_lid);

            $a_lid['lid_id'] = $lid['id'];
            $a_lid['status_id'] = $lid[' status_id'];
            if (isset($data['message'])) {
                $a_lid['message'] = $data[' message'];
            }
            $a_lid['created_at'] = Now();
            unset($a_lid['office_id']);

            DB::table('logs')->insert($a_lid);
        }
        if ($res) {
            return response('Lids updated', 200);
        }
    }

    public function getTiersUser($user_id)
    {
        return Lid::select('lids.*', 'statuses.name as status')->leftJoin('statuses', 'statuses.id', '=', 'lids.status_id')->where(['user_id' => $user_id, 'docs_compl' => 1])->get();
    }

    public function getUsersTier()
    {
        $office_id = session()->get('office_id');
        $where_office = '';
        if ($office_id > 0) {
            $where_office = ' AND u.office_id = ' . $office_id;
        }
        $res = [];
        $sql = "SELECT u.id,u.role_id,u.office_id,u.`fio`,u.`group_id`,u.`order`,(SELECT COUNT(*) FROM lids l WHERE user_id = u.id ) hm,(SELECT COUNT(*) FROM lids l WHERE user_id = u.id AND l.docs_compl) hm_docs FROM users u WHERE u.id IN (SELECT user_id FROM lids l WHERE docs_compl = 1 GROUP BY user_id) " . $where_office . " ORDER BY u.`group_id`,u.`name` ASC";

        $res['users'] = DB::select($sql);

        $sql = "SELECT u.id,u.role_id,u.office_id,u.`fio`,u.`group_id`,u.`order` FROM users u WHERE u.id IN (SELECT u.group_id FROM users u WHERE u.id IN (SELECT user_id FROM lids l WHERE docs_compl = 1  GROUP BY user_id)) " . $where_office;
        $res['group'] = DB::select($sql);
        return $res;
    }
}
