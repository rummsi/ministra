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
use Ministra\Lib\StbGroup;
use Ministra\Lib\SysEvent;
use Ministra\Lib\User;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$search = @$_GET['search'];
$letter = @$_GET['letter'];
if (!empty($_POST['change_tariff_plan'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['tariff_plan_id' => (int) $_POST['tariff_plan_id']], ['id' => (int) $_GET['id']]);
    if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById([(int) $_GET['id']]);
        $user = \Ministra\Lib\User::getInstance((int) $_GET['id']);
        $event->sendMsgAndReboot($user->getLocalizedText('Tariff plan is changed, please restart your STB'));
    }
    \header('Location: profile.php?id=' . @$_GET['id']);
    exit;
}
if (@$_POST['save']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $stb_groups = new \Ministra\Lib\StbGroup();
    $member = $stb_groups->getMemberByUid((int) $_GET['id']);
    if (empty($member)) {
        $stb_groups->addMember(['mac' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($_POST['mac']), 'uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($_POST['mac']), 'stb_group_id' => $_POST['group_id']]);
    } else {
        $stb_groups->setMember(['stb_group_id' => $_POST['group_id']], $member['id']);
    }
    \header('Location: profile.php?id=' . @$_GET['id']);
    exit;
}
if (@$_POST['account']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $stb_groups = new \Ministra\Lib\StbGroup();
    $member = $stb_groups->getMemberByUid((int) $_GET['id']);
    if (empty($member)) {
        $stb_groups->addMember(['mac' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::o6c94c7b9823303431b00444e69340ade($_POST['mac']), 'uid' => \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::e2007d6bdd1c5d517d04d4fdf5eac8bb($_POST['mac']), 'stb_group_id' => $_POST['group_id']]);
    } else {
        $stb_groups->setMember(['stb_group_id' => $_POST['group_id']], $member['id']);
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['fname' => $_POST['fname'], 'phone' => $_POST['phone'], 'ls' => $_POST['ls'], 'comment' => $_POST['comment'], 'expire_billing_date' => $_POST['expire_billing_date']], ['id' => (int) $_GET['id']]);
    \header('Location: profile.php?id=' . @$_GET['id']);
    exit;
}
if (@$_GET['video_out']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $video_out = @$_GET['video_out'];
    $id = (int) $_GET['id'];
    if ($video_out == 'svideo') {
        $new_video_out = 'svideo';
    } else {
        $new_video_out = 'rca';
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['video_out' => $new_video_out], ['id' => $id]);
    \header('Location: profile.php?id=' . $id);
    exit;
}
if (@$_GET['parent_password'] && $_GET['parent_password'] == 'default') {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) $_GET['id'];
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['parent_password' => '0000'], ['id' => $id]);
    \header('Location: profile.php?id=' . $id);
    exit;
}
if (@$_GET['settings_password'] && $_GET['settings_password'] == 'default') {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) $_GET['id'];
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['settings_password' => '0000'], ['id' => $id]);
    \header('Location: profile.php?id=' . $id);
    exit;
}
if (@$_GET['fav_itv'] && $_GET['fav_itv'] == 'default') {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) $_GET['id'];
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->use_caching(['fav_itv.uid=' . (int) $id])->update('fav_itv', ['fav_ch' => ''], ['uid' => $id]);
    \header('Location: profile.php?id=' . $id);
    exit;
}
if (isset($_GET['set_services'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) @$_GET['id'];
    $set = (int) $_GET['set_services'];
    if ($set == 0) {
    } else {
        $set = 1;
    }
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('users', ['additional_services_on' => $set], ['id' => $id]);
    \header('Location: profile.php?id=' . @$_GET['id']);
    exit;
}
if (isset($_GET['id']) && isset($_GET['package_id']) && isset($_GET['subscribed'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $id = (int) $_GET['id'];
    $package_id = (int) $_GET['package_id'];
    $subscribed = (int) $_GET['subscribed'];
    $user = \Ministra\Lib\User::getInstance($id);
    if ($subscribed) {
        $user->subscribeToPackage($package_id, \null, \true);
    } else {
        $user->unsubscribeFromPackage($package_id, \null, \true);
    }
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link type="text/css" href="../adm/css/jquery.ui.all.css" rel="stylesheet"/>
    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../adm/js/jquery-ui-1.8.16.custom.min.js"></script>
    <script type="text/javascript" src="../adm/js/jquery.tmpl.min.js"></script>
    <script type="text/javascript">

    $(function () {

      $('#expire_billing_date').datepicker({
        dateFormat: 'yy-mm-dd ',
        dayNamesMin: [
          '<?php 
echo \htmlspecialchars(\_('Sun'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Mon'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Tue'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Wed'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Thu'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Fri'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('Sat'), \ENT_QUOTES);
?>'
        ],
        firstDay: 1,
        minDate: new Date(),
        monthNames: [
          '<?php 
echo \htmlspecialchars(\_('January'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('February'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('March'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('April'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('May'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('June'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('July'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('August'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('September'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('October'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('November'), \ENT_QUOTES);
?>',
          '<?php 
echo \htmlspecialchars(\_('December'), \ENT_QUOTES);
?>'
        ]
      });
    });
    </script>
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

        table.other {
            border-width: 1px;
            border-style: solid;
            border-color: #E5E5E5;
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
    <?php 
$id = (int) @$_GET['id'];
$arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\J314c10aa912ede885c71176b652431a5::z55bad8d1e166d966765492584ab3ab41($id);
if (!empty($arr)) {
    $user = $arr;
    $mac = $arr['mac'];
    $ip = $arr['ip'];
    $video_out = $arr['video_out'];
    $parent_password = $arr['parent_password'];
    $settings_password = $arr['settings_password'];
    $tariff_plan_id = \Ministra\Lib\User::getInstance((int) $arr['id'])->getProfileParam('tariff_plan_id');
}
$fav_ch = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('fav_itv')->where(['uid' => $id])->get()->first('fav_ch');
$fav_ch_arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::d4a074f5a08e1a553d9ba42fd52addac(\base64_decode($fav_ch));
if (\is_array($fav_ch_arr)) {
    $fav_ch_count = \count($fav_ch_arr);
} else {
    $fav_ch_count = 0;
}
$tariff_plans = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name')->from('tariff_plan')->orderby('name')->get()->all();
$users = \Ministra\Lib\User::getInstance($id);
$packages = $users->getPackages();
if (empty($packages)) {
    $packages = [];
}
?>
    <title><?php 
echo \_('User profile');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="700">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('User profile');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="users.php"><< <?php 
echo \_('Back');
?></a> | <a
                    href="userlog.php?id=<?php 
echo $id;
?>"><?php 
echo \_('Logs');
?></a> | <a
                    href="events.php?mac=<?php 
echo $mac;
?>"><?php 
echo \_('Events');
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

            <table cellpadding="0" cellspacing="3" style="float:left;">
                <tr>
                    <td class="other" width="320">
                        <table>
                            <tr>
                                <td></td>
                                <td><b><?php 
echo \check_keep_alive_txt($arr['keep_alive']);
?></b></td>
                            </tr>
                            <tr>
                                <td>mac:</td>
                                <td><b><?php 
echo $mac;
?></b></td>
                            </tr>
                            <tr>
                                <td>ip:</td>
                                <td><b><?php 
echo $ip;
?></b></td>
                            </tr>
                            <tr>
                                <td>v/out:</td>
                                <td><b><?php 
echo \strtoupper($video_out);
?></b></td>
                            </tr>
                            <tr>
                                <td>pass:</td>
                                <td>[<?php 
echo $parent_password;
?>] <a href="#"
                                                                      onclick="if(confirm('<?php 
echo \htmlspecialchars(\_('Reset to default password?'), \ENT_QUOTES);
?>')){document.location='profile.php?parent_password=default&id=<?php 
echo $id;
?>'}"><?php 
echo \htmlspecialchars(\_('Reset'), \ENT_QUOTES);
?></a></td>
                            </tr>
                            <tr>
                                <td><?php 
echo \_('Access control');
?>:</td>
                                <td>[<?php 
echo $settings_password;
?>] <a href="#"
                                                                        onclick="if(confirm('<?php 
echo \htmlspecialchars(\_('Reset to default password?'), \ENT_QUOTES);
?>')){document.location='profile.php?settings_password=default&id=<?php 
echo $id;
?>'}"><?php 
echo \htmlspecialchars(\_('Reset'), \ENT_QUOTES);
?></a></td>
                            </tr>
                            <tr>
                                <td><?php 
echo \_('favorite tv');
?>:</td>
                                <td>[<?php 
\printf(\_('%s channels'), $fav_ch_count);
?>] <a href="#"
                                                                                             onclick="if(confirm('<?php 
echo \htmlspecialchars(\_('Reset favorite TV channels? The channels will be reset only if immediately restart the stb!'), \ENT_QUOTES);
?>')){document.location='profile.php?fav_itv=default&id=<?php 
echo $id;
?>'}"><?php 
echo \_('Reset');
?></a>
                                </td>
                            </tr>
                            <tr>
                                <td>version:</td>
                                <td><?php 
echo \htmlspecialchars($arr['version']);
?></td>
                            </tr>
                            <tr>
                                <td>hardware:</td>
                                <td><?php 
echo \htmlspecialchars($arr['hw_version']);
?></td>
                            </tr>
                            <tr>
                                <td>model:</td>
                                <td><?php 
echo \htmlspecialchars($arr['stb_type']);
?></td>
                            </tr>
                            <tr>
                                <td>locale:</td>
                                <td><?php 
echo \htmlspecialchars($arr['locale']);
?></td>
                            </tr>
                        </table>
                    </td>
                    <td>
                    </td>
                </tr>
            </table>

            <form method="post">
                <table style="float:left;margin-top: 3px" class="other" cellpadding="0" cellspacing="3">
                    <tr>
                        <td>
                            <?php 
echo \_('Full name');
?>:
                        </td>
                        <td>
                            <input type="text" name="fname" value="<?php 
echo $user['fname'];
?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
echo \_('Login');
?>:
                        </td>
                        <td>
                            <input type="text" name="login" value="<?php 
echo $user['login'];
?>" disabled="disabled"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
echo \_('Account number');
?>:
                        </td>
                        <td>
                            <input type="text" name="ls" value="<?php 
echo $user['ls'];
?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
echo \_('Last change of status');
?>:
                        </td>
                        <td>
                            <input type="text" name="" readonly="readonly" disabled="disabled"
                                   value="<?php 
echo $user['last_change_status'];
?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
echo \_('Account balance');
?>:
                        </td>
                        <td>
                            <input type="text" name="" readonly="readonly" disabled="disabled"
                                   value="<?php 
echo $user['account_balance'];
?>"/>
                        </td>
                    </tr>
                    <?php 
if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_internal_billing', \false)) {
    ?>
                        <tr>
                            <td>
                                <?php 
    echo \_('Expire billing date');
    ?>:
                            </td>
                            <td>
                                <input type="text" name="expire_billing_date" id="expire_billing_date"
                                       value="<?php 
    echo \date('Y-m-d', \strtotime($user['expire_billing_date']));
    ?>"/>
                            </td>
                        </tr>
                    <?php 
}
?>
                    <tr>
                        <td>
                            <?php 
echo \_('Phone number');
?>:
                        </td>
                        <td>
                            <input type="text" name="phone" value="<?php 
echo $user['phone'];
?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <?php 
echo \_('Comment');
?>:<br>
                            <textarea name="comment" rows="5" cols="36"><?php 
echo $user['comment'];
?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <?php 
echo \_('Group');
?>:
                        </td>
                        <td>
                            <input type="hidden" name="mac" value="<?php 
echo $user['mac'];
?>">
                            <select name="group_id">
                                <option value="0">--------</option>
                                <?php 
$stb_groups = new \Ministra\Lib\StbGroup();
$all_groups = $stb_groups->getAll();
$member = $stb_groups->getMemberByUid((int) $_GET['id']);
foreach ($all_groups as $group) {
    $selected = '';
    if (!empty($member) && $member['stb_group_id'] == $group['id']) {
        $selected = 'selected';
    }
    echo '<option value="' . $group['id'] . '" ' . $selected . '>' . $group['name'] . '</option>';
}
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type="submit" name="account"/>
                        </td>
                    </tr>
                </table>
            </form>

            <?php 
if (\Ministra\Lib\Admin::isPageActionAllowed()) {
    ?>
                <table cellpadding="0" cellspacing="3" width="641">
                    <tr>
                        <td class="other">
                            <table align="center" width="80%">

                                <?php 
    if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tariff_plans', \false)) {
        ?>

                                    <?php 
        if (\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::getSafe('enable_tv_subscription_for_tariff_plans', \false)) {
            ?>
                                        <tr align="center">
                                            <td>
                                                <a href="subscribe.php?id=<?php 
            echo $id;
            ?>"><?php 
            echo \_('TV subscription');
            ?></a>
                                                (<?php 
            echo \kop2grn(\Ministra\OldAdmin\get_cost_sub_channels());
            ?>)
                                            </td>
                                        </tr>
                                    <?php 
        }
        ?>

                                    <tr>
                                        <td align="center">
                                            <form method="post">
                                                <?php 
        echo \_('Tariff plan');
        ?>:
                                                <select name="tariff_plan_id">
                                                    <option value="0">---</option>
                                                    <?php 
        foreach ($tariff_plans as $plan) {
            if ($tariff_plan_id == $plan['id']) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            echo '<option value="' . $plan['id'] . '" ' . $selected . '>' . $plan['name'] . '</option>';
        }
        ?>
                                                </select>
                                                <input type="submit" name="change_tariff_plan"
                                                       value="<?php 
        echo \htmlspecialchars(\_('Change'), \ENT_QUOTES);
        ?>">
                                            </form>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center">
                                            <?php 
        if (empty($packages)) {
            echo \_('No packages available');
        } else {
            ?>
                                                <table align="center" class="list" cellspacing="0" cellpadding="3">
                                                    <caption><?php 
            echo \_('Packages');
            ?></caption>
                                                    <tr>
                                                        <th><?php 
            echo \_('Name');
            ?></th>
                                                        <th><?php 
            echo \_('Optional');
            ?></th>
                                                        <th><?php 
            echo \_('Subscribed');
            ?></th>
                                                    </tr>
                                                    <?php 
            foreach ($packages as $package) {
                echo '<tr>';
                echo '<td><a href="services_packages.php?edit=1&id=' . $package['package_id'] . '">' . $package['name'] . '</a></td>';
                echo '<td>' . ($package['optional'] ? 'yes' : 'no') . '</td>';
                if ($package['optional']) {
                    echo '<td><a href="?id=' . $id . '&package_id=' . $package['package_id'] . '&subscribed=' . ($package['subscribed'] ? 0 : 1) . '">' . ($package['subscribed'] ? 'yes' : 'no') . '</a></td>';
                } else {
                    echo '<td>' . ($package['subscribed'] ? 'yes' : 'no') . '</td>';
                }
                echo '</tr>';
            }
            ?>
                                                </table>
                                                <?php 
        }
        ?>
                                        </td>
                                    </tr>

                                <?php 
    } else {
        ?>
                                    <tr align="center">
                                        <td>
                                            <a href="subscribe.php?id=<?php 
        echo $id;
        ?>"><?php 
        echo \_('TV subscription');
        ?></a>
                                            (<?php 
        echo \kop2grn(\Ministra\OldAdmin\get_cost_sub_channels());
        ?>)
                                        </td>
                                    </tr>
                                    <tr align="center">
                                        <td>
                                            <b><?php 
        echo \_('Additional services');
        ?></b>: <?php 
        echo \Ministra\OldAdmin\additional_services_btn($id);
        ?>
                                        </td>
                                    </tr>
                                <?php 
    }
    ?>

                            </table>
                        </td>
                        <td>
                        </td>
                    </tr>
                </table>

            <?php 
}
?>

        </td>
    </tr>
</table>


