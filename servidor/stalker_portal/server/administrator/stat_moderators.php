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
echo '<pre>';
echo '</pre>';
$search = @$_GET['search'];
$letter = @$_GET['letter'];
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
echo \_('Moderators statistics');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="600">
    <tr>
        <td align="center" valign="middle" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Moderators statistics');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a> | <a
                    href="tasks_archive.php"><?php 
echo \_('Video archive');
?></a> | <a
                    href="karaoke_archive.php"><?php 
echo \_('Karaoke archive');
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
        <td align="center">
            <?php 
$sql = 'select * from administrators where access=2';
if (!\Ministra\Lib\Admin::isPageActionAllowed()) {
    $sql .= " and login='" . $_SESSION['login'] . "'";
}
$administrators = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
while ($arr = $administrators->next()) {
    $closed = \Ministra\OldAdmin\get_closed_tasks($arr['id']);
    $rejected = \Ministra\OldAdmin\get_rejected_tasks($arr['id']);
    $closed_2 = $closed - $rejected;
    $total_open_karaoke = \Ministra\OldAdmin\get_open_karaoke($arr['id']);
    $closed_karaoke = \Ministra\OldAdmin\get_closed_karaoke($arr['id']);
    $open_karaoke = $total_open_karaoke - $closed_karaoke;
    ?>

                <b><?php 
    echo $arr['login'];
    ?></b>

                <table width="600" align="center">
                    <tr>
                        <td width="50%" align="center" valign="top">
                            <a href="last_closed_tasks.php?id=<?php 
    echo $arr['id'];
    ?>"><?php 
    echo \_('Movie');
    ?></a>
                            <table border="1" width="200" cellspacing="0">
                                <tr>
                                    <td width="170">
                                        <?php 
    echo \_('Total');
    ?>
                                    </td>
                                    <td width="30">
                                        <?php 
    echo \Ministra\OldAdmin\get_total_tasks($arr['id']);
    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo \_('Opened');
    ?>
                                    </td>
                                    <td>
                                        <?php 
    echo \Ministra\OldAdmin\get_open_tasks($arr['id']);
    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo \_('Closed');
    ?>
                                    </td>
                                    <td>
                                        <?php 
    echo $closed_2;
    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php 
    echo \_('Rejected');
    ?>
                                    </td>
                                    <td>
                                        <?php 
    echo $rejected;
    ?>
                                    </td>
                                </tr>
                            </table>
                        </td>

                        <td width="50%" align="center" valign="top">
                            <a href="last_closed_karaoke.php?id=<?php 
    echo $arr['id'];
    ?>"><?php 
    echo \_('Karaoke');
    ?></a>
                            <table border="1" width="200" cellspacing="0">
                                <tr>
                                    <td width="170">
                                        <?php 
    echo \_('Total');
    ?>
                                    </td>
                                    <td width="30">
                                        <?php 
    echo $total_open_karaoke;
    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170">
                                        <?php 
    echo \_('Opened');
    ?>
                                    </td>
                                    <td width="30">
                                        <?php 
    echo $open_karaoke;
    ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="170">
                                        <?php 
    echo \_('Closed');
    ?>
                                    </td>
                                    <td width="30">
                                        <?php 
    echo $closed_karaoke;
    ?>
                                    </td>
                                </tr>
                            </table>
                        </td>

                    </tr>
                </table>
                <br>

                <?php 
}
?>

        </td>
    </tr>
</table>
</body>
</html>

