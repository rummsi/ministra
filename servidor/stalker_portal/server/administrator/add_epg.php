<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Itv;
\error_reporting(\E_ALL);
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
if (!$error) {
    if (@$_GET['save'] && $_GET['yy'] && $_GET['mm'] && $_GET['dd'] && @$_GET['id']) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        $epg = $_POST['epg'];
        $yy = $_GET['yy'];
        if ($_GET['mm'] < 10) {
            $mm = '0' . $_GET['mm'];
        } else {
            $mm = $_GET['mm'];
        }
        if ($_GET['dd'] < 10) {
            $dd = '0' . $_GET['dd'];
        } else {
            $dd = $_GET['dd'];
        }
        $time_from = $yy . '-' . $mm . '-' . $dd . ' 00:00:00';
        $time_to = $yy . '-' . $mm . '-' . $dd . ' 23:59:59';
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('epg', ['ch_id' => (int) $_GET['id'], 'time>=' => $time_from, 'time<=' => $time_to]);
        $tmp_epg = \preg_split("/\n/", \stripslashes(\trim($epg)));
        $date = $yy . '-' . $mm . '-' . $dd;
        for ($i = 0; $i < \count($tmp_epg); ++$i) {
            $epg_line = \trim($tmp_epg[$i]);
            $line_arr = \Ministra\OldAdmin\get_line($date, $tmp_epg, $i);
            if (empty($line_arr)) {
                continue;
            }
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('epg', ['ch_id' => (int) $_GET['id'], 'name' => $line_arr['name'], 'time' => $line_arr['time'], 'time_to' => $line_arr['time_to'], 'duration' => $line_arr['duration'], 'real_id' => $_GET['id'] . '_' . \strtotime($line_arr['time'])]);
        }
        \header('Location: add_epg.php?id=' . $_GET['id'] . '&mm=' . $_GET['mm'] . '&dd=' . $_GET['dd'] . '&yy=' . $_GET['yy'] . '&saved=1');
        exit;
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
    <title>
        <?php 
echo \_('EPG');
?>
    </title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('EPG');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="add_itv.php"><< <?php 
echo \_('Back');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <strong>
                    <?php 
if (@$_GET['saved']) {
    echo \_('Saving was successful');
}
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
if (@$_GET['edit']) {
    $channel = \Ministra\Lib\Itv::getById((int) @$_GET['id']);
    if (!empty($channel)) {
        $name = $channel['name'];
        $cmd = $channel['cmd'];
        $descr = $channel['descr'];
        $status = $channel['status'];
    }
}
?>
            <script>
            function save() {
              form = document.getElementById('form');

              name = document.getElementById('name').value;
              cmd = document.getElementById('cmd').value;
              id = document.getElementById('id').value;
              descr = document.getElementById('descr').value;

              action = 'add_itv.php?name=' + name + '&cmd=' + cmd + '&id=' + id + '&descr=' + descr;
              //alert(action)
              if (document.getElementById('action').value == 'edit') {
                action += '&update=1';
              } else {
                action += '&save=1';
              }

              //alert(action)
              form.setAttribute('action', action);
              document.location = action;
              //form.submit()
            }

            function load_epg() {
              form = document.getElementById('form');
              id = document.getElementById('id').options[document.getElementById('id').selectedIndex].value;
              yy = document.getElementById('yy').options[document.getElementById('yy').selectedIndex].value;
              mm = document.getElementById('mm').options[document.getElementById('mm').selectedIndex].value;
              dd = document.getElementById('dd').options[document.getElementById('dd').selectedIndex].value;
              //alert('id:'+id+' yy:'+yy+' mm:'+mm+' dd:'+dd)
              action = 'add_epg.php?id=' + id + '&yy=' + yy + '&mm=' + mm + '&dd=' + dd;
              document.location = action;
            }

            function save_epg() {
              form = document.getElementById('form');
              id = document.getElementById('id').options[document.getElementById('id').selectedIndex].value;
              yy = document.getElementById('yy').options[document.getElementById('yy').selectedIndex].value;
              mm = document.getElementById('mm').options[document.getElementById('mm').selectedIndex].value;
              dd = document.getElementById('dd').options[document.getElementById('dd').selectedIndex].value;
              epg = document.getElementById('epg').value;

              action = 'add_epg.php?id=' + id + '&yy=' + yy + '&mm=' + mm + '&dd=' + dd + '&save=1';
              //alert(action)
              //document.location=action
              form.setAttribute('action', action);
              form.submit();
            }
            </script>
            <br>
            <table align="center" class='list'>
                <tr>
                    <td>
                        &nbsp;
                    </td>
                </tr>
                <tr>
                    <td>
                        <form id="form" method="POST">
                            <table align="center">
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Channel');
?>:
                                    </td>
                                    <td>
                                        <!--input type="text" name="name" id="name" value="<?php 
?>"-->
                                        <select name="id" id="id">
                                            <?php 
echo \Ministra\OldAdmin\construct_option(@$_GET['id']);
?>
                                        </select>

                                        <!--input type="hidden" id="id" value="<?php 
?>"-->
                                        <input type="hidden" id="action" value="<?php 
if (@$_GET['edit']) {
    echo 'edit';
}
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Date');
?>:
                                    </td>
                                    <td>
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
                                        <!--input id="cmd" type="text" value="<?php 
?>"-->&nbsp;
                                        <input type="button"
                                               value="<?php 
echo \htmlspecialchars(\_('Load EPG'), \ENT_QUOTES);
?>"
                                               onclick="load_epg()">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right" valign="top">
                                        EPG:
                                    </td>
                                    <td>
                                        <!--input id="descr" type="text" value="<?php 
?>"-->
                                        <textarea name="epg" id="epg" cols="70"
                                                  rows="20"><?php 
echo \Ministra\OldAdmin\load_epg(@$_GET['id']);
?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="submit"
                                               value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>"
                                               onclick="save_epg()">&nbsp;<input type="button"
                                                                                 value="<?php 
echo \htmlspecialchars(\_('New'), \ENT_QUOTES);
?>"
                                                                                 onclick="document.location='add_epg.php'">
                                    </td>
                                </tr>
                            </table>
                        </form>
                        <a name="form"></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>

