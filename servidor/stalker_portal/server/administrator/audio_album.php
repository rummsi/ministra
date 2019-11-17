<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (@$_GET['del'] && !empty($_GET['id'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_compositions', ['album_id' => (int) $_GET['id']]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_genre', ['album_id' => (int) $_GET['id']]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_albums', ['id' => (int) $_GET['id']]);
    \header('Location: audio_album.php');
    exit;
}
if (isset($_GET['status']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_albums', ['status' => (int) @$_GET['status']], ['id' => (int) @$_GET['id']]);
    \header('Location: audio_album.php');
    exit;
}
if (!empty($_POST)) {
    if (empty($_POST['performer_id']) || empty($_POST['name']) || empty($_POST['genre_ids'])) {
        $error = \_('Error: all fields are required') . ' <a href="#form">#</a>';
    } elseif (isset($_POST['save'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
        $album_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_albums', ['performer_id' => $_POST['performer_id'], 'name' => $_POST['name'], 'year_id' => $_POST['year_id'], 'country_id' => $_POST['country_id'], 'added' => 'NOW()'])->insert_id();
        $_POST['genre_ids'] = \array_unique(\array_values(\array_filter($_POST['genre_ids'], function ($genre_id) {
            return $genre_id != 0;
        })));
        $genres_data = [];
        foreach ($_POST['genre_ids'] as $genre_id) {
            $genres_data[] = ['album_id' => $album_id, 'genre_id' => $genre_id];
        }
        if (!empty($genres_data)) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_genre', $genres_data);
        }
        if (!empty($_FILES['cover']['name'])) {
            if ($cover = \Ministra\OldAdmin\handle_upload_logo($_FILES['cover'], $album_id)) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_albums', ['cover' => $cover], ['id' => $album_id]);
            } else {
                $error = \_('Error: could not save cover') . ' <a href="#form">#</a>';
            }
        }
        if (empty($error)) {
            \header('Location: audio_album.php');
            exit;
        }
    } elseif (isset($_POST['update'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        $album_id = (int) $_GET['id'];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_albums', ['performer_id' => $_POST['performer_id'], 'name' => $_POST['name'], 'year_id' => $_POST['year_id'], 'country_id' => $_POST['country_id'], 'added' => 'NOW()'], ['id' => $album_id]);
        $existed_genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_genre')->where(['album_id' => $album_id])->get()->all('genre_id');
        $_POST['genre_ids'] = \array_unique(\array_values(\array_filter($_POST['genre_ids'], function ($genre_id) {
            return $genre_id != 0;
        })));
        $need_to_add_genres = \array_diff($_POST['genre_ids'], $existed_genres);
        $need_to_delete_genres = \array_diff($existed_genres, $_POST['genre_ids']);
        if (!empty($need_to_add_genres)) {
            $genres_data = [];
            foreach ($need_to_add_genres as $genre_id) {
                $genres_data[] = ['album_id' => $album_id, 'genre_id' => $genre_id];
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_genre', $genres_data);
        }
        if (!empty($need_to_delete_genres)) {
            foreach ($need_to_delete_genres as $genre_id) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_genre', ['album_id' => $album_id, 'genre_id' => $genre_id]);
            }
        }
        if ($_POST['remove_cover'] && empty($_FILES['cover']['name'])) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_albums', ['cover' => ''], ['id' => $album_id]);
        } elseif (!empty($_FILES['cover']['name'])) {
            if ($cover = \Ministra\OldAdmin\handle_upload_logo($_FILES['cover'], $album_id)) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_albums', ['cover' => $cover], ['id' => $album_id]);
            } else {
                $error = \_('Error: could not save cover') . ' <a href="#form">#</a>';
            }
            if (empty($error)) {
                \header('Location: audio_album.php?edit=1&id=' . (int) @$_GET['id'] . '#form');
                exit;
            }
        }
    }
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <style type="text/css">

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
        }

        td {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: #000000;
        }

        .list {
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
        }

        .list2 {
            border-width: 1px;
            border-style: solid;
            border-color: #c5c5c5;
            padding-left: 5px;
        }

        a {
            color: #0000FF;
            font-weight: bold;
            text-decoration: none;
        }

        a:link, a:visited {
            color: #5588FF;
            font-weight: bold;
        }

        a:hover {
            color: #0000FF;
            font-weight: bold;
            text-decoration: underline;
        }

    </style>
    <title>
        <?php 
echo \_('AUDIO ALBUMS');
?>
    </title>
    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script>

    $(function () {
      $('.del_cover').live('click', function () {
        $('.cover_block').html('');
        $('.remove_cover').val('1');
        return false;
      });

      $('.genre').change(function () {
        var genre_id = $(this).find('option:selected').val();
        var idx = parseInt($(this).attr('data-number'), 10);

        if (idx < 4) {
          if (genre_id == 0) {
            $('.genre-' + (idx + 1)).hide();
          } else if (genre_id > 0) {
            $('.genre-' + (idx + 1)).show();
          }
        }
      });

      $('.goto_form').click(function () {
        $('html, body').animate({
          scrollTop: $('#form').offset().top
        }, 2000);
      });

    });
    </script>
</head>
<?php 
$MAX_PAGE_ITEMS = 30;
$page = isset($_GET['page']) ? $_GET['page'] : 0;
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_albums')->count()->get()->counter();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = \ceil($total_items / $MAX_PAGE_ITEMS);
$albums = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('audio_albums.*,
        audio_performers.name as performer_name,
        audio_years.name as album_year,
        countries.name' . (isset($_COOKIE['language']) && $_COOKIE['language'] == 'ru' ? '' : '_en') . ' as album_country')->from('audio_albums')->join('audio_performers', 'audio_albums.performer_id', 'audio_performers.id', 'LEFT')->join('audio_years', 'audio_albums.year_id', 'audio_years.id', 'LEFT')->join('countries', 'audio_albums.country_id', 'countries.id', 'LEFT')->limit($MAX_PAGE_ITEMS, $page_offset)->get();
if (isset($_GET['id'])) {
    $current_album = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_albums')->where(['id' => (int) $_GET['id']])->get()->first();
    $current_album_genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_genre')->where(['album_id' => (int) $_GET['id']])->get()->all('genre_id');
}
$all_performers = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_performers')->orderby('name')->get()->all();
$all_genres = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_genres')->orderby('name')->get()->all();
$all_years = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_years')->orderby('name')->get()->all();
$all_countries = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('countries')->orderby('name' . (isset($_COOKIE['language']) && $_COOKIE['language'] == 'ru' ? '' : '_en'))->get()->all();
?>
<body>

<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('AUDIO ALBUMS');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td>
            <a href="index.php"><< <?php 
echo \_('Back');
?></a>
            | <a href="javascript://" class="goto_form"><?php 
echo \_('Add');
?></a>
            | <a href="audio_performer.php"><?php 
echo \_('Performers');
?></a>
            | <a href="audio_genre.php"><?php 
echo \_('Genres');
?></a>
            | <a href="audio_year.php"><?php 
echo \_('Years');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <strong>
                    <?php 
echo $error;
?>
                </strong>
            </font>
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td>
            <table border="0" align="center" width="620">
                <tr>
                    <td>
                        <form action="" method="GET">
                            <input type="text" name="search"
                                   value="<?php 
echo isset($_GET['search']) ? $_GET['search'] : '';
?>"><input
                                    type="submit" value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">
                            &nbsp;
                            <font color="Gray"><?php 
echo \_('search by album name or performer');
?></font>
                        </form>
                    <td>
                </tr>
            </table>

            <center>
                <table class='list' cellpadding='3' cellspacing='0'>
                    <tr>
                        <td class='list'><b><?php 
echo \_('Title');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Tracks');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Genre');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Year');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Country');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Language');
?></b></td>
                        <td class='list'>&nbsp;</td>
                    </tr>
                    <tr>
                        <?php 
while ($album = $albums->next()) {
    echo '<tr>';
    echo "<td class='list'><a href='audio_track.php?album_id=" . $album['id'] . "'>" . $album['performer_name'] . ' - ' . $album['name'] . "</a></td>\n";
    echo "<td class='list'>" . \Ministra\OldAdmin\count_album_tracks($album['id']) . "</td>\n";
    echo "<td class='list'>" . \implode(', ', \Ministra\OldAdmin\get_album_genres($album['id'])) . "</td>\n";
    echo "<td class='list'>" . \_($album['album_year']) . "</td>\n";
    echo "<td class='list'>" . $album['album_country'] . "</td>\n";
    echo "<td class='list'>" . \implode(', ', \Ministra\OldAdmin\get_album_languages($album['id'])) . "</td>\n";
    echo "<td class='list' nowrap><a href='?edit=1&id=" . $album['id'] . "#form'>edit</a>&nbsp;&nbsp;";
    echo "<a href='#' onclick='if(confirm(\"" . \htmlspecialchars(\_('Do you really want to delete this record?'), \ENT_QUOTES) . '")){document.location="audio_album.php?del=1&id=' . $album['id'] . '&search=' . @$_GET['search'] . "\"}'>del</a>&nbsp;&nbsp;\n";
    if ($album['status']) {
        echo "<a href='?status=0&id=" . $album['id'] . "'><font color='Green'>on</font></a>";
    } else {
        echo "<a href='?status=1&id=" . $album['id'] . "'><font color='Red'>off</font></a>";
    }
    echo '</td>';
    echo '</tr>';
}
?>
                    </tr>
                </table>
                <table width='600' align='center' border=0>
                    <tr>
                        <td width='100%' align='center'>
                            <?php 
echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
?>
                        </td>
                    </tr>
                </table>

                <a name="form"></a>
                <table align="center" class='list'>
                    <tr>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <form id="form" enctype="multipart/form-data" method="POST">
                                <table align="center">
                                    <tr>
                                        <td align="right">
                                            <?php 
echo \_('Performer');
?>:
                                        </td>
                                        <td>
                                            <select name="performer_id">
                                                <option value="0">---</option>
                                                <?php 
foreach ($all_performers as $performer) {
    if (!empty($current_album) && $current_album['performer_id'] == $performer['id']) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option value="' . $performer['id'] . '" ' . $selected . '>' . $performer['name'] . '</option>';
}
?>
                                            </select>
                                            <a href="audio_performer.php"><?php 
echo \_('add');
?></a>
                                            <input type="hidden" id="id" value="<?php 
echo @$_GET['id'];
?>">
                                            <input type="hidden" name="<?php 
echo @$_GET['id'] ? 'update' : 'save';
?>"
                                                   value="1">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Album');
?>:
                                        </td>
                                        <td>
                                            <input name="name" type="text" size="40"
                                                   value="<?php 
echo @$current_album['name'];
?>">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Genres');
?>:
                                        </td>
                                        <td>

                                            <?php 
for ($i = 0; $i <= 3; ++$i) {
    ?>
                                                <div class="genre-<?php 
    echo $i + 1;
    ?>"
                                                    <?php 
    echo empty($current_album_genres[$i]) && $i > 0 && empty($current_album_genres[$i - 1]) && !empty($current_album) || empty($current_album) && $i > 0 ? 'style="display:none"' : '';
    ?>>
                                                    <select class="genre" data-number="<?php 
    echo $i + 1;
    ?>"
                                                            name="genre_ids[]">
                                                        <option value="0">---</option>
                                                        <?php 
    foreach ($all_genres as $genre) {
        if (!empty($current_album_genres[$i]) && $current_album_genres[$i] == $genre['id']) {
            $selected = 'selected';
        } else {
            $selected = '';
        }
        echo '<option value="' . $genre['id'] . '" ' . $selected . '>' . \_($genre['name']) . '</option>';
    }
    ?>
                                                    </select>
                                                    <?php 
    if ($i == 0) {
        ?>
                                                        <a href="audio_genre.php"><?php 
        echo \_('add');
        ?></a>
                                                    <?php 
    }
    ?>
                                                </div>
                                                <?php 
}
?>
                                        </td>
                                    </tr>

                                    <tr style="">
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Year');
?>:
                                        </td>
                                        <td>
                                            <select name="year_id">
                                                <option value="0">---</option>
                                                <?php 
foreach ($all_years as $year) {
    if (!empty($current_album) && $current_album['year_id'] == $year['id']) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option value="' . $year['id'] . '" ' . $selected . '>' . $year['name'] . '</option>';
}
?>
                                            </select>
                                            <a href="audio_year.php"><?php 
echo \_('add');
?></a>
                                        </td>
                                    </tr>

                                    <tr style="">
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Country');
?>:
                                        </td>
                                        <td>
                                            <select name="country_id">
                                                <option value="0">---</option>
                                                <?php 
foreach ($all_countries as $country) {
    if (!empty($current_album) && $current_album['country_id'] == $country['id']) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option value="' . $country['id'] . '" ' . $selected . '>' . $country['name' . ($_COOKIE['language'] == 'ru' ? '' : '_en')] . '</option>';
}
?>
                                            </select>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Cover');
?>:
                                        </td>
                                        <td>
                                            <?php 
if (!empty($current_album['cover'])) {
    ?>
                                                <div class="cover_block">
                                                    <img src="<?php 
    echo \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('portal_url') . 'misc/audio_covers/' . \ceil($current_album['id'] / 100) . '/' . $current_album['cover'];
    ?>"
                                                         style="float: left"/>
                                                    <div style="float:left"><a href="#" class="del_cover">x</a></div>
                                                </div>
                                            <?php 
}
?>
                                            <input name="remove_cover" class="remove_cover" type="hidden" value="0">
                                            <input name="cover" class="cover" size="27" type="file">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <input type="submit"
                                                   value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>">
                                            &nbsp;
                                            <input type="button"
                                                   value="<?php 
echo \htmlspecialchars(\_('New'), \ENT_QUOTES);
?>"
                                                   onclick="document.location='audio_album.php'">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                    </tr>
                </table>
            </center>
        </td>
    </tr>
</table>

</body>
</html>

