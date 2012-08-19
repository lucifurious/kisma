<?php
/**
 * HttpResponse.php
 */
namespace Kisma\Core\Interfaces;
/**
 * HttpResponse
 * An interface defining all of the currently known HTTP v1.1 response codes
 */
interface HttpResponse
{
	//*************************************************************************
	//* Class Constants
	//*************************************************************************

	/**
	 * Success/Status (2xx)
	 *************************************************************************/
	/**
	 * @var int
	 */
	const Ok = 200;
	/**
	 * @var int
	 */
	const Created = 201;
	/**
	 * @var int
	 */
	const Accepted = 202;
	/**
	 * @var int
	 */
	const NonAuthoritativeInformation = 203;
	/**
	 * @var int
	 */
	const NoContent = 204;
	/**
	 * @var int
	 */
	const ResetContent = 205;
	/**
	 * @var int
	 */
	const PartialContent = 206;

	/**
	 * Redirection (3xx)
	 *************************************************************************/
	/**
	 * @var int
	 */
	const MultipleChoices = 300;
	/**
	 * @var int
	 */
	const MovedPermanently = 301;
	/**
	 * @var int
	 */
	const Found = 302;
	/**
	 * @var int
	 */
	const SeeOther = 303;
	/**
	 * @var int
	 */
	const NotModified = 304;
	/**
	 * @var int
	 */
	const UseProxy = 305;
	/**
	 * @var int
	 */
	const TemporaryRedirect = 307;

	/**
	 * Client Errors (4xx)
	 *************************************************************************/
	/**
	 * @var int
	 */
	const BadRequest = 400;
	/**
	 * @var int
	 */
	const Unauthorized = 401;
	/**
	 * @var int
	 */
	const PaymentRequired = 402;
	/**
	 * @var int
	 */
	const Forbidden = 403;
	/**
	 * @var int
	 */
	const NotFound = 404;
	/**
	 * @var int
	 */
	const MethodNotAllowed = 405;
	/**
	 * @var int
	 */
	const NotAcceptable = 406;
	/**
	 * @var int
	 */
	const ProxyAuthenticationRequired = 407;
	/**
	 * @var int
	 */
	const RequestTimeout = 408;
	/**
	 * @var int
	 */
	const Conflict = 409;
	/**
	 * @var int
	 */
	const Gone = 410;
	/**
	 * @var int
	 */
	const LengthRequired = 411;
	/**
	 * @var int
	 */
	const PreconditionFailed = 412;
	/**
	 * @var int
	 */
	const RequestEntityTooLarge = 413;
	/**
	 * @var int
	 */
	const RequestUriTooLong = 414;
	/**
	 * @var int
	 */
	const UnsupportedMediaType = 415;
	/**
	 * @var int
	 */
	const RequestedRangeNotSatisfiable = 416;
	/**
	 * @var int
	 */
	const ExpectationFailed = 417;

	/***
	 * Server Errors (5xx)
	 *************************************************************************/
	/**
	 * @var int
	 */
	const InternalServerError = 500;
	/**
	 * @var int
	 */
	const NotImplemented = 501;
	/**
	 * @var int
	 */
	const BadGateway = 502;
	/**
	 * @var int
	 */
	const ServiceUnavailable = 503;
	/**
	 * @var int
	 */
	const GatewayTimeout = 504;
	/**
	 * @var int
	 */
	const HttpVersionNotSupported = 505;
}
