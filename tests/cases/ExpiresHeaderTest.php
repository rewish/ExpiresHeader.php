<?php
require_once dirname(__FILE__) . '/../../ExpiresHeader.php';

class ExpiresHeaderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @var ExpiresHeader
	 */
	public $obj;

	public function setUp()
	{
		$this->obj = new ExpiresHeaderMock;
	}

	public function test__construct()
	{
		putenv('SCRIPT_NAME=' . __FILE__);
		putenv('REQUEST_URI=' . __FILE__);
		try {
			new ExpiresHeader;
			$this->fail();
		} catch(ExpiresHeaderException $e) {
			$this->assertSame($e->getMessage(), 'Bad Request');
			$this->assertSame($e->getCode(), 400);
		}
		putenv('SCRIPT_NAME');
		putenv('REQUEST_URI');
	}

	public function testSetFilePath()
	{
		try {
			$this->obj->setFilePath('./NotFound');
			$this->fail();
		} catch(ExpiresHeaderException $e) {
			$this->assertSame($e->getMessage(), 'Not Found');
			$this->assertSame($e->getCode(), 404);
		}
		$this->assertNull($this->obj->_filePath);
		$this->assertSame($this->obj, $this->obj->setFilePath(TEST_IMAGE_PATH));
		$this->assertSame($this->obj->_filePath, TEST_IMAGE_PATH);
	}

	public function testSetConfig()
	{
		$config = array(
			'days' => 10,
			'gzip' => false
		);
		$this->assertNotSame($this->obj->_config, $config);
		$this->assertSame($this->obj, $this->obj->setConfig($config));
		$this->assertSame($this->obj->_config, $config);
	}

	public function testSetMimeType()
	{
		$ext  = 'test_ext';
		$type = 'foo';
		$this->assertArrayNotHasKey($ext, $this->obj->_mimeTypes);
		$this->assertSame($this->obj, $this->obj->setMimeType($ext, $type));
		$this->assertArrayHasKey($ext, $this->obj->_mimeTypes);
		$this->assertSame($this->obj->_mimeTypes[$ext], $type);
	}

	public function testPathToMimeType()
	{
		$ext = 'png';
		$this->assertSame($this->obj->_mimeTypes[$ext], $this->obj->pathToMimeType(TEST_IMAGE_PATH));
	}

	public function testDisplay()
	{
		$this->obj->setFilePath(TEST_IMAGE_PATH)->display();
		$this->assertSame(ob_get_clean(), file_get_contents(TEST_IMAGE_PATH));
	}

	public function testAddHeader()
	{
		try {
			$this->obj->addHeader();
			$this->fail();
		} catch (ExpiresHeaderException $e) {
			$this->assertSame($e->getMessage(), 'Forbidden');
			$this->assertSame($e->getCode(), 403);
		}
		$this->obj->setFilePath(TEST_IMAGE_PATH)->addHeader();
		$this->markTestIncomplete('Header test');
	}
}

class ExpiresHeaderMock extends ExpiresHeader
{
	public function __get($name)
	{
		return $this->{$name};
	}
}