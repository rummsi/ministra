<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$default_template = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('settings')->get()->first('default_template');
if (!empty($_POST['template'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('settings', ['default_template' => $_POST['template']]);
    \header('Location: themes.php');
    exit;
}
$themes = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\B05fd1aa4b15191a1638079bdd4bd6b17::Z0fd1c2d07cda5c7a6fc59959fc2aa1b2();
if (!empty($themes[$default_template])) {
    $current_theme = $themes[$default_template];
} else {
    $current_theme = ['name' => \ucwords(\str_replace('_', ' ', $default_template)), 'preview' => '../../c/template/' . $default_template . '/preview.png'];
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

        .template {
            width: 350px;
            height: 220px;
            border: 1px solid #fff;
            float: left;
            margin: 0 10px 50px 10px;
            cursor: pointer;
        }

        .apply-btn {
            visibility: hidden;
        }

        .template:hover, .template[data-selected="true"] {
            border: 1px solid #88BBFF;
        }

        .template[data-selected="true"] .apply-btn {
            visibility: visible;
        }

        .template-title {
            height: 20px;
            width: 200px;
            position: relative;
            top: -8px;
            background-color: #fff;
            font-weight: bold;
        }

        .template-preview {
            width: 320px;
            height: 180px;
            border: 1px solid #ccc;
        }
    </style>
    <title><?php 
echo \_('Templates');
?></title>

    <script src="../adm/js/jquery-1.7.1.min.js"></script>

    <script>
    $(function () {
      var default_template = '<?php 
echo $default_template;
?>';

      $('.template').click(function () {
        $('.template').each(function () {
          $(this).removeAttr('data-selected');
        });

        $(this).attr('data-selected', 'true');
      });
    });
    </script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0" width="750">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('Templates');
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
    <pre>
    <?php 
echo $error;
?>
    </pre>
                </strong>
            </font>
            <br>
            <br>
        </td>
    </tr>
    <tr>
        <td align="center">

            <b><?php 
echo \_('CURRENT THEME');
?>: <span
                        class="current-skin"><?php 
echo $current_theme['name'];
?></span></b>
            <div class="template_preview" style="width: 160px; height: 90px; border: 1px solid #ccc;margin-top: 10px">
                <img width="160" height="90"
                     onerror="$(this).parent().text('<?php 
echo \_('preview not available');
?>')"
                     src="<?php 
echo $current_theme['preview'];
?>"/>
            </div>

            <div class="preview-list" style="margin-top: 60px">
                <?php 
foreach ($themes as $theme) {
    ?>
                    <div class="template">
                        <div class="template-title"><?php 
    echo $theme['name'];
    ?></div>
                        <div class="template-preview">
                            <img width="320" height="180"
                                 onerror="$(this).parent().text('<?php 
    echo \_('preview not available');
    ?>')"
                                 src="<?php 
    echo $theme['preview'];
    ?>"/>
                        </div>

                        <form method="POST">
                            <input class="template_select" name="template" value="<?php 
    echo $theme['id'];
    ?>"
                                   type="hidden">
                            <input type="submit" class="apply-btn" value="<?php 
    echo \_('Apply');
    ?>"
                                   style="margin-top: 30px"/>
                        </form>
                    </div>
                    <?php 
}
?>
            </div>
        </td>
    </tr>
</table>

