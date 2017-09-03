'use strict';

angular.
    module('bookDetail').
    component('bookDetail', {
        templateUrl: 'book-detail/book-detail.template.html',
        controller: ['$routeParams', 'Book',
            function BookDetailController($routeParams, Book) {
                var self = this;
                self.book = Book.get({bookId: $routeParams.bookId}, function (book) {
                }, function(reason) {
                    self.error = 'Ошибка ' + reason.status + ' ' + reason.statusText;
                });
            }
        ]
    });
