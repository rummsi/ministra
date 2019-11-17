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
if (\count($_POST) > 0) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $hist_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('moderators_history', ['task_id' => $_POST['task_id'], 'from_usr' => $_SESSION['uid'], 'to_usr' => $_POST['to_usr'], 'comment' => $_POST['comment'], 'send_time' => 'NOW()', 'reply_to' => $_POST['reply_to']])->insert_id();
    if ($hist_id) {
        \js_redirect('tasks.php', \_('message sended'));
    } else {
        echo 'error';
    }
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
    </style>
    <title><?php 
echo \_('Send message');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Send message');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="tasks.php"><< <?php 
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
$reply_to = @$_GET['id'];
$task_id = \Ministra\OldAdmin\get_task_id_by_msg_id($reply_to);
$media_name = \Ministra\OldAdmin\get_media_name_by_task_id($task_id);
$to_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('moderators_history')->where(['id' => $reply_to])->get()->first('from_usr');
$to = \Ministra\OldAdmin\get_moderator_login_by_id($to_id);
?>
            <table border="0" align="center" width="620">
                <tr>
                    <td align="center"><br>
                        <br>
                        <br>

                        <form method="POST">
                            <table border="0">
                                <tr>
                                    <td valign="top"><?php 
echo \_('Movie');
?>:</td>
                                    <td>
                                        <?php 
echo $media_name;
?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php 
echo \_('To');
?>:</td>
                                    <td>
                                        <?php 
echo $to;
?>
                                        <input type="hidden" name="to_usr" value="<?php 
echo $to_id;
?>">
                                        <input type="hidden" name="task_id" value="<?php 
echo $task_id;
?>">
                                        <input type="hidden" name="reply_to" value="<?php 
echo $reply_to;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top"><?php 
echo \_('Comment');
?>:</td>
                                    <td>
                                        <textarea name="comment" cols="30" rows="8"></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>
                                        <input type="submit"
                                               value="<?php 
echo \htmlspecialchars(\_('Send'), \ENT_QUOTES);
?>">
                                    </td>
                                </tr>

                            </table>
                        </form>
                    <td>
                </tr>
            </table>

        </td>
    </tr>
</table>

