<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Video;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (\count($_POST) > 0) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $task_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('moderator_tasks', ['to_usr' => $_POST['to_usr'], 'media_type' => 2, 'media_id' => $_POST['id'], 'start_time' => 'NOW()'])->insert_id();
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('moderators_history', ['task_id' => $task_id, 'from_usr' => $_SESSION['uid'], 'to_usr' => $_POST['to_usr'], 'comment' => $_POST['comment'], 'send_time' => 'NOW()']);
    \Ministra\Lib\Video::log((int) $_POST['id'], '<a href="msgs.php?task=' . $task_id . '">' . \_('task open') . '</a>', (int) $_POST['to_usr']);
    if ($task_id) {
        \js_redirect('add_video.php', \_('the task has been sent'));
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
echo \_('Create task');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Create task');
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
                                        <input type="text" size="32" readonly
                                               value="<?php 
echo \Ministra\OldAdmin\get_sended_video();
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php 
echo \_('To');
?>:</td>
                                    <td>
                                        <select name="to_usr">
                                            <option>- - - - - - - - - - - - -
                                                <?php 
echo \Ministra\OldAdmin\get_moderators();
?>
                                        </select>
                                        <input type="hidden" name="id" value="<?php 
echo @$_GET['id'];
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

