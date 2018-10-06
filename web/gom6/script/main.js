	function $(){ 
		var elements = new Array(); 
		for (var i = 0; i < arguments.length; i++){ 
			var element = arguments[i]; 
			if (typeof element == 'string'){
				element = document.getElementById(element); 
			}
			if (arguments.length == 1){
				return element; 
			}
				elements.push(element); 
		} 
		return elements; 
	}

	function createCookie(name,value,days){
		if (days){
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));
			var expires = "; expires="+date.toGMTString();
		}
		else var expires = "";
		document.cookie = name+"="+value+expires+"; path=/";
	}
	function readCookie(name){
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++){
		   var c = ca[i];
		   while (c.charAt(0)==' ') c = c.substring(1,c.length);
		   if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
		}
		return null;
	}
	function proxyurl(){
		if ($("Url").value != "http://www.baidu.com"){
			createCookie("proxyurl",$("Url").value,7);
		}
	}
	function myload(){
		if (readCookie('proxyurl') != null){
			$("Url").value=readCookie('proxyurl');
		}
		$("Server").options[Math.floor(Math.random()*19)].selected="selected";
	}


function showc(number){

if(document.getElementById('c'+number).style.display=='none'){
document.getElementById('c'+number).style.display="";
}
else{
document.getElementById('c'+number).style.display="none";
}
}

function closec(number){
		document.getElementById('c'+number).style.display ="none"
                               }
function google(){
var gurl = 'http://www.google.cn/search?hl=zh-CN&q='+document.getElementById('Url').value;
window.location.href=gurl;
                             }
function gourl(){
var id=document.getElementById('Server').value;
document.getElementById('js').src ="http://proxy.gom6.com/sd.php?url="+id;
}

