<?php
/**
 * Kisma(tm) : PHP Nanoframework (http://github.com/Pogostick/kisma/)
 * Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 *
 * Dual licensed under the MIT License and the GNU General Public License (GPL) Version 2.
 * See {@link http://github.com/Pogostick/kisma/licensing/} for complete information.
 *
 * @copyright		Copyright 2011, Pogostick, LLC. (http://www.pogostick.com/)
 * @link			http://github.com/Pogostick/kisma/ Kisma(tm)
 * @license			http://github.com/Pogostick/kisma/licensing/
 * @author			Jerry Ablan <kisma@pogostick.com>
 * @category		Kisma\Examples\Blog
 * @package			kisma.examples.blog.models
 * @since			v1.0.0
 * @filesource
 */
namespace ExampleBlog\Document;

use Kisma\Container\CouchDb\Document;

/**
 * @Document @Table(name="kisma_examples_blog_posts")
 */
class BlogPost extends Document
{
	//*************************************************************************
	//* Public Members
	//*************************************************************************

	/** @Field(type="string") */
	public $title;

	/** @Field(type="string") */
	public $body;

	/** @Field(type="string") */
	public $author;

	/** @Field(type="string") */
	public $authorId;

	/** @Field(type="string") */
	public $postDate;

}
