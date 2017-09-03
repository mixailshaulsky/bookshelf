'use strict';

angular.
module('bookDelete').
component('bookDelete', {
    templateUrl: 'book-delete/book-delete.template.html',
    controller: ['$routeParams', 'Book',
        function BookDetailController($routeParams, Book) {
            var self = this;
            this.book = Book.delete({bookId: $routeParams.bookId}, function(response) {
                self.error = response.error;
                self.success = response.success;
            }, function(reason) {
                self.error = 'Ошибка ' + reason.status;
            });
        }
    ]
});
