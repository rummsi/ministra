<?php

\session_start();
\ob_start();
require __DIR__ . '/common.php';
if (!isset($locale)) {
    $locale = 'en';
}
use Ministra\Lib\Admin;
use Ministra\Lib\AdminAccess;
use Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89;
use Ministra\Lib\Karaoke;
$error = '';
\Ministra\Lib\Admin::checkAuth();
\Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_VIEW);
$search = @$_GET['search'];
$letter = @$_GET['letter'];
echo '<pre>';
echo '</pre>';
if (!$_SERVER['QUERY_STRING']) {
    unset($_SESSION['upload']);
}
$ext = '';
if (@$_GET['path']) {
    \preg_match("/[(\\S+)](.)[(\\S+)]\$/", $_GET['path'], $arr);
    $ext = @$arr[0];
}
if (isset($_FILES['screenshot'])) {
    if (\is_uploaded_file($_FILES['screenshot']['tmp_name'])) {
        if (\preg_match('/jpeg/', $_FILES['screenshot']['type'])) {
            $upload_id = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('screenshots', ['name' => $_FILES['screenshot']['name'], 'size' => $_FILES['screenshot']['size'], 'type' => $_FILES['screenshot']['type']])->insert_id();
            $_SESSION['upload'][] = $upload_id;
            $img_path = \get_save_folder($upload_id);
            \rename($_FILES['screenshot']['tmp_name'], $img_path . '/' . $upload_id . '.jpg');
        }
    }
}
if (isset($_GET['accessed']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\set_karaoke_accessed(@$_GET['id'], @$_GET['accessed']);
    $id = $_GET['id'];
    if ($_GET['accessed'] == 1) {
        \chmod(\KARAOKE_STORAGE_DIR . '/' . $id . '.mpg', 0444);
    } else {
        \chmod(\KARAOKE_STORAGE_DIR . '/' . $id . '.mpg', 0666);
    }
    \header('Location: add_karaoke.php?letter=' . @$_GET['letter'] . '&search=' . @\urldecode($_GET['search']) . '&page=' . @$_GET['page']);
    exit;
}
if (isset($_GET['done']) && @$_GET['id']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CONTEXT_ACTION);
    \Ministra\OldAdmin\set_karaoke_done(@$_GET['id'], @$_GET['done']);
    $id = $_GET['id'];
    \header('Location: add_karaoke.php?letter=' . @$_GET['letter'] . '&search=' . @\urldecode($_GET['search']) . '&page=' . @$_GET['page']);
    exit;
}
if (@$_GET['del']) {
    \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_DELETE);
    \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->delete('karaoke', ['id' => (int) @$_GET['id']]);
    \header("Location: add_karaoke.php?search={$search}&letter={$letter}");
}
$type = '';
if (!$error) {
    $rtsp_url = @\trim($_POST['rtsp_url']);
    $protocol = @$_POST['protocol'];
    if ($protocol == 'custom') {
        $rtsp_url = @\trim($_POST['rtsp_url']);
    } else {
        $rtsp_url = '';
    }
    $status = $rtsp_url ? 1 : 0;
    if (@$_GET['save']) {
        if (@$_GET['name']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_CREATE);
            $datetime = \date('Y-m-d H:i:s');
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->insert('karaoke', ['name' => @$_GET['name'], 'protocol' => $protocol, 'rtsp_url' => $rtsp_url, 'genre_id' => @$_POST['genre_id'], 'singer' => @$_POST['singer'], 'author' => @$_POST['author'], 'added' => $datetime, 'status' => $status, 'add_by' => @$_SESSION['uid']]);
            unset($_SESSION['upload']);
            \header("Location: add_karaoke.php?search={$search}&letter={$letter}");
            exit;
        }
        $error = \_('Error: all fields are required');
    }
    if (@$_GET['update']) {
        if (@$_GET['name']) {
            \Ministra\Lib\Admin::checkAccess(\Ministra\Lib\AdminAccess::ACCESS_EDIT);
            \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->update('karaoke', ['name' => $_GET['name'], 'protocol' => $protocol, 'rtsp_url' => $rtsp_url, 'genre_id' => @$_POST['genre_id'], 'singer' => @$_POST['singer'], 'status' => $status, 'author' => @$_POST['author']], ['id' => (int) @$_GET['id']]);
            unset($_SESSION['upload']);
            \header("Location: add_karaoke.php?search={$search}&letter={$letter}");
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

        .list2 {
            border-width: 1px;
            border-style: solid;
            border-color: #c5c5c5;
            padding-left: 5px;
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
    <script language="JavaScript" src="js.js"></script>
    <title>
        <?php 
echo \_('KARAOKE');
?>
    </title>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
            <font size="5px" color="White"><b>&nbsp;<?php 
echo \_('KARAOKE');
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
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
$where = '';
if ($search) {
    if (!$where) {
        $where .= ' where ';
    } else {
        $where .= ' and ';
    }
    $where .= ' karaoke.name like "%' . $search . '%"';
}
if ($letter) {
    if (!$where) {
        $where .= ' where ';
    } else {
        $where .= ' and ';
    }
    $where .= ' karaoke.name like "' . $letter . '%"';
}
if (@$_GET['status']) {
    if (@$_GET['status'] == 'on') {
        $op_accessed = 1;
    } elseif (@$_GET['status'] == 'off') {
        $op_accessed = 0;
    }
    if ($where) {
        $where .= ' and accessed=' . $op_accessed;
    } else {
        $where .= 'where accessed=' . $op_accessed;
    }
}
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query("select * from karaoke {$where}")->count();
$page_offset = $page * $MAX_PAGE_ITEMS;
$total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
if (!empty($_GET['letter'])) {
    $orderby = 'name';
} else {
    $orderby = 'id';
}
$all_karaoke = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query('select karaoke.*,administrators.login, media_claims.media_type, media_claims.media_id, ' . "media_claims.sound_counter, media_claims.video_counter\n                from karaoke\n                left join administrators on administrators.id=karaoke.add_by\n                left join media_claims on karaoke.id=media_claims.media_id and media_claims.media_type='karaoke'\n                {$where} group by karaoke.id, karaoke.add_by order by {$orderby} LIMIT {$page_offset}, {$MAX_PAGE_ITEMS}");
?>
            <table border="0" align="center" width="620">
                <tr>
                    <td>
                        <form action="" method="GET">
                            <input type="text" name="search" value="<?php 
echo $search;
?>">
                            <input type="submit"
                                   value="<?php 
echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
?>">&nbsp;
                            <font
                                    color="Gray"><?php 
echo \_('search by clip name');
?></font>
                        </form>
                    <td>
                </tr>
                <?php 
if (\substr($locale, 0, 2) == 'ru') {
    ?>
                    <tr>
                        <td align="center">
                            <a href="?letter=А">А</a>&nbsp;
                            <a href="?letter=Б">Б</a>&nbsp;
                            <a href="?letter=В">В</a>&nbsp;
                            <a href="?letter=Г">Г</a>&nbsp;
                            <a href="?letter=Д">Д</a>&nbsp;
                            <a href="?letter=Е">Е</a>&nbsp;
                            <a href="?letter=Ё">Ё</a>&nbsp;
                            <a href="?letter=Ж">Ж</a>&nbsp;
                            <a href="?letter=З">З</a>&nbsp;
                            <a href="?letter=И">И</a>&nbsp;
                            <a href="?letter=Й">Й</a>&nbsp;
                            <a href="?letter=К">К</a>&nbsp;
                            <a href="?letter=Л">Л</a>&nbsp;
                            <a href="?letter=М">М</a>&nbsp;
                            <a href="?letter=Н">Н</a>&nbsp;
                            <a href="?letter=О">О</a>&nbsp;
                            <a href="?letter=П">П</a>&nbsp;
                            <a href="?letter=Р">Р</a>&nbsp;
                            <a href="?letter=С">С</a>&nbsp;
                            <a href="?letter=Т">Т</a>&nbsp;
                            <a href="?letter=У">У</a>&nbsp;
                            <a href="?letter=Ф">Ф</a>&nbsp;
                            <a href="?letter=Х">Х</a>&nbsp;
                            <a href="?letter=Ц">Ц</a>&nbsp;
                            <a href="?letter=Ч">Ч</a>&nbsp;
                            <a href="?letter=Ш">Ш</a>&nbsp;
                            <a href="?letter=Щ">Щ</a>&nbsp;
                            <a href="?letter=Ъ">Ъ</a>&nbsp;
                            <a href="?letter=Ы">Ы</a>&nbsp;
                            <a href="?letter=Ь">Ь</a>&nbsp;
                            <a href="?letter=Э">Э</a>&nbsp;
                            <a href="?letter=Ю">Ю</a>&nbsp;
                            <a href="?letter=Я">Я</a>&nbsp;
                        <td>
                    </tr>
                <?php 
}
?>
                <tr>
                    <td align="center">
                        <a href="?letter=A">A</a>&nbsp;
                        <a href="?letter=B">B</a>&nbsp;
                        <a href="?letter=C">C</a>&nbsp;
                        <a href="?letter=D">D</a>&nbsp;
                        <a href="?letter=E">E</a>&nbsp;
                        <a href="?letter=F">F</a>&nbsp;
                        <a href="?letter=G">G</a>&nbsp;
                        <a href="?letter=H">H</a>&nbsp;
                        <a href="?letter=I">I</a>&nbsp;
                        <a href="?letter=J">J</a>&nbsp;
                        <a href="?letter=K">K</a>&nbsp;
                        <a href="?letter=L">L</a>&nbsp;
                        <a href="?letter=M">M</a>&nbsp;
                        <a href="?letter=N">N</a>&nbsp;
                        <a href="?letter=O">O</a>&nbsp;
                        <a href="?letter=P">P</a>&nbsp;
                        <a href="?letter=Q">Q</a>&nbsp;
                        <a href="?letter=R">R</a>&nbsp;
                        <a href="?letter=S">S</a>&nbsp;
                        <a href="?letter=T">T</a>&nbsp;
                        <a href="?letter=U">U</a>&nbsp;
                        <a href="?letter=V">V</a>&nbsp;
                        <a href="?letter=W">W</a>&nbsp;
                        <a href="?letter=X">X</a>&nbsp;
                        <a href="?letter=Y">Y</a>&nbsp;
                        <a href="?letter=Z">Z</a>&nbsp;
                    <td>
                </tr>
            </table>

            <table>
                <tr>
                    <td>
                        <select id="sort_opt" onchange="change_list()">
                            <option value="">---
                            <option value="on" <?php 
if (@$_GET['status'] == 'on') {
    echo 'selected';
}
?>>on
                            <option value="off" <?php 
if (@$_GET['status'] == 'off') {
    echo 'selected';
}
?>>off
                        </select>
                    </td>
                </tr>
            </table>
            <?php 
echo "<center><table class='list' cellpadding='3' cellspacing='0'>";
echo '<tr>';
echo "<td class='list'><b>" . \_('File name') . '</b></td>';
echo "<td class='list'><b>" . \_('Song name') . '</b></td>';
echo "<td class='list'><b>" . \_('Performer') . '</b></td>';
echo "<td class='list'><b>" . \_('Posted by') . '</b></td>';
echo "<td class='list'><b>" . \_('When') . '</b></td>';
echo "<td class='list'><b>" . \_('Claims about<br>audio/video') . "</b></td>\n";
echo "<td class='list'>&nbsp;</td>";
echo '</tr>';
while ($arr = $all_karaoke->next()) {
    echo "<tr>\n";
    echo "<td class='list'><a href='javascript://'";
    if (empty($arr['rtsp_url'])) {
        echo " onclick='open_info({$arr['id']})'";
    }
    echo '>' . \Ministra\OldAdmin\check_file($arr['id']) . '</a></td>';
    echo "<td class='list'>" . $arr['name'] . '</td>';
    echo "<td class='list'>" . $arr['singer'] . '</td>';
    echo "<td class='list'>" . $arr['login'] . '</td>';
    echo "<td class='list'>" . $arr['added'] . '</td>';
    echo "<td class='list' align='center'>";
    if (\Ministra\Lib\Admin::isActionAllowed()) {
        echo "<a href='#' onclick='if(confirm(\"" . \_('Do you really want to reset claims counter?') . '")){document.location="claims.php?reset=1&media_id=' . $arr['media_id'] . '&media_type=' . $arr['media_type'] . "\"}'>";
    }
    echo "<span style='color:red;font-weight:bold'>" . $arr['sound_counter'] . ' / ' . $arr['video_counter'] . '</span>';
    if (\Ministra\Lib\Admin::isActionAllowed()) {
        echo '</a>';
    }
    echo "</td>\n";
    echo "<td class='list'><a href='?edit=1&id=" . $arr['id'] . "&search={$search}&letter={$letter}#form'>edit</a>&nbsp;&nbsp;";
    if (\Ministra\Lib\Admin::isActionAllowed()) {
        echo "<a href='#' onclick='if(confirm(\"" . \_('Do you really want to delete this record?') . '")){document.location="add_karaoke.php?del=1&id=' . $arr['id'] . '&letter=' . @$_GET['letter'] . '&search=' . @$_GET['search'] . "\"}'>del</a>&nbsp;&nbsp;\n";
    }
    echo \Ministra\OldAdmin\get_karaoke_accessed_color($arr['id']) . '&nbsp;&nbsp;';
    echo \Ministra\OldAdmin\get_done_karaoke_color($arr['id']);
    echo "</td>\n";
    echo "</tr>\n";
    ?>
    <tr style="display:none;" id="info_<?php 
    echo $arr['id'];
    ?>" bgcolor="#f2f2f2">

        <td colspan="7">
            <table cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td id="loading_bar_<?php 
    echo $arr['id'];
    ?>" style="display:">
                                    &nbsp;<?php 
    echo \_('Loading');
    ?>...
                                </td>
                                <td id="error_bar_<?php 
    echo $arr['id'];
    ?>" style="display:none">
                                    <font color="red"><?php 
    echo \_('Not found');
    ?>!</font>
                                </td>
                                <td style="display:none" id="storages_<?php 
    echo $arr['id'];
    ?>">
                                    <table class='list' border="1" cellpadding='0' cellspacing='0'
                                           id="storages_content_<?php 
    echo $arr['id'];
    ?>">
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
                <?php 
}
echo "</table>\n";
echo "<table width='600' align='center' border=0>";
echo '<tr>';
echo "<td width='100%' align='center'>";
echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</center>';
if (@$_GET['edit']) {
    $arr = \Ministra\Lib\Karaoke::getById((int) @$_GET['id']);
    if (!empty($arr)) {
        $name = $arr['name'];
        $genre_id = $arr['genre_id'];
        $singer = $arr['singer'];
        $author = $arr['author'];
        $year = $arr['year'];
        $rtsp_url = $arr['rtsp_url'];
        $protocol = $arr['protocol'];
    }
    $screenshots = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('screenshots')->where(['media_id' => (int) @$_GET['id']])->get()->all('id');
    if (!empty($screenshots)) {
        $_SESSION['upload'] = $screenshots;
    }
}
$upload_str = '';
?>
    <script type="text/javascript">
    function change_list() {
      var opt_sort = document.getElementById('sort_opt');
      document.location = 'add_karaoke.php?status=' + opt_sort.options[opt_sort.selectedIndex].value +
        <?php 
echo '\'&search=' . @$_GET['search'] . '&letter=' . @$_GET['letter'] . '&page=' . @$_GET['page'] . '\'' . "\n";
?>;
    }

    function open_info(id) {
      var info_display = document.getElementById('info_' + id).style.display;
      if (info_display == 'none') {
        document.getElementById('info_' + id).style.display = '';
        doLoad('karaoke_info', id);
      } else {
        document.getElementById('info_' + id).style.display = 'none';
        document.getElementById('storages_content_' + id).innerHTML = '';
      }
    }

    function display_info(arr, id) {
      //alert(arr.toSource())
      if (arr.length > 0) {
        document.getElementById('loading_bar_' + id).style.display = 'none';

        table = '<tr>';
        table += '<td class="list2" width="70"><?php 
echo \htmlspecialchars(\_('Server'), \ENT_QUOTES);
?></td>';
        table += '<td class="list2" width="70"><?php 
echo \htmlspecialchars(\_('File'), \ENT_QUOTES);
?></td>';
        table += '</tr>';

        for (i = 0; i < arr.length; i++) {
          table += '<tr>';
          table += '<td class="list2"><b>' + arr[i]['storage_name'] + '</b></td>';
          table += '<td class="list2"><b><font color="green">' + arr[i]['file'] + '</font></b></td>';
          table += '</tr>';
        }

        document.getElementById('storages_content_' + id).innerHTML = table;
        document.getElementById('error_bar_' + id).style.display = 'none';
        document.getElementById('storages_' + id).style.display = '';
        document.getElementById('path_' + id).style.color = 'green';
      } else {
        document.getElementById('loading_bar_' + id).style.display = 'none';
        document.getElementById('error_bar_' + id).style.display = '';
        document.getElementById('path_' + id).style.color = 'red';
      }
    }

    function doLoad(get, data) {

      var req = new Subsys_JsHttpRequest_Js();
      req.onreadystatechange = function () {
        if (req.readyState == 4) {

          if (req.responseJS) {

            if (get == 'karaoke_info') {

              var info = req.responseJS.data;
              if (info != null) {
                display_info(info, data);
              }
              return;
            }

          } else {
            if (get == 'karaoke_info') {
              alert('<?php 
echo \htmlspecialchars(\_('Error: The file or directory may contain invalid characters'), \ENT_QUOTES);
?>');
            }
          }
        }
      };
      req.caching = false;

      req.open('POST', 'load.php?get=' + get, true);
      send = { data: data };
      req.send(send);
    }

    function hint() {
      alert(document.getElementById('f_file').value);
    }

    function save() {
      form_ = document.getElementById('form_');

      name = document.getElementById('name').value;
      //path = document.getElementById('f_file').value
      id = document.getElementById('id').value;
      //description = document.getElementById('description').value

      //action = 'add_video.php?name='+name+'&path='+path+'&id='+id
      action = 'add_karaoke.php?name=' + name + '&id=' + id +
        '&letter=<?php 
echo @$_GET['letter'];
?>&search=<?php 
echo @$_GET['search'];
?>';

      if (document.getElementById('action').value == 'edit') {
        action += '&update=1';
      } else {
        action += '&save=1';
      }

      form_.setAttribute('action', action);
      form_.setAttribute('method', 'POST');
      form_.submit();
    }

    function check_protocol() {

      var protocol_obj = document.getElementById('protocol');
      var rtsp_url_block = document.getElementById('rtsp_url_block');

      if (protocol_obj.options[protocol_obj.selectedIndex].value == 'custom') {
        rtsp_url_block.style.display = '';
      } else {
        rtsp_url_block.style.display = 'none';
      }
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
                <form id="form_" enctype="multipart/form-data" method="POST">
                    <table align="center">
                        <tr>
                            <td align="right">
                                <?php 
echo \_('Name');
?>:
                            </td>
                            <td>
                                <input type="text" size="40" name="name" id="name" value='<?php 
echo @$name;
?>'>
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
                            <td align="right" valign="top">
                                <?php 
echo \_('Protocol');
?>:
                            </td>
                            <td>
                                <select name="protocol" id="protocol" onchange="check_protocol()">
                                    <option value="nfs" <?php 
if ($protocol == 'nfs') {
    echo 'selected';
}
?>>NFS
                                    </option>
                                    <option value="http" <?php 
if ($protocol == 'http') {
    echo 'selected';
}
?>>HTTP
                                    </option>
                                    <option value="custom" <?php 
if ($protocol == 'custom') {
    echo 'selected';
}
?>>Custom URL
                                    </option>
                                </select>
                            </td>
                        </tr>
                        <tr id="rtsp_url_block" <?php 
if ($protocol != 'custom') {
    echo 'style="display:none"';
}
?>>
                            <td align="right" valign="top">
                                RTSP/HTTP URL:
                            </td>
                            <td>
                                <input name="rtsp_url" id="rtsp_url" type="text" onblur="" size="40"
                                       value="<?php 
echo @$rtsp_url;
?>"> (<?php 
echo \_('include solution');
?>)
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                                <?php 
echo \_('Performer');
?>:
                            </td>
                            <td>
                                <input type="text" size="40" name="singer" id="singer" value='<?php 
echo @$singer;
?>'>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">
                                <?php 
echo \_('Author');
?>:
                            </td>
                            <td>
                                <input name="author" type="text" size="40" value='<?php 
echo @$author;
?>'>
                            </td>
                        </tr>
                        <tr>
                            <td align="right" valign="top">
                                <?php 
echo \_('Genre');
?>:
                            </td>
                            <td>
                                <select name="genre_id">
                                    <option value="0"/>
                                    -----------
                                    <?php 
echo \Ministra\OldAdmin\get_genres_karaoke($genre_id);
?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td align="right">
                            </td>
                            <td>
                                <?php 
echo $upload_str;
?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <input type="button" value="<?php 
echo \htmlspecialchars(\_('Save'), \ENT_QUOTES);
?>"
                                       onclick="save()">&nbsp;<input type="button"
                                                                     value="<?php 
echo \htmlspecialchars(\_('New'), \ENT_QUOTES);
?>"
                                                                     onclick="document.location='add_karaoke.php'">
                            </td>
                        </tr>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <br>
                                <b><?php 
echo \_('Form filling order');
?>:</b><br><br>
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

