'use strict';

angular.
    module('core.book').
    factory('Book', ['$resource', 'config',
        function($resource, config) {
            return $resource(config.apiUrl + '/book', {}, {
                list: {
                    url: config.apiUrl + '/books',
                    method: 'GET',
                    params: {
                        limit: config.booksListLimit
                    }
                },
                create: {
                    method: 'POST',
                    hasBody: true
                },
                get: {
                    url: config.apiUrl + '/book/:bookId',
                    methdo: 'GET'
                },
                update: {
                    url: config.apiUrl + '/book/:bookId',
                    method: 'PUT'
                },
                delete: {
                    url: config.apiUrl + '/book/:bookId',
                    method: 'DELETE'
                },
                saveCover: {
                    method: 'POST'
                },
                uploadImage: {
                    url: config.apiUrl + '/book/:bookId/cover',
                    method: 'POST',
                    transformRequest: angular.identity,
                    headers: {'Content-Type': undefined, enctype: 'multipart/form-data'}
                }
            });
        }
    ]);
