<?php

	$action = 'action_' . $_REQUEST['action'];
	if( function_exists($action) ) {
		$notification = (object)[];
		$notification->type = '';
		$notification->text = 'Do not set';
		$action();

	} else {
		$archives = get_archives();
	}

	function action_download() {
		global $notification;

		if( !$_REQUEST['path'] ) {
			$notification->type = 'error';
			$notification->text = 'Path is not set';
			return;
		}

		$basename = basename($_REQUEST['path']);
		if( file_exists($basename) ) {
			$filesize = get_file_size( filesize($basename) );
			$notification->type = 'warning';
			$notification->text = 'File `<u>' . $basename . '</u>` ('. $filesize .') already exist.';
			return;
		}

		$download = download_file($_REQUEST['path']);
		if( $download->success ) {
			$notification->type = 'success';
			$notification->text = '`<u>'. $download->filename . '</u>` (' . $download->filesize . ') downloaded.';
		} else {
			var_dump($download);
			$notification->type = 'warning';
			$notification->text = '`<u>'. $download->filename . '</u>` can\'t be downloaded.';
		}
	}

	function action_extract() {
		global $notification;

		if( !$_REQUEST['extract'] || !file_exists($_REQUEST['extract']) ) {
			$notification->type = 'error';
			$notification->text = '`<u>'. $_REQUEST['extract'] .'</u>` doesn\'t exist';
			return;
		}

		if( extract_archive($_REQUEST['extract']) ) {
			$notification->type = 'success';
			$notification->text = '<u>' . $_REQUEST['extract'] . '</u> successfully extracted';
		} else {
			$notification->type = 'warning';
			$notification->text = 'Can\'t extract `<u>' . $_REQUEST['extract'] . '</u>`';
		}
	}

	function get_archives() {
		$archives = array();
		foreach (glob('*.tar*') as $filename) {
			$archives[] = $filename;
		}
		return $archives;
	}

	function download_file($path) {
		if(!$path) return false;

		$download = (object)[];
		$download->success = false;
		$download->path = $path;
		$download->filename = basename($path);

		$download->filesize = file_put_contents($download->filename, fopen($download->path, 'r'));
		$download->success = (bool) $download->filesize;
		if( $download->success ) {
			$download->filesize = get_file_size( $download->filesize );
		}
		return $download;
	}

	function extract_archive($path) {
		if( !$path ) return false;

		try {
			if(substr($path, -7) == '.tar.gz') {
				$decompressed_file = new PharData($path);
				$decompressed_file->decompress(); // creates filename.tar
				$unlink_file = $path;
				$path = substr($path, 0, -3);
			}

			// unarchive from the tar
			$phar = new PharData($path);
			$phar->extractTo('.');

			// remove .tar
			if( $unlink_file ) {
				unlink($unlink_file);
			}

			return true;

		} catch( Exception $e ) {
			return false;
		}
	}

	function get_file_size($size) {
		$base = log($size, 1024);
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
			-moz-flex-direction: column;
			-ms-align-items: center;
			-ms-flex-direction: column;
			-o-flex-direction: column;
			-webkit-flex-direction: column;
			align-items: center;
			background-color: #eee;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: -webkit-flex;
			display: flex;
			flex-direction: column;
			height: 100%;
			justify-content: center;
			margin: 0;
			padding: 0;
		}
		.container {
			background-color: #fff;
			border-radius: 2px;
			box-shadow: 0 1px 0 0 #d7d8db, 0 0 0 1px #e3e4e8;
			margin: 10px auto;
			max-width: 460px;
			min-width: 300px;
			padding: 15px;
			text-align: center;
			width: 90%;
		}
		.row {
			-ms-align-items: center;
			align-items: center;
			border-bottom: 1px solid #eee;
			display: -moz-flex;
			display: -ms-flex;
			display: -o-flex;
			display: -webkit-flex;
			display: flex;
			justify-content: space-between;
			margin: 3px 0;
			padding: 3px 0;
		}
		.row:last-child {
			border-bottom: none;
		}
		h1 { margin: 0 0 20px }
		.success { color: #0a0 }
		.warning { color: #ca0 }
		.error { color: #a00 }
		input {
			border-radius: 2px;
			border: none;
			display: block;
			font-size: 1.1em;
			margin: 15px 0;
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
			font-size: .8rem;
			margin-left: auto;
			max-width: 9rem;
			padding: 2px 5px;
			text-decoration: none;
			text-transform: uppercase;
		}
		.btn-big {
			font-size: .9rem;
			padding: 5px 15px;
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
		<?php if($notification): ?>

			<p class="<?=$notification->type ?>"><?=$notification->text ?></p>
			<a href="download.php" class="btn btn-big">Back</a>

		<?php else: ?>

			<form>
				<input type="hidden" name="action" value="download">
				<input type="text" name="path" placeholder="https://wordpress.org/latest.tar.gz" pattern="https?://.+?\..+" required="required">
				<button class="btn btn-big">Download</button>
			</form>

		<?php endif; ?>
	</div>

	<?php if( $archives ): ?>
	<div class="container">
	<?php foreach($archives as $archive): ?>
		<div class="row">
			<span><?=$archive; ?></span>
			<form method="post">
				<input type="hidden" name="action" value="extract">
				<input type="hidden" name="extract" value="<?=$archive; ?>">
				<button class="btn">extract</button>
			</form>
		</div>
	<?php endforeach; ?>
	</div>
	<?php endif; ?>

</body>
</html>