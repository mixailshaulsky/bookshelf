'use strict';

angular.
    module('bookList').
    component('bookList', {
        templateUrl: 'book-list/book-list.template.html',
        controller: ['Book', '$location', '$route', '$routeParams', 'config',
            function BookListController(Book, $location, $route, $routeParams, config) {
                var self = this;
                this.pages = 1;
                this.page = $routeParams.page || 1;
                this.query = $routeParams.query;
                this.orderString = $routeParams.order || '';

                // Pagination
                this.limit = config.booksListLimit;
                this.getOffset = function() {
                    return self.limit * (self.page - 1);
                };
                this.getNextPage = function () {
                    return self.page + 1 > self.pages ? self.pages : self.page + 1;
                };
                this.getPrevPage = function () {
                    return self.page - 1 < 1 ? self.page : self.page - 1;
                };
                this.getPagesRange = function() {
                    var range = [];
                    for (var i = 1; i <= self.pages; i++) {
                        range.push(i);
                    }
                    return range;
                };

                // Core request
                this.loadBooks = function() {
                    Book.list({
                            q: this.query,
                            order: self.orderString,
                            limit: self.limit,
                            offset: self.getOffset()
                    }).$promise.then(function(response) {
                        self.books = response.books;
                        self.page = response.page || 1;
                        self.pages = response.pages || 1;
                        self.error = !self.books.length ? 'Ничего не найдено' : null;
                    }, self.errorHandler);
                };

                // Search action
                this.search = function(resetPage) {
                    resetPage = typeof resetPage !== 'undefined' ? resetPage : true;
                    if (!this.query && !this.orderString) return false;
                    self.page = resetPage ? 1 : self.page;
                    self.loadBooks();
                    self.searched = this.query;
                };

                // Initial load
                this.loadBooks();

                // Sorting stuff
                this.buildSortingString = function() {
                    var orderFields = [];
                    angular.forEach(self.booksOrder, function(orders, orderField) {
                        if (orders.asc) {
                            orderFields.push(orderField);
                        } else if (orders.desc) {
                            orderFields.push('-' + orderField);
                        }
                    });
                    return orderFields.join(',');
                };
                this.initOrder = function() {
                    var init = {author:{asc:null,desc:null},title:{asc:null,desc:null}};
                    angular.forEach(init, function(value, field) {
                        angular.forEach(value, function(state, order) {
                            init[field][order] = self.getOrderStateByFieldAndOrder(field, order)
                        });
                    });
                    return init;
                };
                this.getOrderStateByFieldAndOrder = function(field, order) {
                    var state = null;
                    var fieldIndex = self.orderString.indexOf(field);
                    if (fieldIndex === 0) {
                        state = order === 'asc';
                    } else if (fieldIndex > 0) {
                        var prefix = self.orderString.substring(fieldIndex - 1, fieldIndex);
                        state = prefix === '-' && order === 'desc' || prefix !== '-' && order === 'asc';
                    }
                    return state;
                };
                this.changeOrder = function(sortField) {
                    self.booksOrder[sortField]['asc'] = !self.booksOrder[sortField]['asc'];
                    self.booksOrder[sortField]['desc'] = !self.booksOrder[sortField]['asc'];
                    self.orderString = self.buildSortingString();
                    self.search(false);
                };
                this.booksOrder = this.initOrder();


                this.getClass = function (path) {
                    return ($location.path().substr(0, path.length) === path) ? 'active' : '';
                };

                this.reload = function() {
                    $route.reload();
                };

                this.errorHandler = function(reason) {
                    self.error = 'Ошибка ' + reason.status + ' ' + reason.statusText;
                    return false;
                }
            }
        ]
    });
