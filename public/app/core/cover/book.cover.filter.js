'use strict';

angular.
module('core').
filter('bookCover', ['config', function(config) {
    return function(input, bookId, apiUrl) {
        return input ? config.apiUrl + '/book/' + bookId + '/cover/' : 'default-cover.jpg';
    };
}]);