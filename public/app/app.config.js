'use strict';

angular.
    module('bookshelfApp').
    constant('config', {
        apiUrl : '/api/v1',
        booksListLimit: 10
    }).
    config(['$routeProvider',
        function config($routeProvider) {
            $routeProvider.
                when('/books/:page?/:query?/:order?', {
                    template: '<book-list></book-list>'
                }).
                when('/book/create/', {
                    template: '<book-edit></book-edit>'
                }).
                when('/book/:bookId', {
                    template: '<book-detail></book-detail>'
                }).
                when('/book/edit/:bookId', {
                    template: '<book-edit></book-edit>'
                }).
                when('/book/delete/:bookId', {
                    template: '<book-delete></book-delete>'
                }).
                otherwise('/books/');
        }
    ]);
