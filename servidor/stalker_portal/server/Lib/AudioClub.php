<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\StbApi\AudioClub as AudioClubInterface;
class AudioClub extends \Ministra\Lib\AjaxResponse implements \Ministra\Lib\StbApi\AudioClub
{
    public function createLink()
    {
    }
    public function getCategories()
    {
        return [['alias' => 'albums', 'title' => \_('Albums')], ['alias' => 'performers', 'title' => \_('Artists')], ['alias' => 'playlists', 'title' => \_('Playlists')], ['alias' => 'genres', 'title' => \_('Genres')], ['alias' => 'years', 'title' => \_('Years')]];
    }
    public function getOrderedList()
    {
        $category = empty($_REQUEST['category']) ? 'albums' : $_REQUEST['category'];
        if ($category == 'albums') {
            return $this->getAlbumsList();
        } elseif ($category == 'performers') {
            return $this->getPerformersList();
        } elseif ($category == 'genres') {
            return $this->getGenresList();
        } elseif ($category == 'years') {
            return $this->getYearsList();
        } elseif ($category == 'playlists') {
            return $this->getPlaylistList();
        }
    }
    private function getAlbumsList()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_albums.*,
                audio_performers.name as performer_name,
                audio_years.name as album_year,
                countries.name' . ($this->stb->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? '' : '_en') . ' as album_country
            ')->from('audio_albums')->join('audio_performers', 'audio_albums.performer_id', 'audio_performers.id', 'LEFT')->join('audio_years', 'audio_albums.year_id', 'audio_years.id', 'LEFT')->join('countries', 'audio_albums.country_id', 'countries.id', 'LEFT')->where(['audio_albums.status' => 1])->orderby('added', 'DESC')->limit(self::MAX_PAGE_ITEMS, $offset);
        if (!empty($_REQUEST['performer_id'])) {
            $result->where(['performer_id' => (int) $_REQUEST['performer_id']]);
        }
        if (!empty($_REQUEST['genre_id'])) {
            $result->join('audio_genre', 'audio_albums.id', 'audio_genre.album_id', 'LEFT')->where(['genre_id' => (int) $_REQUEST['genre_id']]);
        }
        if (!empty($_REQUEST['year_id'])) {
            $result->where(['year_id' => (int) $_REQUEST['year_id']]);
        }
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['name'] = $this->response['data'][$i]['performer_name'] . ' - ' . $this->response['data'][$i]['name'];
            $this->response['data'][$i]['genres'] = \implode(', ', $this->getAlbumGenres($this->response['data'][$i]['id']));
            $this->response['data'][$i]['tracks'] = $this->countAlbumTracks($this->response['data'][$i]['id']);
            $this->response['data'][$i]['languages'] = \implode(', ', $this->getAlbumLanguages($this->response['data'][$i]['id']));
            $this->response['data'][$i]['album_year'] = \_($this->response['data'][$i]['album_year']);
            $this->response['data'][$i]['cover_uri'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/audio_covers/' . \ceil($this->response['data'][$i]['id'] / 100) . '/' . $this->response['data'][$i]['cover'];
            $this->response['data'][$i]['is_album'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
    public function getAlbumGenres($album_id)
    {
        $genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_genres.name')->from('audio_genre')->where(['album_id' => $album_id])->join('audio_genres', 'audio_genre.genre_id', 'audio_genres.id', 'LEFT')->orderby('audio_genres.name')->get()->all('name');
        return \array_map(function ($genre) {
            return \_($genre);
        }, $genres);
    }
    private function countAlbumTracks($album_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['album_id' => $album_id])->count()->get()->counter();
    }
    private function getAlbumLanguages($album_id)
    {
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_languages.name')->from('audio_compositions')->where(['album_id' => $album_id])->join('audio_languages', 'audio_compositions.language_id', 'audio_languages.id', 'LEFT')->orderby('audio_languages.name')->groupby('audio_languages.name')->get()->all('name');
    }
    private function getPerformersList()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_performers')->orderby('name')->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['is_performer'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
    private function getGenresList()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_genres.*')->from('audio_genres')->join('audio_genre', 'audio_genres.id', 'audio_genre.genre_id', 'INNER')->orderby('audio_genres.name')->groupby('audio_genres.id')->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['name'] = \_($this->response['data'][$i]['name']);
            $this->response['data'][$i]['is_genre'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
    private function getYearsList()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_years.*')->from('audio_years')->join('audio_albums', 'audio_years.id', 'audio_albums.year_id', 'INNER')->orderby('audio_years.name', 'DESC')->groupby('audio_years.id')->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['name'] = \_($this->response['data'][$i]['name']);
            $this->response['data'][$i]['is_year'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
    public function getPlaylistList()
    {
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_playlists')->where(['user_id' => $this->stb->id])->orderby('name')->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $this->response['data'][$i]['is_playlist'] = true;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
    public function getTrackList()
    {
        $album_id = empty($_REQUEST['album_id']) ? 0 : (int) $_REQUEST['album_id'];
        $playlist_id = empty($_REQUEST['playlist_id']) ? 0 : (int) $_REQUEST['playlist_id'];
        if ($playlist_id) {
            $playlist_tracks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_playlist_tracks')->where(['playlist_id' => $playlist_id])->orderby('added')->get()->all('track_id');
        }
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->select('audio_compositions.*, audio_languages.name as language')->where(['audio_compositions.status' => 1])->join('audio_languages', 'audio_compositions.language_id', 'audio_languages.id', 'LEFT');
        if ($album_id) {
            $result->where(['album_id' => $album_id])->orderby('number');
        }
        if ($playlist_id && isset($playlist_tracks)) {
            $result->in('audio_compositions.id', $playlist_tracks);
            if (!empty($playlist_tracks)) {
                $result->orderby('field(audio_compositions.id,' . \implode(',', $playlist_tracks) . ')');
            }
        }
        if (empty($_REQUEST['as_playlist'])) {
            $result->limit(self::MAX_PAGE_ITEMS, $offset);
        }
        $this->setResponseData($result);
        if ($album_id) {
            $album_ids = [$album_id];
        } else {
            $album_ids = [];
            for ($i = 0; $i < \count($this->response['data']); ++$i) {
                $album_ids[] = $this->response['data'][$i]['album_id'];
            }
            $album_ids = \array_unique($album_ids);
        }
        $albums_map = [];
        if (!empty($album_ids)) {
            $albums = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_albums.*,
                    audio_performers.name as performer_name,
                    audio_years.name as album_year,
                    countries.name' . ($this->stb->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? '' : '_en') . ' as album_country
                ')->from('audio_albums')->join('audio_performers', 'audio_albums.performer_id', 'audio_performers.id', 'LEFT')->join('audio_years', 'audio_albums.year_id', 'audio_years.id', 'LEFT')->join('countries', 'audio_albums.country_id', 'countries.id', 'LEFT')->in('audio_albums.id', $album_ids)->get()->all();
            foreach ($albums as $album) {
                $albums_map[$album['id']] = $album;
            }
        }
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $item = $this->response['data'][$i];
            if ($playlist_id) {
                $this->response['data'][$i]['name'] = (isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['performer_name'] . ' - ' : '') . $this->response['data'][$i]['name'];
            } else {
                $this->response['data'][$i]['name'] = $this->response['data'][$i]['number'] . '. ' . $this->response['data'][$i]['name'];
            }
            $this->response['data'][$i]['performer_name'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['performer_name'] : '';
            $this->response['data'][$i]['cmd'] = \strpos($this->response['data'][$i]['url'], 'http://') === 0 ? \str_replace(' ', '%20', $this->response['data'][$i]['url']) : $this->response['data'][$i]['url'];
            $this->response['data'][$i]['album_name'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['name'] : '';
            $this->response['data'][$i]['album_year'] = isset($albums_map[$item['album_id']]) ? \_($albums_map[$item['album_id']]['album_year']) : '';
            $this->response['data'][$i]['album_country'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['album_country'] : '';
            $this->response['data'][$i]['cover_uri'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/audio_covers/' . \ceil($item['album_id'] / 100) . '/' . (isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['cover'] : '0.jpg');
            $this->response['data'][$i]['is_track'] = true;
            $this->response['data'][$i]['is_audio'] = true;
        }
        return $this->response;
    }
    public function getUserPlaylists()
    {
        $playlists = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_playlists')->where(['user_id' => $this->stb->id])->orderby('name')->get()->all();
        return $playlists;
    }
    public function createPlaylist()
    {
        if (empty($_REQUEST['name'])) {
            return false;
        }
        $playlist_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_playlists', ['user_id' => $this->stb->id, 'name' => $_REQUEST['name'], 'modified' => 'NOW()'])->insert_id();
        if (!$playlist_id) {
            return false;
        }
        if ($_REQUEST['track_id']) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_playlist_tracks', ['playlist_id' => $playlist_id, 'track_id' => (int) $_REQUEST['track_id'], 'added' => 'NOW()']);
        }
        return $playlist_id;
    }
    public function addTrackToPlaylist()
    {
        if (empty($_REQUEST['track_id']) || empty($_REQUEST['playlist_id'])) {
            return false;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_playlist_tracks', ['playlist_id' => (int) $_REQUEST['playlist_id'], 'track_id' => (int) $_REQUEST['track_id'], 'added' => 'NOW()'])->insert_id();
    }
    public function removeFromPlaylist()
    {
        if (empty($_REQUEST['track_id']) || empty($_REQUEST['playlist_id'])) {
            return false;
        }
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_playlist_tracks', ['playlist_id' => (int) $_REQUEST['playlist_id'], 'track_id' => (int) $_REQUEST['track_id']])->result();
    }
    public function deletePlaylist()
    {
        if (empty($_REQUEST['playlist_id'])) {
            return false;
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_playlist_tracks', ['playlist_id' => (int) $_REQUEST['playlist_id']]);
        return \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_playlists', ['id' => (int) $_REQUEST['playlist_id']])->result();
    }
    public function trackSearch()
    {
        $search_str = empty($_REQUEST['search_str']) ? '' : (string) $_REQUEST['search_str'];
        if (\strlen($search_str) < 1) {
            $search_str = '';
        }
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->select('audio_compositions.*, audio_languages.name as language')->where(['audio_compositions.status' => 1])->join('audio_languages', 'audio_compositions.language_id', 'audio_languages.id', 'LEFT')->like(['audio_compositions.name' => "%{$search_str}%"])->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        $album_ids = [];
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $album_ids[] = $this->response['data'][$i]['album_id'];
        }
        $album_ids = \array_unique($album_ids);
        $albums_map = [];
        if (!empty($album_ids)) {
            $albums = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_albums.*,
                    audio_performers.name as performer_name,
                    audio_years.name as album_year,
                    countries.name' . ($this->stb->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? '' : '_en') . ' as album_country
                ')->from('audio_albums')->join('audio_performers', 'audio_albums.performer_id', 'audio_performers.id', 'LEFT')->join('audio_years', 'audio_albums.year_id', 'audio_years.id', 'LEFT')->join('countries', 'audio_albums.country_id', 'countries.id', 'LEFT')->in('audio_albums.id', $album_ids)->get()->all();
            foreach ($albums as $album) {
                $albums_map[$album['id']] = $album;
            }
        }
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $item = $this->response['data'][$i];
            $data = [];
            $data['name'] = (isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['performer_name'] . ' - ' : '') . $this->response['data'][$i]['name'];
            $data['performer_name'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['performer_name'] : '';
            $data['cmd'] = \strpos($this->response['data'][$i]['url'], 'http://') === 0 ? \str_replace(' ', '%20', $this->response['data'][$i]['url']) : $this->response['data'][$i]['url'];
            $data['album_name'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['name'] : '';
            $data['album_year'] = isset($albums_map[$item['album_id']]) ? \_($albums_map[$item['album_id']]['album_year']) : '';
            $data['album_country'] = isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['album_country'] : '';
            $data['cover_uri'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/audio_covers/' . \ceil($item['album_id'] / 100) . '/' . (isset($albums_map[$item['album_id']]) ? $albums_map[$item['album_id']]['cover'] : '0.jpg');
            $data['is_track'] = true;
            $data['is_audio'] = true;
            $this->response['data'][$i] = $data;
        }
        return $this->response;
    }
    public function albumSearch()
    {
        $search_str = empty($_REQUEST['search_str']) ? '' : (string) $_REQUEST['search_str'];
        if (empty($search_str) || \strlen($search_str) < 1) {
            $search_str = '';
        }
        $offset = $this->page * self::MAX_PAGE_ITEMS;
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_albums.*,
                audio_performers.name as performer_name,
                audio_years.name as album_year,
                countries.name' . ($this->stb->I13d54c0eabb5210dfedd94e8e165e5ba() == 'ru' ? '' : '_en') . ' as album_country
            ')->from('audio_albums')->join('audio_performers', 'audio_albums.performer_id', 'audio_performers.id', 'LEFT')->join('audio_years', 'audio_albums.year_id', 'audio_years.id', 'LEFT')->join('countries', 'audio_albums.country_id', 'countries.id', 'LEFT')->where(['audio_albums.status' => 1])->like(['audio_albums.name' => "%{$search_str}%"])->orderby('added', 'DESC')->limit(self::MAX_PAGE_ITEMS, $offset);
        $this->setResponseData($result);
        for ($i = 0; $i < \count($this->response['data']); ++$i) {
            $data = [];
            $data['name'] = $this->response['data'][$i]['performer_name'] . ' - ' . $this->response['data'][$i]['name'];
            $data['genres'] = \implode(', ', $this->getAlbumGenres($this->response['data'][$i]['id']));
            $data['tracks'] = $this->countAlbumTracks($this->response['data'][$i]['id']);
            $data['languages'] = \implode(', ', $this->getAlbumLanguages($this->response['data'][$i]['id']));
            $data['album_year'] = \_($this->response['data'][$i]['album_year']);
            $data['cover_uri'] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/audio_covers/' . \ceil($this->response['data'][$i]['id'] / 100) . '/' . $this->response['data'][$i]['cover'];
            $data['is_album'] = true;
            $data['is_search_result'] = true;
            $this->response['data'][$i] = $data;
        }
        if (!empty($_REQUEST['row'])) {
            $this->response['selected_item'] = $_REQUEST['row'] + 1;
            $this->response['cur_page'] = $this->cur_page == 0 ? 1 : $this->cur_page;
        }
        return $this->response;
    }
}
