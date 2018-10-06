<?php
$list=file('Datalist.php');
$eurl=$list[$_GET['url']];
$aburl=trim(str_replace('[url]',base64_encode('proxy.gom6.com'),$eurl));
?>
<iframe id="hint" src="<?=$aburl?>" width=0 height=0 frameborder=0></iframe>
<div id="p"></div>
<script>
var tim=1;
function setVisiblity()
{
if(document.getElementById("hint").readyState=="complete")
 {
 document.getElementById("p").innerHTML="连接成功"+tim/10+"秒";
 } 
 else{
 tim++;
setTimeout("setVisiblity(tim)",100);
 document.getElementById("p").innerHTML="速度测试中......";

}
 };
setVisiblity();
 </script>