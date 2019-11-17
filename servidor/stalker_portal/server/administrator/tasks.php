<?php

$start_time = \microtime(1);
\session_start();
\ob_start();
require_once __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
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
echo \_('Tasks');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Tasks');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
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
            <?php 
$where = '';
$uid = $_SESSION['uid'];
if (!\Ministra\Lib\Admin::isPageActionAllowed()) {
    $where = 'where id=' . @$_SESSION['uid'];
}
$sql = "select * from administrators {$where} order by login";
$administrators = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
while ($arr = $administrators->next()) {
    ?>

                <table border="0" align="center" width="680">
                    <tr>
                        <td align="center">
                            <br>
                            <b><?php 
    echo $arr['login'];
    ?></b> - <a href="last_closed_tasks.php?id=<?php 
    echo $arr['id'];
    ?>"><?php 
    echo \_('video');
    ?></a>
                            <br>
                            <table border="1" width="100%" cellspacing="0">
                                <tr>
                                    <td><b>#</b></td>
                                    <td><b><?php 
    echo \_('Movie');
    ?></b></td>
                                    <td><b><?php 
    echo \_('Opening date');
    ?></b></td>
                                    <td><b><?php 
    echo \_('Status');
    ?></b></td>
                                    <td><b><?php 
    echo \_('Messages');
    ?></b></td>
                                </tr>
                                <?php 
    $sql_open = 'select moderator_tasks.*, count(moderators_history.id) as counter, ' . 'video.name as name, video.status as status, video.accessed as accessed ' . 'from moderator_tasks inner join video on media_id=video.id ' . 'left join moderators_history on task_id=moderator_tasks.id ' . 'where moderator_tasks.ended=0 and archived=0 ' . "and moderator_tasks.to_usr={$arr['id']} " . 'group by moderators_history.task_id';
    $open_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql_open);
    $num = 1;
    while ($arr_open = $open_tasks->next()) {
        echo '<tr ';
        if (\Ministra\OldAdmin\is_answered($arr_open['id'])) {
            echo "bgcolor='#ccffcc'";
        } else {
            echo "bgcolor='#ffcccc'";
        }
        echo ' >';
        echo '<td>' . $num . '.</td>';
        echo '<td><span style="color:' . \Ministra\OldAdmin\get_video_color($arr_open) . '; font-weight:bold">' . $arr_open['name'] . '</span></td>';
        echo '<td>' . $arr_open['start_time'] . '</td>';
        echo '<td>' . ($arr_open['accessed'] ? '<b style="color:#f00">on</b>' : '<b style="color:#008000">on</b>') . '</td>';
        echo '<td>' . $arr_open['counter'] . ' / <a href="msgs.php?task=' . $arr_open['id'] . '" class="msgs"><b>' . \Ministra\OldAdmin\get_count_unreaded_msgs($arr_open['id']) . '</b></a></td>';
        echo '</tr>';
        ++$num;
    }
    ?>
                            </table>
                            <br>
                            <br>
                            <br>
                            <b><?php 
    echo $arr['login'];
    ?></b> - <a href="last_closed_karaoke.php?id=<?php 
    echo $arr['id'];
    ?>"><?php 
    echo \_('karaoke');
    ?></a>
                            <table border="1" width="100%" cellspacing="0">
                                <tr>
                                    <td><b>#</b></td>
                                    <td><b><?php 
    echo \_('Title');
    ?></b></td>
                                    <td><b><?php 
    echo \_('Performer');
    ?></b></td>
                                    <td><b><?php 
    echo \_('Date');
    ?></b></td>
                                </tr>

                                <?php 
    $open_karaoke = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('karaoke')->where(['archived' => 0, 'accessed' => 0, 'add_by' => $arr['id']])->get();
    $num = 1;
    while ($arr_open_kar = $open_karaoke->next()) {
        echo '<tr>';
        echo '<td>' . $num . '.</td>';
        echo '<td>' . $arr_open_kar['name'] . '</td>';
        echo '<td>' . $arr_open_kar['singer'] . '</td>';
        echo '<td>' . $arr_open_kar['added'] . '</td>';
        echo '</tr>';
        ++$num;
    }
    ?>

                            </table>

                        <td>
                    </tr>
                </table>
                <br>
                <br>
                <br>
                <hr>
                <?php 
}
?>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
if (\Ministra\Lib\Admin::isPageActionAllowed()) {
    $sql = 'select moderator_tasks.*, video.name as name, administrators.login as login ' . 'from administrators,moderator_tasks inner join video on media_id=video.id where ' . 'administrators.id=moderator_tasks.to_usr and  ended=0 and archived=0 and ' . '(UNIX_TIMESTAMP(NOW())-UNIX_TIMESTAMP(start_time))>864000';
    $tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
    if ($tasks->count() > 0) {
        echo '<center><b><font color="Red">' . \_('Expired 10 days') . '</font></b></center>';
        echo '<table border="1" width="680" cellspacing="0">';
        echo '<tr>';
        echo '<td><b>#</b></td>';
        echo '<td><b>' . \_('Movie') . '</b></td>';
        echo '<td><b>' . \_('Date') . '</b></td>';
        echo '<td><b>' . \_('Opening date') . '</b></td>';
        echo '<td>&nbsp;</td>';
        echo '</tr>';
        $num = 0;
        while ($arr = $tasks->next()) {
            ++$num;
            echo '<tr>';
            echo '<td>' . $num . '</td>';
            echo '<td>' . $arr['name'] . '</td>';
            echo '<td>' . $arr['login'] . '</td>';
            echo '<td>' . $arr['start_time'] . '</td>';
            echo '<td><a href="reject_task.php?id=' . $arr['id'] . '&send_to=' . $arr['media_id'] . '">' . \_('reject') . '</a></td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    echo '<br>';
    echo '<br>';
    echo '<br>';
}
?>
        </td>
    </tr>
</table>
<?php 
echo 'queries: ' . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::get_num_queries() . '<br>';
echo 'generated in: ' . \round(\microtime(1) - $start_time, 3) . 's';
