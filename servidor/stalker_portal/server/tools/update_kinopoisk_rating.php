<?php

require __DIR__ . '/../common.php';
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Kinopoisk;
use Ministra\Lib\KinopoiskException;
use Ministra\Lib\Logger;
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('kinopoisk_rating', \true)) {
    \_log('Notice: kinopoisk rating disabled');
    return;
}
$movies = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('video')->where(['accessed' => 1, 'status' => 1, 'rating_last_update<' => \date('Y-m-d H:i:s', \time() - 30 * 24 * 3600)])->get()->all();
foreach ($movies as $movie) {
    try {
        if (!empty($movie['kinopoisk_id'])) {
            $rating = \Ministra\Lib\Kinopoisk::getRatingById($movie['kinopoisk_id']);
        } else {
            $rating = \Ministra\Lib\Kinopoisk::getRatingByName($movie['o_name']);
        }
    } catch (\Ministra\Lib\KinopoiskException $e) {
        \_log('Error: ' . $movie['path'] . ' (' . $movie['id'] . ') - ' . $e->getMessage());
        $logger = new \Ministra\Lib\Logger();
        $logger->setPrefix('kinopoisk_');
        $logger->error(\sprintf("[%s] - %s - \"%s\"\n", \date('r'), $e->getMessage(), \base64_encode($e->getResponse())));
        continue;
    }
    if ($rating && !empty($rating['kinopoisk_id']) && !empty($rating['rating_kinopoisk']) && $rating['rating_kinopoisk'] != $movie['rating_kinopoisk']) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('video', ['kinopoisk_id' => $rating['kinopoisk_id'], 'rating_kinopoisk' => empty($rating['rating_kinopoisk']) ? '' : $rating['rating_kinopoisk'], 'rating_count_kinopoisk' => empty($rating['rating_count_kinopoisk']) ? '' : $rating['rating_count_kinopoisk'], 'rating_imdb' => empty($rating['rating_imdb']) ? '' : $rating['rating_imdb'], 'rating_count_imdb' => empty($rating['rating_count_imdb']) ? '' : $rating['rating_count_imdb'], 'rating_last_update' => 'NOW()'], ['id' => $movie['id']]);
        \_log('Update: movie ' . $movie['path'] . ' (' . $movie['id'] . ')');
    } else {
        \_log('Ignore: movie ' . $movie['path'] . ' (' . $movie['id'] . ') rating updated');
    }
    \sleep(1);
}
