<?php
/* >>> SETUP YOUR GUESTBOOK <<< */
/* Detailed information found in the readme file */
/* File version: 1.6 $ Timestamp: 14th Feb 2008 18:37 */

/* Password for admin area */
$settings['apass']='admin';

/* Website title */
$settings['website_title']="Gom6 Web代理";

/* Website URL */
$settings['website_url']='http://proxy.gom6.com/';

/* Guestbook title */
$settings['gbook_title']="在线代理 网页代理 在线WEB代理 在线网页代理,我们要争做最好、最人性化、速度最快的在线代理聚合站";

/* Name of the file where guestbook entries will be stored */
$settings['logfile']='gom6ies.txt';

/* Use "Your website" field? 1 = YES, 0 = NO */
$settings['use_url']=0;

/* Open URLs in a new window? 1 = YES, 0 = NO */
$settings['url_blank']=0;

/* Allow private posts (readable only by admin)? 1 = YES, 0 = NO */
$settings['use_private']=1;

/* Hide e-mail addresses? 1 = YES, 0 = NO */
$settings['hide_emails']=1;

/* Allow smileys? 1 = YES, 0 = NO */
$settings['smileys']=1;

/* Send you an e-mail when a new entry is added? 1 = YES, 0 = NO */
$settings['notify']=0;

/* Your e-mail. Only required if $settings['notify'] is set to 1 */
$settings['admin_email']='zhluosss@163.com';

/* URL of the gbook.php file. Only required if $settings['notify'] is set to 1 */
$settings['gbook_url']='http://proxy.gom6.com/ms/gbook.php';

/* Filter bad words? 1 = YES, 0 = NO */
$settings['filter']=1;

/* Filter language. Please refer to readme for info on how to add more bad words to the list! */
$settings['filter_lang']='en';

/* Prevent automated submissions (recommended YES)? 0 = NO, 1 = YES, GRAPHICAL, 2 = YES, TEXT */
$settings['autosubmit']=1;

/* Checksum - just type some digits or chars. Used to help prevent SPAM */
$settings['filter_sum']='k39dw28rh3';

/* Use JunkMark(tm) SPAM filter (recommended YES)? 1 = YES, 0 = NO */
$settings['junkmark_use']=1;

/* JunkMark(tm) score limit after which messages are marked as SPAM */
$settings['junkmark_limit']=60;

/* Ban IP address if JunkMark(tm) score is 100 (100% SPAM)? 1 = YES, 0 = NO */
$settings['junkmark_ban100']=1;

/* Ignore proxy servers from JunkMark check? 1 = YES, 0 = NO */
$settings['ignore_proxies']=0;

/* Show "NO GUESTBOOK SPAM" banner? 1 = YES, 0 = NO */
$settings['show_nospam']=1;

/* Prevent multiple submissions in the same session? 1 = YES, 0 = NO */
$settings['one_per_session']=1;

/* Maximum chars word length */
$settings['max_word']=75;

/* Language file */
$settings['language']='language.inc.php';

/* Allow IPv6 format? 1 = YES, 0 = NO */
$settings['allow_IPv6']=0;


/* DO NOT EDIT BELOW */
if (!defined('IN_SCRIPT')) {die('Invalid attempt!');}
ini_set('display_errors', 0);
ini_set('log_errors', 1);
?>
