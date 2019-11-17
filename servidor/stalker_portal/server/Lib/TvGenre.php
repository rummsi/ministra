<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class TvGenre
{
    private $language;
    public function setLocale($language)
    {
        $this->language = $language;
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->H545968ec0047813afbe3121bb5b6c9a5($this->language);
    }
    public function getById($id, $pretty_id = false)
    {
        if ($pretty_id) {
            $genres = $this->getAll($pretty_id, true);
            $genres = \array_filter($genres, function ($genre) use($id) {
                return $id == $genre['id'];
            });
            if (empty($genres)) {
                return;
            }
            $genres = \array_values($genres);
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_genre')->where(['id' => $genres[0]['_id']])->get()->first();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_genre')->where(['id' => (int) $id])->get()->first();
    }
    public function getAll($pretty_id = false, $include_internal_id = false)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tv_genre')->orderby('number')->get()->all();
        $genres = \array_map(function ($item) use($pretty_id, $include_internal_id) {
            if ($include_internal_id) {
                $item['_id'] = $item['id'];
            }
            if ($pretty_id) {
                $item['id'] = \preg_replace(["/\\s/i", '/[^a-z0-9-]/i'], ['-', ''], $item['title']);
            }
            $item['censored'] = (bool) $item['censored'];
            $item['title'] = \_($item['title']);
            return $item;
        }, $genres);
        return $genres;
    }
}
