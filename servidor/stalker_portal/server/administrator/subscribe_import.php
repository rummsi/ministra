<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5;
use Ministra\Lib\SysEvent;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
echo '<pre>';
echo '</pre>';
$result = [];
$add_services_on = [];
$add_serv_on_counter = 0;
$add_serv_off_counter = 0;
if (@$_FILES['userfile']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    if (\is_uploaded_file($_FILES['userfile']['tmp_name'])) {
        $f_cont = \file($_FILES['userfile']['tmp_name']);
        $log = '';
        $updated = 0;
        $errors = 0;
        $cut_on = 0;
        $update_fav = @(int) $_POST['update_fav'];
        $update_status = @(int) $_POST['update_status'];
        $service_id_map = \Ministra\OldAdmin\get_service_id_map();
        $stb_id_map = \Ministra\OldAdmin\get_stb_id_map();
        $subscription_map = \Ministra\OldAdmin\get_subscription_map();
        $all_payed_ch = \Ministra\OldAdmin\get_all_payed_ch();
        $all_payed_ch_100 = \Ministra\OldAdmin\get_all_payed_ch_100();
        $base_channels = \Ministra\OldAdmin\get_base_channels();
        $extended_packet = [231, 146, 162, 151, 149, 27, 47, 29, 115, 153, 154, 156, 150, 116, 178];
        $base_channels = [];
        $bonus1 = \Ministra\OldAdmin\get_bonus1();
        $bonus2 = \Ministra\OldAdmin\get_bonus2();
        $stb_id_arr = [];
        foreach ($f_cont as $cont_str) {
            list($ls, $macs, $ch) = \explode(',', $cont_str);
            $macs_arr = \explode(';', $macs);
            $ch = \trim($ch);
            $ls = \trim($ls);
            foreach ($macs_arr as $mac) {
                if (\preg_match('/[а-я,А-Я]/', $mac)) {
                    \Ministra\OldAdmin\_log('mac "' . $mac . '", ЛС ' . $ls . ' содержит русские буквы ');
                }
                if (\strpos($mac, 'ts') !== \false) {
                    $mac = \str_replace('ts', '', $mac);
                    $ch = '00203';
                }
                $mac = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($mac);
                if (@\array_key_exists($mac, $stb_id_map)) {
                    $stb = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::R2015a49c88e682d6b426a39593db218e($mac);
                    $status = $stb['status'];
                    if ($status == 1 && $update_status) {
                        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['status' => 0, 'last_change_status' => 'NOW()'], ['mac' => $mac]);
                        $event = new \Ministra\Lib\SysEvent();
                        $event->setUserListByMac($mac);
                        $event->sendCutOn();
                        ++$cut_on;
                    }
                    $stb_id = $stb_id_map[$mac];
                    $stb_id_arr[] = $stb_id;
                    if (\array_key_exists($ch, $service_id_map)) {
                        if (!@\array_key_exists($stb_id, $result)) {
                            $result[$stb_id] = [];
                        }
                        $result[$stb_id][] = (int) $service_id_map[$ch];
                    } elseif ($ch == '00494' || $ch == '00674' || $ch == '00675' || $ch == '00725' || $ch == '00726' || $ch == '00746' || $ch == '00747' || $ch == '00754') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, \Ministra\OldAdmin\get_all_payed_ch_discovery());
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, \Ministra\OldAdmin\get_all_hd_channels());
                        if ($ch == '00674' || $ch == '00675' || $ch == '00725' || $ch == '00726' || $ch == '00746' || $ch == '00747') {
                            $add_services_on[] = $stb_id;
                        }
                    } elseif ($ch == '00116' || $ch == '00139' || $ch == '00203' || $ch == '00021' || $ch == '00274' || $ch == '00283' || $ch == '00350' || $ch == '00343' || $ch == '00381' || $ch == '00382' || $ch == '00389' || $ch == '00426' || $ch == '00609' || $ch == '00610') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, $all_payed_ch);
                        if ($ch == '00203' || $ch == '00021' || $ch == '00274' || $ch == '00283' || $ch == '00350' || $ch == '00343' || $ch == '00389' || $ch == '00609' || $ch == '00610') {
                            $add_services_on[] = $stb_id;
                        }
                    } elseif ($ch == '00100') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, $all_payed_ch_100);
                    } elseif ($ch == '00493') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, [270, 271, 272, 273, 274, 275]);
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, \Ministra\OldAdmin\get_all_hd_channels());
                    } elseif ($ch == '00160' || $ch == '00161' || $ch == '00162' || $ch == '00169' || $ch == '00170' || $ch == '00432' || $ch == '00433') {
                        $add_services_on[] = $stb_id;
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, []);
                    } elseif ($ch == '00649') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, [270, 271, 272, 273, 274, 275]);
                    } elseif ($ch == '00630' || $ch == '00642' || $ch == '00673' || $ch == '00724' || $ch == '00745' || $ch == '00750' || $ch == '00751' || $ch == '00752') {
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, $extended_packet);
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, $bonus1);
                        $result[$stb_id] = \Ministra\OldAdmin\merge_services(!empty($result[$stb_id]) ? $result[$stb_id] : \null, [245, 263]);
                        if ($ch == '00673' || $ch == '00724' || $ch == '00745') {
                            $add_services_on[] = $stb_id;
                        }
                    } else {
                        if (!@\array_key_exists($stb_id, $result)) {
                            $result[$stb_id] = [];
                        }
                        \Ministra\OldAdmin\_log('услуга "' . $ch . '" не найдена');
                    }
                } else {
                    \Ministra\OldAdmin\_log('mac "' . $mac . '", ЛС ' . $ls . ' не найден');
                    ++$errors;
                }
            }
        }
    }
    $stb_id_arr = \array_unique($stb_id_arr);
    if (\count($stb_id_arr) > 0) {
        $add_serv_off_counter = \count($stb_id_arr);
        $stb_id_str = \implode(',', $stb_id_arr);
        $sql = "update users set additional_services_on=0 where id in ({$stb_id_str})";
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
    }
    if (\count($add_services_on) > 0) {
        $add_serv_on_counter = \count($add_services_on);
        $add_services_on_str = \implode(',', $add_services_on);
        $sql = "update users set additional_services_on=1 where id in ({$add_services_on_str})";
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
    }
    foreach ($result as $uid => $sub) {
        if (\count($sub) == 0) {
            $bonus = [];
        } else {
            $bonus = $bonus1;
        }
        $sub = \array_merge($sub, $bonus2);
        $sub = \array_unique($sub);
        $sub_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($sub));
        $bonus_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($bonus));
        if (\array_key_exists($uid, $subscription_map)) {
            $sql = "update itv_subscription set sub_ch='{$sub_str}', bonus_ch='{$bonus_str}', addtime=NOW() where uid={$uid}";
        } else {
            $sql = "insert into itv_subscription (uid, sub_ch, bonus_ch, addtime) value ({$uid}, '{$sub_str}', '{$bonus_str}', NOW())";
        }
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($uid);
        $event->sendUpdateSubscription();
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($uid);
        $event->sendMsg('Каналы обновлены согласно подписке.');
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql)->result();
        if ($result) {
            ++$updated;
            if ((bool) \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_subscription') && $update_fav) {
                $fav_channels = \array_unique(\array_merge($sub, $bonus, $base_channels));
                $data_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($fav_channels));
                $id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('fav_itv')->where(['uid' => $uid])->get()->first('id');
                if ($id) {
                    $sql = "update fav_itv set fav_ch='" . $data_str . "', addtime=NOW() where uid='" . $uid . "'";
                } else {
                    $sql = "insert into fav_itv (uid, fav_ch, addtime) values ('" . $uid . "', '" . $data_str . "', NOW())";
                }
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
            }
        }
    }
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

        select {
            font-family: Verdana, Arial, Helvetica, sans-serif;
            font-weight: bold;
            width: 200px;
            border: thin 1;
        }

        select.all {
            height: 500px;
        }

        select.sub {
            height: 350px;
        }

        select.bonus {
            height: 100px;
        }
    </style>

    <?php 
