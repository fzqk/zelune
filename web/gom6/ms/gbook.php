<?php
# PHP guestbook (GBook)
# Version: 1.6
# File last modified: 14th Feb 2008 18:28
# File name: gbook.php
# http://www.PHPJunkYard.com

##############################################################################
# COPYRIGHT NOTICE                                                           #
# Copyright 2004-2008 PHPJunkyard All Rights Reserved.                       #
#                                                                            #
# This script may be used and modified free of charge by anyone so long as   #
# this copyright notice and the comments above remain intact. By using this  #
# code you agree to indemnify Klemen Stirn from any liability that might     #
# arise from it's use.                                                       #
#                                                                            #
# Selling the code for this program without prior written consent is         #
# expressly forbidden. In other words, please ask first before you try and   #
# make money off this program.                                               #
#                                                                            #
# Obtain permission before redistributing this software over the Internet or #
# in any other medium. In all cases copyright and header must remain intact. #
# This Copyright is in full effect in any country that has International     #
# Trade Agreements with the United States of America or with                 #
# the European Union.                                                        #
##############################################################################

#############################
#     DO NOT EDIT BELOW     #
#############################

error_reporting(E_ALL ^ E_NOTICE);
define('IN_SCRIPT',true);

require('settings.php');
require($settings['language']);
$settings['verzija']='1.6';

/* First thing to do is make sure the IP accessing GBook hasn't been banned */
gbook_CheckIP();

$a=gbook_input($_REQUEST['a']);

/* And this will start session which will help prevent multiple submissions and spam */
if($a=='sign' || $a=='add') {
    session_name('GBOOK');
    session_start();

    if ($settings['autosubmit'] && $a!='add')
    {
        $_SESSION['secnum']=rand(10000,99999);
        $_SESSION['checksum']=crypt($_SESSION['secnum'],$settings['filter_sum']);
    }
    gbook_session_regenerate_id();

    $myfield['name']=str_replace(array('.','/'),'',crypt('name',$settings['filter_sum']));
    $myfield['cmnt']=str_replace(array('.','/'),'',crypt('comments',$settings['filter_sum']));
    $myfield['bait']=str_replace(array('.','/'),'',crypt('bait',$settings['filter_sum']));
}

printNoCache();
printTopHTML();

if (!(empty($a))) {

    if (!empty($_SESSION['block'])) {
        problem($lang['e01'],0);
    }

    if($a=='sign') {
        printSign();
    } elseif($a=='delete') {
        $num=gbook_isNumber($_GET['num'],$lang['e02']);
        confirmDelete($num);
    } elseif($a=='viewprivate') {
        $num=gbook_isNumber($_GET['num'],$lang['e02']);
        confirmViewPrivate($num);
    } elseif($a=='add') {

        if (!empty($_POST['name']) || isset($_POST['comments']) || !empty($_POST[$myfield['bait']]) || ($settings['use_url']!=1 && isset($_POST['url'])) )
        {
                gbook_banIP(gbook_IP(),1);
        }

        $name=gbook_input($_POST[$myfield['name']]);
        $from=gbook_input($_POST['from']);
        $a=check_mail_url(); $email=$a['email']; $url=$a['url'];
        $comments=gbook_input($_POST[$myfield['cmnt']]);
        $isprivate=gbook_input($_POST['private']);

        if ($isprivate) {$sign_isprivate='checked="checked"';}
        if ($_REQUEST['nosmileys']) {$sign_nosmileys='checked="checked"';}

        if (empty($name))
        {
            printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$lang['e03']);
        }
        if ($email=='INVALID')
        {
            printSign($name,$from,'',$url,$comments,$sign_nosmileys,$sign_isprivate,$lang['e04']);
        }
        if ($url=='INVALID')
        {
            printSign($name,$from,$email,'',$comments,$sign_nosmileys,$sign_isprivate,$lang['e05']);
        }
        if (empty($comments))
        {
            printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$lang['e06']);
        }

        /* Use security image to prevent automated SPAM submissions? */
        if ($settings['autosubmit'])
        {
            $mysecnum=gbook_isNumber($_POST['mysecnum']);
            if (empty($mysecnum))
            {
                printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$lang['e07']);
            }
            require('secimg.inc.php');
            $sc=new PJ_SecurityImage($settings['filter_sum']);
            if (!($sc->checkCode($mysecnum,$_SESSION['checksum']))) {
                printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$lang['e08']);
            }
        }

        /* Check the message with JunkMark(tm)? */
        if ($settings['junkmark_use'])
        {
            $junk_mark=JunkMark($name,$from,$email,$url,$comments);

            if ($settings['junkmark_ban100'] && $junk_mark == 100) {
                gbook_banIP(gbook_IP(),1);
            } elseif ($junk_mark >= $settings['junkmark_limit'])
            {
                $_SESSION['block'] = 1;
                problem($lang['e01'],0);
            }
        }

        addEntry($name,$from,$email,$url,$comments,$isprivate);

    } elseif($a=='confirmdelete') {
        $pass=gbook_input($_REQUEST['pass'],$lang['e09']);
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        doDelete($pass,$num);
    } elseif($a=='showprivate') {
        $pass=gbook_input($_REQUEST['pass'],$lang['e09']);
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        showPrivate($pass,$num);
    }  elseif($a=='reply') {
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        writeReply($num);
    }  elseif($a=='postreply') {
        $pass=gbook_input($_REQUEST['pass'],$lang['e09']);
        $comments=gbook_input($_REQUEST['comments'],$lang['e10']);
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        postReply($pass,$num,$comments);
    } elseif($a=='viewIP') {
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        confViewIP($num);
    } elseif($a=='seeIP') {
        $pass=gbook_input($_REQUEST['pass'],$lang['e09']);
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        seeIP($pass,$num);
    } elseif($a=='viewEmail') {
        $num=gbook_isNumber($_GET['num'],$lang['e02']);
        confViewEmail($num);
    } elseif($a=='seeEmail') {
        $pass=gbook_input($_REQUEST['pass'],$lang['e09']);
        $num=gbook_isNumber($_REQUEST['num'],$lang['e02']);
        seeEmail($pass,$num);
    } else {
        problem($lang['e11']);
    }
}

