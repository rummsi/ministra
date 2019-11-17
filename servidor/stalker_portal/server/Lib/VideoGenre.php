<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class VideoGenre
{
    private $language;
    private $range = array();
    private $order = array();
    public function setLocale($language)
    {
        $this->language = $language;
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::getInstance()->H545968ec0047813afbe3121bb5b6c9a5($this->language);
    }
    public function setRange($range = array())
    {
        if (\is_numeric($range)) {
            $this->range = [$range];
        } elseif (\is_array($range)) {
            $this->range = $range;
        }
    }
    public function setOrder($field = 'title', $dir = 'ASC')
    {
        $this->order[$field] = $dir;
    }
    public function getIdMap()
    {
        $genres = $this->getAll(true, false, true);
        $map = [];
        foreach ($genres as $genre) {
            $map[$genre['_id']] = $genre['id'];
        }
        return $map;
    }
    public function getAll($pretty_id = false, $group = true, $include_internal_id = false)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('*')->from('cat_genre');
        if ($group) {
            $genres->select('GROUP_CONCAT(category_alias) as categories, GROUP_CONCAT(id) as ids')->groupby('title');
        } else {
            $genres->select('category_alias as categories');
        }
        if (!empty($this->range)) {
            $genres->in('id', $this->range);
        }
        if (!empty($this->order)) {
            $genres->orderby($this->order);
        }
        $genres = $genres->get()->all();
        $genres = \array_map(function ($item) use($pretty_id, $include_internal_id) {
            if ($include_internal_id) {
                $item['_id'] = $item['id'];
            }
            if ($pretty_id) {
                $item['id'] = \preg_replace(["/\\s/i", '/[^a-z0-9-]/i'], ['-', ''], $item['title']);
            }
            $item['original_title'] = $item['title'];
            $item['title'] = \_($item['title']);
            $item['categories'] = \array_map(function ($id) {
                return (int) $id;
            }, \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->getInstance()->from('media_category')->in('category_alias', \explode(',', $item['categories']))->get()->all('id'));
            return $item;
        }, $genres);
        return $genres;
    }
    public function getById($id, $pretty_id = false)
    {
        if ($pretty_id) {
            $genres = $this->getAll($pretty_id);
            $genres = \array_filter($genres, function ($genre) use($id) {
                return $id == $genre['id'];
            });
            if (empty($genres)) {
                return;
            }
            $titles = \array_map(function ($genre) {
                return $genre['original_title'];
            }, \array_values($genres));
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->in('title', $titles)->get()->all();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->where(['id' => (int) $id])->get()->first();
    }
    public function getByIdAndCategory($id, $category_id, $pretty_id = false)
    {
        $category = new \Ministra\Lib\VideoCategory();
        $category = $category->getById($category_id, $pretty_id);
        if (empty($category)) {
            return;
        }
        if ($pretty_id) {
            $genres = $this->getAll($pretty_id, false, true);
            $genres = \array_filter($genres, function ($genre) use($id, $category) {
                return $id == $genre['id'] && $genre['category_alias'] == $category['category_alias'];
            });
            if (empty($genres)) {
                return;
            }
            $genres = \array_values($genres);
            return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->where(['id' => $genres[0]['_id']])->get()->first();
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->where(['id' => (int) $id, 'category_alias' => $category['category_alias']])->get()->first();
    }
    public function getByCategoryId($category_id, $pretty_id = false)
    {
        $category = new \Ministra\Lib\VideoCategory();
        $category = $category->getById($category_id, $pretty_id);
        if (empty($category)) {
            return [];
        }
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('cat_genre')->where(['category_alias' => $category['category_alias']])->get()->all();
        if ($pretty_id) {
            $genres = \array_map(function ($genre) {
                $genre['_id'] = $genre['id'];
                $genre['id'] = \preg_replace(["/\\s/i", '/[^a-z0-9-]/i'], ['-', ''], $genre['title']);
                $genre['title'] = \_($genre['title']);
                return $genre;
            }, $genres);
        }
        return $genres;
    }
}