$id = (int) @$_GET['id'];
?>
    <title><?php 
echo \_('Subscription import');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="620">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Subscription import');
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
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
if (!$_FILES) {
    ?>
                <form enctype="multipart/form-data" method="POST">
                    <table class="list" align="center" border="0" cellpadding="0" cellspacing="0" width="300">
                        <tr>
                            <td width="50%" align="right"><?php 
    echo \_('File');
    ?>:</td>
                            <td><input name="userfile" type="file"></td>
                        </tr>
                        <tr>
                            <td align="right">&nbsp;</td>
                            <td align="left"><input name="update_status" type="checkbox" checked
                                                    value="1"> <?php 
    echo \_('Update status');
    ?></td>
                        </tr>
                        <tr>
                            <td align="right">&nbsp;</td>
                            <td align="left"><input name="update_fav" type="checkbox"
                                                    value="1"> <?php 
    echo \_('Update favorites');
    ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" value="<?php 
    echo \htmlspecialchars(\_('Import'), \ENT_QUOTES);
    ?>">
                            </td>
                        </tr>
                        <table>
                </form>
            <?php 
} else {
    echo "<table align='center' width='350'>";
    echo '<tr>';
    echo '<td>';
    echo "<b>Обновлено {$updated} подписок,<br> включено {$cut_on} приставок,<br> отключено доп сервисов у {$add_serv_off_counter} приставок,<br> включено доп сервисов у {$add_serv_on_counter},<br> всего {$errors} ошибок</b><br><br>\n";
    echo $log;
    echo '</td>';
    echo '</tr>';
}
?>
        </td>
    </tr>
</table>
</body>
</html>

