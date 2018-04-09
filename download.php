<?php
	$path = $_REQUEST['path'];

	if( $path ) {
		$name = basename($path);

		if( !file_exists($name) ) {
			$file_size = file_put_contents($name, fopen($path, 'r'));
			$is_downloaded = (bool) $file_size;
			if( $is_downloaded ) {
				$file_size = get_file_size($file_size);
			}

		} else {
			$exist_file = get_file_size( filesize($name) );
		}
	}

	function get_file_size($file_size) {
		$base = log($file_size, 1024);
		$suffixes = array('', 'KB', 'MB', 'GB', 'TB');
		return round(pow(1024, $base - floor($base)), 2) .' '. $suffixes[floor($base)];
	}

?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Download from ...</title>
	<style>
		* { box-sizing: border-box }
		html {
			font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";
			font-size: 16px;
			height: 100%;
		}
		body {
			-ms-align-items: center;
			align-items: center;
			background-color: #eee;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: -webkit-flex;
			display: flex;
			height: 100%;
			justify-content: center;
			margin: 0;
			padding: 0;
		}
		.container {
			background-color: #fff;
			border-radius: 2px;
			box-shadow: 0 1px 0 0 #d7d8db, 0 0 0 1px #e3e4e8;
			margin: 20px auto;
			max-width: 460px;
			min-width: 300px;
			padding: 15px;
			text-align: center;
			width: 90%;
		}
		h1 { margin: 0 0 20px }
		.succ-text { color: #0a0 }
		.warn-text { color: #ca0 }
		.err-text { color: #a00 }
		label {
			display: block;
			text-align: left;
			margin-bottom: 5px;
			font-size: .8rem;
		}
		input {
			border-radius: 2px;
			border: none;
			display: block;
			font-size: 1.1em;
			margin-bottom: 15px;
			margin-top: 10px;
			padding: 5px;
			position: relative;
			transition: all .2s ease-out;
			width: 100%;
		}
		input:focus,
		input:empty,
		input:invalid {
			box-shadow: none;
			border: none;
			border-bottom: 2px solid #fa0;
		}
		input:valid { border-bottom-color: #0a0 }
		.btn {
			background-color: #5af;
			border-radius: 2px;
			border: none;
			box-shadow: none;
			color: #fff;
			cursor: pointer;
			display: block;
			font-size: .9rem;
			margin-left: auto;
			max-width: 9rem;
			padding: 5px 15px;
			text-decoration: none;
			text-transform: uppercase;
		}
		.btn:hover,
		.btn:active,
		.btn:focus {
			color: #fff;
			text-decoration: none;
		}
	</style>
</head>
<body>
	<div class="container">
		<h1>Download file to server</h1>
	<?php if($path): ?>

		<?php if($is_downloaded): ?>
			<p class="succ-text"><u><?=$name; ?></u> (<?=$file_size; ?>) downloaded</p>
			<a href="download.php" class="btn">Back</a>
		<?php elseif($exist_file): ?>
			<p class="warn-text">File <u><?=$name; ?></u> (<?=$exist_file; ?>) already exist</p>
			<a href="download.php" class="btn">Back</a>
		<?php else: ?>
			<p class="err-text"><u><?=$name; ?></u> can't be downloaded</p>
			<p>Please, check the link: <a href="<?=$path; ?>" target="blank"><?=$path; ?></a></p>
			<a href="download.php" class="btn">Back</a>
		<?php endif; ?>

	<?php else: ?>

		<form>
			<input type="text" name="path" placeholder="https://wordpress.org/latest.tar.gz" pattern="https?://.+?\..+" required="required">
			<button class="btn">Download</button>
		</form>

	<?php endif; ?>
	</div>
</body>
</html>