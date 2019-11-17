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
echo '<pre>';
echo '</pre>';
$search = @$_GET['search'];
$letter = @$_GET['letter'];
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
    </style>
    <title><?php 
echo \_('Video tasks archive');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
    <tr>
        <td align="center" valign="middle" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Video tasks archive');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="<?php 
echo isset($_GET['id']) ? 'tasks_archive.php' : 'stat_moderators.php';
?>"><< <?php 
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
            <?php 
if (@$_GET['id']) {
    $archive_id = (int) $_GET['id'];
    $sql = 'select * from administrators';
    if (!\Ministra\Lib\Admin::isPageActionAllowed()) {
        $sql .= " where login='" . $_SESSION['login'] . "'";
    }
    $administrators = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
    while ($arr = $administrators->next()) {
        $uid = $arr['id'];
        ?>

            <table border="0" align="center" width="760">
                <tr>
                    <td align="center">
                        <b><?php 
        echo $arr['login'];
        ?></b>
                        <center><?php 
        echo \_('SD movies');
        ?></center>
                        <table border="1" width="100%" cellspacing="0">
                            <tr>
                                <td>#</td>
                                <td><?php 
        echo \_('Movie');
        ?></td>
                                <td><?php 
        echo \_('Opening date');
        ?></td>
                                <td><?php 
        echo \_('Closing date');
        ?></td>
                                <td width="100"><?php 
        echo \_('Duration, min');
        ?></td>
                            </tr>
                            <?php 
        $done_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('video.name, video.time, moderator_tasks.id, start_time, end_time')->from('moderator_tasks')->where(['ended' => 1, 'rejected' => 0, 'archived' => $archive_id, 'to_usr' => $uid, 'hd' => 0])->join('video', 'video.id', 'media_id', 'INNER')->get();
        $length = 0;
        $total_length = 0;
        $num = 0;
        while ($arr_done = $done_tasks->next()) {
            ++$num;
            $length = $arr_done['time'];
            $total_length += $length;
            echo '<tr>';
            echo "<td>{$num}</td>";
            echo "<td><a href='msgs.php?task={$arr_done['id']}'>" . $arr_done['name'] . '</a></td>';
            echo '<td nowrap>' . $arr_done['start_time'] . '</td>';
            echo '<td nowrap>' . $arr_done['end_time'] . '</td>';
            echo "<td align='right'>" . $length . '</td>';
            echo '</tr>';
        }
        ?>
                        </table>
                        <table border="0" width="100%">
                            <tr>
                                <td width="100%" align="right"> <?php 
        echo \_('Total duration, min');
        ?>:
                                    <b><?php 
        echo $total_length;
        ?></b></td>
                            </tr>
                        </table>
                        <br>
                        <br>

                        <center><?php 
        echo \_('HD movies');
        ?></center>
                        <table border="1" width="100%" cellspacing="0">
                            <tr>
                                <td>#</td>
                                <td><?php 
        echo \_('Movie');
        ?></td>
                                <td><?php 
        echo \_('Opening date');
        ?></td>
                                <td><?php 
        echo \_('Closing date');
        ?></td>
                                <td width="100"><?php 
        echo \_('Duration, min');
        ?></td>
                            </tr>
                            <?php 
        $done_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('video.name, video.time, moderator_tasks.id, start_time, end_time')->from('moderator_tasks')->where(['ended' => 1, 'rejected' => 0, 'archived' => $archive_id, 'to_usr' => $uid, 'hd' => 1])->join('video', 'video.id', 'media_id', 'INNER')->get();
        $length = 0;
        $total_length = 0;
        $num = 0;
        while ($arr_done = $done_tasks->next()) {
            ++$num;
            $length = $arr_done['time'];
            $total_length += $length;
            echo '<tr>';
            echo "<td>{$num}</td>";
            echo "<td><a href='msgs.php?task={$arr_done['id']}'>" . $arr_done['name'] . '</a></td>';
            echo '<td nowrap>' . $arr_done['start_time'] . '</td>';
            echo '<td nowrap>' . $arr_done['end_time'] . '</td>';
            echo "<td align='right'>" . $length . '</td>';
            echo '</tr>';
        }
        ?>
                        </table>
                        <table border="0" width="100%">
                            <tr>
                                <td width="100%" align="right"> <?php 
        echo \_('Total duration, min');
        ?>:
                                    <b><?php 
        echo $total_length;
        ?></b></td>
                            </tr>
                        </table>
                        <br>
                        <br>

                        <center><?php 
        echo \_('Rejected tasks');
        ?></center>
                        <table border="1" width="100%" cellspacing="0">
                            <tr>
                                <td>#</td>
                                <td><?php 
        echo \_('Movie');
        ?></td>
                                <td><?php 
        echo \_('Opening date');
        ?></td>
                                <td><?php 
        echo \_('Closing date');
        ?></td>
                                <td width="100"><?php 
        echo \_('Duration, min');
        ?></td>
                            </tr>
                            <?php 
        $rejected_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderator_tasks')->where(['ended' => 1, 'rejected' => 1, 'archived' => $archive_id, 'to_usr' => $uid])->get();
        $num = 0;
        while ($arr_rej = $rejected_tasks->next()) {
            ++$num;
            $length = \Ministra\OldAdmin\get_media_length_by_id($arr_rej['media_id']);
            echo '<tr>';
            echo "<td>{$num}</td>";
            echo "<td><a href='msgs.php?task={$arr_rej['id']}'>" . \Ministra\OldAdmin\get_media_name_by_id($arr_rej['media_id'], 'Video') . '</a></td>';
            echo '<td nowrap>' . $arr_rej['start_time'] . '</td>';
            echo '<td nowrap>' . $arr_rej['end_time'] . '</td>';
            echo "<td align='right'>" . $length . '</td>';
            echo '</tr>';
        }
        ?>
                        </table>
                        <br>
                        <br>
                        <hr>
                        <br>
                        <br>

                        <?php 
    }
} else {
    $page = @$_REQUEST['page'] + 0;
    $MAX_PAGE_ITEMS = 30;
    $total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->count()->from('tasks_archive')->get()->counter();
    $page_offset = $page * $MAX_PAGE_ITEMS;
    $total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
    $archive_tasks = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tasks_archive')->orderby('year, month')->limit($MAX_PAGE_ITEMS, $page_offset)->get();
    while ($arr = $archive_tasks->next()) {
        ?>

                                <table border="1" width="200" cellspacing="0">
                                    <tr>
                                        <td align="center">
                                            <a href="tasks_archive.php?id=<?php 
        echo $arr['id'];
        ?>"><?php 
        echo $arr['year'] . '-' . $arr['month'];
        ?></a>
                                        </td>
                                    </tr>
                                </table>
                                <br>

                                <?php 
    }
    echo "<table width='600' align='center' border=0>\n";
    echo "<tr>\n";
    echo "<td width='100%' align='center'>\n";
    echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
}
?>

                    </td>
                </tr>
            </table>
</body>
</html>

