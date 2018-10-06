<?php
$lang = $_GET['lang'];
$lang_dir = 'language/';
$lang = empty($_GET['lang'])?'':$_GET['lang'];
$langs = array('cn', 'tw', 'en', 'jp', 'ko');
if(empty($_GET['lang']) || !in_array($_GET['lang'], $langs)) {
	if(strpos($_SERVER["HTTP_ACCEPT_LANGUAGE"],'zh')>-1){
		$lang = 'cn';
	}else{
		$lang = 'en';
	}
} else {
	$lang = $_GET['lang'];
}
include_once($lang_dir.$lang.'.php');

$filename = 'Datalist.php';

function getIP ()
{
global $_SERVER;
if (getenv('HTTP_CLIENT_IP')) {
$ip = getenv('HTTP_CLIENT_IP');
} 
else if (getenv('HTTP_X_FORWARDED_FOR')) {
$ip = getenv('HTTP_X_FORWARDED_FOR');
} 
else if (getenv('REMOTE_ADDR')) {
$ip = getenv('REMOTE_ADDR');
} 
else {
$ip = $_SERVER['REMOTE_ADDR'];
}
return $ip;
}
$ip=getIP();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"

"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="utf-8" />
	<meta content="all" name="robots" />
	<link rel="shortcut icon" href="favicon.ico"/>
	<meta name="keywords" content="<?=$lang2?>"/>
	<meta name="description" content="<?=$lang3?>"/>
	<meta name="verify-v1" content="wiAivak7DVkVtQTqOrVRe9wZsqZReDb1iTgrO0qSSIk=" />
	<title><?=$lang1?></title>

