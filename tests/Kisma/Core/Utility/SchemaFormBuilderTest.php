<?php
namespace Kisma\Core\Utility;

class SchemaFormBuilderTest extends \PHPUnit_Framework_TestCase
{
	protected $_schema = array();

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp()
	{
		$this->_schema = array(
			'client_id'               => array( 'type' => 'string', 'length' => 64, 'required' => true ),
			'client_secret'           => array( 'type' => 'string', 'length' => 128, 'required' => true ),
			'redirect_uri'            => array( 'type' => 'string', 'length' => 1024, 'required' => true ),
			'scope'                   => array( 'type' => 'array', 'length' => 64, 'required' => false ),
			'certificate_file'        => array( 'type' => 'string', 'length' => 1024, 'required' => false ),
			'authorize_url'           => array( 'type' => 'string', 'length' => 1024, 'required' => false ),
			'grant_type'              => array(
				'type'     => 'select',
				'required' => false,
				'default'  => 'authorization_code',
				'options'  => array( 0 => 'Option 0', 1 => 'Option 1', 2 => 'Option 2' ),
			),
			'auth_type'               => array(
				'type'     => 'select',
				'required' => false,
				'default'  => 0,
				'options'  => array( 0 => 'Option 0', 1 => 'Option 1', 2 => 'Option 2' ),
			),
			'access_type'             => array(
				'type'     => 'select',
				'required' => false,
				'default'  => 0,
				'options'  => array( 0 => 'Option 0', 1 => 'Option 1', 2 => 'Option 2' ),
			),
			'flow_type'               => array(
				'type'     => 'select',
				'required' => false,
				'default'  => 0,
				'options'  => array( 0 => 'Option 0', 1 => 'Option 1', 2 => 'Option 2' ),
			),
			'access_token_param_name' => array( 'type' => 'string', 'length' => 64, 'required' => false ),
			'auth_header_name'        => array( 'type' => 'string', 'length' => 64, 'required' => false ),
			'access_token'            => array( 'type' => 'string', 'length' => 128, 'required' => false ),
			'access_token_type'       => array( 'type' => 'int', 'required' => false ),
			'access_token_secret'     => array( 'type' => 'string', 'length' => 128, 'required' => false ),
			'access_token_expires'    => array( 'type' => 'int', 'required' => false ),
			'refresh_token'           => array( 'type' => 'string', 'length' => 64, 'required' => false ),
			'refresh_token_expires'   => array( 'type' => 'int', 'required' => false ),
		);
	}

	public function testCreate()
	{
		$_form = SchemaFormBuilder::create( $this->_schema );

		$this->assertNotEmpty( $_form );
	}
}
