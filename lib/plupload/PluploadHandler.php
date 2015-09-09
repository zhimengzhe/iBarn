<?php


define('PLUPLOAD_MOVE_ERR', 103);
define('PLUPLOAD_INPUT_ERR', 101);
define('PLUPLOAD_OUTPUT_ERR', 102);
define('PLUPLOAD_TMPDIR_ERR', 100);
define('PLUPLOAD_TYPE_ERR', 104);
define('PLUPLOAD_UNKNOWN_ERR', 111);
define('PLUPLOAD_SECURITY_ERR', 105);


class PluploadHandler {

	public static $conf;

	private static $_error = null;

	private static $_errors = array(
		PLUPLOAD_MOVE_ERR => "Failed to move uploaded file.",
		PLUPLOAD_INPUT_ERR => "Failed to open input stream.",
		PLUPLOAD_OUTPUT_ERR => "Failed to open output stream.",
		PLUPLOAD_TMPDIR_ERR => "Failed to open temp directory.",
		PLUPLOAD_TYPE_ERR => "File type not allowed.",
		PLUPLOAD_UNKNOWN_ERR => "Failed due to unknown error.",
		PLUPLOAD_SECURITY_ERR => "File didn't pass security check."
	);


	/**
	 * Retrieve the error code
	 *
	 * @return int Error code
	 */
	static function get_error_code()
	{
		if (!self::$_error) {
			return null;
		} 

		if (!isset(self::$_errors[self::$_error])) {
			return PLUPLOAD_UNKNOWN_ERR;
		}

		return self::$_error;
	}


	/**
	 * Retrieve the error message
	 *
	 * @return string Error message
	 */
	static function get_error_message()
	{
		if ($code = self::get_error_code()) {
			return self::$_errors[$code];
		}
		return '';
	}


	/**
	 * 
	 */
	static function handle($conf = array())
	{
		// 2H execution time
		@set_time_limit(7200);

		self::$_error = null; // start fresh

		$conf = self::$conf = array_merge(array(
			'file_data_name' => 'file',
			'tmp_dir' => ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload",
			'target_dir' => false,
			'cleanup' => true,
			'max_file_age' => 48 * 3600,
			'chunk' => isset($_REQUEST['chunk']) ? intval($_REQUEST['chunk']) : 0,
			'chunks' => isset($_REQUEST['chunks']) ? intval($_REQUEST['chunks']) : 0,
			'file_name' => isset($_REQUEST['name']) ? $_REQUEST['name'] : false,
			'allow_extensions' => false,
			'delay' => 0,
			'cb_sanitize_file_name' => array(__CLASS__, 'sanitize_file_name'),
			'cb_check_file' => false,
		), $conf);
		try {
			if (!$conf['file_name']) {
			 	if (!empty($_FILES)) {
					$conf['file_name'] = $_FILES[$conf['file_data_name']]['name'];
				} else {
					throw new Exception('', PLUPLOAD_INPUT_ERR);
				}
			}
			// Cleanup outdated temp files and folders
			if ($conf['cleanup']) {
				self::cleanup();
			}

			// Fake network congestion
			if ($conf['delay']) {
				usleep($conf['delay']);
			}

//			if (is_callable($conf['cb_sanitize_file_name'])) {
//				$file_name = call_user_func($conf['cb_sanitize_file_name'], $conf['file_name']);
//			} else {
//				$file_name = $conf['file_name'];
//			}
            $file_name = $conf['file_name'];
			// Check if file type is allowed
			if ($conf['allow_extensions']) {
				if (is_string($conf['allow_extensions'])) {
					$conf['allow_extensions'] = preg_split('{\s*,\s*}', $conf['allow_extensions']);
				}

				if (!in_array(strtolower(pathinfo($file_name, PATHINFO_EXTENSION)), $conf['allow_extensions'])) {
					throw new Exception('', PLUPLOAD_TYPE_ERR);
				}
			}
			$file_path = rtrim($conf['target_dir'], DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $file_name;
            if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
                $file_path = iconv('utf-8', 'gbk//IGNORE', $file_path);
            }
			$tmp_path = $file_path . ".part";
			// Write file or chunk to appropriate temp location
			if ($conf['chunks']) {
				self::write_file_to("$file_path.dir.part" . DIRECTORY_SEPARATOR . $conf['chunk']);
				// Check if all chunks already uploaded
				if ($conf['chunk'] == $conf['chunks'] - 1) { 
					self::write_chunks_to_file("$file_path.dir.part", $tmp_path);
				}
			} else {
				self::write_file_to($tmp_path);
			}

			// Upload complete write a temp file to the final destination
			if (!$conf['chunks'] || $conf['chunk'] == $conf['chunks'] - 1) {
				if (is_callable($conf['cb_check_file']) && !call_user_func($conf['cb_check_file'], $tmp_path)) {
					@unlink($tmp_path);
					throw new Exception('', PLUPLOAD_SECURITY_ERR);
				}

				rename($tmp_path, $file_path);

				return array(
					'name' => $file_name,
					'path' => $file_path,
					'size' => filesize($file_path)
				);
			} 

			// ok so far
			return true;

		} catch (Exception $ex) {
			self::$_error = $ex->getCode();
			return false;
		}
	}


