Input handling from a POST or GET request is very important, as is the validation and sanitation of the input received.

```
use App\Controllers\Shared\Parameters\iParameters;

use Respect\Validation\Rules;
use Enovision\Slim\Input\Middleware\Input;
use Enovision\Slim\Jsonp\Middleware\Jsonp;
use App\Helpers\Util;

$app->post('/parameters/headers', function ($request, $response, $args) {
	$postman = $request->getAttribute('input.Postman');

    if ($postman->hasErrors()) {
        $feedback = $postman->Error();
    } else {
        $feedback = $this->iParameters->loadParameterHeaders($request, $response);
    }
    
    Util::Response($feedback, $response);

})->add(new Input([
        'search' => false,
        'userId' => [],
        'language' => 'DE',
        'age' => [
            'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
            'default' => 23
        ],
        'start' => 0,
        'limit' => 50
    ])
)->add(new Jsonp);
```

### Input Middleware

The `Input` middleware can be found in folder `\Enovision\Slim\Input\Middleware`. This middleware takes care of all the GET and POST requests. 

It takes care of:
* error handling in case some parameters are incorrect or missing
* sanitizing the input received
* default values option, in case parameters are missing but should not be a showstopper
* It works with group routing

#### The Input array handling the expected input

Sample:
```
[
   'search' => false,
   'language' => 'DE',
   'userId' => [],
   'length' => [
     'validator' = new Rules\AllOf(
         new Rules\Between(5, 55)
     ) 
   ],
   'age' => [
      'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
      'default' => 23
   ],
   'start' => 0,
   'limit' => 50
]
```
The parameter key tag (like: 'search') is the GET or POST key that you are expecting. You can have on this tag an *array* or a *value* attached. 

In case of a *value*, it will be treated as the *default* value for this parameter.

In case of an *array* you can have the following options:

* validator
* sanitizer
* default

##### Required values

When a value is required, but has no default value, you just add it as following:

```
'userId' => [],
```
In that case it will execute the default sanitation, but no validation of the input.

Same thing, but now with different sanitation and some validation:
```
'userId' => [
    'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
    'validator' = new Rules\AllOf(
         new Rules\Between(2000, 5000)
     )
]
```
This gives you all the other options as well. By just leaving out the 'default' tag, it becomes automatically a required value.

##### Validation

