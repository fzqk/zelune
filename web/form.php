<style type="text/css">
	#proxy_container {
		width: 800px;
		position: absolute;
		top: 0px;
		left: 20px;
	}
	#proxy_form {
		background-color: #00CCFF;
		height: 80px;
		margin: 0px;
		padding: 20px 20px 0px 20px;
	}
	#proxy_url {
		font-family: Tahoma, sans-serif;
		font-size: 16px;
		color: #999999;
		background-color: white;
		display: block;
		width: 760px;
		padding: 10px;
		border: 0px;
	}
	#proxy_button {
		font-family: "Lucida Sans Unicode", sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #FFFFFF;
		background-color: #FF4444;
		float: right;
		height: 32px;
		padding: 0px 6px;
		border: 0px;
		cursor: pointer;
	}
	#proxy_toggle {
		font-family: "Lucida Sans Unicode", sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #FFFFFF;
		text-decoration: none;
		background-color: #FF9900;
		display: block;
		float: left;
		margin: 0px 6px 0px 0px;
		padding: 6px 8px;
		border: 0px;
		-moz-outline-width: 0px;
	}
	#proxy_options {
		padding: 8px 0px 0px 0px;
	}
	#proxy_options label {
		font-family: "Lucida Sans Unicode", sans-serif;
		font-size: 14px;
		font-weight: bold;
		color: #FFFFFF;
		cursor: pointer;
	}
</style>
<div id="proxy_container" align="left">
	<form method="post" action="./" id="proxy_form">
		<!-- Make sure you leave the two input fields the same! -->
		<input type="hidden" name="__proxy_action" value="redirect_browse" />
		<input type="text" name="__proxy_url" id="proxy_url" />
		<!-- This one you can change -->
		<input type="submit" value="Browse" id="proxy_button" />
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
	</form>
	<!-- You don't need this, remove it if you like -->
	<a href="#" id="proxy_toggle" onclick="toggle_form(); return false;">Hide Me</a>
</div>