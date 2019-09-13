<?php

namespace Enovision\Slim\Http\Helper;

class Postman {
	/**
	 * Array with the sanitized and validated input
	 * @var array
	 */
	private $inputValues = [];
	/**
	 * Array with the input rules from the route definition
	 * @var array
	 */
	private $inputRules = [];
	/**
	 * Boolean containing 'false' or 'true' depending on if the
	 * input has errors or not
	 * @var bool
	 */
	private $hasErrors = false;
	/**
	 * Array containing information about the errors found
	 *
	 * <pre>
	 * [
	 *	 'error' => 'Parameter missing',
	 *	 'param' => 'customerId',
	 *	 'input' => null
	 * ]
	 * </pre>
	 * @var array
	 */
	private $errors = [];
	/**
	 * Not used yet
	 * @var array|null
	 */
	private $loggedInUser = null;
	/**
	 * Not used yet
	 * @var null
	 */
	private $token = null;
	/**
	 * Contains a 'false' or 'true' depending on if the
	 * JSONP attribute is in the $request
	 * @var bool
	 */
	private $JSONP = false;
	/**
	 * This contains the callback key string value that is in the $request
	 * @var null|string
	 */
	private $callbackKey = null;
	/**
	 * Array containing the unsanitized and validated input values
	 * @var array
	 */
	private $rawInputValues = [];
	/**
	 * @var array
	 * Array containing the input values that have to be ignored
	 */
	private $ignored = [];

	/**
	 * Postman constructor.
	 *
	 * @param $request
	 * @param string $callbackKey
	 */
	function __construct( $request, $callbackKey = 'callback' ) {
		$this->callbackKey = $callbackKey;
		$this->loggedInUser = $this->inputValues;
		$this->setJSONP( $request->getAttribute( 'JSONP' ) );
	}

	/**
	 * @param $property
	 *
	 * @return mixed|null
	 */
	public function __get( $property ) {
		return isset( $this->inputValues[ $property ] ) ? $this->inputValues[ $property ] : null;
	}

	/* Setters */

	/**
	 * {@inheritdoc}
	 */
	public function setError( $state = false ) {
		$this->hasErrors = $state;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setInputValues( $values = [] ) {
		$this->inputValues = $values;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setErrors( $errors = [] ) {
		$this->errors = $errors;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setJSONP( $state = false ) {
		if ( is_string( $state ) ) {
			$this->JSONP = is_string( $state );
			$this->setCallbackKey( $state );
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function setInputRules( $rules = [] ) {
		$this->inputRules[] = $rules;
	}

	/**
	 * {@inheritdoc}
	 */
	public function setInputRule( $key, $value ) {
		$count = count( $this->inputRules );
		if ( $count === 0 ) {
			return;
		}

		$this->inputRules[ $count - 1 ][ $key ] = $value;

	}

	/**
	 * {@inheritdoc}
	 */
	public function setCallbackKey( $key = '' ) {
		$this->callbackKey = $key;
	}

	/* Getters */

	/**
	 * {@inheritdoc}
	 */
	public function getErrors() {
		return $this->errors;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInputValues() {
		return $this->inputValues;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRawInputValues() {
		return $this->rawInputValues;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getRaw( $property ) {
		return isset( $this->rawInputValues[ $property ] ) ? $this->rawInputValues[ $property ] : null;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getError() {
		return $this->hasErrors;
	}

	/**
	 * {@inheritdoc}
	 */
	public function hasErrors() {
		return $this->getError();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getJSONP() {
		return $this->JSONP;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getInputRules( $idx = null ) {
		$ir = $this->inputRules;
		$count = count( $ir );

		if ( $count === 0 ) {
			return [];
		}

		$idx = $idx === null ? $count - 1 : $idx;

		$rules = isset( $ir[ $idx ] ) ? $ir[ $idx ] : [];

		if ( count( $rules ) > 0 && $this->getJSONP() && $this->hasCallbackKey( $rules ) === false ) {

			foreach ( $ir as $ix => $rule ) {
				$callbackKey = $this->hasCallBackKey( $rule );
				if ( $callbackKey !== false ) {
					$ir[ $idx ][ $callbackKey[0] ] = $callbackKey[1];
					break;
				}
			}

			if ( $this->hasCallbackKey( $ir[ $idx ] ) === false ) {
				$ir[ $idx ][ $this->getCallbackKey() ] = [];
			}

		}

		return $ir[ $idx ];
	}

	/**
	 * {@inheritdoc}
	 */
	private function hasCallbackKey( $rules ) {
		return array_key_exists( $this->callbackKey, $rules ) ? [
			$this->callbackKey,
			$rules[ $this->callbackKey ]
		] : false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getCallbackKey() {
		return $this->callbackKey;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getAll() {
		return [
			'errors'         => $this->getErrors(),
			'inputValues'    => $this->getInputValues(),
			'hasErrors'      => $this->getError(),
			'JSONP'          => $this->getJSONP(),
			'rawInputValues' => $this->getRawInputValues()
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function pushError( $error ) {
		$key = array_search( $error['param'], array_column( $this->errors, 'param' ) );
		if ( $key === false ) {
			$this->errors[] = $error;
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function addInputValue( $idx, $value ) {
		$this->inputValues[ $idx ] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function addRawInputValue( $idx, $value ) {
		$this->rawInputValues[ $idx ] = $value;
	}

	/**
	 * {@inheritdoc}
	 */
	public function applyIgnore( $ignore = [] ) {
		$inputValues = $this->getInputValues();

		$this->ignored = array_merge($this->ignored, $ignore);

		foreach ( $this->ignored as $ignoreMe ) {
			if ( array_key_exists( $ignoreMe, $inputValues ) ) {
				unset( $inputValues[ $ignoreMe ] );
			}
		}

		$this->setInputValues($inputValues);
	}

	/**
	 * {@inheritdoc}
	 */
	public function Error( $message = null ) {
		$message = $message === null ? _( 'HTTP Input Error' ) : $message;

		return [
			'success' => false,
			'message' => $message,
			'errors'  => $this->getErrors(),
			'good'    => $this->getInputValues(),
			'ignored' => $this->ignored
		];
	}
}
