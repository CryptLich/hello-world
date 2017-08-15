<?php
/*-------------------------------------------------*\
DMBS is MySQL5

The structure of the table "config":
+-------------+----------------+
|    key      |     value      |
+-------------+----------------+
|  site_name  |    test.com    |
|  home_dir   |       /        |
| admin_mail  | admin@admin.ad |
| enbl_plugin |       1        |
|  def_lang   |    english     |
|  us_cookie  |       1        |
+-------------+----------------+

The content of the table "users":
+-------------+----------------+
|    name     |    password    |
+-------------+----------------+
|    user     |     123456     |
|    admin    |     654321     |
+-------------+----------------+

You will see the structure of the table "pages" in the code.

\*-------------------------------------------------*/

function strip_deep($arr){
    foreach($arr as $key => $val){
        if(is_array($val)) $arr[$key]=strip_deep($val);
        else $arr[$key]=$val;
    }
    return $arr;
}

error_reporting(0);

foreach($_REQUEST as $key => $val){
    if(!in_array($key,array('_SERVER','_GET','_POST','_COOKIE','_SESSION','_FILES','GLOBALS'))) {
        $$key=(get_magic_quotes_gpc()?(is_array($val)?strip_deep($val):stripslashes($val)):$val);
        }
    }

global $CONF;

$res=mysql_query('SELECT `key`,`value` FROM config');

while($tmp=mysql_fetch_assoc($res))
    $CONF[$tmp['key']]=$tmp['value'];

if(!empty($CONF['site_disabled'])) {
    if(!empty($CONF['text_close'])) echo($CONF['text_close']);
    else echo($CONF['site_name'].' is temporarily down.  Please check back soon.'."\r\n");
    exit;
}

if((isset($_GET['language'])) && (preg_match('/^[a-z]+$/',$_GET['language']))) {
    if(file_exists('lang/'.$language.'.php')) {
        $CONF['patch_lang'] = 'lang/'.$language.'.php';
    }
} else {
    $CONF['patch_lang'] = 'lang/'.$CONF['def_lang'].'.php';
}

require_once($CONF['patch_lang']);

if(!empty($id)) {
    $id=str_replace("'","",$id);
    $sql="SELECT page_title, page_autor, page_content, page_date FROM pages WHERE id='".$id."'";
    if($CONF['enbl_plugin'] && !empty($CONF['plugin']) && !empty($plugin) && strpos($CONF['plugin'],$plugin)) {
            $sql.=" AND plugin='".addslashes($plugin)."'";
    }
    
    $sql.=' LIMIT 0,1';

    $res=mysql_query($sql);
                
    // Let's imagine that echo_page function just print a page content 
    // without additional sanitization of validation

    echo_page($res);
}
?>
