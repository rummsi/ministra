<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$storage_name = @$_GET['storage'];
$storage = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['storage_name' => $storage_name])->get()->first();
if (empty($storage)) {
    echo '<center><h1>низя!</h1></center>';
    exit;
}
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
echo \_('Current views on storage');
echo $storage_name;
?></title>
    </head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px"
                  color="White"><b>&nbsp;<?php 
echo \_('Current views on storage');
?> <?php 
echo $storage_name;
?>
                    &nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a>
            <?php 
?>
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
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
$where = ' where now_playing_type=' . (int) $_GET['type'] . " and storage_name='{$storage_name}' and UNIX_TIMESTAMP(keep_alive)>UNIX_TIMESTAMP(NOW())-" . \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("select * from users {$where}")->count();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
$query = "select * from users {$where} order by mac LIMIT {$page_offset}, {$MAX_PAGE_ITEMS}";
$users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query);
echo "<center><table class='list' cellpadding='3' cellspacing='0'>\n";
echo '<tr>';
echo "<td class='list'><b>MAC</b></td>\n";
echo "<td class='list'><b>" . \_('Movie') . "</b></td>\n";
echo "<td class='list'><b>" . \_('Begin') . "</b></td>\n";
echo "</tr>\n";
while ($arr = $users->next()) {
    echo '<tr>';
    echo "<td class='list'><a href='profile.php?id=" . $arr['id'] . "'>" . $arr['mac'] . "</a></td>\n";
    echo "<td class='list'>" . $arr['now_playing_content'] . "</td>\n";
    echo "<td class='list'>" . $arr['now_playing_start'] . "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";
echo "<table width='700' align='center' border=0>\n";
echo "<tr>\n";
echo "<td width='100%' align='center'>\n";
echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</center>\n";
