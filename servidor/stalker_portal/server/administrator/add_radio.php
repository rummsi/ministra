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
if (@$_GET['del']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('radio', ['id' => (int) @$_GET['id']]);
    \header('Location: add_radio.php');
    exit;
}
if (isset($_GET['status']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('radio', ['status' => (int) @$_GET['status']], ['id' => (int) @$_GET['id']]);
    \header('Location: add_radio.php');
    exit;
}
if (!$error) {
    if (@$_POST['number'] && !\Ministra\OldAdmin\check_number_radio($_POST['number']) && !@$_GET['update']) {
        $error = \sprintf(\_('Error: channel with number "%s" is already in use'), (int) $_POST['number']);
    }
    if (@$_GET['save'] && !$error) {
        if (@$_GET['cmd'] && @$_GET['name']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('radio', ['name' => @$_POST['name'], 'number' => @$_POST['number'], 'volume_correction' => @$_POST['volume_correction'], 'cmd' => @$_POST['cmd']]);
            \header('Location: add_radio.php');
            exit;
        }
        $error = \_('Error: all fields are required');
    }
    if (@$_GET['update'] && !$error) {
        if (@$_GET['cmd'] && @$_GET['name']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('radio', ['name' => $_POST['name'], 'cmd' => $_GET['cmd'], 'volume_correction' => $_POST['volume_correction'], 'number' => $_POST['number']], ['id' => (int) @$_GET['id']]);
            \header('Location: add_radio.php');
        } else {
            $error = \_('Error: all fields are required');
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
echo \_('RADIO channels');
?>
    </title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('RADIO channels');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.html"><< <?php 
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
        <td>
            <?php 
$all_radio = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->orderby('number')->get()->all();
echo "<center><table class='list' cellpadding='3' cellspacing='0'>";
echo '<tr>';
echo "<td class='list'><b>id</b></td>";
echo "<td class='list'><b>" . \_('Number') . '</b></td>';
echo "<td class='list'><b>" . \_('Name') . '</b></td>';
echo "<td class='list'><b>" . \_('URL') . '</b></td>';
echo '</tr>';
foreach ($all_radio as $arr) {
    echo '<tr>';
    echo "<td class='list'>" . $arr['id'] . '</td>';
    echo "<td class='list'>" . $arr['number'] . '</td>';
    echo "<td class='list'>" . $arr['name'] . '</td>';
    echo "<td class='list'>" . $arr['cmd'] . '</td>';
    echo "<td class='list'><a href='?edit=1&id=" . $arr['id'] . "#form'>edit</a>&nbsp;&nbsp;";
    echo "<a href='?del=1&id=" . $arr['id'] . "'>del</a>&nbsp;&nbsp;";
    if ($arr['status']) {
        echo "<a href='?status=0&id=" . $arr['id'] . "'><font color='Green'>on</font></a>&nbsp;&nbsp;";
    } else {
        echo "<a href='?status=1&id=" . $arr['id'] . "'><font color='Red'>off</font></a>&nbsp;&nbsp;";
    }
    echo '</tr>';
}
echo '</table></center>';
if (@$_GET['edit']) {
    $arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('radio')->where(['id' => (int) @$_GET['id']])->get()->first();
    if (!empty($arr)) {
        $name = $arr['name'];
        $number = $arr['number'];
        $cmd = $arr['cmd'];
        $status = $arr['status'];
        $volume_correction = $arr['volume_correction'];
    }
}
?>
            <script>
            function save() {
              form_ = document.getElementById('form_');

              name = document.getElementById('name').value;
              cmd = document.getElementById('cmd').value;
              id = document.getElementById('id').value;

              action = 'add_radio.php?name=' + name + '&cmd=' + cmd + '&id=' + id;

              if (document.getElementById('action').value == 'edit') {
                action += '&update=1';
              } else {
                action += '&save=1';
              }

              form_.setAttribute('action', action);
              form_.setAttribute('method', 'POST');
              form_.submit();
            }

            function popup(src) {
              window.open(src, 'win_' + src,
                'width=300,height=200,toolbar=0,location=0,directories=0,menubar=0,scrollbars=0,resizable=1,status=0,fullscreen=0'
              );
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
                        <form id="form_" method="POST">
                            <table align="center">
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Number');
?>:
                                    </td>
                                    <td>
                                        <input type="text" name="number" id="number" value="<?php 
echo @$number;
?>"
                                               maxlength="3">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Name');
?>:
                                    </td>
                                    <td>
                                        <input type="text" name="name" size="50" id="name"
                                               value="<?php 
echo @$name;
?>">
                                        <input type="hidden" id="id" value="<?php 
echo @$_GET['id'];
?>">
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
echo \_('URL');
?>:
                                    </td>
                                    <td>
                                        <input id="cmd" name="cmd" size="50" type="text" value="<?php 
echo @$cmd;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td align="right">
                                        <?php 
echo \_('Volume correction');
?>:
                                    </td>
                                    <td>
                                        <input id="volume_correction" name="volume_correction" size="50" type="text"
                                               value="<?php 
echo @$volume_correction;
?>">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                    </td>
                                    <td>
                                        <input type="button"
                                               value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>"
                                               onclick="save()">&nbsp;<input type="button"
                                                                             value="<?php 
echo \htmlspecialchars(\_('New'), \ENT_QUOTES);
?>"
                                                                             onclick="document.location='add_radio.php'">
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

