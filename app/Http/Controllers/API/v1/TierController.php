<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Lid;

use App\Models\User;

use DB;

class TierController extends Controller
{
    public function getTiersUser($user_id)
    {
        return Lid::where(['user_id' => $user_id, 'docs_compl' => 1])->get();
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
