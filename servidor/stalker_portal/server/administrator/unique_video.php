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
$storage_name = @$_GET['storage'];
$search = @$_GET['search'];
$letter = @$_GET['letter'];
if (@$_GET['view'] != 'text') {
    ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php 
    echo \_('Search unique movies');
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
.list{
    border-width: 1px;
    border-style: solid;
    border-color: #E5E5E5;
}
.list2{
    border-width: 1px;
    border-style: solid;
    border-color: #c5c5c5;
    padding-left: 5px;
}
a{
    color:#0000FF;
    font-weight: bold;
    text-decoration:none;
}
a:link,a:visited {
    color:#5588FF;
    font-weight: bold;
}
a:hover{
    color:#0000FF;
    font-weight: bold;
    text-decoration:underline;
}

.search_form th{
    text-align: center;
}
.search_form td{
    text-align: center;
}
</style>

<script language="JavaScript" src="js.js"></script>

<script>
    <?php 
    if (\Ministra\Lib\Admin::isPageActionAllowed()) {
        echo "var can_md5dum=1\n";
    } else {
        echo "var can_md5dum=0\n";
    }
    ?>
function doLoad(get, data){

    var req = new Subsys_JsHttpRequest_Js();
    req.onreadystatechange = function() {
        if (req.readyState == 4) {

            if (req.responseJS) {

                if (get == 'vclub_info'){

                    var info = req.responseJS.data;
                    if(info != null){
                        display_info(info, data);
                    }
                    return;
                }

                if (get == 'startmd5sum'){
                    if (req.responseJS.error){
                        document.getElementById('md5sum_link_'+data.media_name+'_'+data.storage_name).innerHTML = '<?php 
    echo \htmlspecialchars(\_('error'), \ENT_QUOTES);
    ?>';
                        alert(req.responseJS.error);
                    }else{
                        document.getElementById('md5sum_link_'+data.media_name+'_'+data.storage_name).innerHTML = '<?php 
    echo \htmlspecialchars(\_('counting'), \ENT_QUOTES);
    ?>';
                    }
                }

                if (get == 'chk_name'){
                    var resp = req.responseJS;
                    if(resp != null){
                        resp_check_name(resp);
                    }
                    return;
                }

                if (get == 'chk_org_name'){
                    var resp = req.responseJS;
                    if(resp != null){
                        resp_check_org_name(resp);
                    }
                    return;
                }

                if (get == 'get_cat_genres'){
                    var resp = req.responseJS.data;
                    if(resp != null){
                        set_cat_genres(resp);
                    }
                    return;
                }

            }else{
                if (get == 'vclub_info'){
                    alert('<?php 
    echo \htmlspecialchars(\_('Error: The file or directory may contain invalid characters'), \ENT_QUOTES);
    ?>');
                }
            }
        }
    };
    req.caching = false;

    req.open('POST', 'load.php?get='+get, true);
    send = {data : data};

    req.send(send);
}

function open_info(id){
    var info_display = document.getElementById('info_'+id).style.display;
    if (info_display == 'none'){
        document.getElementById('info_'+id).style.display = '';
        doLoad('vclub_info', id);
    }else{
        document.getElementById('info_'+id).style.display = 'none';
        document.getElementById('storages_content_'+id).innerHTML = '';
    }
}

function display_info(arr, id){
    //alert(arr.toSource())
    if (arr.length > 0){
        document.getElementById('loading_bar_'+id).style.display = 'none';

        var md5sum = '';
        var table  = '<tr>';
        table += '<td class="list2" width="70"><?php 
    echo \htmlspecialchars(\_('Server'), \ENT_QUOTES);
    ?></td>';
        table += '<td class="list2" width="200"><?php 
    echo \htmlspecialchars(\_('Folder'), \ENT_QUOTES);
    ?></td>';
        table += '<td class="list2" width="60"><?php 
    echo \htmlspecialchars(\_('Series'), \ENT_QUOTES);
    ?></td>';
        table += '<td class="list2">&nbsp;</td>';
        table += '</tr>';

        for (i=0; i<arr.length; i++){
            var md5btn_txt = '';
            if (arr[i]['files'][0]['status'] == 'done'){
                if (arr[i]['files'][0]['md5'] != ''){
                    md5btn_txt = '<?php 
    echo \htmlspecialchars(\_('check'), \ENT_QUOTES);
    ?>';
                }else{
                    md5btn_txt = '<?php 
    echo \htmlspecialchars(\_('count md5 sum'), \ENT_QUOTES);
    ?>';
                }
            }else{
                md5btn_txt = '<?php 
    echo \htmlspecialchars(\_('counting'), \ENT_QUOTES);
    ?>';
            }
            table +='<tr>';
                 table +='<td class="list2"><b>'+arr[i]['storage_name']+'</b></td>';
                 table +='<td class="list2"><b><a href="#" onclick="document.getElementById(\'files_'+id+'_'+arr[i]['storage_name']+'\').style.display=\'\';return false;"><font color="green">'+arr[i]['path']+'</font></a></b></td>';
                 table +='<td class="list2">'+arr[i]['series']+'</td>';
                 table +='<td class="list2"><sub><a href="#" id="md5sum_link_'+arr[i]['path']+'_'+arr[i]['storage_name']+'" onclick="md5sum(this,\''+arr[i]['files'][0]['status']+'\',\''+arr[i]['path']+'\', \''+arr[i]['storage_name']+'\');return false;">'+md5btn_txt+'</a><sub></td>';
            table +='</tr>';

            table +='<tr style="display:none" id="files_'+id+'_'+arr[i]['storage_name']+'">';
            table +='<td colspan="4" class="list2" width="100%" style="padding-right:5px">';
            table +='<table width="100%" border="0" cellpadding="0" cellspacing="0">';
            for (j=0; j<arr[i]['files'].length; j++){
                table +='<tr>';
                if(arr[i]['files'][j]['status'] == 'done'){
                    md5sum = arr[i]['files'][j]['md5'];
                }else{
                    md5sum = '<?php 
    echo \htmlspecialchars(\_('counting'), \ENT_QUOTES);
    ?>...';
                }

                table +='<td nowrap width="100%" align="right"><sub><b>'+arr[i]['files'][j]['name']+'</b> '+md5sum+'</sub></td>';

                table +='</tr>';
            }
            table +='<tr><td><sub><br></sub></td></tr>';
            table +='</table>';
            table +='</td>';
            table +='</tr>';

            //document.getElementById('series_'+id).innerHTML = arr[i]['series'];
        }

        document.getElementById('storages_content_'+id).innerHTML = table;
        document.getElementById('error_bar_'+id).style.display = 'none';
        document.getElementById('storages_'+id).style.display = '';
        //document.getElementById('path_'+id).style.color = 'green';
    }else{
        document.getElementById('loading_bar_'+id).style.display = 'none';
        document.getElementById('error_bar_'+id).style.display = '';
        //document.getElementById('path_'+id).style.color = 'red';
    }
}

function md5sum(obj, status, media_name, storage_name){
    if (can_md5dum){
        if (status == 'done'){
            obj.innerHTML = '<?php 
    echo \htmlspecialchars(\_('waiting...'), \ENT_QUOTES);
    ?>';
            doLoad('startmd5sum',{'media_name':media_name, 'storage_name':storage_name});
        }
    }else{
        alert('<?php 
    echo \htmlspecialchars(\_('You are not authorized for this action'), \ENT_QUOTES);
    ?>');
    }
}

</script>
</head>
<body>
<table align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
    <td align="center" valign="middle" width="100%" bgcolor="#88BBFF">
    <font size="5px" color="White"><b>&nbsp;<?php 
    echo \_('Search unique movies');
    ?>&nbsp;</b></font>
    </td>
</tr>
<tr>
    <td width="100%" align="left" valign="bottom">
        <a href="storages.php"><< <?php 
    echo \_('Back');
    ?></a> | <a href="?<?php 
    echo $_SERVER['QUERY_STRING'] . '&view=text';
    ?>"><?php 
    echo \_('Plain text');
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

<form method="get">
<table class="search_form" align="center" width="60%">
    <tr>
        <td>
            <table>
                <tr>
                    <th><?php 
    echo \_('On server');
    ?></th>
                </tr>
                <tr>
                    <td>
                        <?php 
    $storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['status' => 1])->get();
    while ($arr = $storages->next()) {
        echo '<input type="radio" id="' . $arr['storage_name'] . '_on_storage" name="on_storage[]" value="' . $arr['storage_name'] . '"';
        $on_storage = [];
        if (@\is_array($_GET['on_storage'])) {
            $on_storage = $_GET['on_storage'];
        }
        if (\in_array($arr['storage_name'], $on_storage)) {
            echo ' checked';
        }
        echo '></input><label for="' . $arr['storage_name'] . '_on_storage">' . $arr['storage_name'] . '</label><br>';
    }
    ?>
                    </td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <th><?php 
    echo \_('Excluding');
    ?></th>
                </tr>
                <tr>
                    <td>
                        <?php 
    $storages = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storages')->where(['status' => 1])->get();
    while ($arr = $storages->next()) {
        echo '<input type="checkbox" id="' . $arr['storage_name'] . '_exclude_storage" name="exclude[]" value="' . $arr['storage_name'] . '"';
        $exclude = [];
        if (@\is_array($_GET['exclude'])) {
            $exclude = $_GET['exclude'];
        }
        if (\in_array($arr['storage_name'], $exclude)) {
            echo ' checked';
        }
        echo '></input><label for="' . $arr['storage_name'] . '_exclude_storage">' . $arr['storage_name'] . '</label><br>';
    }
    ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="submit" value="<?php 
    echo \htmlspecialchars(\_('Search'), \ENT_QUOTES);
    ?>" name="search"/>
        </td>
    </tr>
</table>
</form>

    <?php 
} else {
    \header('Content-Type: text/plain');
}
$page = @$_REQUEST['page'] + 0;
$MAX_PAGE_ITEMS = 30;
$page_offset = $page * $MAX_PAGE_ITEMS;
$on_storage = [];
if (isset($_GET['on_storage']) && \is_array($_GET['on_storage'])) {
    $on_storage = $_GET['on_storage'];
}
$on_storage_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->from('storage_cache')->where(['media_type' => 'vclub', 'status' => 1])->in('storage_name', $on_storage)->get()->all('media_id');
$on_storage_ids_str = \implode(',', $on_storage_ids);
if (empty($on_storage_ids_str)) {
    $on_storage_ids_str = '0';
}
$exclude = [];
if (@\is_array($_GET['exclude'])) {
    $exclude = $_GET['exclude'];
}
$exclude_q = [];
foreach ($exclude as $storage) {
    $exclude_q[] = "'" . $storage . "'";
}
$exclude_str = \implode(',', $exclude_q);
if (empty($exclude_str)) {
    $exclude_str = "'0'";
}
$sql = "select *, count(storage_name) as storages from storage_cache where media_type='vclub' and status=1 and media_id in (" . $on_storage_ids_str . ') and storage_name not in (' . $exclude_str . ') group by media_id having storages=1';
$result_ids = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql)->all('media_id');
$result_ids_str = \implode(',', $result_ids);
if (empty($result_ids_str)) {
    $result_ids_str = '0';
}
$sql = 'select * from video where status in(1,3) and accessed=1 and id in(' . $result_ids_str . ')';
$total_items = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql)->count();
$total_pages = (int) ($total_items / $MAX_PAGE_ITEMS + 0.999999);
$sql = 'select *, count_first_0_5+count_second_0_5 as views from video where status in(1,3) and accessed=1 and id in(' . $result_ids_str . ') order by views';
if (@$_GET['view'] != 'text') {
    $sql .= " limit {$page_offset}, {$MAX_PAGE_ITEMS}";
}
$video = \Ministra\Lib\S642b6461e59cef199375bfb377c17a39\L18e6d54d6202a6e70c8e428830aa4c89::getInstance()->query($sql);
$page = @(int) $_GET['page'];
$i = $page * $MAX_PAGE_ITEMS + 1;
if (@$_GET['view'] != 'text') {
    echo "<center><table class='list' cellpadding='3' cellspacing='0'>\n";
    echo '<tr>';
    echo "<td class='list'><b>#</b></td>\n";
    echo "<td class='list'><b>id</b></td>\n";
    echo "<td class='list'><b>" . \_('Movie') . ' (' . \_('folder') . ")</b></td>\n";
    echo "<td class='list'><b>" . \_('Total views per month') . "</b></td>\n";
    echo "</tr>\n";
    while ($arr = $video->next()) {
        echo '<tr>';
        echo "<td class='list'>" . $i . "</td>\n";
        echo "<td class='list'>" . $arr['id'] . "</td>\n";
        echo "<td class='list'><a href='javascript://' onclick='open_info({$arr['id']})'>" . \Ministra\OldAdmin\get_path_color($arr['id'], $arr['path']) . "</a></td>\n";
        echo "<td class='list'>" . $arr['views'] . "</td>\n";
        echo "</tr>\n";
        ?>

        <tr style="display:none;" id="info_<?php 
        echo $arr['id'];
        ?>" bgcolor="#f2f2f2">
        <td>
            &nbsp;
        </td>

        <td colspan="10">
        <table cellpadding="0" cellspacing="0">
          <tr>
            <td>
              <table cellpadding="0" cellspacing="0">
               <tr>
                <td id="loading_bar_<?php 
        echo $arr['id'];
        ?>" style="display:">
                    <?php 
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
                    <table class='list' border="1" cellpadding='0' cellspacing='0' id="storages_content_<?php 
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
        ++$i;
    }
    echo "</table>\n";
    echo "<table width='700' align='center' border=0>\n";
    echo "<tr>\n";
    echo "<td width='100%' align='center'>\n";
    echo \Ministra\OldAdmin\page_bar($MAX_PAGE_ITEMS, $page, $total_pages);
    echo "</td>\n";
    echo "</tr>\n";
    echo "</table>\n";
    echo "</center>\n";
} else {
    while ($arr = $video->next()) {
        echo $i . "\t" . $arr['path'] . "\t" . $arr['views'] . "\r\n";
        ++$i;
    }
}
