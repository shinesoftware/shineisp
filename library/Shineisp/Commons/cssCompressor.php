<?php
/**
 * Handle the css stylesheet compression
 * 
 * @author shinesoftware
 *
 */
class Shineisp_Commons_cssCompressor {

	var $_srcs = array();
	var $_debug = true;
	var $_cache_dir = "";

	/**
	 * Adds a source file to the list of files to compile.  Files will be
	 * concatenated in the order they are added.
	 */
	function add($file) {
		$this->_srcs[] = $file;
		return $this;
	}

	/**
	 * Sets the directory where the compilation results should be cached, if
	 * not set then caching will be disabled and the compiler will be invoked
	 * for every request (NOTE: this will hit ratelimits pretty fast!)
	 */
	function cacheDir($dir) {
		$this->_cache_dir = $dir;
		return $this;
	}

	/**
	 * Writes the compiled response.  Reading from either the cache, or
	 * invoking a recompile, if necessary.
	 */
	function write() {

		// No cache directory so just dump the output.
		if ($this->_cache_dir == "") {
			echo $this->_compile();

		} else {
			$cache_file = $this->_getCacheFileName();
			if ($this->_isRecompileNeeded($cache_file)) {
				$result = $this->_compile();
				file_put_contents($cache_file, $result);
				return $this->_getHash() . ".css";
			} else {
				// No recompile needed, but see if we can send a 304 to the browser.
				$cache_mtime = filemtime($cache_file);
				$etag = md5_file($cache_file);
				header("Last-Modified: ".gmdate("D, d M Y H:i:s", $cache_mtime)." GMT");
				header("Etag: $etag");
				if (@strtotime(@$_SERVER['HTTP_IF_MODIFIED_SINCE']) == $cache_mtime ||
						@trim(@$_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
					header("HTTP/1.1 304 Not Modified");
				} else {
					// Read the cache file and send it to the client.
					return $this->_getHash() . ".css";
				}
			}
		}
	}

	// ----- Privates -----

	function _isRecompileNeeded($cache_file) {
		// If there is no cache file, we obviously need to recompile.
		if (!file_exists($cache_file)) return true;

		$cache_mtime = filemtime($cache_file);

		// If the source files are newer than the cache file, recompile.
		foreach ($this->_srcs as $src) {
			
			if (filemtime($src) > $cache_mtime){
				Shineisp_Commons_Utilities::log("Updating CSS Cache: $src must be updated.");
				return true;
			}
		}

		// Cache is up to date.
		Shineisp_Commons_Utilities::log("CSS Cache is up to date.");
		return false;
	}

	function _compile() {
		$buffer = "";
		
		foreach ($this->_srcs as $item){
			$buffer .= file_get_contents($item) . "\n\n";
		}
		
		/* remove comments */
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
			
		/* remove tabs, spaces, newlines, etc. */
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		
		return $buffer;
	}

	function _getCacheFileName() {
		return $this->_cache_dir . $this->_getHash() . ".css";
	}

	function _getHash() {
		return md5(implode(",", $this->_srcs) . "-" . $this->_debug);
	}

}
