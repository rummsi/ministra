<?php

\set_time_limit(120);
\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Epg;
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
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('epg_setting', ['uri' => @$_POST['uri'], 'id_prefix' => @$_POST['id_prefix']]);
    \header('Location: epg_setting.php');
    exit;
}
$id = @(int) $_GET['id'];
if (!empty($id)) {
    if (@$_POST['edit']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('epg_setting', ['uri' => @$_POST['uri'], 'id_prefix' => @$_POST['id_prefix']], ['id' => (int) $_GET['id']]);
        \header('Location: epg_setting.php');
        exit;
    } elseif (@$_GET['del']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('epg_setting', ['id' => (int) $_GET['id']]);
        \header('Location: epg_setting.php');
        exit;
    } elseif (isset($_GET['status'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('epg_setting', ['status' => (int) $_GET['status']], ['id' => (int) $_GET['id']]);
        \header('Location: epg_setting.php');
        exit;
    }
}
if (@$_GET['edit'] && !empty($id)) {
    $action_name = 'edit';
    $action_value = \_('Save');
    $edit = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('epg_setting')->where(['id' => $id])->get()->first();
}
if (isset($_GET['update_epg'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_PAGE_ACTION);
    $epg = new \Ministra\Lib\Epg();
    if (isset($_GET['force'])) {
        $force = \true;
    } else {
        $force = \false;
    }
    $error = $epg->updateEpg($force);
}
$settings = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('epg_setting')->get()->all();
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
echo \_('EPG settings');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('EPG settings');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a> | <a
                    href="?update_epg"><?php 
echo \_('Update EPG');
?></a> | <a
                    href="?update_epg&force"><?php 
echo \_('Force update EPG');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <strong>
    <pre>
    <?php 
echo $error;
?>
    </pre>
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
                    <td>ID</td>
                    <td>URI</td>
                    <td><?php 
echo \_('ID prefix');
?></td>
                    <td>ETag/MD5</td>
                    <td><?php 
echo \_('Updated');
?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
foreach ($settings as $setting) {
    echo '<tr>';
    echo '<td>' . $setting['id'] . '</td>';
    echo '<td>' . $setting['uri'] . '</td>';
    echo '<td>' . $setting['id_prefix'] . '</td>';
    echo '<td>' . $setting['etag'] . '</td>';
    echo '<td>' . $setting['updated'] . '</td>';
    echo '<td>';
    if ($setting['status'] == 1) {
        $status_str = 'on';
        $color = 'Green';
        $new_status = 0;
    } else {
        $status_str = 'off';
        $color = 'Red';
        $new_status = 1;
    }
    echo '<a href="?status=' . $new_status . '&id=' . $setting['id'] . '" style="color:' . $color . '">' . $status_str . '</a>&nbsp;';
    echo '<a href="?edit=1&id=' . $setting['id'] . '">edit</a>&nbsp;';
    echo '<a href="?del=1&id=' . $setting['id'] . '" onclick="if(confirm(\'' . \_('Do you really want to delete this record?') . '\')){return true}else{return false}">del</a>';
    echo '</td>';
    echo '</tr>';
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
                        <td>URI</td>
                        <td><input type="text" name="uri" value="<?php 
echo @$edit['uri'];
?>" size="50"/></td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('ID prefix');
?>:</td>
                        <td><input type="text" name="id_prefix" value="<?php 
echo @$edit['id_prefix'];
?>" size=""/></td>
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

