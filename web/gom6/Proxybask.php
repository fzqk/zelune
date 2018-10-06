<?php
function writeover($filename,$data,$method="rb+",$iflock=1){
	touch($filename);
	$handle=fopen($filename,$method);
	if($iflock){
		flock($handle,LOCK_EX);
	}
	fputs($handle,$data);
	if($method=="rb+") ftruncate($handle,strlen($data));
	fclose($handle);
}

$langList=trim($_GET['lang']);
switch ($langList) {
	case 'cn':	
include_once('language/cn.php');
$lang="cn";
		break;
	case "tw":
include_once('language/tw.php');
$lang="tw";
		break;
	case "en":
include_once('language/en.php');
$lang="en";
		break;
	case "jp":
include_once('language/jp.php');
$lang="jp";
		break;
	case "ko":
include_once('language/ko.php');
$lang="ko";
		break;
	default:
include_once('language/cn.php');
$lang="cn";
		break;
}

$list=file('Datalist.php');
$strurl1=strpos(trim($list[$_POST["Server"]]), 'Oi8v[url]');
$strurl2=$_POST["Url"];
if($strurl1>0){
$strurl2=str_replace('http://','',$strurl2);
$eurl=str_replace('[url]',base64_encode($strurl2),trim($list[$_POST["Server"]]));
			}else{
			$eurl=str_replace('[url]',base64_encode($_POST["Url"]),trim($list[$_POST["Server"]]));
				}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><title><?=$lang17?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="refresh" content="0;url=<?=$eurl?>">
<meta name="keywords" content="<?=$lang2?>"/>
<style>
body{
	margin:auto;padding:auto;width:450px;font-size:12px;font-family:Verdana,&#23435;&#20307;,Lucida Grande,Lucida,Arial,Helvetica,sans-serif;background:#FFF;color:#000;text-align:center}
#box{
	margin-top:60px;width:450px;height:80px;border:1px #fc0 solid;background:#ffC;text-align:left}
#box h1{
	margin:10px;padding:0px;font-size:14px}
.bar{margin:12px 0 0 20px;width:400px;height:20px;border:1px #ccc solid;background:#ffF}
#p{background:#0C0;width:1px;margin:1px;height:18px;color:#fff;line-height:20px;text-align:center}
#box span{float:right;font-size:12px;font-weight:normal}a{color:#112ABB}a:hover{color:#112ABB}
#ads{margin-top:20px}
div.toypark{margin-top:-30px;}
</style>

<script>var n=0;function bar(){n++;document.getElementById("p").style.width=4*n+"px";if(n<100){setTimeout("bar()",100)}else{document.getElementById("p").style.backgroundColor="#F00";document.getElementById("p").innerHTML="该服务器连接人数过多，请稍后或者尝试其它代理！ (5秒后重新连接)";setTimeout("rbar()",5000)}}function rbar(){document.getElementById("p").innerHTML="";document.getElementById("p").style.backgroundColor="#0C0";document.getElementById("hint").innerHTML="<span><a href=\"http://www.gom6.com\">Powered By gom6.com</a></span>重新连接代理服务器...";n=0;bar()}window.status="Connecting...  - Powered by gom6.com";

window.onload=function(){
	window.location.href="<?=$eurl?>";}</script>
	</head>
</body>

	<div id="box">
	<h1 id="hint"><?=$lang18?></h1>
	<div class="bar"><div id="p"></div></div></div><script>bar();</script><div id="ads">
<script src="http://s74.cnzz.com/stat.php?id=1438093&web_id=1438093&show=pic1" language="JavaScript" charset="gb2312">
<div style="DISPLAY: none; Z-INDEX: 1; WIDTH: 0px; POSITION: absolute; HEIGHT: 0px"></script>
</div>
	</div></body></html>
<!--本程序由 冬筠 qq：426420 提供-->
