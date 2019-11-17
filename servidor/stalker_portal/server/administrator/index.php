<?php

if (!isset($locale)) {
    $locale = 'en';
}
if (!isset($allowed_locales)) {
    $allowed_locales = [];
}
\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\StreamServer;
\Ministra\Lib\Admin::checkAuth();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php 
echo \_('Ministra MW admin interface');
?></title>
    <style type="text/css">
        td, table.menu {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: #000000;
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
            background-color: #88BBFF
        }

        td.other {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: #000000;
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
            background-color: #FFFFFF;
        }

        .td_stat {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            text-decoration: none;
            color: #000000;
            text-align: right;
            border-width: 0px;
            border-style: solid;
            border-color: #E5E5E5;
            background-color: #f5f5f5;
        }

        .style1 {
            color: #FFFFFF;
            font-weight: bold;
            font-size: 16px
        }

        a {
            color: #FFFFFF;
            font-weight: bold;
            text-decoration: none;
        }

        a:link {
            color: #FFFFFF;
            font-weight: bold;
        }

        a:visited {
            color: #FFFFFF;
            font-weight: bold;
            text-decoration: none;
        }

        a:hover {
            color: #FFFFFF;
            font-weight: bold;
            text-decoration: underline;
        }

        .lang {
            color: blue !important;
            font-weight: normal !important;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }
    </style>
    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../adm/js/jquery.cookies.2.2.0.js"></script>

    <script type="text/javascript">

    $(function () {
      $('.langs').change(function (e) {
        var lang = $('.langs option:selected').val().substr(0, 2);
        $.cookies.set('language', lang, { expiresAt: new Date(2037, 1, 1) });
        document.location = document.location;
      });

      $('.goto_new_adm').click(function () {
        window.location = '../adm/';
      });

      $('.hide_banner').click(function () {
        $('.adm_banner').hide();
        $.cookies.set('hide_banner', 1, { expiresAt: new Date(2037, 1, 1) });
      });
    });

    </script>

</head>

<body>
<?php 
if (empty($_COOKIE['hide_banner'])) {
    ?>
    <div class="adm_banner"
         style="background-color: #E5E5E5; height: 22px; width: 100%; margin: -8px -8px 10px -8px; text-align: center;
         padding: 10px; color:#868895; font-family: Arial, Helvetica, sans-serif">
        <span style="padding-right: 20px"><?php 
    echo \_('Wish for more options? Try the new Ministra admin interface.');
    ?></span>
        <input type="button" class="goto_new_adm" value="<?php 
    echo \_('Try now!');
    ?>"
               style="color: #fff; background-color: #2B78E4; border: 1px solid #84878B; padding: 3px 10px"> <input
                class="hide_banner" type="button" value="<?php 
    echo \_('Later');
    ?>">
    </div>
<?php 
}
?>

<div style="width: 80%; margin:0 auto; text-align: right">
    <select class="langs">
        <?php 
foreach ($allowed_locales as $lang => $loc) {
    echo '<option value="' . $loc . '" ' . ($locale == $loc ? 'selected' : '') . '>' . $lang . '</option>';
}
?>
    </select>
</div>
<br>
<br>
<table width="80%" border="0" align="center">
    <tr>
        <td align="right" class="other" style="border-width: 0px;"><?php 
echo \date('Y-m-d H:i:s');
?>
            <?php 
$ver = @\file_get_contents(\PROJECT_PATH . '/../c/version.js');
if (!empty($ver)) {
    $start = \strpos($ver, "'") + 1;
    $end = \strrpos($ver, "'");
    $ver = \substr($ver, $start, $end - $start);
    echo '(v ' . $ver . ')';
}
?>
        </td>
    </tr>
</table>

<table width="80%" border="1" align="center" cellpadding="3" cellspacing="0" class="menu">
    <tr>
        <td colspan="3" align="center"><span class="style1"><?php 
echo \_('Sections');
?></span></td>
    </tr>

    <tr>
        <td width="47%">
            <div align="center"><a href="add_itv.php"><?php 
echo \_('IPTV channels');
?></a></div>
        </td>
        <td width="6%">&nbsp;</td>
        <td width="47%">
            <div align="center"><a href="users.php"><?php 
echo \_('Users');
?></a></div>
        </td>
    </tr>

    <tr>
        <td>
            <div align="center"><a href="add_video.php"><?php 
echo \_('VIDEO CLUB');
?></a></div>
        </td>
        <td>&nbsp;</td>
        <td align="center"><a href="events.php"><?php 
echo \_('Events');
?></a></td>
    </tr>

    <tr>
        <td>
            <div align="center"><a href="audio_album.php"><?php 
echo \_('AUDIO CLUB');
?></a></div>
        </td>
        <td>&nbsp;</td>
        <td align="center"><?php 
if (\Ministra\Lib\Admin::isSuperUser()) {
    ?><a
                    href="administrators.php"><?php 
    echo \_('Administrators');
    ?></a><?php 
}
?></td>
    </tr>

    <tr>
        <td>
            <div align="center"><a href="add_karaoke.php"><?php 
