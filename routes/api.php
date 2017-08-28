<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
$app->group(['prefix' => 'api/v1'], function() use($app) {
    $app->get('/books', 'BookshelfController@getBooks');
    $app->get('/book/{id}', 'BookshelfController@getBook');
    $app->get('/book/{id}/cover', 'BookshelfController@getBookCover');

    $app->post('/book', 'BookshelfController@addBook');
    $app->post('/book/{id}/cover', 'BookshelfController@addBookCover');
    $app->put('/book/{id}', 'BookshelfController@updateBook');
    $app->delete('/book/{id}', 'BookshelfController@deleteBook');
});