<?php

namespace Enovision\Slim\Http\Middleware;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;
use Enovision\Slim\Http\Helper\Postman as Postman;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Class to control your request and response input
 * with validation and sanitization
 *
 * See Interface for usage sample
 *
 * @class Input
 * @author J.J. van de Merwe
 *
 */
class Input implements InputInterface {
	/**
	 * @var array
	 */
	protected $inputRules = [];

	/**
	 * @var array
	 */
	protected $ignore = [];
	/**
	 * @var string
	 */
	protected $prefix;
	/**
	 * @var string
	 */
	protected $callbackKey;
	/**
	 * @var array
	 */
	private $messages;

	/**
	 * Input constructor.
	 *
	 * @param array $inputRules
	 * @param array $ignore
	 * @param string $prefix
	 * @param string $callbackKey
	 */
	public function __construct( array $inputRules, $ignore = [], $prefix = 'input.', $callbackKey = 'callback' ) {
		$this->inputRules = $inputRules;
		$this->ignore = $ignore;
		$this->prefix = $prefix;
		$this->callbackKey = $callbackKey;

		$this->messages = [
			'missing' => _( 'Parameter missing' )
		];
	}

	/**
	 * @param $request
	 * @param $response
	 * @param $next
	 *
	 * @return mixed
	 */
	public function __invoke( Request $request, Response $response, $next ) {
		if ( ! $next ) {
			return $response;
		}

		$postman = $request->getAttribute( $this->prefix . 'Postman' );

		if ( $postman === null ) {
			$postman = new Postman( $request, $this->callbackKey );
		}

		$postman->setInputRules( $this->inputRules );

		$postman = $this->queryInput( $request, $postman );

		$request = $request->withAttribute( $this->prefix . 'Postman', $postman );

		return $next( $request, $response );
	}

	/**
     * {@inheritdoc}
     */
	public function queryInput( Request $request, Postman $postman ) {

		foreach ( $postman->getInputRules() as $idx => $rule ) {

			$result = null;
			$sanitizer = FILTER_SANITIZE_STRING;
			$validator = null;
			$default = null;

			if ( is_array( $rule ) ) {
				$default = array_key_exists( 'default', $rule ) ? $rule['default'] : $default;
				$validator = array_key_exists( 'validator', $rule ) ? $rule['validator'] : $validator;
				$sanitizer = array_key_exists( 'sanitizer', $rule ) ? $rule['sanitizer'] : $sanitizer;
			} else {
				$default = $rule;
				$sanitizer = $this->getSanitizer( $rule );
			}

			// get the input variables
			if ( $request->isGet() ) {
				$result = $request->getQueryParam( $idx );
			} elseif ( $request->isPost() ) {
				$result = $request->getParsedBodyParam( $idx );
			}

			$result = $result === null ? $default : $result;

			// Process the result
			if ( v::nullType()->validate( $result ) && $default === null ) {
				$postman->setError( true );
				$postman->pushError( [
					'error' => $this->messages['missing'],
					'param' => $idx,
					'input' => null
				] );
			} else {

				// clean the input value, see also $rule['sanitizer']
				$sanitized = filter_var( $result, $sanitizer );

				// validation if required, based on the rule['validator']
				$valid = true;
				$message = '';

				if ( $validator !== null ) {

					try {
						$validator->check( $sanitized );
					} catch ( ValidationException $e ) {
						$valid = false;
						$message = $e->getMainMessage();
					}

				}

				if ( $valid ) {
					$postman->addInputValue( $idx, $sanitized );
					$postman->addRawInputValue( $idx, $result );
				} else {
					$postman->setError( true );
					$postman->pushError( [
						'param' => $idx,
						'error' => $message,
						'input' => $sanitized
					] );
				}
			}
		}

		$postman->applyIgnore( $this->ignore );

		return $postman;
	}

	/**
     * {@inheritdoc}
     */
	public function getSanitizer( $val ) {
		return is_bool( $val ) ? null : FILTER_SANITIZE_STRING;
	}
}
