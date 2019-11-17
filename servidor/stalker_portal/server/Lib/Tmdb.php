<?php

namespace Ministra\Lib;

use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
class Tmdb
{
    public static function getRatingByName($orig_name)
    {
        $info = self::getInfoByName($orig_name);
        if (!$info) {
            return false;
        }
        $fields = \array_fill_keys(['kinopoisk_url', 'kinopoisk_id', 'rating_kinopoisk', 'rating_count_kinopoisk', 'rating_imdb', 'rating_count_imdb'], true);
        return \array_intersect_key($info, $fields);
    }
    public static function getInfoByName($orig_name)
    {
        if (empty($orig_name)) {
            return false;
        }
        $ch = \curl_init();
        if ($ch === false) {
            throw new \Ministra\Lib\TmdbException(\_('Curl initialization error'), \curl_error($ch));
        }
        $orig_name = \urlencode($orig_name);
        $lang = self::getLanguage();
        $search_url = 'http://api.themoviedb.org/3/search/multi?query=' . $orig_name . '&api_key=' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('tmdb_api_key') . "&language={$lang}&include_image_language={$lang}";
        $curl_options = [CURLOPT_URL => $search_url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Connection: keep-alive', 'Cache-Control: no-cache', 'Pragma: no-cache', 'Accept: application/json']];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy')) {
            $curl_options[CURLOPT_PROXY] = \str_replace('tcp://', '', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy'));
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_login') && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_password')) {
                $curl_options[CURLOPT_PROXYUSERPWD] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_login') . ':' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_password');
            }
        }
        \curl_setopt_array($ch, $curl_options);
        $response = \curl_exec($ch);
        \curl_close($ch);
        if ($response === false) {
            throw new \Ministra\Lib\TmdbException(\_('Curl exec failure'), \curl_error($ch));
        }
        $results = \json_decode($response, true);
        if ((!\array_key_exists('status_code', $results) || $results['status_code'] == 1) && !empty($results['results'])) {
            foreach ($results['results'] as $result) {
                if (!empty($result['media_type']) && ($result['media_type'] == 'tv' || $result['media_type'] == 'movie')) {
                    $movie_id = $result['id'];
                    return self::getInfoById($movie_id, $result['media_type']);
                }
            }
        }
        return $results;
    }
    private static function getLanguage()
    {
        $language = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('vclub_default_lang', '');
        if (empty($language)) {
            $locales = [];
            $allowed_locales = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('allowed_locales');
            foreach ($allowed_locales as $lang => $locale) {
                $locales[\substr($locale, 0, 2)] = $locale;
            }
            $accept_language = !empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
            if (!empty($_COOKIE['language']) && (\array_key_exists($_COOKIE['language'], $locales) || \in_array($_COOKIE['language'], $locales))) {
                $language = \substr($_COOKIE['language'], 0, 2);
            } else {
                if ($accept_language && \array_key_exists(\substr($accept_language, 0, 2), $locales)) {
                    $language = \substr($accept_language, 0, 2);
                } else {
                    \reset($locales);
                    $language = \key($locales);
                }
            }
        }
        return $language;
    }
    public static function getInfoById($id, $type = 'movie')
    {
        $movie_info = ['kinopoisk_id' => $id];
        $tmdb_api_key = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('tmdb_api_key');
        $lang = self::getLanguage();
        $request_url = 'http://api.themoviedb.org/3/' . $type . '/' . $id . '?append_to_response=releases,credits&api_key=' . $tmdb_api_key . "&language={$lang}&include_image_language={$lang}";
        $movie_url = 'https://www.themoviedb.org/' . $type . '/' . $id;
        $movie_info['kinopoisk_url'] = $movie_url;
        $ch = \curl_init();
        $curl_options = [CURLOPT_URL => $request_url, CURLOPT_HEADER => false, CURLOPT_RETURNTRANSFER => true, CURLOPT_HTTPHEADER => ['Connection: keep-alive', 'Cache-Control: no-cache', 'Pragma: no-cache', 'Accept: application/json']];
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy')) {
            $curl_options[CURLOPT_PROXY] = \str_replace('tcp://', '', \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy'));
            if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_login') && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::exist('http_proxy_password')) {
                $curl_options[CURLOPT_PROXYUSERPWD] = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_login') . ':' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('http_proxy_password');
            }
        }
        \curl_setopt_array($ch, $curl_options);
        $page = \curl_exec($ch);
        $moviedata = \json_decode($page, true);
        if (!\array_key_exists('status_code', $moviedata) || $moviedata['status_code'] == 1) {
            if (!empty($moviedata['imdb_id']) && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('omdb_api_key')) {
                $imdb_request = 'http://www.omdbapi.com/?i=' . $moviedata['imdb_id'] . '&apikey=' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('omdb_api_key');
                $curl_options[CURLOPT_URL] = $imdb_request;
                \curl_setopt_array($ch, $curl_options);
                $page = \curl_exec($ch);
                \curl_close($ch);
                $imdbdata = \json_decode($page, true);
            }
            if (isset($moviedata['title'])) {
                $movie_info['name'] = $moviedata['title'];
            } elseif (isset($moviedata['name'])) {
                $movie_info['name'] = $moviedata['name'];
            }
            if (empty($movie_info['name'])) {
                throw new \Ministra\Lib\TmdbException(\sprintf(\_("Movie name in '%s' not found"), $movie_url), $page);
            }
            if (isset($moviedata['original_title'])) {
                $movie_info['o_name'] = $moviedata['original_title'];
            } elseif (isset($moviedata['original_name'])) {
                $movie_info['o_name'] = $moviedata['original_name'];
            }
            if (empty($movie_info['o_name'])) {
                $movie_info['o_name'] = $movie_info['name'];
            }
            $movie_info['cover'] = 'http://image.tmdb.org/t/p/w154' . $moviedata['poster_path'];
            $movie_info['cover_big'] = 'http://image.tmdb.org/t/p/w342' . $moviedata['poster_path'];
            if (isset($moviedata['release_date'])) {
                $movie_info['year'] = \substr($moviedata['release_date'], 0, 4);
            } elseif (isset($moviedata['last_air_date'])) {
                $movie_info['year'] = \substr($moviedata['last_air_date'], 0, 4);
            }
            $movie_info['duration'] = (int) $moviedata['runtime'];
            $crew = $moviedata['credits']['crew'];
            $directors = [];
            $count = 0;
            foreach ($crew as $crew_member) {
                if ($crew_member['job'] === 'Director') {
                    $directors[] = $crew_member['name'];
                    ++$count;
                    if ($count == 3) {
                        break;
                    }
                }
            }
            $movie_info['director'] = \implode(', ', $directors);
            $cast = $moviedata['credits']['cast'];
            $actors = [];
            $count = 0;
            foreach ($cast as $cast_member) {
                $actors[] = $cast_member['name'];
                ++$count;
                if ($count == 8) {
                    break;
                }
            }
            $movie_info['actors'] = \implode(', ', $actors);
            $movie_info['description'] = $moviedata['overview'];
            $mpaa_rating = '';
            $age_rating = '';
            $releases = $moviedata['releases']['countries'];
            foreach ($releases as $release_item) {
                if ($release_item['iso_3166_1'] === 'US') {
                    $mpaa_rating = $release_item['certification'];
                }
                if ($release_item['iso_3166_1'] === 'DE') {
                    $age_rating = $release_item['certification'];
                }
            }
            $movie_info['age'] = $age_rating . '+';
            $movie_info['rating_mpaa'] = $mpaa_rating;
            $movie_info['rating_kinopoisk'] = $moviedata['vote_average'];
            $movie_info['rating_count_kinopoisk'] = (int) $moviedata['vote_count'];
            if (!empty($imdbdata['imdbRating'])) {
                $movie_info['rating_imdb'] = $imdbdata['imdbRating'];
            }
            if (!empty($imdbdata['imdbVotes'])) {
                $movie_info['rating_count_imdb'] = (int) \str_replace(',', '', $imdbdata['imdbVotes']);
            }
            $production_countries = $moviedata['production_countries'];
            $prod_countries = [];
            foreach ($production_countries as $prod_country_item) {
                $prod_countries[] = $prod_country_item['name'];
            }
            $movie_info['country'] = \implode(', ', $prod_countries);
        } else {
            throw new \Ministra\Lib\TmdbException(\_('Location does not contain movie id.') . ' ' . \sprintf(\_("Location: ('%s')"), $movie_url), $moviedata);
        }
        return $movie_info;
    }
    public static function getRatingById($id, $type = 'movie')
    {
        $info = self::getInfoById($id, $type);
        if (!$info) {
            return false;
        }
        $fields = \array_fill_keys(['kinopoisk_url', 'kinopoisk_id', 'rating_kinopoisk', 'rating_count_kinopoisk', 'rating_imdb', 'rating_count_imdb'], true);
        return \array_intersect_key($info, $fields);
    }
}
