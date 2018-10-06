<?
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
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="utf-8" />
	<meta content="all" name="robots" />
	<link rel="shortcut icon" href="favicon.ico"/>
	<title>GoM6在线代理 WEB代理使用帮助</title>

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
	input.in{padding:1px 1px 1px 55px;border:1px solid #7b9ebe;font-size:14px;background:url(images/inbg.gif) no-repeat #ffc;;}
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
		div.help
       {	 
		 background:#ffc;
		 width:50%;
		 BORDER: 1px solid green; 
		 margin-left:25%; margin-right:25%; 
		 padding:0 3px 0 3px;
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
</head>
<body>
<div class="headbox">
     

	<a href="http://proxy.gom6.com" >HOME</a>
	<a href="#" >HELP</a>
	<a href="ms" target="_blank"><?=$lang10?></a>
	<a href="proxydown.php">DOWNLOAD</a>

</div>
<br/>
<div class="logobox">
	<a href="/"><img src="images/logo-cn.gif" alt="web online proxy" border="0"></a>
</div>
<br/>

<div class="help" id="c2" style="cursor:pointer" >
<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>
</div>

<div class="count">
<br><br><br>
<?=$lang16?> <?=date("Y/m/d",filemtime($filename))?> by <a href="http://proxy.gom6.com/">gom6.com</a> 
<script src="http://s74.cnzz.com/stat.php?id=1438093&web_id=1438093&show=pic1" language="JavaScript" charset="gb2312"></script>

</div>
</body>
</html>