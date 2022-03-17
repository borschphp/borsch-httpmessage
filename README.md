# Borsch - HTTP Message

A simple PSR-7 and PSR-17 implementation.

This package is part of the Borsch Framework.

## Installation

Via [composer](https://getcomposer.org/) :

`composer require borschphp/httpmessage`

## Usage

Use the Factories to create PSR-7 instances:

```php
$request = (new RequestFactory())->createRequest('GET', 'https://example.com');

$response = (new ResponseFactory())->createResponse(200, 'OK');
$response->getBody()->write('<h1>Hello world !</h1>')
```

The package also includes custom responses to improve usability and readiness:

- EmptyResponse
- TextResponse
- HtmlResponse
- JsonResponse
- XMLResponse
- RedirectResponse

```php
$my_json_data = ['foo' => 'bar', 'baz' => 42];

// instead of
$response = (new ResponseFactory())
    ->createResponse(200)
    ->withHeader('Content-Type', 'application/json');

$response->getBody()->write(json_encode($my_json_data));

return $response;

// simply do
return new JsonResponse($my_json_data);
```

## License

The package is licensed under the MIT license. See [License File](https://github.com/borschphp/borsch-httpmessage/blob/master/LICENSE.md) for more information.