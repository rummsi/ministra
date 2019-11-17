<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
class VideoCategory
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
            $categories = $this->getAll($pretty_id);
            $categories = \array_filter($categories, function ($category) use($id) {
                return $id == $category['id'];
            });
            if (empty($categories)) {
                return;
            }
            $categories = \array_values($categories);
            $category = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->where(['category_name' => $categories[0]['original_title']])->get()->first();
            if (!empty($category)) {
                $category['id'] = $id;
            }
            return $category;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->where(['id' => (int) $id])->get()->first();
    }
    public function getAll($pretty_id = false)
    {
        $categories = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('media_category')->orderby('num')->get()->all();
        $categories = \array_map(function ($item) use($pretty_id) {
            if ($pretty_id) {
                $item['id'] = \preg_replace('/_/i', '-', $item['category_alias']);
            }
            $item['original_title'] = $item['category_name'];
            $item['title'] = \_($item['category_name']);
            $item['censored'] = (bool) $item['censored'];
            return $item;
        }, $categories);
        return $categories;
    }
}
