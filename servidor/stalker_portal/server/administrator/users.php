<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\RemotePvr;
$error = '';
$last_action = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$search = @$_GET['search'];
$letter = @$_GET['letter'];
if (@$_GET['action'] == 'cut_off' && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('deny_change_user_status', \false)) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\cut_off_user(@$_GET['id']);
    \header('Location: users.php?search=' . $_GET['search']);
    exit;
}
if (@$_GET['del'] && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('deny_delete_user', \false)) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    $id = (int) @$_GET['id'];
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('users', ['id' => $id]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('fav_itv', ['uid' => $id]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('fav_vclub', ['uid' => $id]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('media_favorites', ['uid' => $id]);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('access_tokens', ['uid' => $id]);
    \Ministra\Lib\RemotePvr::delAllUserRecs($id);
    \header('Location: users.php?search=' . $_GET['search'] . '&page=' . $_GET['page']);
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

        table.list tr:nth-child(odd) {
            background: #FFFFFF;
        }

        table.list tr:nth-child(even) {
            background: #EFF5FB;
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
echo \_('Users');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="760">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Users');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a> | <a href="user.php"><?php 
echo \_('Add');
?></a> | <a
                    href="testers.php"><?php 
echo \_('Testers');
?></a> | <a
                    href="stbgroups.php"><?php 
echo \_('Stb groups');
?></a> | <a
                    href="all_userlog.php"><?php 
echo \_('All logs');
?></a> | <a href="today_user_status_report.php"
                                                                                  target="_blank"><?php 
echo \_('Report');
?></a>
            | <a href="subscribe_import.php"><?php 
echo \_('Subscription import');
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
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
$where = '';
if ($search) {
    $where = 'where mac like "%' . $search . '%" or ip like "%' . $search . '%" or login like "%' . $search . '%" or ls like "%' . $search . '%" or users.name like "%' . $search . '%" or fname like "%' . $search . '%"';
}
$from_time = \Ministra\OldAdmin\construct_time();
if ($from_time) {
    $where .= "where last_active<='{$from_time}'";
}
$now_timestamp = \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2;
$now_time = \date('Y-m-d H:i:s', $now_timestamp);
switch (@$_GET['sort_by']) {
    case 'online':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' order by id");
        break;
    case 'offline':
        \Ministra\OldAdmin\add_where($where, " keep_alive<'{$now_time}' order by id");
        break;
    case 'on':
        \Ministra\OldAdmin\add_where($where, ' status=0 order by id');
        break;
    case 'off':
        \Ministra\OldAdmin\add_where($where, ' status=1 order by id');
        break;
    case 'iptv':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=1 order by id");
        break;
    case 'video':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=2 order by id");
        break;
    case 'audioclub':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=4 order by id");
        break;
    case 'ad':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=9 order by id");
        break;
    case 'karaoke':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=3 order by id");
        break;
    case 'radio':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=5 order by id");
        break;
    case 'my_records':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=6 order by id");
        break;
    case 'shared_records':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=7 order by id");
        break;
    case 'city_info':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=20 order by id");
        break;
    case 'anec_page':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=21 order by id");
        break;
    case 'weather_page':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=22 order by id");
        break;
    case 'game_page':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=23 order by id");
        break;
    case 'horoscope_page':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=24 order by id");
        break;
    case 'course_page':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=25 order by id");
        break;
    case 'infoportal':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type>=20 and now_playing_type<=29 order by id");
        break;
    case 'tv_archive':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=11 order by id");
        break;
    case 'records':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=12 order by id");
        break;
    case 'timeshift':
        \Ministra\OldAdmin\add_where($where, " keep_alive>'{$now_time}' and now_playing_type=14 order by id");
        break;
    case 'none':
    default:
}
$query = "select * from users {$where}";
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query)->count();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
if (!$where) {
    $where = 'order by id';
}
$query = "select users.*, tariff_plan.name as tariff_plan_name from users left join tariff_plan on tariff_plan.id=tariff_plan_id {$where} LIMIT {$page_offset}, {$MAX_PAGE_ITEMS}";
$users = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query);
?>
<script type="text/javascript">
function sort_page() {
  var opt_sort = document.getElementById('sort_by');
  document.location = 'users.php?sort_by=' + opt_sort.options[opt_sort.selectedIndex].value +<?php 
echo '\'&search=' . @$_GET['search'] . '&letter=' . @$_GET['letter'] . '&yy=' . @$_GET['yy'] . '&mm=' . @$_GET['mm'] . '&dd=' . @$_GET['dd'] . '&hh=' . @$_GET['hh'] . '&ii=' . @$_GET['ii'] . '\';';
?>;
}
</script>
<table border="0" align="center" width="620">
    <tr>
        <td>
            <form action="" method="GET">
                <input type="text" name="search" value="<?php 
echo $search;
?>"><input type="submit"
                                                                                       value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">&nbsp;<font
                        color="Gray"><?php 
echo \_('search by MAC, IP, login or account number');
?></font>
            </form>
        <td>
    </tr>
    <tr>
        <td>
            <select id="sort_by" onchange="sort_page()">
                <option value="none"><?php 
echo \_('without sorting');
?>
                <option value="online" <?php 
if (@$_GET['sort_by'] == 'online') {
    echo 'selected';
}
?>>online
                <option value="offline" <?php 
if (@$_GET['sort_by'] == 'offline') {
    echo 'selected';
}
?>>offline
                <option value="on" <?php 
if (@$_GET['sort_by'] == 'on') {
    echo 'selected';
}
?>>on
                <option value="off" <?php 
if (@$_GET['sort_by'] == 'off') {
    echo 'selected';
}
?>>off
                <option value="iptv" <?php 
if (@$_GET['sort_by'] == 'iptv') {
    echo 'selected';
}
?>>iptv
                <option value="video" <?php 
if (@$_GET['sort_by'] == 'video') {
    echo 'selected';
}
?>>video
                <option value="audioclub" <?php 
if (@$_GET['sort_by'] == 'audioclub') {
    echo 'selected';
}
?>>audioclub
                <option value="radio" <?php 
if (@$_GET['sort_by'] == 'radio') {
    echo 'selected';
}
?>>radio
                <option value="karaoke" <?php 
if (@$_GET['sort_by'] == 'karaoke') {
    echo 'selected';
}
?>>karaoke
                <option value="records" <?php 
if (@$_GET['sort_by'] == 'records') {
    echo 'selected';
}
?>>records
                    <!--<option value="my_records" <?php 
?>>my records-->
                    <!--<option value="shared_records" <?php 
?>>shared records
                <option value="infoportal" <?php 
?>>infoportal
                <option value="city_info" <?php 
?>>city_info
                <option value="anec_page" <?php 
?>>anec_page
                <option value="weather_page" <?php 
?>>weather_page
                <option value="game_page" <?php 
?>>game_page
                <option value="horoscope_page" <?php 
?>>horoscope_page
                <option value="course_page" <?php 
?>>course_page
                <option value="ad" <?php 
?>>ad-->
                <option value="tv_archive" <?php 
if (@$_GET['sort_by'] == 'tv_archive') {
    echo 'selected';
}
?>>tv_archive
                <option value="timeshift" <?php 
if (@$_GET['sort_by'] == 'timeshift') {
    echo 'selected';
}
?>>timeshift
            </select>
            <br>
            <br>
        <td>
    </tr>
    <tr>
        <td>
            <form action="" method="GET">
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
                </select>&nbsp;&nbsp;&nbsp;
                <select name="hh" id="hh">
                    <?php 
echo \Ministra\OldAdmin\construct_HH();
?>
                </select>:
                <select name="ii" id="ii">
                    <?php 
echo \Ministra\OldAdmin\construct_II();
?>
                </select>
                <input type="submit" value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">
                <font color="Gray"><?php 
echo \_('search inactive users');
?></font>
            </form>
        <td>
    </tr>
</table>

<?php 
echo "<center><table class='list' cellpadding='3' cellspacing='0' width='1100'>\n";
echo '<tr>';
echo "<td class='list'><b>#</b></td>\n";
echo "<td class='list'><b>MAC</b></td>\n";
echo "<td class='list'><b>IP</b></td>\n";
echo "<td class='list'><b>Login</b></td>\n";
echo "<td class='list'><b>Account</b></td>\n";
echo "<td class='list'><b>Name</b></td>\n";
echo "<td class='list'><b>Tariff</b></td>\n";
echo "<td class='list'><b>" . \_('Type') . "</b></td>\n";
echo "<td class='list' width='250'><b>" . \_('Media') . "</b></td>\n";
echo "<td class='list'><b>" . \_('Time from<br>last playback') . "</b></td>\n";
echo "<td class='list'><b>&nbsp;</b></td>\n";
echo "<td class='list'><b>&nbsp;</b></td>\n";
echo "<td class='list'><b>" . \_('Last change<br>of status') . "</b></td>\n";
echo "</tr>\n";
$i = 0 + $MAX_PAGE_ITEMS * $page;
while ($arr = $users->next()) {
    ++$i;
    $now_playing_content = \htmlspecialchars($arr['now_playing_content']);
    if ($arr['now_playing_type'] == 2 && $arr['storage_name']) {
        $now_playing_content = '[' . $arr['storage_name'] . '] ' . $now_playing_content;
    }
    $status = \check_keep_alive($arr['keep_alive']);
    echo '<tr>';
    echo "<td class='list'>" . $i . "</td>\n";
    echo "<td class='list'><a href='profile.php?id=" . $arr['id'] . "'>" . $arr['mac'] . "</a></td>\n";
    echo "<td class='list'><a href='events.php?mac=" . $arr['mac'] . "'>" . $arr['ip'] . "</a></td>\n";
    echo "<td class='list'><a href='profile.php?id=" . $arr['id'] . "'>" . $arr['login'] . "</a></td>\n";
    echo "<td class='list'>" . $arr['ls'] . "</td>\n";
    echo "<td class='list'>" . $arr['fname'] . "</td>\n";
    echo "<td class='list'>" . $arr['tariff_plan_name'] . "</td>\n";
    echo "<td class='list'>" . (!$status && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_media_info_for_offline_stb', \false) ? '--' : \Ministra\OldAdmin\get_cur_media($arr['now_playing_type'])) . "</td>\n";
    echo "<td class='list'>" . (!$status && \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('hide_media_info_for_offline_stb', \false) ? '' : $now_playing_content) . "</td>\n";
    echo "<td class='list'>" . \Ministra\OldAdmin\get_last_time($arr['now_playing_start']) . "</td>\n";
    echo "<td class='list'><b>" . ($status ? '<font color="Green">online</font>' : '<font color="Red">offline</font>') . "</b></td>\n";
    echo "<td class='list' nowrap>";
    if (\Ministra\Lib\Admin::isActionAllowed() && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('deny_change_user_status', \false)) {
        echo "<a href='users.php?id=" . $arr['id'] . '&search=' . @$_GET['search'] . "&action=cut_off'>" . \Ministra\OldAdmin\get_user_color($arr['id']) . '</a>';
    } else {
        echo '<b>' . \Ministra\OldAdmin\get_user_color($arr['id']) . '</b>';
    }
    if (\Ministra\Lib\Admin::isActionAllowed() && !\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('deny_delete_user', \false)) {
        echo '&nbsp;&nbsp;';
        echo "<a href='#' onclick='if(confirm(\"" . \_('Do you really want to delete this record?') . '")){document.location="users.php?del=1&id=' . $arr['id'] . '&page=' . @$_GET['page'] . '&search=' . @$_GET['search'] . "\"}'>del</a>";
    }
    echo "</td>\n";
    echo "<td class='list'>" . $arr['last_change_status'] . "</td>\n";
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
