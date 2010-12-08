<?php
/**
 * ExpiresHeader
 *
 * PHP version 5
 *
 * @author    Hiroshi Hoaki <rewish.org@gmail.com>
 * @license   http://rewish.org/license/mit MIT License
 * @link      http://rewish.org/php_mysql/expires_header_class
 * @version   0.1.0
 */
class ExpiresHeader
{
	protected $_filePath;
	protected $_config = array(
		'days' => 30,
		'gzip' => true
	);
	protected $_mimeTypes = array(
		'txt'  => 'text/plain',
		'htm'  => 'text/html',
		'html' => 'text/html',
		'css'  => 'text/css',
		'js'   => 'application/javascript',
		'json' => 'application/json',
		'xml'  => 'application/xml',
		'swf'  => 'application/x-shockwave-flash',
		'flv'  => 'video/x-flv',
		'png'  => 'image/png',
		'jpe'  => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg'  => 'image/jpeg',
		'gif'  => 'image/gif',
		'bmp'  => 'image/bmp',
		'ico'  => 'image/x-icon',
		'tiff' => 'image/tiff',
		'tif'  => 'image/tiff',
		'svg'  => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'zip'  => 'application/zip',
		'rar'  => 'application/x-rar-compressed',
		'exe'  => 'application/x-msdownload',
		'msi'  => 'application/x-msdownload',
		'cab'  => 'application/x-cab-compressed',
		'mp3'  => 'audio/mpeg',
		'qt'   => 'video/quicktime',
		'mov'  => 'video/quicktime',
		'pdf'  => 'application/pdf',
		'psd'  => 'image/vnd.adobe.photoshop',
		'ai'   => 'application/postscript',
		'eps'  => 'application/postscript',
		'ps'   => 'application/postscript',
		'doc'  => 'application/msword',
		'rtf'  => 'application/rtf',
		'xls'  => 'application/vnd.ms-excel',
		'ppt'  => 'application/vnd.ms-powerpoint',
		'odt'  => 'application/vnd.oasis.opendocument.text',
		'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	/**
	 * Constructor
	 *
	 * @param string $filePath Target file path
	 * @param array  $config Config
	 * @throws ExpiresHeaderException
	 */
	public function __construct($filePath = null, $config = array())
	{
		if (strstr(getenv('REQUEST_URI'), getenv('SCRIPT_NAME'))) {
			throw new ExpiresHeaderException('Bad Request', 400);
		}
		if (!empty($filePath)) {
			$this->setFilePath($filePath);
		}
		if (!empty($config)) {
			$this->setConfig($config);
		}
	}

	/**
	 * Set file
	 *
	 * @param string $file Target file path
	 * @throws ExpiresHeaderException
	 * @return ExpiresHeader
	 */
	public function setFilePath($filePath)
	{
		if (!file_exists($filePath)) {
			throw new ExpiresHeaderException('Not Found', 404);
		}
		$this->_filePath = $filePath;
		return $this;
	}

	/**
	 * Set config
	 *
	 * @param array $config config
	 * @return ExpiresHeader
	 */
	public function setConfig(Array $config)
	{
		$this->_config = $config + $this->_config;
		return $this;
	}

	/**
	 * Set MIME-Type
	 *
	 * @param string $ext File extension
	 * @param string $type MIME-Type
	 * @return ExpiresHeader
	 */
	public function setMimeType($ext, $type)
	{
		$this->_mimeTypes[$ext] = $type;
		return $this;
	}

	/**
	 * File path to MIME-Type
	 *
	 * @param string $filePath File path
	 * @return string MIME-Type
	 */
	public function pathToMimeType($filePath)
	{
		$ext = strtolower(array_pop(explode('.', $filePath)));
		if (isset($this->_mimeTypes[$ext])) {
			return $this->_mimeTypes[$ext];
		}
		// Default
		return 'application/octet-stream';
	}

	/**
	 * Display
	 *
	 * @return void
	 */
	public function display()
	{
		$this->addHeader();
		if ($this->_config['gzip']) {
			ob_start('ob_gzhandler');
		}
		readfile($this->_filePath);
	}

	/**
	 * Add header
	 *
	 * @throws ExpiresHeaderException
	 * @return void
	 */
	public function addHeader()
	{
		if (!$update = filemtime($this->_filePath)) {
			throw new ExpiresHeaderException('Forbidden', 403);
		}
		$term = time() + 60 * 60 * 24 * $this->_config['days'];
		header('Expires: '. gmdate('D, d M Y H:i:s', $term) .' GMT');
		header('Last-Modified: '. gmdate('D, d M Y H:i:s', $update) .' GMT');
		header('Cache-control: must-revalidate');
		header('Content-Type: '. $this->pathToMimeType($this->_filePath));
	}
}

class ExpiresHeaderException extends Exception
{
}
