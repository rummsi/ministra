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
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('anec', ['id' => (int) $_GET['id']]);
    \header('Location: anecdote.php');
    exit;
}
if (!$error) {
    if (@$_GET['save'] && !$error) {
        if (@$_POST['anec_body']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('anec', ['anec_body' => @$_POST['anec_body'], 'added' => 'NOW()']);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('updated_places', ['anec' => 1]);
            \header('Location: anecdote.php');
            exit;
        }
        $error = \_('Error: all fields are required');
    }
    if (@$_GET['update'] && !$error) {
        if (@$_POST['anec_body']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('anec', ['anec_body' => $_POST['anec_body'], 'added' => 'NOW()'], ['id' => (int) @$_GET['id']]);
            \header('Location: anecdote.php');
            exit;
        }
        $error = \_('Error: all fields are required');
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
echo \_('Jokes');
?>
    </title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Jokes');
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
$MAX_PAGE_ITEMS = 10;
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('select * from anec')->count();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = \ceil($total_items / $MAX_PAGE_ITEMS);
$query = "select * from anec order by id desc LIMIT {$page_offset}, {$MAX_PAGE_ITEMS}";
$all_anecs = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($query)->all();
echo "<table align='center' class='list' border='0' cellpadding='3' cellspacing='0'>";
foreach ($all_anecs as $arr) {
    echo "<tr align='center'>";
    echo "<table align='center' class='list' width='400'>";
    echo '<tr>';
    echo '<td>';
    echo $arr['added'] . " <a href='?edit=1&id=" . $arr['id'] . "#form'>edit</a>&nbsp;&nbsp;";
    echo "<a href='#' onclick='if(confirm(\"" . \_('Do you really want to delete this record?') . '")){document.location="anecdote.php?del=1&id=' . $arr['id'] . "\"}'>del</a>&nbsp;&nbsp;\n";
    echo '<br><br>' . \nl2br($arr['anec_body']);
    echo '</td>';
    echo '</tr>';
    echo '</table>';
    echo '<br>';
    echo '</tr>';
}
echo '</table>';
echo '<center>' . \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages) . '</center>';
if (@$_GET['edit']) {
    $arr = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('anec')->where(['id' => (int) $_GET['id']])->get()->first();
    if (!empty($arr)) {
        $anec_body = $arr['anec_body'];
    }
}
?>
            <script>
            function save() {
              form_ = document.getElementById('form_');

              id = document.getElementById('id').value;

              action = 'anecdote.php?id=' + id;

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
              window.open(src, 'win_' + src, 'width=300,height=200,toolbar=0,location=0,directories=0,menubar=0,scrollbars=0,resizable=1,status=0,fullscreen=0');
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
                                    <td align="right" valign="top">
                                        <?php 
echo \_('Joke');
?>:
                                    </td>
                                    <td>
                                        <textarea name="anec_body" id="anec_body" cols="40"
                                                  rows="10"><?php 
echo @$anec_body;
?></textarea>
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
                                                                             onclick="document.location='anecdote.php'">
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

