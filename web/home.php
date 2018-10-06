<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<title>Zelune Proxy</title>
		<style type="text/css">
			html, body {
				font-family: "Lucida Sans Unicode", "Tahoma", sans-serif;
				font-size: 12px;
				width: 460px;
				margin: 60px auto 20px auto;
			}
			h1 {
				font-size: 2em;
			}
			#proxy_url {
				font-family: "Trebuchet MS", sans-serif;
				font-size: 16px;
				color: #666666;
				width: 432px;
				padding: 4px;
				border: 1px solid #AAAAAA;
			}
			#proxy_options {
				margin: 10px;
			}
			#proxy_button {
				font-family: sans-serif;
				font-size: 16px;
				font-weight: bold;
				padding: 4px;
				cursor: pointer;
			}
			#footer {
				text-align: center;
				padding: 10px 0px;
			}
			#footer a {
				color: #FF4444;
			}
		</style>
	</head>
	<body>
		<h1>Zelune</h1>
		<form method="post" action="./">
			<!-- Make sure you leave the two input fields the same! -->
			<input type="hidden" name="__proxy_action" value="redirect_browse" />
			<input type="text" name="__proxy_url" value="http://www.google.com/" id="proxy_url" />
			<div id="proxy_options">
				<!-- Don't rename the name="_no_xxx" of these input fields! -->
				<label for="__no_javascript">
					<input type="checkbox" name="__no_javascript" id="__no_javascript" />
					Disable JavaScript
				</label>
				<label for="__no_images">
					<input type="checkbox" name="__no_images" id="__no_images" />
					Disable Images
				</label>
				<label for="__no_title">
					<input type="checkbox" name="__no_title" id="__no_title" />
					Strip Title
				</label>
				<label for="__no_meta">
					<input type="checkbox" name="__no_meta" id="__no_meta" />
					Strip Meta
				</label>
			</div>
			<input type="submit" value="Browse" id="proxy_button" />
		</form>
		<div id="footer"><a href="http://www.zelune.com/">Powered by Zelune</a></div>
		<!-- Please leave this link, I give you such a good proxy for such a low price, won't you leave it there? :) -->
	</body>
</html>