echo \_('KARAOKE');
?></a></div>
        </td>
        <td>&nbsp;</td>
        <td align="center"><a href="logout.php">[<?php 
echo $_SESSION['login'];
?>]
                <?php 
echo \_('Logout');
?></a></td>
    </tr>

    <tr>
        <td>
            <div align="center"><a href="add_radio.php"><?php 
echo \_('RADIO');
?></a></div>
        </td>
        <td>&nbsp;</td>
        <td align="center"></td>
    </tr>

    <tr>
        <td>
            <div align="center"><a href="tariffs.php"><?php 
echo \_('TARIFFS');
?></a></div>
        </td>
        <td>&nbsp;</td>
        <td align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('tasks')) {
    echo "<a href='tasks.php'>" . \sprintf(\_('Tasks (new messages: %s)'), \Ministra\OldAdmin\get_count_unreaded_msgs_by_uid()) . '</a>';
}
?></td>
    </tr>

</table>

<br>
<?php 
$online = \Ministra\OldAdmin\get_online_users();
$offline = \Ministra\OldAdmin\get_offline_users();
$cur_tv = \get_cur_playing_type('itv');
$cur_vclub = \get_cur_active_playing_type('vclub');
$cur_tv_archive = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2, 'now_playing_type' => 11])->get()->count();
$cur_records = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2, 'now_playing_type' => 12])->get()->count();
$cur_time_shift = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->where(['UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2, 'now_playing_type' => 14])->get()->count();
$cur_aclub = \get_cur_active_playing_type('aclub');
$cur_karaoke = \get_cur_active_playing_type('karaoke');
$cur_radio = \get_cur_playing_type('radio');
$cur_infoportal = \get_cur_infoportal();
?>
<table width="80%" align="center">
    <tr>
        <td class="other" width="150">
            <table width="150" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="td_stat" style="color:green" width="80">online:</td>
                    <td class="td_stat"><?php 
echo $online;
?></td>
                </tr>
                <tr>
                    <td class="td_stat" style="color:red">offline:</td>
                    <td class="td_stat"><?php 
echo $offline;
?></td>
                </tr>
                <tr>
                    <td class="td_stat">&nbsp;</td>
                    <td class="td_stat"></td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('tv');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_tv;
?></td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('videoclub');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_vclub;
?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php 
$storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->get()->all();
foreach ($storages as $storage) {
    $storage_name = $storage['storage_name'];
    $counter = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['now_playing_type' => 2, 'storage_name' => $storage_name, 'UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->get()->counter();
    echo '<tr>';
    echo '<td class="td_stat" width="80"><b>' . $storage_name . '</b>:</td>';
    echo '<td class="td_stat"><a href="users_on_storage.php?storage=' . $storage_name . '&type=2" style="color:black">' . $counter . '</a></td>';
    echo '</tr>';
}
?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('audioclub');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_aclub;
?></td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('karaoke');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_karaoke;
?></td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('radio');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_radio;
?></td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('infoportal');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_infoportal;
?></td>
                </tr>
                </tr>
            </table>
        </td>

        <td class="other" width="150" valign="top" style="background-color: whiteSmoke">
            <table width="150" border="0" align="left" cellpadding="0" cellspacing="0">
                <!--<tr>
                    <td class="td_stat" height="64" colspan="2"></td>
                </tr>-->
                <tr>
                    <td class="td_stat"><?php 
echo \_('tv archive');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_tv_archive;
?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php 
$storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['for_records' => 1])->get()->all();
foreach ($storages as $storage) {
    $storage_name = $storage['storage_name'];
    $counter = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['now_playing_type' => 11, 'storage_name' => $storage_name, 'UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->get()->counter();
    echo '<tr>';
    echo '<td class="td_stat" width="80"><b>' . $storage_name . '</b>:</td>';
    echo '<td class="td_stat"><a href="users_on_storage.php?storage=' . $storage_name . '&type=11   " style="color:black">' . $counter . '</a></td>';
    echo '</tr>';
}
?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('timeshift');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_time_shift;
?></td>
                </tr>
                <tr>
                    <td colspan="2">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php 
$storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['for_records' => 1])->get()->all();
foreach ($storages as $storage) {
    $storage_name = $storage['storage_name'];
    $counter = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('users')->count()->where(['now_playing_type' => 14, 'storage_name' => $storage_name, 'UNIX_TIMESTAMP(keep_alive)>' => \time() - \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('watchdog_timeout') * 2])->get()->counter();
    echo '<tr>';
    echo '<td class="td_stat" width="80"><b>' . $storage_name . '</b>:</td>';
    echo '<td class="td_stat"><a href="users_on_storage.php?storage=' . $storage_name . '&type=14   " style="color:black">' . $counter . '</a></td>';
    echo '</tr>';
}
?>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="td_stat"><?php 
echo \_('records');
?>:</td>
                    <td class="td_stat"><?php 
echo $cur_records;
?></td>
                </tr>
            </table>
        </td>

        <td class="other" width="160" valign="top" style="background-color: whiteSmoke">
            <table width="100%" border="0" align="left" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="td_stat" height="" colspan="2"></td>
                </tr>
                <tr>
                    <?php 
$stream_servers = \Ministra\Lib\StreamServer::getAllActive(\true);
$streamer_sessions = \array_reduce($stream_servers, function ($sessions, $streamer) {
    return $sessions + $streamer['sessions'];
}, 0);
?>
                    <td class="td_stat"><?php 
echo \mb_strtolower(\_('Stream servers'), 'UTF-8');
?>:</td>
                    <td class="td_stat"><?php 
echo $streamer_sessions;
?></td>
                </tr>

                <tr>
                    <td colspan="2">
                        <table cellpadding="0" cellspacing="0" border="0" width="100%">
                            <?php 
foreach ($stream_servers as $streamer) {
    echo '<tr>';
    echo '<td class="td_stat" width="80"><b>' . $streamer['name'] . '</b>:</td>';
    echo '<td class="td_stat">' . $streamer['sessions'] . '/' . \round($streamer['load'], 2) . '</td>';
    echo '</tr>';
}
?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>

        <td class="other">
        </td>
        <td class="other" width="100">
            <form action="users.php" method="GET">
                <input type="text" name="search" value="">
                <input type="submit"
                       value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">
                <br><font color="Gray"><?php 
echo \_('search by MAC or IP');
?></font>
            </form>
        </td>

    </tr>
</table>
<br>
<table width="80%" border="1" align="center" cellpadding="3" cellspacing="0" class="menu">
    <tr>
        <td colspan="3" align="center"><span class="style1"><?php 
echo \_('Infoportal');
?></span></td>
    </tr>

    <tr>
        <td width="47%" align="center"><a href="city_info.php"><?php 
echo \_('City help');
?></a></td>
        <td width="6%">&nbsp;</td>
        <td width="47%" align="center"><a href="anecdote.php"><?php 
echo \_('Jokes');
?></a></td>
    </tr>

</table>
<br>

<table width="80%" border="1" align="center" cellpadding="3" cellspacing="0" class="menu">
    <tr>
        <td colspan="3" align="center"><span class="style1"><?php 
echo \_('Statistics');
?></span></td>
    </tr>

    <tr>
        <td width="47%" align="center"><a href="stat_video.php"><?php 
echo \_('Video statistics');
?></a></td>
        <td width="6%">&nbsp;</td>
        <td width="47%" align="center"><a href="stat_tv_users.php"><?php 
echo \_('Users statistics for TV');
?></a></td>
    </tr>

    <tr>
        <td align="center"><a href="stat_tv.php"><?php 
echo \_('TV statistics');
?></a></td>
        <td>&nbsp;</td>
        <td align="center"><a href="stat_video_users.php"><?php 
echo \_('Users statistics for VIDEO');
?></a></td>
    </tr>

    <tr>
        <td align="center"><a href="stat_tv_archive.php"><?php 
echo \_('TV Archive statistics');
?></a></td>
        <td>&nbsp;</td>
        <td align="center"><a href="stat_anec_users.php"><?php 
echo \_('Users statistics for Jokes');
?></a></td>
    </tr>

    <tr>
        <td align="center"><a href="stat_timeshift.php"><?php 
echo \_('TimeShift statistics');
?></a></td>
        <td>&nbsp;</td>
        <td align="center"><a href="stat_not_active_users.php"><?php 
echo \_('Inactive users');
?></a></td>
    </tr>

    <tr>
        <td align="center"><a href="stat_moderators.php"><?php 
echo \_('Moderators statistics');
?></a></td>
        <td>&nbsp;</td>
        <td align="center"></td>
    </tr>

    <tr>
        <td align="center"><a href="claims.php"><?php 
echo \_('Claims');
?></a></td>
        <td>&nbsp;</td>
        <td align="center"></td>
    </tr>
</table>
<br>

<table width="80%" border="1" align="center" cellpadding="3" cellspacing="0" class="menu">

    <tr>
        <td colspan="3" align="center"><span class="style1"><?php 
echo \_('Settings');
?></span></td>
    </tr>

    <tr>
        <td width="47%" align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('setting_common')) {
    ?><a
                    href="setting_common.php"><?php 
    echo \_('Firmware auto update');
    ?></a><?php 
}
?></td>
        <td width="6%">&nbsp;</td>
        <td width="47%" align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('storages')) {
    ?><a
                    href="storages.php"><?php 
    echo \_('Storages');
    ?></a><?php 
}
?></td>
    </tr>

    <tr>
        <td width="47%" align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('epg_setting')) {
    ?><a href="epg_setting.php">EPG</a><?php 
}
?>
        </td>
        <td width="6%">&nbsp;</td>
        <td width="47%" align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('stream_servers')) {
    ?><a
                    href="stream_servers.php"><?php 
    echo \_('Stream servers');
    ?></a><?php 
}
?></td>
    </tr>
    <tr>
        <td width="47%" align="center"><?php 
if (\Ministra\Lib\Admin::isAccessAllowed('themes')) {
    ?><a
                    href="themes.php"><?php 
    echo \_('Templates');
    ?></a><?php 
}
?></td>
        <td width="6%">&nbsp;</td>
        <td width="47%" align="center">&nbsp;</td>
    </tr>
</table>
</body>
</html>