<style>
body{
		font-size: 12px;
		font-family: Verdana,Arial,sans-serif;
		background: #FFF;
		color: #000;
		margin: auto;
		padding: auto;
                
	}
        .ipbox{
		margin:auto;height:24px;line-height:24px;
		padding:0 8px 0 8px;
	}
	.ipbox span{float:right;}
	.ipbox span strong{color:red;}
	.ipbox span b{color:green;}
	.ipbox a{margin-right:5px;}

	.headbox{
		margin:auto;height:24px;line-height:24px;
		border-bottom:1px #eee solid;
		padding:0 8px 0 8px;
	}
	.headbox span{float:right;}
	.headbox span strong{color:red;}
	.headbox span b{color:green;}
	.headbox a{margin-right:5px;}
	.logobox{margin:auto;height:73px;padding-top:27px;text-align:center;}
	.menubox{margin:auto;height:30px;line-height:20px;text-align:center;font-size:8px;color:#ccc;}
	.menubox a{font-size:12px;color:#06c;}

	.postbox form{margin:0;padding:0;}
	input.in{padding:1px;border:1px solid #7b9ebe;font-size:14px;background: no-repeat #ffc;;}
	select{border:1px solid #7b9ebe;background:#ffc;} 
    .count{margin-top:10px;text-align:center;}
	.count a{color:#ccc;}
	div.tools 
       {
      		 position: absolute;
       		 top: 25px;
        	 left: 6px;
		 height:350px;
		 width:120px;
       		 z-index: 2;
		 background:#ffc;
		 BORDER: 1px solid green; 
        }
		div.alldiv
       {	 
		 background:#ffc;
		 width:50%;
		 BORDER: 1px solid green; 
		 margin-left:25%; margin-right:25%; 
		 padding:5px 5px 5px 5px;
        }
        .x{
  		    margin:4px 0 4px 6px;
			line-height:20px;
			 }
			 .tools span{float:right;
			 margin:3px 3px 0 0;
			 }
		div.toypark{margin-top:-30px;
            }


</style>
<script type="text/javascript" src="script/main.js"></script> 
<script>
</script>
</head>
<body>
<div class="headbox">
	<span>
        <strong>Language:</strong>&nbsp;
	<a href="?lang=cn" title="Click here set the interface of the site to Chinese.">&#31616;&#20307;&#20013;&#25991;</a>
	<a href="?lang=tw" title="Click here set the interface of the site to Chinese/traditional characters.">&#32321;&#39636;&#20013;&#25991;</a>
	<a href="?lang=en" title="Click here set the interface of the site to English.">English</a>
	<a href="?lang=jp" title="Click here set the interface of the site to Japanese.">&#26085;&#26412;&#35486;</a>
	<a href="?lang=ko" title="Click here set the interface of the site to Korean.">&#54620;&#44397;&#50612;</a>
        </span>

        
	<a href="#" onclick="showc(1)"><?=$lang6?></a>
    <a href="#" onclick="showc(2)">HELP</a>	
	<a href="ms" target="_blank"><?=$lang10?></a>

</div>
<div class="ipbox">
<span>
<?=$lang11?>:&nbsp;<strong><?=$ip?></strong> 
<!--  <?php if($lang == "cn"){echo  '<script language="javascript" src="ip.php?ip='.$ip.'"></script>' ;}?>
 --></span>
</div>
<div class="logobox">
	<a href="/"><img src="images/logo-<?=$lang?>.gif" alt="web online proxy" border="0"></a>
</div>
<br/>
<div class="count">
<div class="postbox" id="c0">
<form method="post" id="fp" action="Proxy.php" target="_blank">
		<input id="Url" name="Url" size=55 class="in" type="text" value="<?=$lang28?>" onfocus="if(this.value == 'http://'){this.value='http://'}else{this.value='http://'}" onmouseover="this.focus()" onblur="if(this.value == 'http://'){this.value='<?=$lang28?>'}" autocomplete="on" />
		      
		        <select id="Server" name="Server" ONCHANGE="">
			<option value='1'><?=$lang21?> 01,<?=$lang22?></option>
			<option value='2'><?=$lang21?> 02,<?=$lang22?></option>
			<option value='3'><?=$lang21?> 03,<?=$lang22?></option>
			<option value='4'><?=$lang21?> 04,<?=$lang22?></option>
			<option value='5'><?=$lang21?> 05,<?=$lang22?></option>
			<option value='6'><?=$lang21?> 06,<?=$lang22?></option>
			<option value='7'><?=$lang21?> 07,<?=$lang22?></option>
			<option value='8'><?=$lang21?> 08,<?=$lang22?></option>
			<option value='9'><?=$lang21?> 09,<?=$lang22?></option>
			<option value='10'><?=$lang21?> 10,<?=$lang22?></option>
			<option value='11'><?=$lang21?> 11,<?=$lang22?></option>
			<option value='12'><?=$lang21?> 12,<?=$lang22?></option>
			<option value='13'><?=$lang21?> 13,<?=$lang22?></option>
			<option value='14'><?=$lang21?> 14,<?=$lang22?></option>
			<option value='15'><?=$lang21?> 15,<?=$lang22?></option>
			<option value='16'><?=$lang21?> 16,<?=$lang22?></option>
			<option value='17'><?=$lang21?> 17,<?=$lang22?></option>
			<option value='18'><?=$lang21?> 18,<?=$lang22?></option>
			<option value='19'><?=$lang21?> 19,<?=$lang22?></option>
			<option value='20'><?=$lang21?> 20,<?=$lang22?></option>
		        </select><br /><iframe id="js" src="" frameborder=0 width=0px height=0px
 scrolling="no" MARGINWIDTH=0 MARGINWIDTH=0></iframe>
		<input type="submit" name="Submit"  onclick="proxyurl();" value="<?=$lang9?>"/>
                <input name="btnG" type="submit" onclick="google();return false;" value="<?=$lang23?>"/>
	</form><script>myload();</script>

</div>
</div>
<br>
<div class="tools" id="c1" style="cursor:pointer">
<script>closec(1);</script>
<span><a href="#" onclick="closec(1)"><img src="images/close.gif"  border="0"></a></span>
<div class="x">
<a href="<?=$lang26?>" target="_blank"><?=$lang25?></a><br />
<a href="cssedit" target="_blank"><?=$lang24?></a><br />
<a href="http://www.cmd5.com/" target="_blank"><?=$lang27?></a><br />
</div>
</div>

<div class="alldiv" id="c2" height="50px" style="cursor:pointer">
<script>closec(2);</script>
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$lang29?>
</p>
</div>


<div class="alldiv" id="c3" style="cursor:pointer" onclick="closec(3);"  title="单击关闭此内容框">
<span>
<a href="http://www.fastpig.cn/" target="_blank">快猪高速代理</a>
</span>
</div>
<br><br>
<div class="menubox">
	<a href="#" onClick="this.style.behavior='url(#default#homepage)';this.setHomePage('http://proxy.gom6.com/');return(false);" style="behavior: url

(#default#homepage)"><?=$lang4?></a> &nbsp;&nbsp;|&nbsp;&nbsp;
	<a href="#" onClick="javascript:window.external.AddFavorite('http://proxy.gom6.com/', 'proxy.gom6.com')"><?=$lang5?></a>
</div>
<div class="count">
<br><br><br>
<?=$lang16?> <?=date("Y/m/d",filemtime($filename))?> by <a href="http://proxy.gom6.com/">gom6.com</a> 
<script src="http://s74.cnzz.com/stat.php?id=1438093&web_id=1438093&show=pic1" language="JavaScript" charset="gb2312"></script>

</div>
</body>
</html>