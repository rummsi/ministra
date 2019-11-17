<?php

\ob_start();
\session_start();
require __DIR__ . '/common.php';
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
foreach (@$_POST as $key => $value) {
}
$error = '';
$action_name = 'add';
$action_value = \_('Add');
$packages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->orderby('external_id')->get()->all();
if (!empty($_POST['add']) && !empty($_POST['name'])) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
    $all_services = empty($_POST['all_services']) ? 0 : (int) $_POST['all_services'];
    $data = ['name' => $_POST['name'], 'description' => $_POST['description'], 'external_id' => empty($_POST['external_id']) ? '' : $_POST['external_id'], 'type' => $_POST['package_type'], 'rent_duration' => $_POST['rent_duration'], 'price' => $_POST['price'], 'all_services' => $all_services];
    if (!empty($_POST['service_type'])) {
        $data['service_type'] = $_POST['service_type'];
    }
    $package_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('services_package', $data)->insert_id();
    if ($all_services) {
        $services = \null;
    } else {
        $services = \json_decode($_POST['services'], \true);
    }
    if ($services) {
        foreach ($services as $service) {
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('service_in_package', ['service_id' => $service, 'package_id' => $package_id, 'type' => $_POST['package_type']]);
        }
    }
    \header('Location: services_packages.php');
    exit;
}
$id = @(int) $_GET['id'];
if (!empty($id)) {
    if (!empty($_POST['edit']) && !empty($_POST['name'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
        $all_services = empty($_POST['all_services']) ? 0 : (int) $_POST['all_services'];
        $data = ['name' => $_POST['name'], 'description' => $_POST['description'], 'external_id' => empty($_POST['external_id']) ? '' : $_POST['external_id'], 'type' => $_POST['package_type'], 'rent_duration' => $_POST['rent_duration'], 'price' => $_POST['price'], 'all_services' => $all_services];
        if (!empty($_POST['service_type'])) {
            $data['service_type'] = $_POST['service_type'];
        }
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('services_package', $data, ['id' => $id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('service_in_package', ['package_id' => $id]);
        if ($all_services) {
            $services = \null;
        } else {
            $services = \json_decode($_POST['services'], \true);
        }
        if ($services) {
            foreach ($services as $service) {
                \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('service_in_package', ['service_id' => $service, 'package_id' => $id, 'type' => $_POST['package_type']]);
            }
        }
        \header('Location: services_packages.php');
        exit;
    } elseif (!empty($_GET['del'])) {
        \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('services_package', ['id' => $id]);
        \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('service_in_package', ['package_id' => $id]);
        \header('Location: services_packages.php');
        exit;
    }
}
if (@$_GET['edit'] && !empty($id)) {
    $action_name = 'edit';
    $action_value = \_('Save');
    $edit_package = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('services_package')->where(['id' => $id])->get()->first();
    $edit_services = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('service_in_package')->where(['package_id' => $id])->orderby('id')->get()->all('service_id');
}
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title><?php 
echo \_('SERVICES PACKAGES');
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

        .multi-selection {
            width: 200px;
            height: 300px;
        }
    </style>

    <script type="text/javascript" src="../adm/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript">

    var picked_services = <?php 
echo empty($edit_services) ? '[]' : \json_encode($edit_services);
?>;
    var picked_type = '<?php 
echo empty($edit_package['type']) ? '' : $edit_package['type'];
?>';
    var picked_service_type = '<?php 
echo empty($edit_package['service_type']) ? '' : $edit_package['service_type'];
?>';

    $(function () {

      $('.edit-mode').attr('disabled', 'disabled');

      $('.submit-form').submit(function () {

        var services = [];

        $('.services-picked option').each(function (idx, element) {
          services.push($(element).val());
        });

        $('.json-services').val(JSON.stringify(services));
        return true;
      });

      $('.package-type').change(function (eventObj) {

        var type = $('.package-type option:selected').val();

        if (type == 'module' || type == '') {
          $('.all_services').attr('disabled', 'disbled');
        } else {
          $('.all_services').removeAttr('disabled');
        }

        if (type == 'video') {
          $('.service-type').removeAttr('disabled');
        } else {
          $('.service-type').attr('disabled', 'disbled');
        }

        if (type != picked_type) {
          picked_services = [];
        }

        $('.services-available option').each(function (index, option) {
          $(option).remove();
        });

        $('.services-picked option').each(function (index, option) {
          $(option).remove();
        });

        if (type != 0) {

          $('.edit-mode').removeAttr('disabled');

          $.get('get.php?get=' + type + '_services', function (data) {
            data = JSON.parse(data);
            var options = data && data.result || [];
            var options_picked = {};
            var options_str = options.reduce(function (prev, curr) {
              if (picked_services.indexOf(curr.id) == -1) {
                return prev + '<option value=' + curr.id + '>' + curr.name + (curr.external ? ' (external)' : '') + (curr.launcher ? ' (launcher)' : '') + '</option>';
              } else {
                /*$('<option value='+curr.id+'>'+curr.name+'</option>').appendTo('.services-picked');*/
                options_picked[curr.id] = { id: curr.id, name: curr.name };
                return prev;
              }
            }, '');

            $.each(picked_services, function (num, row) {
              if (options_picked.hasOwnProperty(row)) {
                $('<option value=' + options_picked[row].id + '>' + options_picked[row].name + (options_picked[row].external || options_picked[row].id.indexOf('external_') === 0 ? ' (external)' : '') + (options_picked[row].launcher || options_picked[row].id.indexOf('launcher_') === 0 ? ' (launcher)' : '') + '</option>').appendTo('.services-picked');
              }
            });

            $(options_str).appendTo('.services-available');

            if ($('.all_services:checked').length) {
              $('.services-available').attr('disabled', 'disabled');
              $('.services-picked').attr('disabled', 'disabled');
            }
          });
        }
      });

      $('.service-type').change(function (eventObj) {
        var type = $('.service-type option:selected').val();

        if (type == 'single') {
          $('.rent-duration-block').show();
          $('.price-block').show();
        } else {
          $('.rent-duration-block').hide();
          $('.price-block').hide();
        }
      });

      $('.services-available').dblclick(function (eventObj) {
        if (eventObj.target instanceof HTMLOptionElement) {
          $(eventObj.target).appendTo('.services-picked');
        }
      });

      $('.services-picked').dblclick(function (eventObj) {
        if (eventObj.target instanceof HTMLOptionElement) {
          $(eventObj.target).appendTo('.services-available');
        }
      });

      $('.multiple-add').click(function () {
        $('.services-available option:selected').each(function (idx, element) {
          $(element).appendTo('.services-picked');
        });
        return false;
      });

      $('.multiple-delete').click(function () {
        $('.services-picked option:selected').each(function (idx, element) {
          $(element).appendTo('.services-available');
        });
        return false;
      });

      $('.package-type option[value=' + picked_type + ']').attr('selected', 'selected');
      $('.package-type').change();

      $('.service-type option[value=' + picked_service_type + ']').attr('selected', 'selected');
      $('.service-type').change();

      $('.all_services').change(function (e) {

        var checked = !!$(this).attr('checked');

        if (checked) {
          $('.services-available').attr('disabled', 'disabled');
          $('.services-picked').attr('disabled', 'disabled');
        } else {
          $('.services-available').removeAttr('disabled');
          $('.services-picked').removeAttr('disabled');
        }
      });
    });

    </script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('SERVICES PACKAGES');
?>&nbsp;</b></font>
        </td>
    </tr>
    <tr>
        <td width="100%" align="left" valign="bottom">
            <a href="tariffs.php"><< <?php 
echo \_('Back');
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
foreach ($packages as $package) {
    echo '<tr>';
    echo '<td>' . $package['external_id'] . '</td>';
    echo '<td>' . $package['name'] . '</td>';
    echo '<td style="color: #555">' . \Ministra\OldAdmin\get_users_count_in_package($package) . '</td>';
    echo '<td>';
    echo '<a href="?edit=1&id=' . $package['id'] . '">edit</a>&nbsp;';
    echo '<a href="?del=1&id=' . $package['id'] . '" onclick="if(confirm(\'' . \_('Do you really want to delete this record?') . '\')){return true}else{return false}">del</a>';
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
            <form class="submit-form" method="POST">
                <table class="form">
                    <tr>
                        <td width="200" align="right"><?php 
echo \_('External ID');
?></td>
                        <td><input type="text" name="external_id" value="<?php 
echo @$edit_package['external_id'];
?>">
                        </td>
                    </tr>
                    <tr>
                        <td align="right"><?php 
echo \_('Title');
?></td>
                        <td><input type="text" name="name" value="<?php 
echo @$edit_package['name'];
?>"></td>
                    </tr>
                    <tr>
                        <td align="right"><?php 
echo \_('Short description');
?></td>
                        <td><textarea name="description"><?php 
echo @$edit_package['description'];
?></textarea>
                    </tr>
                    <tr>
                        <td align="right"><?php 
echo \_('Service');
?></td>
                        <td>
                            <select name="package_type" class="package-type">
                                <option value="">---</option>
                                <option value="tv">tv</option>
                                <option value="video">video</option>
                                <option value="radio">radio</option>
                                <option value="module">module</option>
                                <option value="option">option</option>
                            </select>
                        </td>
                    </tr>

                    <tr>
                        <td align="right"><?php 
echo \_('Service type');
?></td>
                        <td>
                            <select name="service_type" class="service-type">
                                <option value="periodic"><?php 
echo \_('periodic');
?></option>
                                <option value="single"><?php 
echo \_('single');
?></option>
                            </select>
                        </td>
                    </tr>

                    <tr style="display: none" class="rent-duration-block">
                        <td align="right"><?php 
echo \_('Rent duration');
?></td>
                        <td>
                            <input type="text" name="rent_duration" size="7"
                                   value="<?php 
echo @$edit_package['rent_duration'];
?>"> <?php 
echo \_('h');
?>
                        </td>
                    </tr>

                    <tr style="display: none" class="price-block">
                        <td align="right"><?php 
echo \_('Price');
?></td>
                        <td>
                            <input type="number" min="0" step="0.01" name="price"
                                   value="<?php 
echo @$edit_package['price'];
?>">
                        </td>
                    </tr>

                    <tr>
                        <td align="right"><?php 
echo \_('All services');
?></td>
                        <td>
                            <input type="checkbox" name="all_services" class="all_services"
                                   value="1" <?php 
echo @$edit_package['all_services'] == 1 ? 'checked' : '';
?>>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" align="center">
                            <table>
                                <tr>
                                    <td align="center">
                                        <?php 
echo \_('Available');
?><br>
                                        <select multiple="multiple"
                                                class="multi-selection services-available edit-mode">
                                        </select>
                                    </td>
                                    <td>
                                        <button class="edit-mode multiple-add"> >></button>
                                        <br>
                                        <button class="edit-mode multiple-delete"> <<</button>
                                    </td>
                                    <td align="center">
                                        <?php 
echo \_('Selected');
?><br>
                                        <select multiple="multiple" class="multi-selection services-picked edit-mode">
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center"><input type="hidden" name="services" class="json-services">
                            <input type="submit" name="<?php 
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

