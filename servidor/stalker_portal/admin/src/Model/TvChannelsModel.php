<?php

namespace Ministra\Admin\Model;

class TvChannelsModel extends \Ministra\Admin\Model\BaseMinistraModel
{
    private $broadcasting_keys = array('user_agent_filter' => '', 'priority' => '', 'use_http_tmp_link' => false, 'wowza_tmp_link' => '', 'nginx_secure_link' => '', 'flussonic_tmp_link' => '', 'nimble_auth_support' => '', 'xtream_codes_support' => '', 'edgecast_auth_support' => '', 'flexcdn_auth_support' => '', 'enable_monitoring' => false, 'enable_balancer_monitoring' => '', 'monitoring_url' => '', 'use_load_balancing' => false, 'stream_server' => '');
    public function __construct()
    {
        parent::__construct();
    }
    public function getLastModifiedId()
    {
        return $this->mysqlInstance->from('itv')->where(['modified!=' => ''])->orderby('modified', 'DESC')->limit(1, 0)->get()->first('id');
    }
    public function getTotalRowsAllChannels($where = array(), $like = array())
    {
        $params = ['where' => $where];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getAllChannels($params, true);
    }
    public function getAllChannels($param = array(), $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('itv')->join('media_claims', 'itv.id', 'media_claims.media_id and media_claims.media_type="itv"', 'LEFT')->join('tv_genre', 'itv.tv_genre_id', 'tv_genre.id', 'LEFT');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($where)) {
            $this->mysqlInstance->where($where);
        }
        if (!empty($param['in']) && \is_array($param['in']) || !empty($param['not_in']) && \is_array($param['not_in'])) {
            foreach (['in', 'not_in'] as $in) {
                if (\array_key_exists($in, $param)) {
                    $not = $in === 'not_in';
                    foreach ($param[$in] as $field => $values) {
                        $this->mysqlInstance->in($field, \is_array($values) ? $values : [$values], $not);
                    }
                }
            }
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], ' OR ');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function getAllGenres()
    {
        return $this->mysqlInstance->from('tv_genre')->get()->all();
    }
    public function getChannelById($id)
    {
        return $this->mysqlInstance->from('itv')->where(['id' => (int) $id])->get()->first();
    }
    public function getChannelLinksById($id)
    {
        return $this->mysqlInstance->select(' *, `url` as `cmd`, `status` as `monitoring_status`')->from('ch_links')->where(['ch_id' => (int) $id])->orderby('priority')->get()->all();
    }
    public function getStreamersIdMapForLink($link_id)
    {
        $streamers = $this->mysqlInstance->from('ch_link_on_streamer')->where(['link_id' => $link_id])->get()->all();
        $map = [];
        foreach ($streamers as $streamer) {
            $map[$streamer['streamer_id']] = $streamer;
        }
        return $map;
    }
    public function getAllStreamServer()
    {
        return $this->mysqlInstance->from('streaming_servers')->orderby('name')->get()->all();
    }
    public function insertITVChannel($data)
    {
        if (!empty($data['cmd'])) {
            while (list($cmd_key, $cmd_data) = \each($data['cmd'])) {
                \reset($this->broadcasting_keys);
                while (list($key, $value) = \each($this->broadcasting_keys)) {
                    if (\array_key_exists($key, $data) and \array_key_exists($cmd_key, $data[$key])) {
                        $this->broadcasting_keys[$key] |= \is_numeric($data[$key][$cmd_key]) ? (int) $data[$key][$cmd_key] : !empty($data[$key][$cmd_key]) && $data[$key][$cmd_key] !== 'off';
                    }
                }
            }
        }
        $data = \array_merge($data, $this->broadcasting_keys);
        $cmd_val = false;
        if (\is_array($data['cmd']) && !empty($data['cmd'])) {
            $cmd_val = \array_values($data['cmd']);
        }
        $input = ['name' => $data['name'], 'number' => $data['number'], 'use_http_tmp_link' => (int) (!empty($data['use_http_tmp_link']) && $data['use_http_tmp_link'] !== 'off'), 'wowza_tmp_link' => (int) (!empty($data['wowza_tmp_link']) && $data['wowza_tmp_link'] !== 'off'), 'nginx_secure_link' => (int) (!empty($data['nginx_secure_link']) && $data['nginx_secure_link'] !== 'off'), 'tv_archive_type' => !empty($data['tv_archive_type']) ? $data['tv_archive_type'] : null, 'censored' => (int) (!empty($data['censored']) && $data['censored'] !== 'off'), 'base_ch' => (int) (!empty($data['base_ch']) && $data['base_ch'] !== 'off'), 'bonus_ch' => (int) (!empty($data['bonus_ch']) && $data['bonus_ch'] !== 'off'), 'hd' => (int) (!empty($data['hd']) && $data['hd'] !== 'off'), 'cost' => !empty($data['cost']) && \is_numeric($data['cost']) ? $data['cost'] : 0, 'cmd' => $cmd_val !== false ? $cmd_val[0] : '', 'cmd_1' => !empty($data['cmd_1']) ? $data['cmd_1'] : '', 'cmd_2' => !empty($data['cmd_2']) ? $data['cmd_2'] : '', 'cmd_3' => !empty($data['cmd_3']) ? $data['cmd_3'] : '', 'mc_cmd' => (!empty($data['tv_archive_type']) || !empty($data['allow_pvr'])) && !empty($data['mc_cmd']) ? $data['mc_cmd'] : '', 'enable_wowza_load_balancing' => (int) (!empty($data['enable_wowza_load_balancing']) && $data['enable_wowza_load_balancing'] !== 'off'), 'allow_pvr' => (int) (!empty($data['allow_pvr']) && $data['allow_pvr'] !== 'off'), 'allow_local_pvr' => (int) (!empty($data['allow_local_pvr']) && $data['allow_local_pvr'] !== 'off'), 'allow_local_timeshift' => (int) (!empty($data['allow_local_timeshift']) && $data['allow_local_timeshift'] !== 'off'), 'enable_monitoring' => (int) (!empty($data['enable_monitoring']) && $data['enable_monitoring'] !== 'off'), 'descr' => !empty($data['descr']) ? $data['descr'] : '', 'tv_genre_id' => !empty($data['tv_genre_id']) ? $data['tv_genre_id'] : 0, 'languages' => !empty($data['languages']) ? $data['languages'] : '', 'status' => 1, 'xmltv_id' => !empty($data['xmltv_id']) ? $data['xmltv_id'] : '', 'service_id' => !empty($data['service_id']) ? \trim($data['service_id']) : '', 'volume_correction' => !empty($data['volume_correction']) ? (int) $data['volume_correction'] : 0, 'correct_time' => !empty($data['correct_time']) ? (int) $data['correct_time'] : 0, 'modified' => 'NOW()', 'added' => 'NOW()', 'tv_archive_duration' => !empty($data['tv_archive_type']) && !empty($data['tv_archive_duration']) ? (int) $data['tv_archive_duration'] : 0];
        if (!empty($data['tv_archive_type']) && \in_array($data['tv_archive_type'], ['wowza_dvr', 'flussonic_dvr', 'nimble_dvr'])) {
            $input[$data['tv_archive_type']] = 1;
        }
        return $this->mysqlInstance->insert('itv', $input)->insert_id();
    }
    public function updateITVChannel($data)
    {
        if (!empty($data['cmd'])) {
            while (list($cmd_key, $cmd_data) = \each($data['cmd'])) {
                \reset($this->broadcasting_keys);
                while (list($key, $value) = \each($this->broadcasting_keys)) {
                    if (\array_key_exists($key, $data) and \array_key_exists($cmd_key, $data[$key])) {
                        $this->broadcasting_keys[$key] |= (bool) (\is_numeric($data[$key][$cmd_key]) ? (int) $data[$key][$cmd_key] : !empty($data[$key][$cmd_key]) && $data[$key][$cmd_key] !== 'off');
                    }
                }
            }
        }
        $data = \array_merge($data, $this->broadcasting_keys);
        $cmd_val = false;
        if (\is_array($data['cmd']) && !empty($data['cmd'])) {
            $cmd_val = \array_values($data['cmd']);
        }
        $input = ['name' => $data['name'], 'number' => $data['number'], 'use_http_tmp_link' => (int) (!empty($data['use_http_tmp_link']) && $data['use_http_tmp_link'] !== 'off'), 'wowza_tmp_link' => (int) (!empty($data['wowza_tmp_link']) && $data['wowza_tmp_link'] !== 'off'), 'nginx_secure_link' => (int) (!empty($data['nginx_secure_link']) && $data['nginx_secure_link'] !== 'off'), 'tv_archive_type' => !empty($data['tv_archive_type']) ? $data['tv_archive_type'] : null, 'censored' => (int) (!empty($data['censored']) && $data['censored'] !== 'off'), 'base_ch' => (int) (!empty($data['base_ch']) && $data['base_ch'] !== 'off'), 'bonus_ch' => (int) (!empty($data['bonus_ch']) && $data['bonus_ch'] !== 'off'), 'hd' => (int) (!empty($data['hd']) && $data['hd'] !== 'off'), 'cost' => !empty($data['cost']) && \is_numeric($data['cost']) ? $data['cost'] : 0, 'cmd' => $cmd_val !== false ? $cmd_val[0] : '', 'cmd_1' => !empty($data['cmd_1']) ? $data['cmd_1'] : '', 'cmd_2' => !empty($data['cmd_2']) ? $data['cmd_2'] : '', 'cmd_3' => !empty($data['cmd_3']) ? $data['cmd_3'] : '', 'mc_cmd' => (!empty($data['tv_archive_type']) || !empty($data['allow_pvr'])) && !empty($data['mc_cmd']) ? $data['mc_cmd'] : '', 'enable_wowza_load_balancing' => (int) (!empty($data['enable_wowza_load_balancing']) && $data['enable_wowza_load_balancing'] !== 'off'), 'allow_pvr' => (int) (!empty($data['allow_pvr']) && $data['allow_pvr'] !== 'off'), 'allow_local_pvr' => (int) (!empty($data['allow_local_pvr']) && $data['allow_local_pvr'] !== 'off'), 'allow_local_timeshift' => (int) (!empty($data['allow_local_timeshift']) && $data['allow_local_timeshift'] !== 'off'), 'enable_monitoring' => (int) (!empty($data['enable_monitoring']) && $data['enable_monitoring'] !== 'off'), 'descr' => !empty($data['descr']) ? $data['descr'] : '', 'tv_genre_id' => !empty($data['tv_genre_id']) ? $data['tv_genre_id'] : 0, 'languages' => !empty($data['languages']) ? $data['languages'] : '', 'status' => 1, 'xmltv_id' => !empty($data['xmltv_id']) ? $data['xmltv_id'] : '', 'service_id' => !empty($data['service_id']) ? \trim($data['service_id']) : '', 'volume_correction' => !empty($data['volume_correction']) ? (int) $data['volume_correction'] : 0, 'correct_time' => !empty($data['correct_time']) ? (int) $data['correct_time'] : 0, 'modified' => 'NOW()', 'tv_archive_duration' => !empty($data['tv_archive_type']) && !empty($data['tv_archive_duration']) ? (int) $data['tv_archive_duration'] : 0];
        if (!$input['enable_monitoring']) {
            $input['monitoring_status'] = 1;
        }
        if (!empty($data['tv_archive_type']) && \in_array($data['tv_archive_type'], ['wowza_dvr', 'flussonic_dvr', 'nimble_dvr'])) {
            $input[$data['tv_archive_type']] = 1;
        }
        $this->mysqlInstance->update('itv', $input, ['id' => (int) $data['id']]);
        return $data['id'];
    }
    public function insertCHLink($link)
    {
        return $this->mysqlInstance->insert('ch_links', $link)->insert_id();
    }
    public function insertCHLinkOnStreamer($link_id, $streamer_id)
    {
        $this->mysqlInstance->insert('ch_link_on_streamer', ['link_id' => $link_id, 'streamer_id' => $streamer_id]);
    }
    public function getStorages()
    {
        return $this->mysqlInstance->from('storages')->where(['status' => 1, 'for_records' => 1, '`dvr_type` IS NOT' => null, '`dvr_type` <>' => ''])->where([' stream_server_type' => null, 'stream_server_type' => ''], 'OR ')->get()->all();
    }
    public function updateLogoName($id, $logo)
    {
        $this->mysqlInstance->update('itv', ['logo' => $logo], ['id' => $id]);
    }
    public function getFieldFirstVal($field_name, $value)
    {
        return $this->mysqlInstance->from('itv')->where([$field_name => $value])->get()->all($field_name);
    }
    public function getITVByParams($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('itv');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['in']) && \is_array($param['in'])) {
            foreach ($param['in'] as $field => $values) {
                $this->mysqlInstance->in($field, \is_array($values) ? $values : [$values]);
            }
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], ' OR ');
        }
        return $this->mysqlInstance->get()->all();
    }
    public function getUnnecessaryLinks($id, $urls = array())
    {
        $this->mysqlInstance->from('ch_links')->where(['ch_id' => $id]);
        if (!empty($urls) && \is_array($urls)) {
            $this->mysqlInstance->in('url', $urls, true);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function deleteCHLink($ids)
    {
        return $this->mysqlInstance->query('delete from ch_links where id in (' . \implode(',', $ids) . ')')->total_rows();
    }
    public function deleteCHLinkOnStreamer($ids)
    {
        $this->mysqlInstance->query('delete from ch_link_on_streamer where link_id in (' . \implode(',', $ids) . ')');
    }
    public function deleteCHLinkOnStreamerByLinkAndID($link_id, $ids)
    {
        $this->mysqlInstance->query("delete from ch_link_on_streamer where link_id={$link_id} and streamer_id in (" . \implode(',', $ids) . ')');
    }
    public function updateCHLink($link_id, $links)
    {
        unset($links['id']);
        $this->mysqlInstance->update('ch_links', $links, ['id' => $link_id]);
    }
    public function removeChannel($id)
    {
        $this->mysqlInstance->insert('deleted_channels', ['ch_id' => (int) $id, 'deleted' => 'NOW()']);
        return $this->mysqlInstance->delete('itv', ['id' => (int) $id])->total_rows();
    }
    public function changeChannelStatus($id, $status = 0)
    {
        return $this->mysqlInstance->update('itv', ['status' => empty($status) ? 0 : 1, 'modified' => 'NOW()'], ['id' => (int) $id])->total_rows();
    }
    public function updateChannelNum($row)
    {
        return $this->mysqlInstance->update('itv', ['number' => $row['number'], 'modified' => 'NOW()'], ['id' => $row['id']])->total_rows();
    }
    public function updateChannelGroup($params)
    {
        if (\array_key_exists('in', $params)) {
            \reset($params['in']);
            while (list($field, $values) = \each($params['in'])) {
                $this->mysqlInstance->in($field, $values);
            }
        }
        if (\array_key_exists('order', $params)) {
            $this->mysqlInstance->orderby($params['order']);
        }
        if (!\array_key_exists('where', $params)) {
            $params['where'] = [];
        }
        if (!\array_key_exists('set', $params)) {
            $params['set'] = [];
        }
        return $this->mysqlInstance->update('itv', $params['set'], $params['where'])->total_rows();
    }
    public function updateChannelLockedStatus($row)
    {
        return $this->mysqlInstance->update('itv', ['locked' => $row['locked'], 'modified' => 'NOW()'], ['id' => $row['id']])->total_rows();
    }
    public function getEPGForChannel($id, $time_from, $time_to)
    {
        return $this->mysqlInstance->from('epg')->where(['ch_id' => $id, 'time>=' => $time_from, 'time<=' => $time_to])->orderby('time')->get()->all();
    }
    public function deleteEPGForChannel($id, $time_from, $time_to)
    {
        return $this->mysqlInstance->delete('epg', ['ch_id' => $id, 'time>=' => $time_from, 'time<=' => $time_to])->total_rows();
    }
    public function insertEPGForChannel($data)
    {
        return $this->mysqlInstance->insert('epg', $data)->total_rows();
    }
    public function findFirstAfterTime($id, $dt)
    {
        return $this->mysqlInstance->from('epg')->where(['ch_id' => $id, 'time>' => $dt])->get()->first();
    }
    public function getTotalRowsEPGList($where = array(), $like = array())
    {
        $params = ['where' => $where, 'like' => [], 'order' => []];
        if (!empty($like)) {
            $params['like'] = $like;
        }
        return $this->getEPGList($params, true);
    }
    public function getEPGList($param, $counter = false)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('epg_setting');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], $param['limit']['offset']);
        }
        return $counter ? $this->mysqlInstance->count()->get()->counter() : $this->mysqlInstance->get()->all();
    }
    public function updateEPG($param, $where)
    {
        $where = \is_array($where) ? $where : ['id' => $where];
        return $this->mysqlInstance->update('epg_setting', $param, $where)->total_rows();
    }
    public function insertEPG($param)
    {
        return $this->mysqlInstance->insert('epg_setting', $param)->insert_id();
    }
    public function deleteEPG($param)
    {
        return $this->mysqlInstance->delete('epg_setting', $param)->total_rows();
    }
    public function searchOneEPGParam($param = array(), $cond = ' AND ')
    {
        return $this->mysqlInstance->from('epg_setting')->where($param, $cond)->get()->first();
    }
    public function updateITVChannelLogo($id, $logo_name)
    {
        return $this->mysqlInstance->update('itv', ['logo' => $logo_name], ['id' => $id])->total_rows();
    }
    public function getCurrentTasks()
    {
        return $this->mysqlInstance->select('ch_id, storage_name')->from('tv_archive')->get()->all();
    }
    public function checkChannelParams($ch_id)
    {
        return $this->mysqlInstance->from('itv')->where(['id' => $ch_id, 'NOT ISNULL(mc_cmd) AND mc_cmd<>"" and 1=' => 1])->get()->count();
    }
    public function getTotalRowsTvGenresList($where = array(), $like = array())
    {
        $this->mysqlInstance->count()->from('tv_genre')->where($where);
        if (!empty($like)) {
            $this->mysqlInstance->like($like, 'OR');
        }
        return $this->mysqlInstance->get()->counter();
    }
    public function getTvGenresList($param)
    {
        if (!empty($param['select'])) {
            $this->mysqlInstance->select($param['select']);
        }
        $this->mysqlInstance->from('tv_genre');
        if (!empty($param['where'])) {
            $this->mysqlInstance->where($param['where']);
        }
        if (!empty($param['like'])) {
            $this->mysqlInstance->like($param['like'], 'OR');
        }
        if (!empty($param['order'])) {
            $this->mysqlInstance->orderby($param['order']);
        }
        if (!empty($param['limit']['limit'])) {
            $this->mysqlInstance->limit($param['limit']['limit'], \array_key_exists('offset', $param['limit']) ? $param['limit']['offset'] : false);
        }
        return $this->mysqlInstance->get()->all();
    }
    public function insertTvGenres($param)
    {
        return $this->mysqlInstance->query("INSERT INTO `tv_genre` (`title`, `number`, `censored`) SELECT '{$param['title']}', MAX(`number`) + 1, '{$param['censored']}' FROM `tv_genre`")->insert_id();
    }
    public function updateTvGenres($data, $param)
    {
        unset($data['id']);
        return $this->mysqlInstance->update('tv_genre', $data, $param)->total_rows();
    }
    public function deleteTvGenres($param)
    {
        return $this->mysqlInstance->delete('tv_genre', $param)->total_rows();
    }
    public function getChanelDisabledLink($id, $disabled = false)
    {
        $where = ['ch_id' => $id, 'enable_monitoring' => 1];
        if ($disabled) {
            $where['status'] = 0;
        }
        return $this->mysqlInstance->from('ch_links')->where($where)->get()->all();
    }
    public function getLastChannelNumber()
    {
        return $this->mysqlInstance->query('SELECT max(`itv`.`number`) as `last_number` FROM `itv`')->first('last_number');
    }
    public function resetMediaClaims($media_id)
    {
        return $this->mysqlInstance->update('media_claims', ['sound_counter' => 0, 'video_counter' => 0, 'no_epg' => 0, 'wrong_epg' => 0], ['media_id' => (int) $media_id, 'media_type' => 'itv'])->total_rows();
    }
    public function changeITVGenre($id, $tv_genre_id)
    {
        return $this->mysqlInstance->update('itv', ['tv_genre_id' => $tv_genre_id, 'modified' => 'NOW()'], ['id' => $id])->total_rows();
    }
    public function changeITVLanguages($id, $languages)
    {
        return $this->mysqlInstance->update('itv', ['languages' => $languages, 'modified' => 'NOW()'], ['id' => $id])->total_rows();
    }
    public function totalChannelsByField($field, $value, $id = null)
    {
        $where = [$field => $value];
        if ($id) {
            $where['id<>'] = $id;
        }
        return $this->mysqlInstance->count()->from('itv')->where($where)->get()->counter();
    }
}
