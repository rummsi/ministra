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
echo \_('View message');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('View message');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="<?php 
echo $_SERVER['HTTP_REFERER'];
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
        <td>
            <?php 
$id = @$_GET['id'];
$history = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderators_history')->where(['id' => $id])->get()->first();
$id = $history['id'];
$send_time = $history['send_time'];
$task_id = $history['task_id'];
$media_name = \Ministra\OldAdmin\get_media_name_by_task_id($task_id);
$from_usr = $history['from_usr'];
$from = \Ministra\OldAdmin\get_moderator_login_by_id($from_usr);
$to_usr = $history['to_usr'];
$msg = $history['comment'];
$reply_to = $history['reply_to'];
if ($reply_to) {
    $reply_to_msg = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderators_history')->where(['id' => $reply_to])->get()->first('comment');
    $msg = '>' . $reply_to_msg . '<br/><br/>' . $msg;
}
if ($to_usr == @$_SESSION['uid']) {
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('moderators_history', ['readed' => 1, 'read_time' => 'NOW()'], ['id' => $id]);
}
?>
            <table border="0" align="center" width="620">
                <tr>
                    <td align="center"><br>
                        <br>
                        <br>
                        <table width="100%" border="0" cellspacing="0">
                            <tr>
                                <?php 
if ($from_usr != @$_SESSION['uid']) {
    ?>
                                    <td><a href="reply.php?id=<?php 
    echo $id;
    ?>"><?php 
    echo \_('Reply');
    ?></a></td>
                                    <?php 
}
?>
                            </tr>
                        </table>
                        <table width="100%" border="1" cellspacing="0">
                            <tr>
                                <td><?php 
echo \_('Date');
?></td>
                                <td><?php 
echo $send_time;
?></td>
                            </tr>
                            <tr>
                                <td><?php 
echo \_('Media');
?></td>
                                <td><?php 
echo $media_name;
?></td>
                            </tr>
                            <tr>
                                <td><?php 
echo \_('From');
?></td>
                                <td><?php 
echo $from;
?></td>
                            </tr>
                            <tr>
                                <td colspan="2"><?php 
echo $msg;
?></td>
                            </tr>
                        </table>
                        <?php 
if (\Ministra\Lib\Admin::isPageActionAllowed()) {
    ?>
                            <table width="100%" border="0" cellspacing="0">
                                <tr>
                                    <td width="100%" align="right">
                                        <a href="#"
                                           onclick='if(confirm("<?php 
    echo \_('Are you sure you want to close this task?');
    ?>")){document.location="close_task.php?id=<?php 
    echo $task_id;
    ?>"}'><?php 
    echo \_('Task accomplished');
    ?></a><br>
                                        <a href="#"
                                           onclick='if(confirm("<?php 
    echo \_('Are you sure you want to reject this task?');
    ?>")){document.location="reject_task.php?id=<?php 
    echo $task_id;
    ?>"}'><?php 
    echo \_('Task rejected');
    ?></a>
                                    </td>
                                </tr>
                            </table>
                            <?php 
}
?>
                    <td>
                </tr>
            </table>

        </td>
    </tr>
</table>

