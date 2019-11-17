<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class Anecdote implements \Ministra\Lib\StbApi\Anecdote
{
    private $db;
    private $stb;
    private $page;
    public function __construct()
    {
        $this->db = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance();
        $this->stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance();
        $this->page = @(int) $_REQUEST['p'];
    }
    public function getByPage()
    {
        $pages = $this->db->from('anec')->count()->get()->counter();
        $response = [];
        $response['total_items'] = $pages;
        $response['data'] = $this->prepareData($this->db->select('*, DATE(added) as added')->from('anec')->orderby('id', 'DESC')->limit(1, $this->page)->get()->first());
        return $response;
    }
    private function prepareData($data)
    {
        if (empty($data)) {
            return;
        }
        $data['anec_body'] = \nl2br($data['anec_body']);
        $data['rating'] = $this->getRating($data['id']);
        $data['voted'] = $this->isVoted($data['id']);
        return $data;
    }
    private function getRating($id)
    {
        $rating = $this->db->from('anec_rating')->count()->where(['anec_id' => $id])->get()->counter();
        return $rating;
    }
    private function isVoted($id)
    {
        return $this->db->from('anec_rating')->count()->where(['anec_id' => $id, 'uid' => $this->stb->id])->get()->counter();
    }
    public function getBookmark()
    {
        $bookmark = $this->db->from('anec_bookmark')->where(['uid' => $this->stb->id])->get()->first();
        if (!empty($bookmark)) {
            return $this->db->from('anec')->count()->where(['id>=' => $bookmark['anec_id']])->orderby('added', 'DESC')->get()->counter();
        }
        return 0;
    }
    public function setBookmark()
    {
        $anec_id = (int) $_REQUEST['anec_id'];
        $bookmark = $this->db->from('anec_bookmark')->where(['uid' => $this->stb->id])->get()->first();
        if (!empty($bookmark)) {
            return $this->db->update('anec_bookmark', ['anec_id' => $anec_id], ['uid' => $this->stb->id]);
        }
        return $this->db->insert('anec_bookmark', ['uid' => $this->stb->id, 'anec_id' => $anec_id])->insert_id();
    }
    public function setVote()
    {
        $anec_id = (int) $_REQUEST['anec_id'];
        if (!$this->isVoted($anec_id)) {
            $this->db->insert('anec_rating', ['uid' => $this->stb->id, 'anec_id' => $anec_id])->insert_id();
        }
        return $anec_id;
    }
    public function setReaded()
    {
        return $this->db->insert('readed_anec', ['mac' => $this->stb->mac, 'readed' => 'NOW()'])->insert_id();
    }
}
