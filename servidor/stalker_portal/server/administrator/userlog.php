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
$search = @$_GET['search'];
$letter = @$_GET['letter'];
$date = @$_GET['date'];
$id = @$_GET['id'];
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
echo \_('User logs');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="620">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('User logs');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="profile.php?id=<?php 
echo @$_GET['id'];
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
</table>

<?php 
$where = '';
if (!\Ministra\OldAdmin\isset_date()) {
    $date = \date('Y-m-d');
} else {
    $date = $_GET['yy'] . '-' . $_GET['mm'] . '-' . $_GET['dd'];
}
$time_from = $date . ' 00:00:00';
$time_to = $date . ' 23:59:59';
$where .= "where time > '{$time_from}' and time < '{$time_to}'";
$mac = \Ministra\OldAdmin\get_mac_by_id();
if ($mac) {
    $where .= " and mac='{$mac}'";
} else {
    $where .= " and uid='" . $_GET['id'] . "'";
}
$where .= " and action<>'create_link()' and action<>'create_link' ";
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("select * from user_log {$where}")->count();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
$query = "select * from user_log {$where} order by time desc LIMIT  {$page_offset}, {$MAX_PAGE_ITEMS}";
$log = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query);
?>
<script type="text/javascript">
function load_log() {
  yy = document.getElementById('yy').options[document.getElementById('yy').selectedIndex].value;
  mm = document.getElementById('mm').options[document.getElementById('mm').selectedIndex].value;
  dd = document.getElementById('dd').options[document.getElementById('dd').selectedIndex].value;
  if (dd < 10) {
    dd = '0' + dd;
  }
  if (mm < 10) {
    mm = '0' + mm;
  }
  action = 'userlog.php?id=' + <?php 
echo $id;
?> +'&yy=' + yy + '&mm=' + mm + '&dd=' + dd;
  document.location = action;
}
</script>
<table border="0" align="center" width="620">

    <tr>
        <td align="center">
            <b><?php 
echo $mac;
?></b>&nbsp;&nbsp;&nbsp;&nbsp;<?php 
echo \_('Date');
?>
            <select name="yy" id="yy">
                <?php 
echo \Ministra\OldAdmin\construct_YY();
?>
            </select>
            <select name="mm" id="mm">
                <?php 
echo \Ministra\OldAdmin\construct_MM();
?>
            </select>
            <select name="dd" id="dd">
                <?php 
echo \Ministra\OldAdmin\construct_DD();
?>
            </select>
            &nbsp;<input type="button" value="<?php 
echo \htmlspecialchars(\_('Go'), \ENT_QUOTES);
?>"
                         onclick="load_log()">
        <td>
    </tr>
</table>
<?php 
echo "<center><table class='list' cellpadding='3' cellspacing='0' width='620'>\n";
echo '<tr>';
echo "<td class='list'><b>" . \_('Time') . "</b></td>\n";
echo "<td class='list'><b>" . \_('Stb action') . "</b></td>\n";
echo "<td class='list'><b>" . \_('Parameter') . "</b></td>\n";
echo "</tr>\n";
while ($arr = $log->next()) {
    echo '<tr>';
    echo "<td class='list' nowrap>" . $arr['time'] . "</td>\n";
    echo "<td class='list'>" . $arr['action'] . "</td>\n";
    echo "<td class='list'>" . \Ministra\OldAdmin\parse_param_user_log($arr['action'], $arr['param'], $arr['type']) . "</td>\n";
    echo "</tr>\n";
}
echo "</table>\n";
echo "<table width='600' align='center' border=0>\n";
echo "<tr>\n";
echo "<td width='100%' align='center'>\n";
echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
echo "</td>\n";
echo "</tr>\n";
echo "</table>\n";
echo "</center>\n";
