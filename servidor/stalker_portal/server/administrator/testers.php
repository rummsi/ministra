<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
$action_name = 'add';
$action_value = \_('Add');
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
foreach (@$_POST as $key => $value) {
    $_POST[$key] = \trim($value);
}
if (@$_POST['add']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('testers', ['mac' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($_POST['mac'])]);
    \header('Location: testers.php');
    exit;
}
$id = @(int) $_GET['id'];
if (!empty($id)) {
    if (@$_POST['edit']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('testers', ['mac' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($_POST['mac'])], ['id' => $id]);
        \header('Location: testers.php');
    } elseif (@$_GET['del']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('testers', ['id' => $id]);
        \header('Location: testers.php');
    } elseif (isset($_GET['status'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
        $new_status = $_GET['status'];
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('testers', ['status' => $new_status], ['id' => $id]);
        \header('Location: testers.php');
    }
    exit;
}
if (@$_GET['edit'] && !empty($id)) {
    $action_name = 'edit';
    $action_value = \_('Save');
    $edit_tester = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('testers')->where(['id' => $id])->get()->first();
}
$testers = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('testers')->get()->all();
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
echo \_('Testers');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Testers');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="users.php"><< <?php 
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
                    <td>MAC</td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
$n = 1;
foreach ($testers as $tester) {
    echo '<tr>';
    echo '<td>' . $n . '</td>';
    echo '<td>' . $tester['mac'] . '</td>';
    echo '<td>';
    if ($tester['status'] == 1) {
        $status_str = 'on';
        $color = 'Green';
        $new_status = 0;
    } else {
        $status_str = 'off';
        $color = 'Red';
        $new_status = 1;
    }
    echo '<a href="?status=' . $new_status . '&id=' . $tester['id'] . '" style="color:' . $color . '" onclick="if(confirm(\'' . \sprintf(\_('Are you sure you want to change the status of the tester %s?'), $tester['mac']) . '\')){return true}else{return false}">' . $status_str . '</a>&nbsp;';
    echo '<a href="?edit=1&id=' . $tester['id'] . '">edit</a>&nbsp;';
    echo '<a href="?del=1&id=' . $tester['id'] . '" onclick="if(confirm(\'' . \sprintf(\_('Are you sure you want to remove the tester %s?'), $tester['mac']) . '\')){return true}else{return false}">del</a>';
    echo '</td>';
    echo '</tr>';
    ++$n;
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
                        <td>MAC</td>
                        <td><input type="text" name="mac" value="<?php 
echo @$edit_tester['mac'];
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

</body>
</html>

