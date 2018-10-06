<?php
	class proxied {
		
		var $url;
		var $url_first;
		var $ch;
		var $info;
		var $data;
		var $to_unlink;
		
		function proxied ($url) {
			session_start();
			$this->url = $url;
			$this->handle_actions();
			$this->ch = curl_init($url);
			$this->set_options();
			$s = array();
			$r = array();
			if ($_SESSION['__no_javascript'] == 'yes') {
				$s[] = '#<\s*script[^>]*?>.*?<\s*/\s*script\s*>#si';
				$r[] = '';
				$s[] = '#(\bon[a-z]+)\s*=\s*(?:"([^"]*)"?|\'([^\']*)\'?|([^\'"\s>]*))?#si';
				$r[] = '';
				$s[] = '#<noscript>(.*?)</noscript>#si';
				$r[] = '\\1';
			}
			if ($_SESSION['__no_images'] == 'yes') {
				$s[] = '#<(img|image)[^>]*?>#si';
				$r[] = '';
			}
			if ($_SESSION['__no_title'] == 'yes') {
				$s[] = '#<\s*title[^>]*?>.*?<\s*/\s*title\s*>#si';
				$r[] = '';
			}
			if ($_SESSION['__no_meta'] == 'yes') {
				$s[] = '#<(meta)[^>]*?>#si';
				$r[] = '';
			}
			$this->data = preg_replace($s, $r, curl_exec($this->ch));
			$this->info = curl_getinfo($this->ch);
			$this->url = parse_url($this->info['url']);
			$this->url['full'] = $this->info['url'];
			$this->url_first = $this->info['url'];
			header("Content-Type: {$this->info[content_type]}");
			if (eregi('css', $this->info['content_type'])) {
				$this->data = $this->parse_css($this->data);
			} elseif (eregi('html|xml', $this->info['content_type'])) {
				$this->data = $this->parse_html($this->data);
			}
			echo $this->data;
			curl_close($this->ch);
			if ($this->to_unlink) {
				foreach ($this->to_unlink as $file) {
					@unlink($file);
				}
			}
		}
		
		function set_options () {
			if (!array_key_exists('__no_javascript', $_SESSION)) {
				$_SESSION['__no_javascript'] = 'yes';
				$_SESSION['__no_images'] = 'no';
				$_SESSION['__no_title'] = 'no';
				$_SESSION['__no_meta'] = 'no';
			}
			$options = array(
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_AUTOREFERER => true,
				CURLOPT_COOKIEFILE => 'cookies/' . session_id() . '.txt',
				CURLOPT_COOKIEJAR => 'cookies/' . session_id() . '.txt'
			);
			if (count($_POST)) {
				$post = array();
				foreach ($_POST as $key => $value) {
					$post[$key] = $value;
				}
			}
			if (count($_FILES)) {
				$post = $post ? $post : array();
				foreach ($_FILES as $name => $file) {
					$this->to_unlink[] = "uploads/$file[name]";
					move_uploaded_file($file['tmp_name'], "uploads/$file[name]");
					$post[$name] = "@uploads/$file[name]";
				}
			}
			if ($post) {
				$options[CURLOPT_POST] = true;
				$options[CURLOPT_POSTFIELDS] = $post;
			}
			if (ereg('__proxy_url=', $_SERVER['HTTP_REFERER'])) {
				preg_match('#__proxy_url=([^&]+)#', $_SERVER['HTTP_REFERER'], $referer);
				$referer = base64_decode($referer[1]);
				$options[CURLOPT_REFERER] = $referer;
			}
			if (!eregi('google\.com', $this->url)) {
				$options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
			} else {
				$options[CURLOPT_USERAGENT] = 'None';
			}
			foreach ($options as $option => $value) {
				@curl_setopt($this->ch, $option, $value);
			}
		}
		
		function parse_html ($string) {
			$parse = array(
				'a' => array('href'),
				'img' => array('src', 'longdesc'),
				'image' => array('src', 'longdesc'),
				'body' => array('background'),
				'frame' => array('src', 'longdesc'),
				'iframe' => array('src', 'longdesc'),
				'head' => array('profile'),
				'layer' => array('src'),
				'input' => array('src', 'usemap'),
				'form' => array('action'),
				'area' => array('href'),
				'link' => array('href', 'src', 'urn'),
				'meta' => array('content'),
				'param' => array('value'),
				'applet' => array('codebase', 'code', 'object', 'archive'),
				'object' => array('usermap', 'codebase', 'classid', 'archive', 'data'),
				'script' => array('src'),
				'select' => array('src'),
				'hr' => array('src'),
				'table' => array('background'),
				'tr' => array('background'),
				'th' => array('background'),
				'td' => array('background'),
				'bgsound' => array('src'),
				'blockquote' => array('cite'),
				'del' => array('cite'),
				'embed' =>  array('src'),
				'fig' => array('src', 'imagemap'),
				'ilayer' => array('src'),
				'ins' => array('cite'),
				'note' => array('src'),
				'overlay' => array('src', 'imagemap'),
				'q' => array('cite'),
				'ul' => array('src')
			);
			$tags = $this->get_tags($string);
			$to_replace = array();
			foreach ($tags as $tag) {
				$tag_name = $this->get_tag_name($tag);
				$attributes = $this->get_attributes($tag);
				if ($tag_name == 'base' && $attributes['href']) {
					$this->url = parse_url($attributes['href']);
					$this->url['full'] = $attributes['href'];
					$to_replace[] = array(
						'string' => $tag,
						'value' => ''
					);
				}
				if ($attributes['style']) {
					$attributes['style'] = $this->parse_css($attributes['style']);
				}
				if ($parse[$tag_name]) {
					$extra_html = '';
					$relink = true;
					$new_tag = "<$tag_name";
					switch ($tag_name) {
						case 'form':
							if (strtolower($attributes['method']) == 'get' || !$attributes['method']) {
								$url = $attributes['action'] ? $this->encode_url($attributes['action'], false, true) : $this->encode_url($this->url['full'], false, true);
								$extra_html = "<input type=\"hidden\" name=\"__proxy_url\" value=\"$url\" /><input type=\"hidden\" name=\"__proxy_action\" value=\"redirect_get\" />";
								$attributes['action'] = './';
								$attributes['method'] = 'post';
								$relink = false;
							}
						break;
						case 'head':
							if ($_GET['__proxy_form'] != '0') {
								$extra_html = "<script language=\"javascript\" type=\"text/javascript\">\n";
								$extra_html .= "var __proxy_url = '{$this->url_first}';\n";
								$no_javascript = $_SESSION['__no_javascript'] == 'yes' ? 'true' : 'false';
								$extra_html .= "var __no_javascript = $no_javascript;\n";
								$no_images = $_SESSION['__no_images'] == 'yes' ? 'true' : 'false';
								$extra_html .= "var __no_images = $no_images;\n";
								$no_title = $_SESSION['__no_title'] == 'yes' ? 'true' : 'false';
								$extra_html .= "var __no_title = $no_title;\n";
								$no_meta = $_SESSION['__no_meta'] == 'yes' ? 'true' : 'false';
								$extra_html .= "var __no_meta = $no_meta;\n";
								$extra_html .= "</script>\n";
								$extra_html .= '<script language="javascript" type="text/javascript" src="./js/main.js"></script>';
							}
						break;
					}
					if ($attributes) {
						foreach ($attributes as $attribute_name => $attribute_value) {
							if (in_array($attribute_name, $parse[$tag_name])) {
								switch ($tag_name) {
									default:
										if ($relink) {
											if ($attribute_name == 'src') {
												$extra = '&__proxy_form=0';
											} else {
												$extra = '';
											}
											$attribute_value = $this->encode_url($attribute_value) . $extra;
										}
									break;
									case 'meta':
										if (eregi('refresh', $attributes['http-equiv']) && $tag_name == 'meta' && $attribute_name == 'content' && preg_match('#^(\s*[0-9]*\s*;\s*url=)(.*)#i', $attribute_value, $content)) {
											$attribute_value =  $content[1] . $this->encode_url($content[2]);
										}
									break;
								}
							}
							$new_tag .= " $attribute_name=\"$attribute_value\"";
						}
					}
					$new_tag .= ">$extra_html";
					$to_replace[] = array(
						'string' => $tag,
						'value' => $new_tag
					);
				}
			}
			$string = $this->mass_replace($to_replace, $string);
			return $string;
		}
		
		function parse_css ($string) {
			$to_replace = array();
			preg_match_all('#url[\s]*\([\s]*("[^"]+"|\'[^\']+\'|[^\s>]+)[\s]*\)#si', $string, $urls);
			for ($i = 0; $i < count($urls[0]); $i++) {
				$url = $this->encode_url(preg_replace('#^("([^"]+)"|\'([^\']+)\')$#', '\\2\\3', $urls[1][$i]));
				$to_replace[] = array(
					'string' => $urls[0][$i],
					'value' => "url('$url')"
				);
			}
			preg_match_all('#@import[\s]*("[^"]+"|\'[^\']+\'|[^\s>]+)#si', $string, $urls);
			for ($i = 0; $i < count($urls[0]); $i++) {
				$url = $this->encode_url(preg_replace('#^("([^"]+)"|\'([^\']+)\')$#', '\\2\\3', $urls[1][$i]));
				$to_replace[] = array(
					'string' => $urls[0][$i],
					'value' => "@import '$url'"
				);
			}
			$string = $this->mass_replace($to_replace, $string);
			return $string;
		}
		
		function get_tags ($string) {
			preg_match_all('#<([a-z-]+)([^>]+)>#si', $string, $tags);
			return $tags[0];
		}
		
		function get_tag_name ($string) {
			preg_match('#^<([a-z0-9-]+)#i', $string, $matches);
			return strtolower($matches[1]);
		}
		
		function get_attributes ($string) {
			$attributes = array();
			$string = preg_replace('#^<[a-z-]+|>$#i', '', $string);
			if ($string) {
				preg_match_all('#([a-z-]+)=?("[^">]*"|\'[^\'>]*\'|[^\s>]*)#si', $string, $matches);
				for ($i = 0; $i < count($matches[0]); $i++) {
					$attributes[strtolower($matches[1][$i])] = $this->strip_quotes($matches[2][$i]);
				}
				return $attributes;
			} else {
				return false;
			}
		}
		
		function strip_quotes ($string) {
			return ereg_replace('^("([^"]*)"|^\'([^\']*)\')$', '\\2\\3', $string);
		}
		
		function mass_replace ($array, $string) {
			foreach ($array as $replacement) {
				$string = str_replace($replacement['string'], $replacement['value'], $string);
			}
			return $string;
		}
		
		function encode_url ($string, $raw = false, $plain = false) {
			$string = $this->strip_quotes(html_entity_decode($string));
			if (eregi('^[a-z]{2,}:', $string)) {
				
			} elseif (ereg('^/', $string)) {
                $string = "{$this->url[scheme]}://{$this->url[host]}$string";
			} elseif (ereg('^#', $string)) {
				$raw = true;
			} elseif (eregi('^mailto:', $string)) {
				$raw = true;
			} elseif (ereg('^\.\./', $string)) {
				preg_match_all('#\.\./#', $string, $matches);
				$path = ereg_replace('/([^/]*)$', '/', $this->url['path']);
				for ($i = 0; $i < count($matches[0]); $i++) {
					$path = ereg_replace('([^/]*)/$', '', $path);
				}
				$path = ereg_replace('/$', '', $path) . '/';
				$string = ereg_replace('\.\./', '', $string);
				$string = "{$this->url[scheme]}://{$this->url[host]}$path$string";
			} else {
				$string = ereg_replace('^\./', '', $string);
				$path = ereg_replace('/([^/]*)$', '/', $this->url['path']);
				$path = ereg_replace('/$', '', $path) . '/';
				$string = "{$this->url[scheme]}://{$this->url[host]}$path$string";
			}
			return $raw ? $string : (!$plain ? './?__proxy_url=' : '') . base64_encode($string);
		}
		
		function handle_actions () {
			if ($_POST['__proxy_action'] == 'redirect_get') {
				$url = base64_decode($_POST['__proxy_url']);
				unset($_POST['__proxy_action'], $_POST['__proxy_url']);
				$get = '';
				foreach ($_POST as $key => $value) {
					$value = urlencode($value);
					$get .= "&$key=$value";
				}
				$get = ereg('\?', $url) ? $get : ereg_replace('^&', '?', $get);
				$url = base64_encode($url . $get);
				header("Location: ./?__proxy_url=$url");
				exit;
			} elseif ($_POST['__proxy_action'] == 'redirect_browse') {
				$_SESSION['__no_javascript'] = (bool) $_POST['__no_javascript'] ? 'yes' : 'no';
				$_SESSION['__no_images'] = (bool) $_POST['__no_images'] ? 'yes' : 'no';
				$_SESSION['__no_title'] = (bool) $_POST['__no_title'] ? 'yes' : 'no';
				$_SESSION['__no_meta'] = (bool) $_POST['__no_meta'] ? 'yes' : 'no';
				header('Location: ./?__proxy_url=' . base64_encode($_POST['__proxy_url']));
				exit;
			}
		}
		
	}
	$url = @parse_url($_GET['__proxy_url']) && strlen($_GET['__proxy_url']) ? base64_decode($_GET['__proxy_url']) : false;
	if (!$url && !$_POST['__proxy_action']) {
		include 'home.php';
		exit;
	}
	$proxied = new proxied($url);
?>