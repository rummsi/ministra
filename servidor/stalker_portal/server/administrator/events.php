<?php

if (!isset($locale)) {
    $locale = 'en';
}
\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Event;
use Ministra\Lib\StbGroup;
use Ministra\Lib\SysEvent;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$error_counter = 0;
if (@$_GET['del'] == 1) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    $uid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb(@$_GET['mac']);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('events', ['uid' => $uid]);
    \header('Location: events.php?mac=' . @$_GET['mac']);
    exit;
}
if (!empty($_POST['user_list_type']) && !empty($_POST['event'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    if (@$_POST['need_reboot']) {
        $reboot_after_ok = 1;
    } else {
        $reboot_after_ok = 0;
    }
    $event = new \Ministra\Lib\SysEvent();
    $event->setTtl($_POST['ttl']);
    if (@$_POST['user_list_type'] == 'to_all') {
        if ($_POST['event'] == 'send_msg' || $_POST['event'] == 'send_msg_with_video') {
            $event->setUserListByMac('all');
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b03d7ef7b2ba1705d9a43de730650d5f();
        } else {
            $event->setUserListByMac('online');
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::b49b5de1f8caa1ded0e5d2b1848b3a8a();
        }
    } elseif (@$_POST['user_list_type'] == 'to_single') {
        $event->setUserListByMac(@$_POST['mac']);
        $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb(@$_POST['mac']);
        $user_list = [$user_list];
    } elseif (@$_POST['user_list_type'] == 'by_pattern') {
        if (@$_POST['pattern'] == 'mag100') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['hd' => 0]);
        } elseif (@$_POST['pattern'] == 'mag200') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG200']);
        } elseif (@$_POST['pattern'] == 'mag245') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG245']);
        } elseif (@$_POST['pattern'] == 'mag245d') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG245D']);
        } elseif (@$_POST['pattern'] == 'mag250') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG250']);
        } elseif (@$_POST['pattern'] == 'mag254') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG254']);
        } elseif (@$_POST['pattern'] == 'mag255') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG255']);
        } elseif (@$_POST['pattern'] == 'mag256') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG256']);
        } elseif (@$_POST['pattern'] == 'mag257') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG257']);
        } elseif (@$_POST['pattern'] == 'mag260') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG260']);
        } elseif (@$_POST['pattern'] == 'mag270') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG270']);
        } elseif (@$_POST['pattern'] == 'mag275') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG275']);
        } elseif (@$_POST['pattern'] == 'mag351') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG351']);
        } elseif (@$_POST['pattern'] == 'mag352') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'MAG352']);
        } elseif (@$_POST['pattern'] == 'wr320') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'WR320']);
        } elseif (@$_POST['pattern'] == 'ip_stb_hd') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'IP_STB_HD']);
        } elseif (@$_POST['pattern'] == 'aurahd0') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'AuraHD0']);
        } elseif (@$_POST['pattern'] == 'aurahd1') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'AuraHD1']);
        } elseif (@$_POST['pattern'] == 'aurahd9') {
            $user_list = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::a106f6477111fb2da535bef09fdc19db(['stb_type' => 'AuraHD9']);
        } else {
            $user_list = [];
        }
        $error = \sprintf(\_('%s events %s sended, %s errors'), \count($user_list), $_POST['event'], $error_counter) . "<br>\n" . $error;
        $event->setUserListById($user_list);
    } elseif (@$_POST['user_list_type'] == 'by_group') {
        if ((int) $_POST['group_id'] > 0) {
            $stb_groups = new \Ministra\Lib\StbGroup();
            $user_list = $stb_groups->getAllMemberUidsByGroupId($_POST['group_id']);
        } else {
            $user_list = [];
        }
        $error = \sprintf(\_('%s events %s sended, %s errors'), \count($user_list), $_POST['event'], $error_counter) . "<br>\n" . $error;
        $event->setUserListById($user_list);
    } elseif (@$_POST['user_list_type'] == 'by_user_list') {
        if (@$_FILES['user_list']) {
            if (\is_uploaded_file($_FILES['user_list']['tmp_name'])) {
                $f_cont = \file($_FILES['user_list']['tmp_name']);
                if (\is_array($f_cont) && isset($f_cont[0]) && \substr($f_cont[0], 0, 3) == "﻿") {
                    $f_cont[0] = \substr($f_cont[0], 3);
                }
                foreach ($f_cont as $mac) {
                    $uid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($mac);
                    if ($uid) {
                        $user_list[] = $uid;
                    } else {
                        $error .= "mac '" . $mac . "' not found<br>\n";
                        ++$error_counter;
                    }
                }
                $event->setUserListById($user_list);
                $error = \sprintf(\_('%s events %s sended, %s errors'), \count($user_list), $_POST['event'], $error_counter) . "<br>\n" . $error;
            }
        } else {
            $error .= \_('File with list is missing') . '<br>';
        }
    }
    if ($_POST['event'] == 'cut_off') {
        if (!\is_array($user_list)) {
            $user_list = [$user_list];
        }
        $sql = 'update users set status=1, last_change_status=NOW() where id in (' . \implode(',', $user_list) . ')';
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
        $event->sendCutOff();
    }
    switch ($_POST['event']) {
        case 'send_msg':
            if (@$_POST['need_reboot']) {
                $event->sendMsgAndReboot(@$_POST['msg']);
            } else {
                $event->sendMsg(@$_POST['msg']);
            }
            break;
        case 'send_msg_with_video':
            $event->sendMsgWithVideo(@$_POST['msg'], @$_POST['video_url']);
            break;
        case 'reboot':
            $event->sendReboot();
            break;
        case 'reload_portal':
            $event->sendReloadPortal();
            break;
        case 'update_channels':
            $event->sendUpdateChannels();
            break;
        case 'play_channel':
            $event->sendPlayChannel(@$_POST['channel']);
            break;
        case 'update_image':
            $event->sendUpdateImage();
            break;
    }
}
$mac = '';
if (!empty($_POST['mac'])) {
    $mac = $_POST['mac'];
} elseif (!empty($_GET['mac'])) {
    $mac = $_GET['mac'];
}
$uid = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($mac);
$events = \Ministra\Lib\Event::getAllNotEndedEvents($uid);
$debug = '<!--' . \ob_get_contents() . '-->';
\ob_clean();
echo $debug;
if (!empty($_SERVER['HTTP_REFERER']) && \strpos($_SERVER['HTTP_REFERER'], 'events.php') === \false) {
    $_SESSION['back_url'] = $_SERVER['HTTP_REFERER'];
} elseif (empty($_SERVER['HTTP_REFERER'])) {
    $_SESSION['back_url'] = 'index.php';
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
echo \_('Events');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="620">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Events');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="<?php 
echo empty($_SESSION['back_url']) ? 'index.php' : $_SESSION['back_url'];
?>">
                << <?php 
echo \_('Back');
?></a>
            | <a href="events.php"><?php 
echo \_('New event');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <br>
                <br>
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
<script>

function load_events_by_mac() {
  var mac = document.getElementById('mac').value;
  document.location = '?mac=' + mac;
}

function enable_disable_mac() {
  if (document.getElementById('all').checked) {
    document.getElementById('mac').disabled = true;
  } else {
    document.getElementById('mac').disabled = false;
  }
}

function check_event() {
  var event_obj = document.getElementById('event');

  var need_reboot_cbox = document.getElementById('need_reboot');

  if (event_obj.options[event_obj.selectedIndex].value == 'send_msg') {
    document.getElementById('checkbox_need_reboot').style.display = '';
    document.getElementById('msg_row').style.display = '';
    document.getElementById('ttl').value = 7 * 24 * 3600;
  } else if (event_obj.options[event_obj.selectedIndex].value == 'send_msg_with_video') {
    document.getElementById('checkbox_need_reboot').style.display = 'none';
    document.getElementById('msg_row').style.display = '';
    document.getElementById('video_row').style.display = '';
    document.getElementById('ttl').value = 7 * 24 * 3600;
  } else {
    if (need_reboot_cbox.checked) {
      need_reboot_cbox.click();
    }
    document.getElementById('checkbox_need_reboot').style.display = 'none';
    document.getElementById('video_row').style.display = 'none';
    document.getElementById('msg_row').style.display = 'none';
    document.getElementById('ttl').value = "<?php 
echo \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('watchdog_timeout', 120) * 2;
?>";
  }

  if (event_obj.options[event_obj.selectedIndex].value == 'play_channel') {
    document.getElementById('text_channel').style.display = '';
  } else {
    document.getElementById('text_channel').style.display = 'none';
  }

  if (event_obj.options[event_obj.selectedIndex].value == '') {
    document.getElementById('submit_button').disabled = true;
  } else {
    document.getElementById('submit_button').disabled = false;
  }
}

function change_form(obj) {
  var mac_row_obj = document.getElementById('mac_row');
  var user_list_row_obj = document.getElementById('user_list_row');
  var pattern_row = document.getElementById('pattern_row');
  var group_row = document.getElementById('group_row');
  if (obj.value == 'to_single') {
    mac_row_obj.style.display = '';
    pattern_row.style.display = 'none';
    user_list_row_obj.style.display = 'none';
    group_row.style.display = 'none';
  } else if (obj.value == 'to_all') {
    group_row.style.display = 'none';
    mac_row_obj.style.display = 'none';
    pattern_row.style.display = 'none';
    user_list_row_obj.style.display = 'none';
  } else if (obj.value == 'by_user_list') {
    group_row.style.display = 'none';
    mac_row_obj.style.display = 'none';
    pattern_row.style.display = 'none';
    user_list_row_obj.style.display = '';
  } else if (obj.value == 'by_pattern') {
    group_row.style.display = 'none';
    mac_row_obj.style.display = 'none';
    user_list_row_obj.style.display = 'none';
    pattern_row.style.display = '';
  } else if (obj.value == 'by_group') {
    mac_row_obj.style.display = 'none';
    user_list_row_obj.style.display = 'none';
    pattern_row.style.display = 'none';
    group_row.style.display = '';
  }
}

function fill_msg() {
  txt = 'Уважаемый абонент! Срок бесплатного тестирования наших услуг закончился. Просим '+
    'Вас подойти в абонентский отдел (пр-т Ак. Глушко, 11-И, каб.8) для перезаключения договора либо '+
    'возврата оборудования.';
  document.getElementById('msg').value = txt;
}

</script>
<table border="0" align="center" width="620">
    <form action="events.php" method="POST" enctype="multipart/form-data">
        <tr>
            <td align="right" valign="top" width="100">
                <?php 
echo \_('Send');
?>:
            </td>
            <td>
                <input type="radio" name="user_list_type" id="to_single" value="to_single" onchange="change_form(this)"
                       checked="checked"><label for="to_single"><?php 
echo \_('To one');
?></label><br/>
                <input type="radio" name="user_list_type" id="to_all" value="to_all" onchange="change_form(this)"><label
                        for="to_all"><?php 
echo \_('To all');
?></label><br/>
                <input type="radio" name="user_list_type" id="by_user_list" value="by_user_list"
                       onchange="change_form(this)"><label for="by_user_list"><?php 
echo \_('By list');
?></label><br/>
                <input type="radio" name="user_list_type" id="by_pattern" value="by_pattern"
                       onchange="change_form(this)"><label for="by_pattern"><?php 
echo \_('By pattern');
?></label><br/>
                <input type="radio" name="user_list_type" id="by_group" value="by_group"
                       onchange="change_form(this)"><label for="by_group"><?php 
echo \_('To group');
?></label><br/>
            </td>
        </tr>
        <tr id="mac_row">
            <td align="right">
                MAC:
            </td>
            <td>
                <input type="text" name="mac" id="mac" value="<?php 
echo @$mac;
?>">&nbsp
                <input type="button" value="<?php 
echo \htmlspecialchars(\_('Load active events'), \ENT_QUOTES);
?>"
                       onclick="load_events_by_mac()">
            </td>
        </tr>
        <tr id="user_list_row" style="display:none">
            <td align="right">
                <?php 
echo \_('List');
?>:
            </td>
            <td>
                <input name="user_list" type="file">
            </td>
        </tr>
        <tr id="pattern_row" style="display:none">
            <td align="right">
                <?php 
echo \_('Pattern');
?>:
            </td>
            <td>
                <select name="pattern">
                    <option value="mag100">MAG100</option>
                    <option value="mag200">MAG200</option>
                    <option value="mag245">MAG245</option>
                    <option value="mag245d">MAG245D</option>
                    <option value="mag250">MAG250</option>
                    <option value="mag254">MAG254</option>
                    <option value="mag255">MAG255</option>
                    <option value="mag256">MAG256</option>
                    <option value="mag257">MAG257</option>
                    <option value="mag260">MAG260</option>
                    <option value="mag270">MAG270</option>
                    <option value="mag275">MAG275</option>
                    <option value="mag322">MAG322</option>
                    <option value="mag323">MAG323</option>
                    <option value="mag324">MAG324</option>
                    <option value="mag324c">MAG324C</option>
                    <option value="mag325">MAG325</option>
                    <option value="mag349">MAG349</option>
                    <option value="mag350">MAG350</option>
                    <option value="mag351">MAG351</option>
                    <option value="mag352">MAG352</option>
                    <option value="wr320">WR320</option>
                    <option value="ip_stb_hd">IP_STB_HD</option>
                    <option value="aurahd0">AuraHD0</option>
                    <option value="aurahd1">AuraHD1</option>
                    <option value="aurahd9">AuraHD9</option>
                </select>
            </td>
        </tr>
        <tr id="group_row" style="display:none">
            <td align="right">
                <?php 
echo \_('Group');
?>:
            </td>
            <td>
                <select name="group_id">
                    <option value="0">--------</option>
                    <?php 
$stb_groups = new \Ministra\Lib\StbGroup();
$all_groups = $stb_groups->getAll();
foreach ($all_groups as $group) {
    echo '<option value="' . $group['id'] . '">' . $group['name'] . '</option>';
}
?>
                </select>
            </td>
        </tr>
        <tr>
            <td align="right">
                TYPE:
            </td>
            <td>
                <select name="event" id="event" onchange="check_event()">
                    <option value="">----------
                    <option value="send_msg">send_msg
                    <option value="send_msg_with_video">send_msg_with_video
                    <option value="reboot">reboot
                    <option value="reload_portal">reload_portal
                    <option value="update_channels">update_channels
                    <option value="play_channel">play_channel
                    <option value="mount_all_storages">mount_all_storages
                    <option value="cut_off">switch_off
                    <option value="update_image">update_image
                </select>
                <span style="display:none" id="checkbox_need_reboot">
                    <input type="checkbox" name="need_reboot" id="need_reboot"
                           value="1"> <?php 
echo \_('restart on OK');
?></span>
                <span style="display:none" id="text_channel">
                    <input type="text" name="channel" id="channel" size="5"maxlength="3">
                    <?php 
echo \_('channels');
?>
                </span>
            </td>
        </tr>
        <tr>
            <td align="right">
                TTL:
            </td>
            <td>
                <input type="text" name="ttl" id="ttl"
                       value="<?php 
echo \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('watchdog_timeout', 120) * 2;
?>">, <?php 
echo \_('s');
?>
            </td>
        </tr>
        <tr id="msg_row" style="display:none">
            <td align="right" valign="top">
                MSG:
            </td>
            <td>
                <textarea name="msg" id="msg" rows="10" cols="50"></textarea><br/>
                <?php 
if (\substr($locale, 0, 2) == 'ru') {
    ?>
                    <a href="#" onclick="fill_msg()" style="font-size:12px;font-weight:normal">Истек срок
                        тестирования</a>
                <?php 
}
?>
            </td>
        </tr>
        <tr id="video_row" style="display:none">
            <td align="right" valign="top">
                VIDEO URL:
            </td>
            <td>
                <input type="text" name="video_url" id="video_url" size="64"/><br/>
            </td>
        </tr>
        <tr>
            <td align="left"></td>
            <td>
                <input type="submit" id="submit_button" disabled="disabled"
                       value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>">
            </td>
        </tr>
    </form>
</table>
<br><br>
<?php 
if (\is_array($events) && \count($events) > 0) {
    ?>
    <table class='list' align="center" cellpadding='3' cellspacing='0' width='620'>
        <caption><?php 
    \printf(\_('Active events for %s'), $mac);
    ?>
            <a href="events.php?del=1&mac=<?php 
    echo $mac;
    ?>"
               style="font-size:12px"><?php 
    echo \_('clean');
    ?></a>
        </caption>
        <tr>
            <td class='list'><b><?php 
    echo \_('Valid up to');
    ?></b></td>
            <td class='list'><b><?php 
    echo \_('Event');
    ?></b></td>
            <td class='list'><b><?php 
    echo \_('Message');
    ?></b></td>
            <td class='list'><b><?php 
    echo \_('Status');
    ?></b></td>
        </tr>
        <?php 
    foreach ($events as $idx => $arr) {
        echo '<tr>';
        echo "<td class='list' nowrap>" . $arr['eventtime'] . "</td>\n";
        echo "<td class='list'>" . $arr['event'] . "</td>\n";
        echo "<td class='list'>" . $arr['msg'] . "</td>\n";
        echo "<td class='list'>";
        echo $arr['sended'] ? \_('sended') : \_('not sended');
        echo "</td>\n";
        echo "</tr>\n";
    }
    ?>
    </table>
    <?php 
} else {
    if (!empty($_GET['mac'])) {
        echo '<center>' . \sprintf(\_('There are no active events for %s'), $_GET['mac']) . '</center>';
    }
}
