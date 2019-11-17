<?php

namespace Ministra\Admin\Model;

class StatisticsModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getVideoStatTotalRows($func_alias, $where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->{"getVideoStat{$func_alias}List"}($params, true);
    }
    public function getVideoStatAllList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('video')->where($param['where'])->where(['accessed' => 1])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getVideoStatGenreList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $date_obj = new \DateTime('midnight 30 days ago');
        $this->mysqlInstance->from('(
		        SELECT GG.title, N_E_C.count as played_movies
		        FROM cat_genre AS GG
		        LEFT JOIN (
					SELECT G.title, COUNT(*) AS count
					FROM cat_genre AS G, `video` AS V
					WHERE G.id = V.cat_genre_id_1 OR G.id = V.cat_genre_id_2 OR G.id = V.cat_genre_id_3 OR G.id = V.cat_genre_id_4
					GROUP BY G.title
				) AS N_E_C /*not empty count*/ ON GG.title = N_E_C.title
		    GROUP BY GG.title
	        ) AS C_G /* all counted genres */')->join("(\n\t\t\tSELECT COUNT(*) AS total_movies, C_G.title AS c_g_title\n\t\t\tFROM `played_video` AS P_V\n\t\t\tLEFT JOIN `video` AS V ON P_V.video_id = V.id, `cat_genre` AS C_G\n\t\t\tWHERE (V.cat_genre_id_1 = C_G.id OR V.cat_genre_id_2 = C_G.id OR V.cat_genre_id_3 = C_G.id OR V.cat_genre_id_4 = C_G.id)\n\t\t\t\tAND `playtime` > '{$date_obj->format('Y-m-d H:i:s')}'\n\t\t\tGROUP BY C_G.title\n\t\t  ) AS C_V /* all counted views */", 'C_G.title', 'C_V.c_g_title', 'LEFT');
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getVideoStatDailyList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('daily_played_video')->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getNoActiveAbonentTotalRows($func_alias, $where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->{"getNoActiveAbonent{$func_alias}List"}($params, true);
    }
    public function getNoActiveAbonentTvList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->from('users')->where($param['where'])->where(['NOT `users`.`time_last_play_tv`' => null])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getNoActiveAbonentVideoList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->from('users')->where($param['where'])->where(['NOT `users`.`time_last_play_video`' => null])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getDailyClaimsTotalRows($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getDailyClaimsList($params, true);
    }
    public function getDailyClaimsList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('daily_media_claims')->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getClaimsLogsTotalRows($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getClaimsLogsList($params, true);
    }
    public function getClaimsLogsList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->from('`media_claims_log` as M_C_L')->join('`itv` as I', 'M_C_L.`media_id`', 'I.`id` and M_C_L.`media_type` = "itv"', 'LEFT')->join('`karaoke` as K', 'M_C_L.`media_id`', 'K.`id` and M_C_L.`media_type` = "karaoke"', 'LEFT')->join('`video` as V', 'M_C_L.`media_id`', 'V.`id` and M_C_L.`media_type` = "vclub"', 'LEFT')->join('`users` as U', 'M_C_L.`uid`', 'U.`id`', 'LEFT')->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getTvArchiveTotalRows($where = array(), $like = array())
    {
        $params = ['select' => ['count(`ch_id`) as `counter`'], 'where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getTvArchiveList($params, true);
    }
    public function getTvArchiveList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('`played_tv_archive`')->join('itv', 'itv.id', 'played_tv_archive.ch_id', 'INNER')->where($param['where'])->like($param['like'], 'OR')->groupby('ch_id');
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        } else {
            $this->mysqlInstance->orderby('counter', 'DESC');
        }
        if (!empty($params['like'])) {
            $this->mysqlInstance->like($params['like']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->join('users', 'played_tv_archive.uid', 'users.id', 'LEFT')->where(['reseller_id' => $this->reseller_id]);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTimeShiftTotalRows($where = array(), $like = array())
    {
        $params = ['select' => ['count(`ch_id`) as `counter`'], 'where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getTimeShiftList($params, true);
    }
    public function getTimeShiftList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('`played_timeshift`')->join('itv', 'itv.id', 'played_timeshift.ch_id', 'INNER')->where($param['where'])->like($param['like'], 'OR')->groupby('ch_id');
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        } else {
            $this->mysqlInstance->orderby('counter', 'DESC');
        }
        if (!empty($params['like'])) {
            $this->mysqlInstance->like($params['like']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->join('users', 'played_timeshift.uid', 'users.id', 'LEFT')->where(['reseller_id' => $this->reseller_id]);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getAbonentStatTotalRows($func_alias, $where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->{"getAbonentStat{$func_alias}List"}($params, true);
    }
    public function getAbonentStatTvList($param, $counter = false)
    {
        if ($counter) {
            $param['select'][] = 'count(`played_itv`.`id`) as `counter`';
        }
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('users')->join('played_itv', 'users.id', 'played_itv.uid', 'LEFT')->where($param['where'])->where(['NOT played_itv.playtime' => null])->like($param['like'], 'OR')->groupby(['users.id'])->orderby($param['order']);
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getAbonentStatVideoList($param, $counter = false)
    {
        if ($counter) {
            $param['select'][] = 'count(`played_video`.`id`) as `counter`';
        }
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('users')->join('played_video', 'users.id', 'played_video.uid', 'LEFT')->where($param['where'])->like($param['like'], 'OR')->where(['NOT played_video.playtime' => null])->groupby(['users.id'])->orderby($param['order']);
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getAbonentStatAnecList($param, $counter = false)
    {
        if ($counter) {
            $param['select'][] = '`readed_anec`.`mac` as `mac`';
        }
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('readed_anec')->where($param['where'])->like($param['like'], 'OR')->where(['NOT readed' => null])->groupby(['mac'])->orderby($param['order']);
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->join('users', 'readed_anec.mac', 'users.mac', 'LEFT')->where(['reseller_id' => $this->reseller_id]);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTvTotalRows($where = array(), $like = array())
    {
        $params = ['select' => ['count(`played_itv`.id) as `counter`'], 'where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getTvList($params, true);
    }
    public function getTvList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('`played_itv`')->join('itv', 'itv.id', 'played_itv.itv_id', 'LEFT')->where($param['where'])->where([' itv.id IS NOT ' => null])->like($param['like'], 'OR')->groupby('itv_id');
        if (!empty($params['like'])) {
            $this->mysqlInstance->like($params['like']);
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->join('users', 'played_itv.uid', 'users.id', 'LEFT')->where(['reseller_id' => $this->reseller_id]);
        }
        if ($counter) {
            $result = $this->mysqlInstance->get()->all();
            return \count($result);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getTVLocale()
    {
        return $this->mysqlInstance->select(['UPPER(SUBSTRING(`user_locale`, 1, 2)) as `title`', '`user_locale` as `id`'])->from('played_itv')->groupby('user_locale')->orderby('user_locale')->get()->all();
    }
    public function getModeratorsStatRowsList($incoming = array(), $all = false)
    {
        if ($all) {
            $incoming['like'] = [];
        }
        return $this->getModeratorsStatList($incoming, true);
    }
    public function getModeratorsStatList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from($param['from']);
        if (\array_key_exists('joined', $param)) {
            foreach ($param['joined'] as $table => $keys) {
                $this->mysqlInstance->join($table, $keys['left_key'], $keys['right_key'], $keys['type']);
            }
        }
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['groupby'])) {
            $this->mysqlInstance->groupby($param['groupby']);
        }
        if (!empty($param['limit']['limit']) && !$counter) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        if ($counter) {
            $result = $this->mysqlInstance->count()->get()->all();
            if (\count($result) > 1) {
                return \count($result);
            } elseif (!empty($result[0])) {
                list($key, $data) = \each($result[0]);
            } else {
                return 0;
            }
            return $data;
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getAdmins($id = false)
    {
        $this->mysqlInstance->from('administrators');
        if ($id !== false) {
            $this->mysqlInstance->where(['id' => $id]);
        }
        return $this->mysqlInstance->orderby('login')->get()->all();
    }
    public function getArhiveIDs($table)
    {
        return $this->mysqlInstance->select(['id', 'CONCAT_WS(" - ", `year`, `month`) as `title`'])->from($table)->orderby('year, month')->get()->all();
    }
    public function getMinDateFromTable($table, $date_field)
    {
        if (empty($table) || empty($date_field)) {
            return 0;
        }
        $result = $this->mysqlInstance->query("SELECT MIN({$date_field}) as min_date FROM {$table}")->get();
        if ($result = \strtotime($result['min_date'])) {
            return $result;
        }
        return 0;
    }
    public function truncateTable($table_name)
    {
        $this->mysqlInstance->query("TRUNCATE TABLE {$table_name}");
    }
    public function updateDailyClaims($values, $in)
    {
        if (!empty($in)) {
            \reset($in);
            list($key, $val) = \each($in);
            $this->mysqlInstance->in($key, $val);
        } else {
            return 0;
        }
        return $this->mysqlInstance->update('`daily_media_claims`', $values)->total_rows();
    }
    public function updateMediaClaims($values, $in, $where)
    {
        if (!empty($in)) {
            \reset($in);
            list($key, $val) = \each($in);
            $this->mysqlInstance->in($key, $val);
        } else {
            return 0;
        }
        return $this->mysqlInstance->update('`media_claims`', $values, $where)->total_rows();
    }
    public function deleteClaimsLogs($in)
    {
        if (!empty($in)) {
            \reset($in);
            list($key, $val) = \each($in);
            $this->mysqlInstance->in($key, $val);
        } else {
            return 0;
        }
        return $this->mysqlInstance->delete('media_claims_log', [])->total_rows();
    }
    public function cleanDailyClaims()
    {
        return $this->mysqlInstance->delete('daily_media_claims', ['vclub_sound' => 0, 'vclub_video' => 0, 'itv_sound' => 0, 'itv_video' => 0, 'karaoke_sound' => 0, 'karaoke_video' => 0, 'no_epg' => 0, 'wrong_epg' => 0])->total_rows();
    }
    public function cleanMediaClaims()
    {
        return $this->mysqlInstance->delete('media_claims', ['sound_counter' => 0, 'video_counter' => 0, 'no_epg' => 0, 'wrong_epg' => 0])->total_rows();
    }
    public function getUsersDevicesTotalRows($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getUsersDevicesList($params, true);
    }
    public function getUsersDevicesList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('users_devices_statistic');
        $this->mysqlInstance->join('users', 'users_devices_statistic.user_id', 'users.id', 'LEFT');
        $this->mysqlInstance->join('reseller', 'users.reseller_id', 'reseller.id', 'LEFT');
        if (!empty($this->reseller_id)) {
            $this->mysqlInstance->where(['reseller_id' => $this->reseller_id]);
        }
        $this->mysqlInstance->where($param['where'])->like($param['like'], 'OR')->orderby($param['order']);
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
}