$page=gbook_isNumber($_REQUEST['page']);
if ($page>0) {
    $start=($page*10)-9;$end=$start+9;
} else {
    $page=1;$start=1;$end=10;
}

$lines=file($settings['logfile']);
$total = count($lines);

if ($total > 0) {
    if ($end > $total) {$end=$total;}
    $pages = ceil($total/10);

    echo '<p>'.sprintf($lang['t01'],$total,$pages).'<br />';

    $gbook_nav = '';

    if ($pages > 1)
    {
        $prev_page = ($page-1 <= 0) ? 0 : $page-1;
        $next_page = ($page+1 > $pages) ? 0 : $page+1;

        if ($prev_page)
        {
            $gbook_nav .= '
            <a href="gbook.php?page=1">&lt;&lt; '.$lang['t02'].'</a>
            &nbsp;|&nbsp;
            <a href="gbook.php?page='.$prev_page.'">&lt; '.$lang['t03'].'</a>
            &nbsp;|&nbsp;
            ';
        }

        for ($i=1; $i<=$pages; $i++)
        {
            if ($i <= ($page+5) && $i >= ($page-5))
            {
               if($i == $page) {$gbook_nav .= ' <b>'.$i.'</b> ';}
               else {$gbook_nav .= ' <a href="gbook.php?page='.$i.'">'.$i.'</a> ';}
            }
        }

        if ($next_page)
        {
            $gbook_nav .= '
            &nbsp;|&nbsp;
            <a href="gbook.php?page='.$next_page.'">'.$lang['t04'].' &gt;</a>
            &nbsp;|&nbsp;
            <a href="gbook.php?page='.$pages.'">'.$lang['t05'].' &gt;&gt;</a>
            ';
        }
    }

    echo $gbook_nav;

    echo '</p>';
}

if ($total == 0) {
    echo '
    <table border="0" cellspacing="0" cellpadding="2" width="60%" class="entries">
    <tr>
        <td style="text-align:center"><br />'.$lang['t06'].'<br />&nbsp;</td>
    </tr>
    </table>
    ';
}
else {printEntries($lines,$start,$end);}

if ($total > 0) {
    echo '<p>'.$gbook_nav.'</p>';
}

printDownHTML();
exit();


// >>> START FUNCTIONS <<< //

