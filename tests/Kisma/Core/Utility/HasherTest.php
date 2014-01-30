<?php
/**
 * This file is part of the DreamFactory Services Platform(tm) (DSP)
 *
 * DreamFactory Services Platform(tm) <http://github.com/dreamfactorysoftware/dsp-core>
 * Copyright 2012-2013 DreamFactory Software, Inc. <developer-support@dreamfactory.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
namespace Kisma\Core\Utility;

/**
 * HasherTest
 * Tests methods in the Hasher class
 *
 * @package Kisma\Core\Utility
 */
class HasherTest extends \PHPUnit_Framework_TestCase
{

	public function testGenerateUnique()
	{
		\Kisma::set( 'debug.kisma.core.utility.hasher::generate_unique', true );

		$_hash1 = Hasher::generateUnique();
		$_hash2 = Hasher::generateUnique( 'someemailaddress@somewhere.com' );

		$_hash3 = Hasher::generateUnique();
		$_hash4 = Hasher::generateUnique( 'someemailaddress@somewhere.com' );

		$this->assertTrue( $_hash1 != $_hash3 && $_hash2 != $_hash4 );
	}
}
