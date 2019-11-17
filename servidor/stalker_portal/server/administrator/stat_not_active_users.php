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
echo \_('Inactive users per month');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Inactive users per month');
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
        <td valign="top">
            <?php 
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
if ($search) {
    $query = 'select * from users where mac like "%' . $search . '%"';
}
$from_time = \date('Y-m-d H:i:s', \strtotime('-1 month'));
$not_active_in_tv = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['time_last_play_tv<' => $from_time])->orderby('id')->get();
$not_active_in_video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['time_last_play_video<' => $from_time])->orderby('id')->get();
?>

            <table border="0" align="center">
                <tr>
                    <td valign="top">
                        <?php 
$i = 1;
echo "<center><table class='list' cellpadding='3' cellspacing='0'>\n";
echo '<tr>';
echo "<td class='list'><b>#</b></td>\n";
echo "<td class='list'><b>mac</b></td>\n";
echo "<td class='list'><b>" . \_('Latest TV viewing') . "</b></td>\n";
echo "</tr>\n";
while ($arr = $not_active_in_tv->next()) {
    echo '<tr>';
    echo "<td class='list'>" . $i . "</td>\n";
    echo "<td class='list'>" . $arr['mac'] . "</td>\n";
    echo "<td class='list'>" . $arr['time_last_play_tv'] . "</td>\n";
    echo "</tr>\n";
    ++$i;
}
echo "</table>\n";
echo "</center>\n";
?>
                    </td>
                    <td>
                        &nbsp;
                    </td>
                    <td>
                        <?php 
echo "<center><table class='list' cellpadding='3' cellspacing='0'>\n";
echo '<tr>';
echo "<td class='list'><b>#</b></td>\n";
echo "<td class='list'><b>mac</b></td>\n";
echo "<td class='list'><b>" . \_('Latest VIDEO viewing') . "</b></td>\n";
echo "</tr>\n";
$i = 1;
while ($arr_video = $not_active_in_video->next()) {
    echo '<tr>';
    echo "<td class='list'>" . $i . "</td>\n";
    echo "<td class='list'>" . $arr_video['mac'] . "</td>\n";
    echo "<td class='list'>" . $arr_video['time_last_play_video'] . "</td>\n";
    echo "</tr>\n";
    ++$i;
}
echo "</table>\n";
echo "</center>\n";
?>
                    </td>
                </tr>
            </table>

