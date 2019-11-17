<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\SysEvent;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (@$_GET['save']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $sub_str = '';
    $bonus_str = '';
    $uid = @$_POST['uid'];
    if (empty($_POST['sub_ch'])) {
        $sub = [];
    } else {
        $sub = \explode(',', $_POST['sub_ch']);
    }
    if (empty($_POST['bonus_ch'])) {
        $bonus = [];
    } else {
        $bonus = \explode(',', $_POST['bonus_ch']);
    }
    $sub_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($sub));
    $bonus_str = \base64_encode(\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\Z860a165ed018f157fd40ef2297209b46\O29f79fdee870487fdf0c508ebd65b3c1::H62b6690510bd2660999bb4e5c5e18316($bonus));
    $itv_subscription = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('itv_subscription')->where(['uid' => $uid])->get()->first();
    if (!empty($itv_subscription)) {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('itv_subscription', ['sub_ch' => $sub_str, 'bonus_ch' => $bonus_str, 'addtime' => 'NOW()'], ['uid' => $uid])->result();
    } else {
        $result = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('itv_subscription', ['uid' => $uid, 'sub_ch' => $sub_str, 'bonus_ch' => $bonus_str, 'addtime' => 'NOW()'])->insert_id();
    }
    if ($result) {
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($uid);
        $event->sendUpdateSubscription();
        $event = new \Ministra\Lib\SysEvent();
        $event->setUserListById($uid);
        $event->sendMsg(\_('Updated according to the subscription channels.'));
        \js_redirect('profile.php?id=' . $uid, \_('Subscription saved'));
    } else {
        echo \_('error');
    }
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
    <script>
    function add() {
      var all = document.getElementById('all');
      var sel = document.getElementById('sel');
      if (all.selectedIndex >= 0) {
        var item = all.options[all.selectedIndex].value;
        var text = all.options[all.selectedIndex].text;
        all.options[all.selectedIndex] = null;
        sel.options[sel.options.length] = new Option(text, item);
      }
    }

    function add_all() {
      var all = document.getElementById('all');
      var sel = document.getElementById('sel');

      for (i = 0; i < all.options.length; i++) {
        sel.options[sel.options.length] = new Option(all.options[i].text, all.options[i].value);
      }
      all.options.length = 0;
    }

    function del() {
      var all = document.getElementById('all');
      var sel = document.getElementById('sel');
      if (sel.selectedIndex >= 0) {
        var item = sel.options[sel.selectedIndex].value;
        var text = sel.options[sel.selectedIndex].text;
        sel.options[sel.selectedIndex] = null;
        all.options[all.options.length] = new Option(text, item);
      }
    }

    function del_all() {
      var all = document.getElementById('all');
      var sel = document.getElementById('sel');

      for (i = 0; i < sel.options.length; i++) {
        all.options[all.options.length] = new Option(sel.options[i].text, sel.options[i].value);
      }
      sel.options.length = 0;
    }

    function bonus_add() {
      var all = document.getElementById('all');
      var sel = document.getElementById('bonus');
      if (all.selectedIndex >= 0) {
        var item = all.options[all.selectedIndex].value;
        var text = all.options[all.selectedIndex].text;
        all.options[all.selectedIndex] = null;
        sel.options[sel.options.length] = new Option(text, item);
      }
    }

    function bonus_del() {
      var all = document.getElementById('all');
      var sel = document.getElementById('bonus');
      if (sel.selectedIndex >= 0) {
        var item = sel.options[sel.selectedIndex].value;
        var text = sel.options[sel.selectedIndex].text;
        sel.options[sel.selectedIndex] = null;
        all.options[all.options.length] = new Option(text, item);
      }
    }

    function sub(form) {
      var _sel = document.getElementById('sel');
      var _bonus = document.getElementById('bonus');
      var order = '';

      var sub_ch = [];

      for (var i = 0; i < _sel.options.length; i++) {
        order += 'sub[]=' + _sel.options[i].value + '&';
        sub_ch.push(_sel.options[i].value);
      }

      document.getElementById('sub_ch').value = sub_ch.join(',');

      var bonus_ch = [];

      for (i = 0; i < _bonus.options.length; i++) {
        order += 'bonus[]=' + _bonus.options[i].value + '&';
        bonus_ch.push(_bonus.options[i].value);
      }

      document.getElementById('bonus_ch').value = bonus_ch.join(',');

      var form_ = document.getElementById('sub_form');

      form_.setAttribute('action', form_.action + '?save=1');
      form_.setAttribute('method', 'POST');
      form_.submit();
    }
    </script>
    <?php 
$id = (int) @$_GET['id'];
$sub_ch = $bonus_ch = \null;
$sub_ch = \get_sub_channels($sub_ch);
$bonus_ch = \get_bonus_channels($bonus_ch);
?>
    <title><?php 
echo \_('TV channels subscription');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="620">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('TV channels subscription');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="profile.php?id=<?php 
echo @$_GET['id'];
?>"><< <?php 
\_('Back');
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

            <table width="90%" height="85%" align="center">
                <tr align="center" valign="middle">
                    <td valign="middle">
                        <form method="POST" id="sub_form" action="subscribe.php">
                            <table align="center" border="1">
                                <tr>
                                    <td width="33%" align="center" valign="top">
                                        <?php 
echo \_('All channels');
?>
                                        <select multiple id="all" class="all">
                                            <?php 
echo \Ministra\OldAdmin\get_all_channels_opt($sub_ch, $bonus_ch);
?>
                                        </select>
                                        <input type="hidden" name="uid" id="uid" value="<?php 
echo @$_GET['id'];
?>">
                                    </td>

                                    <td width="34%" align="center" height="100%">
                                        <table border="0" height="100%">
                                            <tr>
                                                <td height="80%" align="center">
                                                    <input type="button" value="<?php 
echo \htmlspecialchars(\_('All'), \ENT_QUOTES);
?> >>" onclick="add_all()"/><br>
                                                    <input type="button" value=">>" onclick="add()"/><br>
                                                    <input type="button" value="<<" onclick="del()"/><br>
                                                    <input type="button" value="<< <?php 
echo \_('All');
?>"
                                                           onclick="del_all()"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10%" align="center">
                                                    <input type="button" value=">>" onclick="bonus_add()"/><br><input
                                                            type="button" value="<<" onclick="bonus_del()"/>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="10%" align="center" valign="bottom">
                                                    <input type="hidden" name="sub_ch" id="sub_ch"/>
                                                    <input type="hidden" name="bonus_ch" id="bonus_ch"/>
                                                    <input type="button" value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>" onclick="sub(this.form)"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>

                                    <td width="33%" align="center" valign="top">
                                        <?php 
echo \_('Subscription');
?>
                                        <select multiple id="sel" name="order" class="sub">
                                            <?php 
echo \Ministra\OldAdmin\get_sub_channels_opt($sub_ch);
?>
                                        </select>
                                        <br><br><br>
                                        <?php 
echo \_('Bonus');
?>
                                        <select multiple id="bonus" name="bonus" class="bonus">
                                            <?php 
echo \Ministra\OldAdmin\get_bonus_channels_opt($bonus_ch);
?>
                                        </select>
                                    </td>

                                </tr>
                            </table>
                        </form>
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

