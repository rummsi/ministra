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
if (@$_GET['archive'] == 1 && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) @$_GET['id'];
    $year = \date('Y');
    $month = \date('n');
    if ($month == 1) {
        $month = 12;
        --$year;
    } else {
        --$month;
    }
    $archive_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tasks_archive')->where(['month' => $month, 'year' => $year])->get()->first('id');
    if (empty($archive_id)) {
        $archive_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('tasks_archive', ['date' => 'NOW()', 'year' => $year, 'month' => $month]);
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('moderator_tasks', ['archived' => $archive_id, 'archived_time' => 'NOW()'], ['archived' => 0, 'ended' => 1, 'to_usr' => $id]);
    $archive_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke_archive')->where(['month' => $month, 'year' => $year])->get()->first('id');
    if (empty($archive_id)) {
        $archive_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('karaoke_archive', ['date' => 'NOW()', 'year' => $year, 'month' => $month]);
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['archived' => $archive_id, 'archived_time' => 'NOW()'], ['archived' => 0, 'status' => 1, 'accessed' => 1]);
    \header('Location: last_closed_tasks.php?id=' . $id);
    exit;
}
if (isset($_GET['accessed']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\set_karaoke_accessed(@$_GET['id'], @$_GET['accessed']);
    $id = @$_GET['id'];
    if ($_GET['accessed'] == 1) {
        \chmod(\KARAOKE_STORAGE_DIR . '/' . $id . '.mpg', 0444);
    } else {
        \chmod(\KARAOKE_STORAGE_DIR . '/' . $id . '.mpg', 0666);
    }
    \header('Location: last_closed_karaoke.php?id=' . @$_GET['uid']);
    exit;
}
if (isset($_GET['returned']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\set_karaoke_returned(@$_GET['id'], @$_GET['returned'], @$_GET['reason']);
    \header('Location: last_closed_karaoke.php?id=' . @$_GET['uid']);
    exit;
}
if (isset($_GET['done']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\set_karaoke_done(@$_GET['id'], @$_GET['done']);
    $id = @$_GET['id'];
    \header('Location: last_closed_karaoke.php?id=' . @$_GET['uid']);
    exit;
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

        a.msgs:hover, a.msgs:visited, a.msgs:link {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #000000;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
    <title><?php 
echo \_('Completed Karaoke tasks');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Completed Karaoke tasks');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="stat_moderators.php"><< <?php 
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
        <td>

            <table border="0" align="center" width="620">
                <tr>
                    <td>
                        <font color="Gray">* <?php 
echo \_('report generated by closed-month clips');
?></font>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
<?php 
$where = '';
if (\Ministra\Lib\Admin::isPageActionAllowed()) {
    $uid = @$_GET['id'];
} else {
    $uid = @$_SESSION['uid'];
}
?>

<table border="0" align="center" width="760">
    <tr>
        <td align="center">

            <table border="1" width="100%" cellspacing="0">
                <tr>
                    <td>#</td>
                    <td><?php 
echo \_('Title');
?></td>
                    <td><?php 
echo \_('Performer');
?></td>
                    <td><?php 
echo \_('Turn on date');
?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
$karaoke = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['archived' => 0, 'add_by' => $uid])->orderby('name')->get();
$num = 0;
while ($arr_done = $karaoke->next()) {
    ++$num;
    echo '<tr>';
    echo "<td>{$num}</td>";
    echo '<td>' . $arr_done['name'] . '</td>';
    echo '<td>' . $arr_done['singer'] . '</td>';
    echo '<td>' . $arr_done['added'] . '</td>';
    echo '<td>';
    echo \Ministra\OldAdmin\get_karaoke_accessed_color($arr_done['id']) . '&nbsp;&nbsp;';
    echo \Ministra\OldAdmin\get_done_karaoke_color($arr_done['id']) . '&nbsp;&nbsp;';
    echo \Ministra\OldAdmin\return_karaoke($arr_done['id'], $arr_done['returned'], $arr_done['reason']);
    echo '</td>';
    echo '</tr>';
}
?>
            </table>
            <br>
            <br>

            <br>
            <br>
            <table border="0" width="100%">
            </table>
        <td>
    </tr>
</table>

