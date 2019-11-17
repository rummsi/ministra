<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\Playlist;
$error = '';
$action_name = 'add';
$action_value = \_('Add');
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
foreach (@$_POST as $key => $value) {
    $_POST[$key] = \trim($value);
}
$id = @(int) $_GET['id'];
$playlist = new \Ministra\Lib\Playlist();
$item = $playlist->getById($_GET['playlist_id']);
if (empty($item)) {
    echo 'wtf?';
    exit;
}
$time = 0;
if (!empty($_POST)) {
    $time = (int) $_POST['hh'] * 60 + (int) $_POST['mm'];
}
if (@$_POST['add']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $playlist->addRecord(['time' => $time, 'video_id' => $_POST['video_id'], 'playlist_id' => $_GET['playlist_id']]);
    \header('Location: playlist.php?playlist_id=' . @$_GET['playlist_id']);
    exit;
}
if (!empty($id)) {
    if (@$_POST['edit']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        $playlist->updateRecord(['time' => $time, 'video_id' => $_POST['video_id']], $id);
        \header('Location: playlist.php?playlist_id=' . @$_GET['playlist_id']);
    } elseif (@$_GET['del']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
        $playlist->delRecord($id);
        \header('Location: playlist.php?playlist_id=' . @$_GET['playlist_id']);
    }
    exit;
}
if (@$_GET['edit'] && !empty($id)) {
    $action_name = 'edit';
    $action_value = \_('Save');
    $edit_record = $playlist->getRecord($id);
}
$records = $playlist->getAllRecordsByPlaylistId($_GET['playlist_id']);
$debug = '<!--' . \ob_get_contents() . '-->';
\ob_clean();
echo $debug;
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

        .list, .list td, .form {
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
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
    <title><?php 
echo \_('Playlist');
?> "<?php 
echo $item['name'];
?>"</title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Playlist');
?> "<?php 
echo $item['name'];
?>
                    "&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="playlists.php"><< <?php 
echo \_('Back');
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
        <td align="center">
            <table class='list' cellpadding='3' cellspacing='0'>
                <tr>
                    <td>#</td>
                    <td><?php 
echo \_('Time');
?></td>
                    <td><?php 
echo \_('Movie');
?> ID</td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
$i = 1;
foreach ($records as $record) {
    echo '<tr>';
    echo '<td>' . $i . '</td>';
    echo '<td>' . \Ministra\OldAdmin\time_to_hhmm($record['time']) . '</td>';
    echo '<td>' . $record['video_id'] . '</td>';
    echo '<td>';
    echo '<a href="?playlist_id=' . $_GET['playlist_id'] . '&edit=1&id=' . $record['id'] . '">edit</a>&nbsp;';
    echo '<a href="?playlist_id=' . $_GET['playlist_id'] . '&del=1&id=' . $record['id'] . '" ' . 'onclick="if(confirm(\'' . \_('Do you really want to delete this record?') . '\')){return true}else{return false}">del</a>';
    echo '</td>';
    echo '</tr>';
    ++$i;
}
?>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center">
            <br>
            <br>
            <form method="POST">
                <table class="form">
                    <tr>
                        <td><?php 
echo \_('Time');
?></td>
                        <td>
                            <select name="hh">
                                <option value="-1">--</option>
                                <?php 
$edit_hh = -1;
$edit_mm = -1;
if (!empty($edit_record)) {
    $edit_hh = \floor($edit_record['time'] / 60);
    $edit_mm = $edit_record['time'] - $edit_hh * 60;
}
for ($h = 0; $h < 24; ++$h) {
    if ($h < 10) {
        $h = '0' . $h;
    }
    $selected = '';
    if ($edit_hh == $h) {
        $selected = 'selected';
    }
    echo '<option value="' . $h . '" ' . $selected . '>' . $h . '</option>';
}
?>

                            </select>
                            :
                            <select name="mm">
                                <option value="-1">--</option>
                                <?php 
echo ' : ';
for ($m = 0; $m < 60; ++$m) {
    if ($m < 10) {
        $m = '0' . $m;
    }
    $selected = '';
    if ($edit_mm == $m) {
        $selected = 'selected';
    }
    echo '<option value="' . $m . '" ' . $selected . '>' . $m . '</option>';
}
?>
                            </select>

                        </td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('Movie');
?> ID</td>
                        <td><input name="video_id" value="<?php 
echo @$edit_record['video_id'];
?>"/></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" name="<?php 
echo $action_name;
?>"
                                   value="<?php 
echo $action_value;
?>"/></td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>

