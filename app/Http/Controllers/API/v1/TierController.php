<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lid;
use App\Models\User;
use DB;

class TierController extends Controller
{
    public function getTiersDates(Request $request)
    {
        $data = $request->all();

        $sql = "SELECT `status_id`,`user_id`,`text`,`created_at`,`lid_id`,(SELECT COUNT(id) FROM `tierdoc` td WHERE td.`user_id` = l.`user_id` AND  td.`created_at` BETWEEN '" . $data['dateFrom'] . " 00:00:00' AND '" . $data['dateTo'] . " 23:59:59') docs FROM `logs` l WHERE `created_at` BETWEEN '" . $data['dateFrom'] . " 00:00:00' AND '" . $data['dateTo'] . " 23:59:59' AND `text` = 'docs_compl 1'";

        DB::select($sql);
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
