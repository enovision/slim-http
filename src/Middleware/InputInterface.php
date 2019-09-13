<?php

namespace Enovision\Slim\Http\Middleware;

use Enovision\Slim\Http\Helper\Postman as Postman;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Sample:
 *
 * <pre>
 * $app->post('/something', function ($request, $response, $args) {
 *     $postman = $request->getAttribute('input.Postman');
 *
 *     if ($postman->hasErrors()) {
 *         // do something
 *     } else {
 *         // do something else
 *     }
 *
 * })->add(new Input([
 *         'search' => false,
 *         'userId' => [],
 *         'language' => 'DE',
 *         'age' => [
 *             'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
 *             'default' => 23
 *         ],
 *         'start' => 0,
 *         'limit' => 50
 * ]));
 * </pre>
 *
 *
 * @class Input
 * @author J.J. van de Merwe
 *
 */
interface InputInterface {

	/**
	 * @param $request
	 *
	 * Rules:
	 *
	 * array(
	 *    'name' => array(
	 *       'default' => 'something'
	 *    ),
	 *    'age' => array(
	 *        'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
	 *        'default' => 23
	 *    )
	 * )
	 *
	 */

	/**
	 * Rules:
	 *
	 * <pre>
	 * array(
	 *    'name' => array(
	 *       'default' => 'something'
	 *    ),
	 *    'age' => array(
	 *        'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
	 *        'default' => 23
	 *    )
	 * )
	 * </pre>
	 *
	 * @param Request $request
	 * @param Postman $postman
	 *
	 * @return mixed
	 */

	function queryInput( Request $request, Postman $postman );

	/**
	 * Boolean values are not sanitized !!!
	 *
	 * @param $val
	 *
	 * @return int|null
	 */
	function getSanitizer( $val );
}
