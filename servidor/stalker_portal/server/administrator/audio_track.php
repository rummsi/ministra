<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (empty($_GET['album_id'])) {
    \header('Location: audio_album.php');
    exit;
}
if (@$_GET['del'] && !empty($_GET['id'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    $track = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['id' => (int) $_GET['id']])->get()->first();
    if (!empty($track)) {
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('audio_compositions', ['id' => (int) $_GET['id']]);
    }
    \header('Location: audio_track.php?album_id=' . $track['album_id']);
    exit;
}
if (isset($_GET['status']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_compositions', ['status' => (int) @$_GET['status']], ['id' => (int) @$_GET['id']]);
    $track = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['id' => (int) $_GET['id']])->get()->first();
    \header('Location: audio_track.php?album_id=' . $track['album_id']);
    exit;
}
if (!empty($_POST)) {
    if (empty($_POST['album_id']) || empty($_POST['name']) || empty($_POST['number'])) {
        $error = \_('Error: all fields are required') . ' <a href="#form">#</a>';
    } elseif (isset($_POST['save'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
        $track_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('audio_compositions', ['number' => $_POST['number'], 'name' => $_POST['name'], 'album_id' => $_POST['album_id'], 'language_id' => $_POST['language_id'], 'url' => $_POST['url'], 'added' => 'NOW()'])->insert_id();
        if (empty($error)) {
            \header('Location: audio_track.php?album_id=' . $_POST['album_id']);
            exit;
        }
    } elseif (isset($_POST['update'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        $track_id = (int) $_GET['id'];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('audio_compositions', ['number' => $_POST['number'], 'name' => $_POST['name'], 'album_id' => $_POST['album_id'], 'language_id' => $_POST['language_id'], 'url' => $_POST['url']], ['id' => $track_id]);
        if (empty($error)) {
            \header('Location: audio_track.php?album_id=' . $_POST['album_id'] . '&edit=1&id=' . (int) @$_GET['id'] . '#form');
            exit;
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
echo \_('AUDIO TRACKS');
?>
    </title>
    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script>

    $(function () {
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
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['album_id' => (int) $_GET['album_id']])->count()->get()->counter();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = \ceil($total_items / $MAX_PAGE_ITEMS);
$tracks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->select('audio_compositions.*, audio_languages.name as language')->where(['album_id' => (int) $_GET['album_id']])->join('audio_languages', 'audio_compositions.language_id', 'audio_languages.id', 'LEFT')->orderby('number')->limit($MAX_PAGE_ITEMS, $page_offset)->get();
if (isset($_GET['album_id'])) {
    $current_album = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_albums')->where(['id' => (int) $_GET['album_id']])->get()->first();
}
if (isset($_GET['id'])) {
    $current_track = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_compositions')->where(['id' => (int) $_GET['id']])->get()->first();
}
$all_languages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('audio_languages')->orderby('name')->get()->all();
?>
<body>

<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('AUDIO TRACKS');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td>
            <a href="audio_album.php"><< <?php 
echo \_('Back');
?></a>
            | <a href="javascript://" class="goto_form"><?php 
echo \_('Add');
?></a>
            | <a href="audio_language.php"><?php 
echo \_('Languages');
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
            <table border="0" align="center" width="620" style="display: none">
                <tr>
                    <td>
                        <form action="" method="GET">
                            <input type="text" name="search"
                                   value="<?php 
echo isset($_GET['search']) ? $_GET['search'] : '';
?>"><input
                                    type="submit" value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">&nbsp;
                            <font color="Gray"><?php 
echo \_('search by track name');
?></font>
                        </form>
                    <td>
                </tr>
            </table>

            <center>
                <table class='list' cellpadding='3' cellspacing='0'>
                    <tr>
                        <td class='list'><b><?php 
echo \_('Number');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Name');
?></b></td>
                        <td class='list'><b><?php 
echo \_('URL');
?></b></td>
                        <td class='list'><b><?php 
echo \_('Language');
?></b></td>
                        <td class='list'>&nbsp;</td>
                    </tr>
                    <tr>
                        <?php 
while ($track = $tracks->next()) {
    echo '<tr>';
    echo "<td class='list'>" . $track['number'] . "</a></td>\n";
    echo "<td class='list'>" . $track['name'] . "</a></td>\n";
    echo "<td class='list'>" . $track['url'] . "</a></td>\n";
    echo "<td class='list'>" . $track['language'] . "</a></td>\n";
    echo "<td class='list' nowrap><a href='?album_id=" . $track['album_id'] . '&edit=1&id=' . $track['id'] . "#form'>edit</a>&nbsp;&nbsp;";
    echo "<a href='#' onclick='if(confirm(\"" . \htmlspecialchars(\_('Do you really want to delete this record?'), \ENT_QUOTES) . '")){document.location="audio_track.php?album_id=' . $track['album_id'] . '&del=1&id=' . $track['id'] . '&search=' . @$_GET['search'] . "\"}'>del</a>&nbsp;&nbsp;\n";
    if ($track['status']) {
        echo "<a href='?album_id=" . $track['album_id'] . '&status=0&id=' . $track['id'] . "'><font color='Green'>on</font></a>";
    } else {
        echo "<a href='?album_id=" . $track['album_id'] . '&status=1&id=' . $track['id'] . "'><font color='Red'>off</font></a>";
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
echo \_('Number');
?>:
                                        </td>
                                        <td>
                                            <input type="text" size="40" name="number"
                                                   value="<?php 
echo @$current_track['number'];
?>">
                                            <input type="hidden" name="id" value="<?php 
echo @$_GET['id'];
?>">
                                            <input type="hidden" name="album_id"
                                                   value="<?php 
echo @$_GET['album_id'];
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
echo \_('Title');
?>:
                                        </td>
                                        <td>
                                            <input name="name" type="text" size="40"
                                                   value="<?php 
echo @$current_track['name'];
?>">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td align="right" valign="top">
                                            <?php 
echo \_('URL');
?>:
                                        </td>
                                        <td>
                                            <input name="url" type="text" size="40"
                                                   value="<?php 
echo @$current_track['url'];
?>">
                                        </td>
                                    </tr>

                                    <tr style="">
                                        <td align="right" valign="top">
                                            <?php 
echo \_('Language');
?>:
                                        </td>
                                        <td>
                                            <select name="language_id">
                                                <option value="0">---</option>
                                                <?php 
foreach ($all_languages as $language) {
    if (!empty($current_track) && $current_track['language_id'] == $language['id']) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option value="' . $language['id'] . '" ' . $selected . '>' . $language['name'] . '</option>';
}
?>
                                            </select>
                                            <a href="audio_language.php"><?php 
echo \_('add');
?></a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>
                                        </td>
                                        <td>
                                            <input type="submit"
                                                   value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>">&nbsp;
                                            <input type="button"
                                                   value="<?php 
echo \htmlspecialchars(\_('New'), \ENT_QUOTES);
?>"
                                                   onclick="document.location='audio_track.php?album_id=<?php 
echo @$_GET['album_id'];
?>'">
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