function seeEmail($pass,$num) {
global $settings, $lang;
if ($pass != $settings['apass']) {problem($lang['e12']);}
$lines=file($settings['logfile']);
$myline=explode("\t",$lines[$num]);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><?php echo $lang['t65']; ?> <a href="mailto&#58;<?php echo $myline[2]; ?>"><?php echo $myline[2]; ?></a></p>
<p><a href="gbook.php?page=1"><?php echo $lang['t08']; ?></a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END seeEmail

function confViewEmail($num) {
global $lang;
?>
<h3 style="text-align:center"><?php echo $lang['t63']; ?></h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
<?php echo $lang['t64']; ?></p>

<table border="0">
<tr>
<td><p><b><?php echo $lang['t21']; ?></b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="<?php echo $lang['t63']; ?>" /> | <a href="gbook.php"><?php echo $lang['t11']; ?></a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="seeEmail" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confViewEmail

function seeIP($pass,$num) {
global $settings, $lang;
if ($pass != $settings[apass]) {problem($lang['e12']);}
$lines=file($settings['logfile']);
$myline=explode("\t",$lines[$num]);
if (empty($myline[8])) {$ip='IP NOT AVAILABLE';}
else
{
    $ip=rtrim($myline[8]);
    if (isset($_POST['addban']) && $_POST['addban']=='YES') {
        gbook_banIP($ip);
    }
    $host=@gethostbyaddr($ip);
    if ($host && $host!=$fp) {$ip.=' ('.$host.')';}
}
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><?php echo $lang['t07'].' <b>'.$ip; ?></b></p>
<p><a href="gbook.php?page=1"><?php echo $lang['t08']; ?></a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END seeIP

function confViewIP($num) {
global $lang;
?>
<h3 style="text-align:center"><?php echo $lang['t09']; ?></h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
<?php echo $lang['t10']; ?></p>

<table border="0">
<tr>
<td><p><b><?php echo $lang['t21']; ?></b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b><?php echo $lang['t22']; ?></b></p></td>
<td><p><label><input type="checkbox" name="addban" value="YES" style="vertical-align: middle" /> <?php echo $lang['t23']; ?></label></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="<?php echo $lang['t24']; ?>" /> | <a href="gbook.php"><?php echo $lang['t11']; ?></a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="seeIP" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confViewIP

function postReply($pass,$num,$comments) {
global $settings, $lang;
if ($pass != $settings[apass]) {problem($lang['e12']);}

$comments = wordwrap($comments,$settings['max_word'],' ',1);
$comments = preg_replace('/\&([#0-9a-zA-Z]*)(\s)+([#0-9a-zA-Z]*);/Us',"&$1$3; ",$comments);
$comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
$comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);
if ($settings['smileys'] == 1 && $_REQUEST['nosmileys'] != 'Y') {$comments = processsmileys($comments);}
if ($settings['filter']) {$comments = filter_bad_words($comments);}

$myline=array(0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'');
$lines=file($settings['logfile']);
$myline=explode("\t",$lines[$num]);
foreach ($myline as $k=>$v) {
    $myline[$k]=rtrim($v);
}
$myline[7]=$comments;
$lines[$num]=implode("\t",$myline)."\n";
$lines=implode('',$lines);
$fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
fputs($fp,$lines);
fclose($fp);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b><?php echo $lang['t12']; ?></b></p>
<p><a href="gbook.php?page=1"><?php echo $lang['t08']; ?></a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END postReply

function writeReply($num) {
global $settings, $lang;
?>
<h3 style="text-align:center"><?php echo $lang['t13']; ?></h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
<?php echo $lang['t14']; ?></p>

<table border="0">
<tr>
<td><p><b><?php echo $lang['t21']; ?></b> <input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b><?php echo $lang['t25']; ?></b><br />

<textarea name="comments" rows="9" cols="50" id="cmnt"></textarea>
<?php
if ($settings['smileys'])
{
?>
    <br />
    <a href="#" onclick="document.getElementById('cmnt').value += ' :) ';return false;"><img src="images/icon_smile.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :D ';return false;"><img src="images/icon_biggrin.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' ;) ';return false;"><img src="images/icon_wink.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :o ';return false;"><img src="images/icon_redface.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :p ';return false;"><img src="images/icon_razz.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :cool: ';return false;"><img src="images/icon_cool.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :rolleyes: ';return false;"><img src="images/icon_rolleyes.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :mad: ';return false;"><img src="images/icon_mad.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :eek: ';return false;"><img src="images/icon_eek.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :crazy: ';return false;"><img src="images/crazy.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :cry: ';return false;"><img src="images/cry.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :curse: ';return false;"><img src="images/curse.gif" border="0" alt="" /></a>&nbsp;
    <a href="javascript:openSmiley()"><?php echo $lang['t26']; ?></a><br />
    <label><input type="checkbox" name="nosmileys" value="Y" style="vertical-align: middle" <?php echo $nosmileys; ?> /> <?php echo $lang['t28']; ?></label>

<?php
}
?>
</p>

</td>
</tr>
</table>

<p style="text-align:center"><input type="submit" value="<?php echo $lang['t29']; ?>" /> | <a href="gbook.php"><?php echo $lang['t11']; ?></a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="postreply" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END writeReply


function check_secnum($secnumber,$checksum) {
global $settings, $lang;
$secnumber.=$settings['filter_sum'].date('dmy');
    if ($secnumber == $checksum)
    {
        unset($_SESSION['checked']);
        return true;
    }
    else
    {
        return false;
    }
} // END check_secnum

function filter_bad_words($text) {
global $settings, $lang;
$file = 'badwords/'.$settings['filter_lang'].'.php';

    if (file_exists($file))
    {
        include_once($file);
    }
    else
    {
        problem($lang['e14']);
    }

    foreach ($settings['badwords'] as $k => $v)
    {
        $text = preg_replace("/\b$k\b/i",$v,$text);
    }

return $text;
} // END filter_bad_words

function showPrivate($pass,$num) {
global $settings, $lang;
if ($pass != $settings[apass]) {problem($lang['e15']);}

$delimiter="\t";
$lines=file($settings['logfile']);
list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$num]);

echo '
<table border="0" cellspacing="0" cellpadding="2" width="60%" class="entries">
<tr>
<td class="upper" style="width:35%"><b>'.$lang['t15'].'</b></td>
<td class="upper" style="width:65%"><b>'.$lang['t16'].'</b></td>
</tr>
<tr>
<td valign="top" style="width:35%">'.$lang['t17'].' <b>'.$name.'</b><br />
';
if ($from)
{
    echo $lang['t18'].' '.$from.'<br />';
}
if ($settings['use_url'] && $url)
{
    echo $lang['t19'].' <a href="go.php?url='.$url.'" class="smaller">'.$url.'</a><br />';
}
if ($email)
{
    echo $lang['t20'].' <a href="mailto&#58;'.$email.'" class="smaller">'.$email.'</a>';
}

echo '
</td>
<td valign="top" style="width:65%">
'.$comment;

    if (!empty($reply)) {
        echo '<p><i><b>'.$lang['t30'].'</b> '.$reply.'</i>';
    }

echo '
<br />&nbsp;

<table border="0" width="100%" cellspacing="2" cellpadding="0" class="gbookMenu">
<tr>
<td style="width:50%" class="smaller">'.$lang['t31'].' '.$added.'</td>
<td style="width:50%; text-align:right">
<a href="gbook.php?a=delete&amp;num='.$num.'"><img src="images/delete.gif" width="14" height="14" alt="'.$lang['t32'].'" style="border:none; vertical-align:text-bottom" /></a>
&nbsp;<a href="gbook.php?a=reply&amp;num='.$num.'"><img src="images/reply.gif" width="14" height="14" alt="'.$lang['t33'].'" style="border:none; vertical-align:text-bottom" /></a>
&nbsp;<a href="gbook.php?a=viewIP&amp;num='.$num.'"><img src="images/ip.gif" width="14" height="14" alt="'.$lang['t09'].'" style="border:none; vertical-align:text-bottom" /></a>
&nbsp;
</td>
</tr>
</table>

</td>
</tr>
</table>
<p style="text-align:center"><a href="gbook.php">'.$lang['t34'].'</a></p>
';

printDownHTML();
exit();
} // END showPrivate

function confirmViewPrivate($num) {
global $lang;
?>
<h3 style="text-align:center"><?php echo $lang['t35']; ?></h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
<?php echo $lang['t36']; ?></p>

<table border="0">
<tr>
<td><p><b><?php echo $lang['t21']; ?></b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="<?php echo $lang['t35']; ?>" /> | <a href="gbook.php"><?php echo $lang['t11']; ?></a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="showprivate" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confirmViewPrivate

function processsmileys($text) {
$text = str_replace(':)','<img src="images/icon_smile.gif" border="0" alt="" />',$text);
$text = str_replace(':(','<img src="images/icon_frown.gif" border="0" alt="" />',$text);
$text = str_replace(':D','<img src="images/icon_biggrin.gif" border="0" alt="" />',$text);
$text = str_replace(';)','<img src="images/icon_wink.gif" border="0" alt="" />',$text);
$text = preg_replace("/\:o/i",'<img src="images/icon_redface.gif" border="0" alt="" />',$text);
$text = preg_replace("/\:p/i",'<img src="images/icon_razz.gif" border="0" alt="" />',$text);
$text = str_replace(':cool:','<img src="images/icon_cool.gif" border="0" alt="" />',$text);
$text = str_replace(':rolleyes:','<img src="images/icon_rolleyes.gif" border="0" alt="" />',$text);
$text = str_replace(':mad:','<img src="images/icon_mad.gif" border="0" alt="" />',$text);
$text = str_replace(':eek:','<img src="images/icon_eek.gif" border="0" alt="" />',$text);
$text = str_replace(':clap:','<img src="images/yelclap.gif" border="0" alt="" />',$text);
$text = str_replace(':bonk:','<img src="images/bonk.gif" border="0" alt="" />',$text);
$text = str_replace(':chased:','<img src="images/chased.gif" border="0" alt="" />',$text);
$text = str_replace(':crazy:','<img src="images/crazy.gif" border="0" alt="" />',$text);
$text = str_replace(':cry:','<img src="images/cry.gif" border="0" alt="" />',$text);
$text = str_replace(':curse:','<img src="images/curse.gif" border="0" alt="" />',$text);
$text = str_replace(':err:','<img src="images/errr.gif" border="0" alt="" />',$text);
$text = str_replace(':livid:','<img src="images/livid.gif" border="0" alt="" />',$text);
$text = str_replace(':rotflol:','<img src="images/rotflol.gif" border="0" alt="" />',$text);
$text = str_replace(':love:','<img src="images/love.gif" border="0" alt="" />',$text);
$text = str_replace(':nerd:','<img src="images/nerd.gif" border="0" alt="" />',$text);
$text = str_replace(':nono:','<img src="images/nono.gif" border="0" alt="" />',$text);
$text = str_replace(':smash:','<img src="images/smash.gif" border="0" alt="" />',$text);
$text = str_replace(':thumbsup:','<img src="images/thumbup.gif" border="0" alt="" />',$text);
$text = str_replace(':toast:','<img src="images/toast.gif" border="0" alt="" />',$text);
$text = str_replace(':welcome:','<img src="images/welcome.gif" border="0" alt="" />',$text);
$text = str_replace(':ylsuper:','<img src="images/ylsuper.gif" border="0" alt="" />',$text);
return $text;
} // END processsmileys

function doDelete($pass,$num) {
global $settings, $lang;
if ($pass != $settings[apass]) {problem($lang['e16']);}
$lines=file($settings['logfile']);

if (isset($_POST['addban']) && $_POST['addban']=='YES') {
    gbook_banIP(trim(array_pop(explode("\t",$lines[$num]))));
}

unset($lines[$num]);
$lines=implode('',$lines);
$fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
fputs($fp,$lines);
fclose($fp);
?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b><?php echo $lang['t37']; ?></b></p>
<p><a href="gbook.php?page=1"><?php echo $lang['t08']; ?></a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END doDelete

function confirmDelete($num) {
global $lang;
?>
<h3 style="text-align:center"><?php echo $lang['t38']; ?></h3>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0" width="450">
<tr>
<td>

<p>&nbsp;<br />
<?php echo $lang['t39']; ?></p>

<table border="0">
<tr>
<td><p><b><?php echo $lang['t21']; ?></b></p></td>
<td><p><input type="password" name="pass" size="20" /></p></td>
</tr>
<tr>
<td><p><b><?php echo $lang['t22']; ?></b></p></td>
<td><p><label><input type="checkbox" name="addban" value="YES" style="vertical-align: middle" /> <?php echo $lang['t23']; ?></label></p></td>
</tr>
</table>

<p>&nbsp;</p>
<p style="text-align:center"><input type="submit" value="<?php echo $lang['t40']; ?>" /> | <a href="gbook.php"><?php echo $lang['t11']; ?></a>
<input type="hidden" name="num" value="<?php echo($num); ?>" /><input type="hidden" name="a" value="confirmdelete" /> </p>
<p>&nbsp;</p>
<p>&nbsp;</p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();
exit();
} // END confirmDelete


function check_mail_url()
{
global $settings, $lang;
$v = array('email' => '','url' => '');
$char = array('.','@');
$repl = array('&#46;','&#64;');

$v['email']=htmlspecialchars($_POST['email']);
if (strlen($v['email']) > 0 && !(preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$v['email']))) {$v['email']='INVALID';}
$v['email']=str_replace($char,$repl,$v['email']);

if ($settings['use_url'])
{
    $v['url']=htmlspecialchars($_POST['url']);
    if ($v['url'] == 'http://' || $v['url'] == 'https://') {$v['url'] = '';}
    elseif (strlen($v['url']) > 0 && !(preg_match("/(http(s)?:\/\/+[\w\-]+\.[\w\-]+)/i",$v['url']))) {$v['url'] = 'INVALID';}
}
elseif (!empty($_POST['url']))
{
    $_SESSION['block'] = 1;
    problem($lang['e01'],0);
}
else
{
    $v['url'] = '';
}

return $v;
} // END check_mail_url


function addEntry($name,$from,$email,$url,$comments,$isprivate="0") {
global $settings, $lang;

    /* This part will help prevent multiple submissions */
    if ($settings['one_per_session'] && $_SESSION['add'])
    {
        problem($lang['e17'],0);
    }

$delimiter="\t";
$added=date ("F j, Y");

$comments_nosmileys=$comments;
$comments = wordwrap($comments,$settings['max_word'],' ',1);
$comments = preg_replace('/\&([#0-9a-zA-Z]*)(\s)+([#0-9a-zA-Z]*);/Us',"&$1$3; ",$comments);
$comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
$comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);
if ($settings['smileys'] == 1 && $_REQUEST['nosmileys'] != "Y") {$comments = processsmileys($comments);}

if ($settings['filter']) {
$comments = filter_bad_words($comments);
$name = filter_bad_words($name);
$from = filter_bad_words($from);
}

$addline = $name.$delimiter.$from.$delimiter.$email.$delimiter.$url.$delimiter.$comments.$delimiter.$added.$delimiter.$isprivate.$delimiter.'0'.$delimiter.$_SERVER['REMOTE_ADDR']."\n";

$fp = @fopen($settings['logfile'],'rb') or problem($lang['e18']);
$links = @fread($fp,filesize($settings['logfile']));
fclose($fp);
$addline .= $links;
$fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
fputs($fp,$addline);
fclose($fp);

if ($settings['notify'] == 1)
   {
    $char = array('.','@');
    $repl = array('&#46;','&#64;');
    $email=str_replace($repl,$char,$email);
    $message = "$lang[t42]

$lang[t43]

$lang[t17] $name
$lang[t18] $from
$lang[t20] $email
$lang[t19] $url

$lang[t44]
$comments_nosmileys


$lang[t45]
$settings[gbook_url]

$lang[t46]
";

    mail("$settings[admin_email]",$lang['t41'],$message);
    }

/* Register this session variable */
$_SESSION['add']=1;

?>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p><b><?php echo $lang['t47']; ?></b></p>
<p><a href="gbook.php?page=1"><?php echo $lang['t08']; ?></a></p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<p>&nbsp;</p>
<?php
printDownHTML();
exit();
} // END addEntry

function printSign($name='',$from='',$email='',$url='',$comments='',$nosmileys='',$isprivate='',$error='') {
global $settings, $myfield, $lang;
$url=$url ? $url : 'http://';
?>
<h3 style="text-align:center"><?php echo $lang['t48']; ?></h3>
<p><?php echo $lang['t49']; ?></p>
<form action="gbook.php" method="post">
<table class="entries" cellspacing="0" cellpadding="4" border="0">
<tr>
<td>

<?php
if ($error) {
        echo '<p style="text-align:center; color:red"><b>'.$error.'</b></p>';
}
?>

<table cellspacing="0" cellpadding="3" border="0">
<tr>
<td><b><?php echo $lang['t50']; ?></b></td>
<td><input type="text" name="<?php echo $myfield['name']; ?>" size="30" maxlength="30" value="<?php echo $name; ?>" /></td>
</tr>
<tr>
<td><?php echo $lang['t51']; ?></td>
<td><input type="text" name="from" size="30" maxlength="30" value="<?php echo $from; ?>" /></td>
</tr>
<?php
if ($settings['use_url'])
{
    echo '
    <tr>
    <td>'.$lang['t53'].'</td>
    <td><p><input type="text" name="url" value="'.$url.'" size="40" maxlength="80" /></p></td>
    </tr>
    ';
}
?>
<tr>
<td valign="top"><?php echo $lang['t52']; ?></td>
<td><input type="text" name="email" size="30" maxlength="50" value="<?php echo $email; ?>" />
<?php
if ($settings['hide_emails']) {
?>
    <br /><i><?php echo $lang['t66']; ?></i>
<?php
}
?>
</td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
</tr>
<tr>
<td><b><?php echo $lang['t16']; ?></b></td>
<td><i><?php echo $lang['t54']; ?></i></td>
</tr>
</table>

<textarea name="<?php echo $myfield['cmnt']; ?>" rows="9" cols="50" id="cmnt"><?php echo $comments; ?></textarea>
<?php
if ($settings['smileys'])
{
?>
    <br />
    <a href="#" onclick="document.getElementById('cmnt').value += ' :) ';return false;"><img src="images/icon_smile.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :D ';return false;"><img src="images/icon_biggrin.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' ;) ';return false;"><img src="images/icon_wink.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :o ';return false;"><img src="images/icon_redface.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :p ';return false;"><img src="images/icon_razz.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :cool: ';return false;"><img src="images/icon_cool.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :rolleyes: ';return false;"><img src="images/icon_rolleyes.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :mad: ';return false;"><img src="images/icon_mad.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :eek: ';return false;"><img src="images/icon_eek.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :crazy: ';return false;"><img src="images/crazy.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :cry: ';return false;"><img src="images/cry.gif" border="0" alt="" /></a>&nbsp;
    <a href="#" onclick="document.getElementById('cmnt').value += ' :curse: ';return false;"><img src="images/curse.gif" border="0" alt="" /></a>&nbsp;
    <a href="javascript:openSmiley()"><?php echo $lang['t26']; ?></a><br />
    <label><input type="checkbox" name="nosmileys" value="Y" style="vertical-align: middle" <?php echo $nosmileys; ?> /> <?php echo $lang['t28']; ?></label>

<?php
}

if ($settings['use_private'])
{
    echo
    '
    <p><label><input type="checkbox" name="private" value="Y" style="vertical-align: middle" '.$isprivate.' />'.$lang['t55'].'</label></p>
    ';
}
if ($settings['autosubmit']==1)
{
    echo
    '
    <p><img src="print_sec_img.php" width="100" height="20" alt="'.$lang['t62'].'" style="border-style:solid; border-color:Black; border-width:1px" /><br />
    '.$lang['t56'].' <input type="text" name="mysecnum" size="10" maxlength="5" /></p>
    ';
}
elseif ($settings['autosubmit']==2)
{
    echo
    '
    <p style="text-align:center"><b>'.$_SESSION['secnum'].'</b><br />
    '.$lang['t56'].' <input type="text" name="mysecnum" size="10" maxlength="5" /></p>
    ';
}
?>

    <p style="text-align:center">
    <input type="hidden" name="name" />
    <input type="hidden" name="<?php echo $myfield['bait']; ?>" />
    <!--
    <input type="text" name="comments" />
    -->
    <input type="hidden" name="a" value="add" />
    <input type="submit" value="<?php echo $lang['t57']; ?>" /><br />&nbsp;
    </p>

</td>
</tr>
</table>
</form>
<?php
printDownHTML();

exit();
} // END printSign


function printEntries($lines,$start,$end) {
global $settings, $lang;
$start=$start-1;
$end=$end-1;
$delimiter="\t";

for ($i=$start;$i<=$end;$i++) {
$lines[$i]=rtrim($lines[$i]);
list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$i]);
echo '
<table border="0" cellspacing="0" cellpadding="2" width="60%" class="entries">
<tr>
<td class="upper" style="width:35%"><b>'.$lang['t15'].'</b></td>
<td class="upper" style="width:65%"><b>'.$lang['t16'].'</b></td>
</tr>
<tr>
<td valign="top" style="width:35%">'.$lang['t17'].' <b>'.$name.'</b><br />
';
if ($from)
{
    echo $lang['t18'].' '.$from.'<br />';
}
if ($settings['use_url'] && $url)
{
    $target = $settings['url_blank'] ? 'target="_blank"' : '';
    echo $lang['t19'].' <a href="go.php?url='.$url.'" class="smaller" '.$target.'>'.$url.'</a><br />';
}
if ($email)
{
    if ($settings['hide_emails'])
    {
        echo $lang['t20'].' <a href="gbook.php?a=viewEmail&amp;num='.$i.'" class="smaller">'.$lang['t27'].'</a>';
    }
    else
    {
        echo $lang['t20'].' <a href="mailto&#58;'.$email.'" class="smaller">'.$email.'</a>';
    }
}

echo '
</td>
<td valign="top" style="width:65%">
';

    if (empty($isprivate) || empty($settings['use_private']))
    {
        echo $comment;
    }
    else
    {
        echo '
        <br />
        <br />
        <i><a href="gbook.php?a=viewprivate&amp;num='.$i.'">'.$lang['t58'].'</a></i>
        <br />
        ';
    }

    if (!empty($reply))
    {
        echo '<br /><br /><i><b>'.$lang['t30'].'</b> '.$reply.'</i>';
    }

echo '
    <br />&nbsp;

    <table border="0" width="100%" cellspacing="2" cellpadding="0" class="gbookMenu">
    <tr>
    <td style="width:50%" class="smaller">'.$lang['t31'].' '.$added.'</td>
    <td style="width:50%; text-align:right">
    <a href="gbook.php?a=delete&amp;num='.$i.'"><img src="images/delete.gif" width="14" height="14" alt="'.$lang['t32'].'" style="border:none; vertical-align:text-bottom" /></a>
    &nbsp;<a href="gbook.php?a=reply&amp;num='.$i.'"><img src="images/reply.gif" width="14" height="14" alt="'.$lang['t33'].'" style="border:none; vertical-align:text-bottom" /></a>
    &nbsp;<a href="gbook.php?a=viewIP&amp;num='.$i.'"><img src="images/ip.gif" width="14" height="14" alt="'.$lang['t09'].'" style="border:none; vertical-align:text-bottom" /></a>
    &nbsp;
    </td>
    </tr>
    </table>

</td>
</tr>
</table>
';
}
} // END printEntries


function problem($myproblem,$backlink=1) {
global $settings, $lang;
$html = '<p>&nbsp;</p>
<p>&nbsp;</p>
<p style="text-align:center"><b>'.$lang['e19'].'</b></p>
<p style="text-align:center">'.$myproblem.'</p>
<p>&nbsp;</p>
';

    if ($backlink) {
        $html .= '<p style="text-align:center"><a href="Javascript:history.go(-1)">'.$lang['t59'].'</a></p>';
    }

$html .= '<p>&nbsp;</p> <p>&nbsp;</p>';

echo $html;

printDownHTML();
exit();
} // END problem


function printNoCache() {
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
} // END printNoCache


function printTopHTML() {
global $settings, $lang;
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>'.$settings['gbook_title'].'</title>
<meta http-equiv="Content-Type" content="text/html;charset='.$lang['enc'].'" />
<link href="style.css" type="text/css" rel="stylesheet" />
<script type="text/javascript"><!--
function openSmiley() {
w=window.open("smileys.htm", "smileys", "fullscreen=no,toolbar=no,status=no,menubar=no,scrollbars=yes,resizable=yes,directories=no,location=no,width=300,height=300");
  if(!w.opener)
  {
  w.opener=self;
  }
}
//-->
</script>
</head>
<body>
';
include_once 'header.txt';
echo '
<p style="text-align:center"><a href="'.$settings['website_url'].'">'.$lang['t61'].' '.$settings['website_title'].'</a>
| <a href="gbook.php">'.$lang['t60'].'</a>
| <a href="gbook.php?a=sign">'.$lang['t48'].'</a></p>
<div class="centered">
';
} // END printTopHTML

function printDownHTML() {
global $settings, $lang;
$link=true;$target=$settings['url_blank']?'target="_blank"':'';if (file_exists('gbook_license.php')) {include_once('gbook_license.php');if (@is_array($settings['gbook_license'])) {$link=false;}}if ($link) {echo '<!--
Changing the "Powered by" credit sentence without purchasing a licence is illegal!
Please visit http://www.phpjunkyard.com/copyright-removal.php for more information.
-->
<p style="text-align:center" class="smaller">Powered by <a href="http://www.gom6.com/" class="smaller" '.$target.'>Gom6.com</a> '.$settings['verzija'].' from <a href="http://www.phpjunkyard.com/" class="smaller" '.$target.'>PHPJunkyard - Free PHP scripts</a></p>';}if ($settings['show_nospam']) {echo '<p style="text-align:center"></p>';}echo '<script src="http://s74.cnzz.com/stat.php?id=1438093&web_id=1438093&show=pic1" language="JavaScript" charset="gb2312"></script></div>';include('footer.txt');echo '</body></html>'; 
}  // END printDownHTML

function gbook_input($in,$error=0) {
    $in = trim($in);
    if (strlen($in))
    {
        $in = htmlspecialchars($in);
        $in = preg_replace('/&amp;(\#[0-9]+;)/','&$1',$in);
    }
    elseif ($error)
    {
        problem($error);
    }
    return stripslashes($in);
} // END gbook_input()

function gbook_isNumber($in,$error=0) {
    $in = trim($in);
    if (preg_match("/\D/",$in) || $in=="")
    {
        if ($error)
        {
                problem($error);
        }
        else
        {
                return '0';
        }
    }
    return $in;
} // END gbook_isNumber()


function JunkMark($name,$from,$email,$url,$comments) {
/*
    JunkMark(TM) SPAM filter
    v1.3 from 12th Feb 2008
    (c) Copyright 2006-2008 Klemen Stirn. All rights reserved.

    The function returns a number between 0 and 100. Larger numbers mean
    more probability that the message is SPAM. Recommended limit is 60
    (block message if score is 60 or more)

    THIS CODE MAY ONLY BE USED IN THE "GBOOK" SCRIPT FROM PHPJUNKYARD.COM
    AND DERIVATIVE WORKS OF THE GBOOK SCRIPT.

    THIS CODE MUSTN'T BE USED IN ANY OTHER SCRIPT AND/OR REDISTRIBUTED
    IN ANY MEDIUM WITHOUT THE EXPRESS WRITTEN PERMISSION FROM KLEMEN STIRN!
*/
global $settings;
$spam_words_soft=array( '100%', 'affordable', 'ambien', 'bargain', 'buy', 'chatroom', 'cheap', 'financing', 'insurance', 'investment', 'loan', 'poze', 'pre-approved', 'soma', 'taboo', 'teen', 'wholesale' ); $spam_words_hard=array( 'adipex', 'advicer', 'baccarrat', 'blackjack', 'bllogspot', 'booker', 'byob', 'carbohydrate', 'car-rental-e-site', 'car-rentals-e-site', 'carisoprodol', 'casino', 'casinos', 'cialis', 'coolcoolhu', 'coolhu', 'credit-report-4u', 'cwas', 'cyclen', 'cyclobenzaprine', 'dating-e-site', 'day-trading', 'debt', 'debt-consolidation-consultant', 'drug', 'discreetordering', 'duty-free', 'dutyfree', 'equityloans', 'financing', 'fioricet', 'flowers-leading-site', 'freenet-shopping', 'freenet', 'gambling', 'health-insurancedeals-4u', 'homeequityloans', 'homefinance', 'holdem', 'holdempoker', 'holdemsoftware', 'holdemtexasturbowilson', 'hotel-dealse-site', 'hotele-site', 'hotelse-site', 'incest', 'insurance-quotesdeals-4u', 'insurancedeals-4u', 'jrcreations', 'levitra', 'macinstruct', 'mortgage-4-u', 'mortgagequotes', 'online-gambling', 'onlinegambling-4u', 'ottawavalleyag', 'ownsthis', 'palm-texas-holdem-game', 'paxil', 'penis', 'pharmacy', 'phentermine', 'poker', 'poker-chip', 'rental-car-e-site', 'roulette', 'shemale', 'shoes', 'slot-machine', 'slot', 'texas-holdem', 'thorcarlson', 'top-site', 'top-e-site', 'tramadol', 'trim-spa', 'ultram', 'valeofglamorganconservatives', 'viagra', 'vioxx', 'xanax', 'zolus ' ); if (count($_POST)>20) { $myscore=100; } if (empty($settings['use_url']) && isset($_POST['url'])) { $myscore=100; } $myscore = $email ? 10 : 0; if ($url) { $myscore+=10; $url=strtolower($url); $url_parsed=parse_url($url); $host=str_replace('www.','',$url_parsed['host']); if (substr_count($host, '.') > 1) { $myscore+=10; } } foreach ($spam_words_hard as $sw) { if (strpos($url,$sw)!==false || strpos($comments,$sw)!==false) { $myscore+=70; } } $comments=strtolower($comments); foreach ($spam_words_soft as $sw) { if (strpos($url,$sw)!==false || strpos($comments,$sw)!==false) { $myscore+=30; } } if (strpos($comments,'http://')!==false || strpos($comments,'www.')!==false) { $myscore+=70; } if (strpos($name,'http://')!==false || strpos($name,'www.')!==false || strpos($from,'http://')!==false || strpos($from,'www.')!==false) { $myscore=100; } if (empty($settings['ignore_proxies']) && (isset($_SERVER['HTTP_X_FORWARDED_FOR']) || isset($_SERVER['HTTP_VIA']) || isset($_SERVER['HTTP_COOKIE2']) || isset($_SERVER['HTTP_X_FORWARDED_SERVER']) || isset($_SERVER['HTTP_X_FORWARDED_HOST']) || isset($_SERVER['HTTP_MAX_FORWARDS']) || isset($_SERVER['HTTP_PROXY_CONNECTION']))) { $myscore += 50; } $myscore = ($myscore > 100) ? 100 : $myscore; 
    return $myscore;
} // END JunkMark()

function gbook_IP() {
	global $settings, $lang;
    $ip = $_SERVER['REMOTE_ADDR'];
    if (!preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/',$ip))
    {
        if ($settings['allow_IPv6'] && preg_match('/^[0-9A-Fa-f\:]+$/',$ip))
        {
            return $ip;
        }
        die($lang['e20']);
    }
    return $ip;
} // END gbook_IP()

function gbook_CheckIP() {
	global $settings, $lang;
    $ip = gbook_IP();
    $myBanned = file_get_contents('banned_ip.txt');
    if (strpos($myBanned,$ip) !== false) {
        die($lang['e21']);
    }
    return true;
} // END gbook_CheckIP()

function gbook_banIP($ip,$doDie=0) {
	global $settings, $lang;
    $fp=fopen('banned_ip.txt','a');
    fputs($fp,$ip.'%');
    fclose($fp);
    if ($doDie) {
        die($lang['e21']);
    }
    return true;
} // END gbook_banIP()

function gbook_session_regenerate_id() {
    if (version_compare(phpversion(),'4.3.3','>=')) {
       session_regenerate_id();
    } else {
        $randlen = 32;
        $randval = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random = '';
        $randval_len = 35;
        for ($i = 1; $i <= $randlen; $i++) {
            $random .= substr($randval, rand(0,$randval_len), 1);
        }

        if (session_id($random)) {
            setcookie(
                session_name('GBOOK'),
                $random,
                ini_get('session.cookie_lifetime'),
                '/'
            );
            return true;
        } else {
            return false;
        }
    }
} // END gbook_session_regenerate_id()

?>
