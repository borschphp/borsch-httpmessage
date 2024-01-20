# Borsch - HTTP Message

A simple PSR-7 and PSR-17 implementation.  
It aims to provide an easy and efficient way to handle HTTP requests and responses.

## Installation

The package can be installed via [composer](https://getcomposer.org/). Simply run the following command:

`composer require borschphp/httpmessage`

## Usage

### PSR-7

The package provides implementations for the following PSR-7 interfaces:

- `Psr\Http\Message\RequestInterface`
- `Psr\Http\Message\ResponseInterface`
- `Psr\Http\Message\ServerRequestInterface`
- `Psr\Http\Message\StreamInterface`
- `Psr\Http\Message\UriInterface`
- `Psr\Http\Message\UploadedFileInterface`

You can use the factories provided by the package to create instances of these classes:

```php
use Borsch\Http\Factory\{
    RequestFactory,
    ResponseFactory
};

$requestFactory = new RequestFactory();
$request = $requestFactory->createRequest('GET', 'https://example.com');

$responseFactory = new ResponseFactory();
$response = $responseFactory->createResponse(200, 'OK');
$response->getBody()->write('<h1>Hello world !</h1>');
```

### PSR-17

The package also provides factories for the following PSR-17 interfaces:

- `Psr\Http\Message\RequestFactoryInterface`
- `Psr\Http\Message\ResponseFactoryInterface`
- `Psr\Http\Message\ServerRequestFactoryInterface`
- `Psr\Http\Message\StreamFactoryInterface`
- `Psr\Http\Message\UriFactoryInterface`
- `Psr\Http\Message\UploadedFileFactoryInterface`

You can use these factories to create instances of the PSR-7 classes.

### Custom Responses

The package also includes custom response classes to improve usability and readiness:

- `Borsch\Http\Response\EmptyResponse`
- `Borsch\Http\Response\TextResponse`
- `Borsch\Http\Response\HtmlResponse`
- `Borsch\Http\Response\JsonResponse`
- `Borsch\Http\Response\XmlResponse`
- `Borsch\Http\Response\RedirectResponse`

These classes are built on top of the PSR-7 implementation, and they offer a convenient way to create specific types of
responses. For example, instead of creating a response and setting the correct headers and content manually, you can use
the `JsonResponse` class to automatically set the `Content-Type` header to `application/json` and the body to the json
encoded data.

```php
use Borsch\Http\Response\JsonResponse;

$my_json_data = ['foo' => 'bar', 'baz' => 42];

return new JsonResponse($my_json_data);
```

## Tests

The package includes a set of tests (made with [Pest](https://pestphp.com/)) to ensure that everything is working as expected.  
You can run the tests by executing the following command:

```shell
./vendor/bin/pest
```

Mutation testing has also been used to create this package, with `Infection`.  
More information on [Infection page](https://infection.github.io/guide/index.html).  
To run mutation tests, install `infection` as described in the documentation, then run this command:

```shell
XDEBUG_MODE=coverage infection
```

An text based and HTML report will be generated in the project folder.

## License

The package is licensed under the MIT license. See [License File](https://github.com/borschphp/borsch-httpmessage/blob/master/LICENSE.md)
for more information.