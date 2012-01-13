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

use Doctrine\ODM\CouchDB\Mapping as CouchDB;

/** @Document */
class BlogPost
{
	/** @Id */
	public $id;

	/** @Field */
	public $title;

	/** @Field */
	public $body;

	/** @Field */
	public $author;

	/** @Field */
	public $authorId;

	/** @Field */
	public $postDate;

	/** @Version */
	public $version;

	/** @Attachments */
	public $attachments;

	/**
	 * @param $attachments
	 *
	 * @return BlogPost
	 */
	public function setAttachments( $attachments )
	{
		$this->attachments = $attachments;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAttachments()
	{
		return $this->attachments;
	}

	/**
	 * @param $author
	 *
	 * @return BlogPost
	 */
	public function setAuthor( $author )
	{
		$this->author = $author;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param $authorId
	 *
	 * @return BlogPost
	 */
	public function setAuthorId( $authorId )
	{
		$this->authorId = $authorId;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getAuthorId()
	{
		return $this->authorId;
	}

	/**
	 * @param $body
	 *
	 * @return BlogPost
	 */
	public function setBody( $body )
	{
		$this->body = $body;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getBody()
	{
		return $this->body;
	}

	/**
	 * @param $id
	 *
	 * @return BlogPost
	 */
	public function setId( $id )
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param $postDate
	 *
	 * @return BlogPost
	 */
	public function setPostDate( $postDate )
	{
		$this->postDate = $postDate;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPostDate()
	{
		return $this->postDate;
	}

	/**
	 * @param $title
	 *
	 * @return BlogPost
	 */
	public function setTitle( $title )
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param $version
	 *
	 * @return \ExampleBlog\Models\BlogPost
	 */
	public function setVersion( $version )
	{
		$this->version = $version;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getVersion()
	{
		return $this->version;
	}
}