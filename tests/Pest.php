<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses()
    ->beforeEach(function () {
        $this->message = new \Borsch\Http\Message();
    })
    ->in('Unit/MessageTest.php');

uses()
    ->beforeEach(function () {
        $this->request = new \Borsch\Http\Request(
            'GET',
            new \Borsch\Http\Uri('https://example.com'),
            '1.1',
            null,
            ['Host' => ['example.com'], 'User-Agent' => ['Borsch']]
        );
    })
    ->in('Unit/RequestTest.php');

uses()
    ->beforeEach(function () {
        $this->server_request = new \Borsch\Http\ServerRequest(
            'GET',
            new \Borsch\Http\Uri('https://example.com')
        );
    })
    ->in('Unit/ServerRequestTest.php');

uses()
    ->beforeEach(function () {
        $this->uri = new \Borsch\Http\Uri('https://example.com:8080/path?query=value#fragment');
    })
    ->in('Unit/UriTest.php');

uses()
    ->beforeEach(function () {
        $stream = new \Borsch\Http\Stream();
        $stream->write('Borsch');
        $stream->rewind();
        $this->response = new \Borsch\Http\Response(
            200,
            '',
            $stream,
            ['Host' => ['example.com'], 'User-Agent' => ['Borsch']]
        );
    })
    ->in('Unit/ResponseTest.php');

uses()
    ->beforeEach(function () {
        $this->stream = new \Borsch\Http\Stream();
    })
    ->in('Unit/StreamTest.php');

uses()
    ->beforeEach(function () {
        $stream = new \Borsch\Http\Stream(__DIR__.'/Assets/uploaded_file', 'r');
        $this->uploaded_file = new \Borsch\Http\UploadedFile(
            $stream,
            $stream->getSize(),
            UPLOAD_ERR_OK,
            'uploaded_file',
            'plain/text'
        );
    })
    ->afterEach(function () {
        $uploaded_file = __DIR__.'/Unit/uploaded_file.txt';
        $new_uploaded_file = __DIR__.'/Unit/new_uploaded_file.txt';
        if (file_exists($new_uploaded_file)) {
            unlink(__DIR__.'/Unit/new_uploaded_file.txt');
        }
        if (file_exists($uploaded_file)) {
            unlink(__DIR__.'/Unit/uploaded_file.txt');
        };
    })
    ->in('Unit/UploadedFileTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\ServerRequestFactory();
    })
    ->in('Unit/ServerRequestFactoryTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\RequestFactory();
    })
    ->in('Unit/RequestFactoryTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\ResponseFactory();
    })
    ->in('Unit/ResponseFactoryTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\StreamFactory();
    })
    ->in('Unit/StreamFactoryTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\UriFactory();
    })
    ->in('Unit/UriFactoryTest.php');

uses()
    ->beforeEach(function () {
        $this->factory = new \Borsch\Http\Factory\UploadedFileFactory();
    })
    ->in('Unit/UploadedFileFactoryTest.php');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/
