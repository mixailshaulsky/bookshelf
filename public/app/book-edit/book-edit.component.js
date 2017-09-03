'use strict';

angular.
module('bookEdit').
component('bookEdit', {
    templateUrl: 'book-edit/book-edit.template.html',
    controller: ['$routeParams', 'Book', '$location',
        function BookEditController($routeParams, Book, $location) {
            var self = this;
            this.saving = false;

            if ($routeParams.bookId) {
                self.book = Book.get({bookId: $routeParams.bookId}, function (book) {
                }, this.errorHandler);
            }

            this.update = function() {
                self.saving = true;
                Book.update({bookId: $routeParams.bookId}, self.book).$promise.then(function(response) {
                    self.uploadCover(response.book_id)
                }, this.errorHandler);
            };

            this.create = function() {
                self.saving = true;
                Book.create({}, self.book).$promise.then(function(response) {
                    self.uploadCover(response.book_id)
                }, this.errorHandler);
            };

            this.uploadCover = function(bookId) {
                if (!self.bookCover || !bookId) self.backToBookDetail(bookId);
                var formData = new FormData();
                formData.append('cover', self.bookCover[0]);
                Book.uploadImage({bookId: bookId}, formData, function(response) {
                    self.backToBookDetail(bookId);
                }, self.errorHandler)
            };

            this.backToBookDetail = function(bookId) {
                $location.path('/book/' + bookId);
            };

            this.arrayToString = function(array) {
                return array ? array.join(', '): '';
            };

            this.getClass = function (path) {
                return ($location.path().substr(0, path.length) === path) ? 'active' : '';
            };

            this.errorHandler = function(reason) {
                self.error = 'Ошибка ' + reason.status + ' ' + reason.statusText + ' ' + reason.data.error;
                self.invalid = reason.data;
                self.saving = false;
                return false;
            }
        }
    ]
});