Validation rules are based on the [Respect Validation](https://github.com/Respect/Validation) composer requirement. The documentation on how to apply the rules you can find here: [link](https://github.com/Respect/Validation/blob/master/docs/CONCRETE_API.md).

##### Sanitizer

Sanitizing of the input is done by default, if you don't want to have any sanitizing, you can set the 'sanitizer' to `null`, but that is not recommended.

The default sanitizer is: FILTER_SANITIZE_STRING, but you can overrule this by using your own sanitizer. The sanitizer constants can be found here: [link](http://php.net/manual/en/filter.filters.sanitize.php).

##### Default

When you expect a value from your POST or GET, but it is not received, you can simply add a default value to continue processing. Remember however that this value can't be 'null'
(without quotes). That is treated as a not existing value.

### What happens when the validation fails?

In case the validation fails, it will return an array with the following information:

```
{
	"success": false,
	"message": "Parameter Error",
	"errors": [{
		"param": "length",
		"error": "\"10\" must be greater than or equal to 15",
		"input": "10"
	}, {
		"error": "Parameter missing",
		"param": "callback",
		"input": null
	}],
	"good": {
		"search": false,
		"language": "DE",
		"age": "23",
		"start": "0",
		"limit": "50"
	}
}
```
In the sample above the following validation rules have been applied for *length*:

```
'length' => [
  'validator' => new Rules\AllOf(
      new Rules\Min(15),
      new Rules\Max(55)
  )
]
```
The *search* was not in the parameter send, but still it is in the *good* section with the value `false`. That is because boolean values are not sanitized when having `true` or `false` as value. Otherwise it will
be sanitized as `""` for `false` and `"1"`  for `true`.

### The Postman attribute

This package is creating an attribute in the $request object named 'input.Postman'. It is possible that it has a different prefix, if you have defined this when defining the Input middleware.

You can get access to this 'Postman' object in the following way:
```
$postman = $request->getAttribute('input.Postman');
```
After this you have access to some interesting methods:

```
$result = $postman->hasErrors();  // are errors found in the input? (boolean)
$result = $postman->Error();      // returns an array with information about the error(s)
$result = $postman->getErrors();  // returns an array with only the error(s)
$result = $postman->getInputRules(); // returns an array with the used input rules
$result = $postman->getInputValues(); // returns an array with the sanitized and validated input values
$result = $postman->getRawInputValues(); // returns an array with the unsanitized and unvalidated input values
$result = $postman->getRawInputValue($key); // returns a certain unsanitized/validated input value
```
#### How to access input values:
```
// Let's assume that you have input values named 'customer' and 'name'

$customer = $postman->customer;
$name = $postman->name;
```


### Group routing

```
use App\Controllers\Shared\Parameters\iParameters;

use Respect\Validation\Rules;
use Enovision\Slim\Input\Middleware\Input;
use Enovision\Slim\Jsonp\Middleware\Jsonp;
use Enovision\Slim\Jsonp
use App\Helpers\Util;

$app->get('/prefix', function() {

	$this->post('/parameters/headers', function ($request, $response, $args) {
		$postman = $request->getAttribute('input.Postman');

    	if ($postman->hasErrors()) {
	        $feedback = $postman->Error();
	    } else {
    	    $feedback = $this->iParameters->loadParameterHeaders($request, $response);
    	}
    
    	Util::Response($feedback, $response);

	})->add(new Input([
     	   'search' => false,
           'userId' => [],
           'language' => 'DE',
           'age' => [
              'sanitizer' => FILTER_SANITIZE_NUMBER_INT,
           	  'default' => 23
       	   ]
   ]));
   
   /**
    * Ignoring input values
    */
   
   $this->post('/showinfo', function ($request, $response, $args) {
		$postman = $request->getAttribute('input.Postman');

    	if ($postman->hasErrors()) {
	        $feedback = $postman->Error();
	    } else {
    	    $feedback = $this->iParameters->showInfo($request, $response);
    	}
    
    	Util::Response($feedback, $response);

	})->add(new Input([
       'parameter' => 'something'
   ], ['start', 'limit']));
   
   /**
    * Overruling input values
    */
    
   $this->post('/overruleMe', function ($request, $response, $args) {
		$postman = $request->getAttribute('input.Postman');

    	if ($postman->hasErrors()) {
	        $feedback = $postman->Error();
	    } else {
    	    $feedback = $this->iParameters->showInfo($request, $response);
    	}
    
    	Util::Response($feedback, $response);

	})->add(new Input([
       'parameter' => 'something',
       'start' => 25,
       'limit' => 100
   ])); 

})->add( new Input( [
	'start' => 0,
	'limit' => 50
  ] ) )
  ->add(new Jsonp);
```

As you can see in the sample above, the Input middleware is applied on both group as individual level. This is working cumulative. That is that the input rules on group level are applied, but not overwriting existing (applyIf), to all the routes within that group.

#### Ignoring input values
In the '/showInfo' you see an additional array added to the Input definition. These values are processed as an ignore list. 
These values are, when found, removed from this route. Even when they are defined on group level. 
So in this example, the route will not expect the 'start' and 'limit' 

#### Overruling input values
! When the same input rule is both on group and individual level, then the individual rule will overrule the group rule.

### Passing the postman to a controller
```
use Enovision\Slim\Input\Helper\Postman;
use App\Model\CustomerOrders as Orders;

class Controller
{
    protected $postman;

    public function __construct($container, $request)
    {
        $this->postman = $container->getAttribute('input.Postman');
        ...
    }
    
    public function showOrderInfo($order) {
    	$customer = $this->postman->customer;
        
        $order = Orders->getOrder($customer, $order);
        
        return $order;
    }
    
    ...
}    

```
