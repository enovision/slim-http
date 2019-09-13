<?php

namespace Enovision\Slim\Http\Helper;

interface PostmanInterface {

	/**
	 * @param $property
	 *
	 * @return mixed|null
	 */
	function __get( $property );

	/**
	 * Set the error to a boolean value
	 *
	 * @param bool $state
	 */
	function setError( $state);

	/**
	 * Set the input values (sanitized and validated)
	 *
	 * @param array $values
	 */
	function setInputValues( array $values );

	/**
	 * Set the errors array with the error information
	 *
	 * @param array $errors
	 */
	function setErrors( array $errors );

	/**
	 * Set the JSONP
	 * It will look for a JSONP attribute in the request
	 * and if found, it will set $this->JSONP to the value of 'true'
	 * at the same time it will fill $this->callbackKey with the value
	 * from the 'JSONP' from the request
	 *
	 * @param string $state
	 *
	 * @return mixed
	 */
	function setJSONP( $state );

	/**
	 * internal use
	 *
	 * @param array $rules
	 */
	function setInputRules( array $rules );

	/**
	 * internal use
	 *
	 * @param $key
	 * @param $value
	 */
	function setInputRule( $key, $value );

	/**
	 * internal use
	 *
	 * @param string $key
	 */
	function setCallbackKey( $key );

	/**
	 * Returns an array with validation errors
	 *
	 * @return array
	 */
	function getErrors();

	/**
	 * Returns an array with the sanitized and validated $request values
	 *
	 * @return array
	 */
	function getInputValues();

	/**
	 * Returns an array with the the raw and unvalidated $request values
	 *
	 * @return array
	 */
	function getRawInputValues();

	/**
	 * Returns a raw and unsanitized value by property
	 *
	 * @param string $property
	 *
	 * @return mixed|null
	 */
	function getRaw( $property );

	/**
	 * Returns a boolean value that indicates if the request
	 * contains errors based on the given input rules
	 *
	 * @return bool
	 */
	function getError();

	/**
	 * Returns a boolean value that indicates if the request
	 * contains errors based on the given input rules
	 * (equal to the getError() function)
	 *
	 * @return bool
	 */
	function hasErrors();

	/**
	 * Returns a boolean indicating if the request requires a JSONP
	 *
	 * @return bool
	 */
	function getJSONP();

	/**
	 * Returns an array with array elements with all the used input rules
	 * For every level (group/routed) an element will be added to this array
	 *
	 * @param null $idx
	 *
	 * @return array|mixed
	 */
	function getInputRules( $idx );

	/**
	 * Returns a rule array that contains an entry with the callback key
	 * (internal use)
	 *
	 * @param $rules
	 *
	 * @return array|bool
	 */
	function hasCallbackKey( $rules );

	/**
	 * Returns the callback key string value
	 *
	 * @return null|string
	 */
	function getCallbackKey();

	/**
	 * Returns an array with all the relevant information
	 * regarding inputValues and errors
	 *
	 * @return array
	 */
	function getAll();

	/**
	 * Pushes an error to the $error array
	 * (internal use)
	 *
	 * @param $error
	 */
	function pushError( $error );

	/**
	 * Add a value to the $this->inputValues array
	 * (internal use)
	 *
	 * @param $idx
	 * @param $value
	 */
	function addInputValue( $idx, $value );

	/**
	 * Add a value to the $this->rawInputValues array
	 * (internal use)
	 *
	 * @param $idx
	 * @param $value
	 */
	function addRawInputValue( $idx, $value );

	/**
	 * Apply the ignore array to the inputValues
	 * (internal use)
	 *
	 * @param array $ignore
	 */
	function applyIgnore( array $ignore );
	/**
	 * Output array with Error information
	 *
	 * @param null $message
	 *
	 * @return array
	 */
	function Error( $message = null );
}
