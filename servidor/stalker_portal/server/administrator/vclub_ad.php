<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\VclubAdvertising;
use Ministra\Lib\VideoCategory;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
foreach (@$_POST as $key => $value) {
    if (\is_string($_POST[$key])) {
        $_POST[$key] = \trim($value);
    }
}
$id = @(int) $_GET['id'];
$ad = new \Ministra\Lib\VclubAdvertising();
if (isset($_GET['status']) && !empty($_GET['id'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    $ad->updateById((int) $_GET['id'], ['status' => (int) $_GET['status']]);
    \header('Location: vclub_ad.php');
    exit;
}
if (!empty($_POST['add'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $ad->add($_POST);
    \header('Location: vclub_ad.php');
    exit;
} elseif (!empty($_POST['edit']) && $id) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    $ad->updateById($id, $_POST);
    \header('Location: vclub_ad.php');
    exit;
} elseif (!empty($_GET['del']) && $id) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    $ad->delById($id);
    \header('Location: vclub_ad.php');
    exit;
}
if (!empty($_GET['edit']) && !empty($id)) {
    $current_ad = $ad->getById($id);
}
$ads = $ad->getAllWithStatForMonth();
if (!empty($_GET['id'])) {
    $denied_categories = $ad->getDeniedVclubCategoriesForAd((int) $_GET['id']);
} else {
    $denied_categories = [];
}
$video_category = new \Ministra\Lib\VideoCategory();
$video_categories = $video_category->getAll();
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

    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">
    $(function () {

    });
    </script>

    <title><?php 
echo \_('Video club advertising');
?></title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Video club advertising');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="add_video.php"><< <?php 
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
        <td align="center">
            <table class='list' cellpadding='3' cellspacing='0'>
                <tr>
                    <td><?php 
echo \_('Title');
?></td>
                    <td>Video URL</td>
                    <td><?php 
echo \_('Must watch');
?></td>
                    <td style="text-align: center"><?php 
echo \_('Watch started');
?>
                        <br>(<?php 
echo \_('in the last 30 days');
?>)
                    </td>
                    <td style="text-align: center"><?php 
echo \_('Watch ended');
?>
                        <br>(<?php 
echo \_('in the last 30 days');
?>)
                    </td>
                    <td><?php 
echo \_('Weight');
?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
foreach ($ads as $ad) {
    echo '<tr>';
    echo '<td>' . $ad['title'] . '</td>';
    echo '<td>' . $ad['url'] . '</td>';
    echo '<td>' . ($ad['must_watch'] == 'all' ? \_('All') : $ad['must_watch'] . '%') . '</td>';
    echo '<td>' . (int) $ad['started'] . '</td>';
    echo '<td>' . (int) $ad['ended'] . '</td>';
    echo '<td>' . $ad['weight'] . '</td>';
    echo '<td>';
    echo '<a href="?status=' . (int) (!$ad['status']) . '&id=' . $ad['id'] . '" style="color:' . ($ad['status'] == 0 ? 'red' : 'green') . ';font-weight:bold">' . ($ad['status'] == 0 ? 'off' : 'on') . '</a>&nbsp;';
    echo '<a href="?edit=1&id=' . $ad['id'] . '">edit</a>&nbsp;';
    echo '<a href="?del=1&id=' . $ad['id'] . '" onclick="if(confirm(\'' . \_('Do you really want to delete this record?') . '\')){return true}else{return false}">del</a>';
    echo '</td>';
    echo '</tr>';
}
?>
            </table>
        </td>
    </tr>
    <tr>
        <td align="center">
            <br>
            <br>

            <form method="POST">
                <table class="form">
                    <tr>
                        <td><?php 
echo \_('Title');
?></td>
                        <td>
                            <input type="text" name="title" size="37" maxlength="40"
                                   value="<?php 
echo @$current_ad['title'];
?>"/>
                            <input type="hidden" name="<?php 
echo !empty($_GET['edit']) ? 'edit' : 'add';
?>" value="1">
                        </td>
                    </tr>
                    <tr>
                        <td>Video URL</td>
                        <td><input type="text" name="url" size="37" value="<?php 
echo @$current_ad['url'];
?>"/></td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('Weight');
?></td>
                        <td><input type="text" name="weight" size="37"
                                   value="<?php 
echo empty($current_ad) ? '1' : $current_ad['weight'];
?>"/></td>
                    </tr>
                    <tr>
                        <td valign="top"><?php 
echo \_('Deny for video categories');
?></td>
                        <td class="categories-container">
                            <select name="denied_categories[]" multiple="multiple"
                                    size="<?php 
echo \count($video_categories);
?>">
                                <?php 
foreach ($video_categories as $video_category) {
    if (!empty($denied_categories) && \in_array($video_category['id'], $denied_categories)) {
        $selected = 'selected';
    } else {
        $selected = '';
    }
    echo '<option value="' . $video_category['id'] . '" ' . $selected . '>' . $video_category['category_name'] . '</option>';
}
?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('Must watch');
?></td>
                        <td>
                            <select name="must_watch">
                                <option value="all" <?php 
echo @$current_ad['must_watch'] == 'all' ? 'selected' : '';
?>><?php 
echo \_('All');
?></option>
                                <option value="90" <?php 
echo @$current_ad['must_watch'] == '90' ? 'selected' : '';
?>>
                                    90%
                                </option>
                                <option value="80" <?php 
echo @$current_ad['must_watch'] == '80' ? 'selected' : '';
?>>
                                    80%
                                </option>
                                <option value="70" <?php 
echo @$current_ad['must_watch'] == '70' ? 'selected' : '';
?>>
                                    70%
                                </option>
                                <option value="60" <?php 
echo @$current_ad['must_watch'] == '60' ? 'selected' : '';
?>>
                                    60%
                                </option>
                                <option value="50" <?php 
echo @$current_ad['must_watch'] == '50' ? 'selected' : '';
?>>
                                    50%
                                </option>
                                <option value="40" <?php 
echo @$current_ad['must_watch'] == '40' ? 'selected' : '';
?>>
                                    40%
                                </option>
                                <option value="30" <?php 
echo @$current_ad['must_watch'] == '30' ? 'selected' : '';
?>>
                                    30%
                                </option>
                                <option value="20" <?php 
echo @$current_ad['must_watch'] == '20' ? 'selected' : '';
?>>
                                    20%
                                </option>
                                <option value="10" <?php 
echo @$current_ad['must_watch'] == '10' ? 'selected' : '';
?>>
                                    10%
                                </option>
                                <option value="5" <?php 
echo @$current_ad['must_watch'] == '5' ? 'selected' : '';
?>>
                                    5%
                                </option>
                                <option value="0" <?php 
echo @$current_ad['must_watch'] == '0' ? 'selected' : '';
?>>
                                    0%
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td><input type="submit" value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>"/></td>
                    </tr>
                </table>
            </form>

        </td>
    </tr>
</table>

