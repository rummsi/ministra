<?php

\ob_start();
\session_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
foreach (@$_POST as $key => $value) {
}
$error = '';
$action_name = 'add';
$action_value = \_('Add');
$tariff_plans = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->orderby('external_id')->get()->all();
if (!empty($_POST['add']) && !empty($_POST['name'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $plan_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('tariff_plan', ['name' => $_POST['name'], 'user_default' => empty($_POST['user_default']) ? 0 : 1, 'external_id' => empty($_POST['external_id']) ? '' : $_POST['external_id']])->insert_id();
    $packages = \json_decode($_POST['packages'], \true);
    if ($packages) {
        foreach ($packages as $package) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('package_in_plan', ['plan_id' => $plan_id, 'package_id' => $package['id'], 'optional' => $package['optional']]);
        }
    }
    \header('Location: tariffs.php');
    exit;
}
$id = @(int) $_GET['id'];
if (!empty($id)) {
    if (!empty($_POST['edit']) && !empty($_POST['name'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('tariff_plan', ['name' => $_POST['name'], 'user_default' => empty($_POST['user_default']) ? 0 : 1, 'external_id' => empty($_POST['external_id']) ? '' : $_POST['external_id']], ['id' => $id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('package_in_plan', ['plan_id' => $id]);
        $packages = \json_decode($_POST['packages'], \true);
        if ($packages) {
            foreach ($packages as $package) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('package_in_plan', ['plan_id' => $id, 'package_id' => $package['id'], 'optional' => $package['optional']]);
            }
        }
        \header('Location: tariffs.php');
        exit;
    } elseif (!empty($_GET['del'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('tariff_plan', ['id' => $id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('package_in_plan', ['plan_id' => $id]);
        \header('Location: tariffs.php');
        exit;
    }
}
if (@$_GET['edit'] && !empty($id)) {
    $action_name = 'edit';
    $action_value = \_('Save');
    $edit_tariff = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['id' => $id])->get()->first();
    $default_packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('package_id as id, optional')->from('package_in_plan')->where(['plan_id' => $id])->get()->all();
    $default_packages = \array_map(function ($package) {
        $package['optional'] = (int) $package['optional'];
        return $package;
    }, $default_packages);
} else {
    $default_packages = [];
}
$user_default_tariff_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('tariff_plan')->where(['user_default' => 1])->get()->first('id');
$packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->select('id, name')->from('services_package')->get()->all();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php 
echo \_('TARIFF PLANS');
?></title>
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

    (function ($) {
      $.fn.packagePicker = function (options) {

        var defaults = {
          packages: [/*
                        {"id" : 1, "name" : "package 1"},
                        {"id" : 2, "name" : "package 2"},
                        {"id" : 3, "name" : "package 3"},
                        {"id" : 4, "name" : "package 4"},
                        {"id" : 5, "name" : "package 5"}
                    */],

          optional_title: 'optional',

          default_packages: [
            /*{"id" : 2, "optional" : true},
            {"id" : 4, "optional" : true}*/
          ]
        };

        var opts = $.extend(defaults, options);

        var packages_str = opts['packages'].reduce(function (previous, current) {
          return previous + '<option value="' + current.id + '">' + current.name + '</option>';
        }, '<option value="0">---</option>');

        opts['default_packages'].map(function (t_package) {
          addPackagePicker(t_package);
        });

        addPackagePicker();

        this.closest('form').submit(function () {
          var packages = [];
          $(this).find('.package-block').each(function (idx, element) {
            var packet_id = $(element).find('option:selected').val();
            var optional = !!$(element).find('input:checked').size();
            if (packet_id != 0) {
              packages.push({ 'id': packet_id, 'optional': optional });
            }
          });
          $('.json-packages').val(JSON.stringify(packages));
          return true;
        });

        // Bind onchange events
        $('.packages').live('change', function (eventObj) {

          updateDisabledPackages();

          if ($(eventObj.target).find('option:selected').val() == 0) {
            cleanEmptyPickers();
          } else {
            addPackagePicker();
          }
        });

        // Add new picker
        function addPackagePicker(selected) {
          selected_val = selected && selected.id || 0;

          if (selected_val == 0 && $('.packages option:selected[value=0]').size() > 0) {
            return;
          }

          var num = $('.packages').size() + 1;

          $('<div class="package-block"><select class="packages">' + packages_str + '</select><input type="checkbox"'
            + (selected && selected.optional ? ' checked="checked"' : '')
            + '/>' + opts['optional_title'] + '</div>')
            .appendTo('.package-container')
            .find('option[value=' + selected_val + ']')
            .attr('selected', 'selected');

          updateDisabledPackages();
        }

        // Removes all empty pickers and add one empty
        function cleanEmptyPickers() {
          $('.packages option:selected[value=0]').each(function (idx, element) {
            $(element).parent().parent().remove();
          });

          addPackagePicker();
        }

        // Disabled all used packages
        function updateDisabledPackages() {

          var selected = [];

          $('.packages option:selected').each(function (idx, element) {
            if ($(element).val() != 0) {
              selected.push(parseInt($(element).val(), 10));
            }
          });

          $('.packages option').each(function (idx, element) {

            var picked = $(element).parent().find('option:selected').val();

            if (selected.indexOf(parseInt($(element).val(), 10)) >= 0 && $(element).val() != picked) {
              $(element).attr('disabled', 'disabled');
            } else {
              $(element).removeAttr('disabled');
            }
          });
        }
      };
    })(jQuery);

    $(function () {
      $('.package-container').packagePicker({
        optional_title: '<?php 
echo \htmlspecialchars(\_('optional'), \ENT_QUOTES);
?>',
        packages: <?php 
echo \json_encode($packages);
?>,
        default_packages: <?php 
echo \json_encode($default_packages);
?>
      });
    });

    </script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px"
                  color="White"><b>&nbsp;&nbsp;<?php 
echo \_('TARIFF PLANS');
?> <?php 
if (!\Ministra\Lib\S642b6461e59cef199375bfb377c17a39\a777f7659bfaad9ba0acb83e0c546a50::get('enable_tariff_plans')) {
    echo '(' . \_('disabled') . ')';
}
?>&nbsp;&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="index.php"><< <?php 
echo \_('Back');
?></a> | <a
                    href="services_packages.php"><?php 
echo \_('Services packages');
?></a>
        </td>
    </tr>
    <tr>
        <td align="center">
            <font color="Red">
                <strong>
                    <?php 
echo @$error;
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
echo \_('External ID');
?></td>
                    <td><?php 
echo \_('Title');
?></td>
                    <td><?php 
echo \_('Total users');
?></td>
                    <td>&nbsp;</td>
                </tr>
                <?php 
foreach ($tariff_plans as $plan) {
    echo '<tr ' . ($plan['user_default'] == 1 ? 'style="background-color: #ecffec;"' : '') . '>';
    echo '<td>' . $plan['external_id'] . '</td>';
    echo '<td>' . $plan['name'] . '</td>';
    echo '<td style="color: #555">' . \Ministra\OldAdmin\get_users_count_in_tariff($plan) . '</td>';
    echo '<td>';
    echo '<a href="?edit=1&id=' . $plan['id'] . '">edit</a>&nbsp;';
    echo '<a href="?del=1&id=' . $plan['id'] . '" onclick="if(confirm(\'' . \_('Do you really want to delete this record?') . '\')){return true}else{return false}">del</a>';
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
                        <td width="100"><?php 
echo \_('External ID');
?></td>
                        <td><input type="text" name="external_id" value="<?php 
echo @$edit_tariff['external_id'];
?>">
                        </td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('Title');
?></td>
                        <td><input type="text" name="name" value="<?php 
echo @$edit_tariff['name'];
?>"></td>
                    </tr>
                    <tr>
                        <td><?php 
echo \_('Use as default');
?></td>
                        <td><input type="checkbox"
                                   name="user_default"
                                   value="1"
                                <?php 
echo !empty($edit_tariff['user_default']) && $edit_tariff['user_default'] == 1 ? 'checked="checked"' : '';
?>
                                <?php 
echo !empty($user_default_tariff_id) && (empty($edit_tariff) || $user_default_tariff_id != $edit_tariff['id']) ? 'disabled="disabled"' : '';
?>
                            ></td>
                    </tr>
                    <tr>
                        <td style="vertical-align: top"><?php 
echo \_('Packages');
?></td>
                        <td class="package-container"></td>
                    </tr>
                    <tr>
                        <td><input type="hidden" name="packages" class="json-packages"></td>
                        <td><input type="submit" name="<?php 
echo $action_name;
?>"
                                   value="<?php 
echo $action_value;
?>"/></td>
                    </tr>
                </table>
            </form>
        </td>
    </tr>
</table>
</body>
</html>