	/**
	 * Writes either a multipart/form-data message or a binary stream 
	 * to the specified file.
	 *
	 * @throws Exception In case of error generates exception with the corresponding code
	 *
	 * @param string $file_path The path to write the file to
	 * @param string [$file_data_name='file'] The name of the multipart field
	 */
	static function write_file_to($file_path, $file_data_name = false)
	{
        session_write_close();
		if (!$file_data_name) {
			$file_data_name = self::$conf['file_data_name'];
		}

		$base_dir = dirname($file_path);
		if (!file_exists($base_dir)) {
            mkdir($base_dir, 0777, true);
		}
		if (!empty($_FILES) && isset($_FILES[$file_data_name])) {
			if ($_FILES[$file_data_name]["error"] || !is_uploaded_file($_FILES[$file_data_name]["tmp_name"])) {
				throw new Exception('', PLUPLOAD_MOVE_ERR);
			}
			move_uploaded_file($_FILES[$file_data_name]["tmp_name"], $file_path);
		} else {	
			// Handle binary streams
			if (!$in = @fopen("php://input", "rb")) {
				throw new Exception('', PLUPLOAD_INPUT_ERR);
			}
			if (!$out = @fopen($file_path, "wb")) {
				throw new Exception('', PLUPLOAD_OUTPUT_ERR);
			}

			while ($buff = fread($in, 4096)) {
				fwrite($out, $buff);
			}

			@fclose($out);
			@fclose($in);
		}
	}


	/**
	 * Combine chunks from the specified folder into the single file.
	 *
	 * @throws Exception In case of error generates exception with the corresponding code
	 *
	 * @param string $chunk_dir Temp directory with the chunks
	 * @param string $file_path The file to write the chunks to
	 */
	static function write_chunks_to_file($chunk_dir, $file_path)
	{
        session_write_close();
		if (!$out = @fopen($file_path, "wb")) {
			throw new Exception('', PLUPLOAD_OUTPUT_ERR);
		}

		for ($i = 0; $i < self::$conf['chunks']; $i++) {
			$chunk_path = $chunk_dir . DIRECTORY_SEPARATOR . $i;
			if (!file_exists($chunk_path)) {
				throw new Exception('', PLUPLOAD_MOVE_ERR);
			}

			if (!$in = @fopen($chunk_path, "rb")) {
				throw new Exception('', PLUPLOAD_INPUT_ERR);
			}

			while ($buff = fread($in, 4096)) {
				fwrite($out, $buff);
			}
			@fclose($in);

			// chunk is not required anymore
			@unlink($chunk_path);
		}
		@fclose($out);

		// Cleanup
		self::rrmdir($chunk_dir);
	}


	static function no_cache_headers() 
	{
		// Make sure this file is not cached (as it might happen on iOS devices, for example)
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}


	static function cors_headers($headers = array(), $origin = '*')
	{
		$allow_origin_present = false;

		if (!empty($headers)) {
			foreach ($headers as $header => $value) {
				if (strtolower($header) == 'access-control-allow-origin') {
					$allow_origin_present = true;
				}
				header("$header: $value");
			}
		}

		if ($origin && !$allow_origin_present) {
			header("Access-Control-Allow-Origin: $origin");
		}

		// other CORS headers if any...
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
			exit; // finish preflight CORS requests here
		}
	}


	private static function cleanup() 
	{
		// Remove old temp files	
		if (file_exists(self::$conf['target_dir'])) {
			foreach(glob(self::$conf['target_dir'] . '/*.part') as $tmpFile) {
				if (time() - filemtime($tmpFile) < self::$conf['max_file_age']) {
					continue;
				}
				if (is_dir($tmpFile)) {
					self::rrmdir($tmpFile);
				} else {
					@unlink($tmpFile);
				}
			}
		}
	}


	/**
	 * Sanitizes a filename replacing whitespace with dashes
	 *
	 * Removes special characters that are illegal in filenames on certain
	 * operating systems and special characters requiring special escaping
	 * to manipulate at the command line. Replaces spaces and consecutive
	 * dashes with a single dash. Trim period, dash and underscore from beginning
	 * and end of filename.
	 *
	 * @author WordPress
	 *
	 * @param string $filename The filename to be sanitized
	 * @return string The sanitized filename
	 */
	private static function sanitize_file_name($filename)
	{
	    $special_chars = array("?", "[", "]", "/", "\\", "=", "<", ">", ":", ";", ",", "'", "\"", "&", "$", "#", "*", "(", ")", "|", "~", "`", "!", "{", "}");
	    $filename = str_replace($special_chars, '', $filename);
	    $filename = preg_replace('/[\s-]+/', '-', $filename);
	    $filename = trim($filename, '.-_');
	    return $filename;
	}


	/** 
	 * Concise way to recursively remove a directory 
	 * http://www.php.net/manual/en/function.rmdir.php#108113
	 *
	 * @param string $dir Directory to remove
	 */
	private static function rrmdir($dir) 
	{
		foreach(glob($dir . '/*') as $file) {
			if(is_dir($file))
				self::rrmdir($file);
			else
				unlink($file);
		}
		rmdir($dir);
	}
}

