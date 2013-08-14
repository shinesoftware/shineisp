<?php
/*
 * 
 */
class Zend_View_Helper_Image extends Zend_View_Helper_Abstract {
	private $_name = null;
	private $_width = null;
	private $_height = null;
	private $_src = null;
	private $_imagePath = null;
	private $_fileName = null;
	private $_imgMime = null;
	private $_validMime = array ('image/png', 'image/jpeg', 'image/jpg', 'image/gif' );
	protected $width = 100;
	protected $height = 100;
	
	public function image($name, $imagePath = null, $attribs = array()) {
		
		// set name
		$this->_name = $this->view->escape ( $name );
		
		// set path
		$this->_setImagepath ( $imagePath );
		
		// set attributes
		$this->_setAttributes ( $attribs );
		
		// add action to image (e.g. generate thumbnail)
		// default action set dimensions
		if (! $this->_setAction ()) {
			$this->_setDimensions ();
		}
		
		// render image
		return $this->_render ();
	}
	
	/**
	 * Return image relative path
	 *
	 * @return string
	 */
	public function getImagePath() {
		return $this->_imagePath;
	}
	
	/**
	 * Return image src
	 *
	 * @return string
	 */
	public function getSrc() {
		return $this->_src;
	}
	
	/**
	 * Return image width
	 *
	 * @return string
	 */
	public function getWidth() {
		return $this->_width;
	}
	
	/**
	 * Return image height
	 *
	 * @return string
	 */
	public function getHeight() {
		return $this->_height;
	}
	
	/**
	 * Return image name
	 *
	 * @return string
	 */
	public function getImageName() {
		return $this->_fileName;
	}
	
	/**
	 * Set new image after a specific action applied  on the current image
	 *
	 * @param string $path
	 * @return self
	 */
	public function setNewImage($path, $width = null, $height = null) {
		// set image new path
		$this->_setImagepath ( $path );


		if ($width !== null) {
			$this->_width = $width;
		}
		
		if ($height !== null) {
			$this->_height = $height;
		}
	}
	
	/**
	 * render image html tag
	 *
	 * @return string
	 */
	protected function _render() {
		$xhtml = '<img src="' . $this->_src . '" ' . $this->_attribs . ' id="' . $this->_name . '"';
		$xhtml .= ! empty ( $this->_width ) ? ' width="' . $this->_width . '"' : '';
		$xhtml .= ! empty ( $this->_height ) ? ' height="' . $this->_height . '"' : '';
		
		$endTag = " />";
		if (($this->view instanceof Zend_View_Abstract) && ! $this->view->doctype ()->isXhtml ()) {
			$endTag = ">";
		}
		return $xhtml . $endTag;
	}
	
	/**
	 * Retrieve image sizes and type
	 * APPLICATION_PUBLIC constants needed for path to public root
	 *
	 * @todo add cache beacuse getimagesize() is expensive to use.
	 * @return boolean
	 */
	protected function _setDimensions() {
		// get image size
		$path = PUBLIC_PATH . $this->_imagePath;
		
		if (! $imgInfo = @getimagesize ( $path )) {
			return false;
		}
		// is image mime allowed
		if (! in_array ( $imgInfo ['mime'], $this->_validMime )) {
			return false;
		}
		// set image info
		$this->_imgMime = $imgInfo ['mime'];
		$this->_height = $imgInfo [1];
		$this->_width = $imgInfo [0];
		return true;
	}
	
	/**
	 * Set image path
	 *
	 * @param string $path
	 * @return self
	 */
	protected function _setImagepath($path) {
		$this->_imagePath = $path;
		$this->_fileName = basename ( $path );
		$this->_src = $this->view->baseUrl ( $path, true );
		return $this;
	}
	
	/**
	 * Set image attributes
	 *
	 * @param array $attribs
	 * @return self
	 */
	protected function _setAttributes($attribs) {
		$alt = '';
		$class = '';
		$map = '';
		$class = '';
		
		if (! empty ( $attribs ['width'] ) && is_numeric ( $attribs ['width'] )) {
			$this->width = $attribs ['width'];
		}
		if (! empty ( $attribs ['height'] ) && is_numeric ( $attribs ['height'] )) {
			$this->height = $attribs ['height'];
		}
		
		if (isset ( $attribs ['alt'] )) {
			$alt = 'alt="' . $this->view->escape ( $attribs ['alt'] ) . '" ';
		}
		
		if (isset ( $attribs ['title'] )) {
			$title = 'title="' . $this->view->escape ( $attribs ['title'] ) . '" ';
		} else {
			$title = 'title="' . $this->view->escape ( $attribs ['alt'] ) . '" ';
		}
		
		if (isset ( $attribs ['map'] )) {
			$map = 'usemap="#' . $this->view->escape ( $attribs ['map'] ) . '" ';
		}
		
		if (isset ( $attribs ['class'] )) {
			$class = 'class="' . $this->view->escape ( $attribs ['class'] ) . '" ';
		}
		$this->_attribs = $alt . $title . $map . $class;
		return $this;
	}
	
	/**
	 * Set specific action your image. e.g. resize image, crop, etc...
	 *
	 * @param string $action
	 * @return boolean
	 */
	protected function _setAction() {
		
		// dir to where you want to save the thumbnail image
		$relativePath = dirname ( $this->getImagePath () ) . '/thumbs/';
	
		$dir = PUBLIC_PATH . '/' . $relativePath;
		
		
		clearstatcache ();
		
		// create the directory if it does not exist
		if (! is_dir ( $dir )) {
			if(@mkdir ( $dir ) === false){
				Shineisp_Commons_Utilities::log($dir . " cannot be created.");
			}
		}
		
		// name of the image based on the size of the thumbnail
		// @todo the sizes can be in config file/database. for not its hard coded
		$newFileName = $this->width . 'x' . $this->height . '_' . $this->getImageName ();
		$thumbPath = $dir . $newFileName;
		
		// if thumbnail exists then set cache image and return false
		if (file_exists ( $thumbPath )) {
			$this->setNewImage ( $relativePath . $newFileName );
			return false;
		}

		// if image product not exists set the default image
		if (!file_exists ( PUBLIC_PATH . $this->getImagePath () )) {
			$this->setNewImage ( "/media/products/default.png" );
		}
		
		// resize image
		$image = new Shineisp_Commons_Image ( );
		
		// open original image to resize it
		// set the thumnail sizes
		// set new image path and quality
		$image->open ( PUBLIC_PATH . $this->getImagePath () )->resize ( $this->width, $this->height )->save ( $thumbPath, 75 );
		
		// pass new image details to image view helper
		$this->setNewImage ( $relativePath . $newFileName, $image->getWidth (), $image->getHeight () );
		return true;
	}
}