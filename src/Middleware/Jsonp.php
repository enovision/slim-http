<?php
/**
 * JSONP Middleware Class for the Slim Framework
 *
 * @author  Johan van de Merwe <j.vd.merwe@enovision.net>
 * @since  08-10-2017
 *
 * Simple class to wrap the response of the application in a JSONP callback function.
 * The class is triggered when a get parameter of callback is found
 *
 * Usage
 * ====
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Enovision\Slim\Http\Middleware\Jsonp;
 *
 */

namespace Enovision\Slim\Http\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Sample:
 *
 * <pre>
 * $app->post('/something', function ($request, $response, $args) {
 *     // do something
 * })->add(new Jsonp);
 * </pre>
 *
 * @uses Enovision\Slim\Http\Middleware
 */
class Jsonp {
	protected $callbackKey;

	/**
	 * Jsonp constructor.
	 *
	 * @param string $callbackKey
	 */
	public function __construct( $callbackKey = 'callback' ) {
		$this->callbackKey = $callbackKey;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param callable $next
	 *
	 * @return Response
	 */
	public function __invoke( Request $request, Response $response, callable $next ) {
		/*--- Before (start) ---*/

		$callback = null;

		if ( $request->isPost() ) {
			$callback = $request->getParsedBodyParam( $this->callbackKey );
		} else {
			$callback = $request->getQueryParam( $this->callbackKey );
		}

		if ( empty( $callback ) ) {
			$callback = $request->getAttribute( $this->callbackKey );
		}

		$before = htmlspecialchars( $callback ) . '(';
		$after = ')';

		if ( ! empty( $callback ) ) {
			$response->write( $before );
		}

		$request = $request->withAttribute( 'JSONP', $this->callbackKey );

		/*--- Before (end) ---*/

		$response = $next( $request, $response );

		/*--- After (start) ---*/

		if ( ! empty( $callback ) ) {
			$response->withHeader( 'Content-type', 'application/javascript' )
			         ->write( $after );
		}

		/*--- After (end) ---*/

		return $response;
	}
}
