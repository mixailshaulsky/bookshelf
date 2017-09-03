'use strict';


// Ajax spinner directive
angular.
    module('bookshelfApp').
        directive('ifLoading', ['$http', function($http) {
            return {
                restrict: 'A',
                link: function(scope, elem) {
                    scope.isLoading = isLoading;

                    scope.$watch(scope.isLoading, toggleElement);

                    function toggleElement(loading) {
                        if (loading) {
                            elem.show();
                        } else {
                            elem.hide();
                        }
                    }
                    function isLoading() {
                        return $http.pendingRequests.length > 0;
                    }
                }
            };
        }]).
    // File input read directive
    directive('fileInput', ['$parse', function ($parse) {
        return {
            restrict: 'A',
            link: function (scope, element, attributes) {
                element.bind('change', function () {
                    $parse(attributes.fileInput)
                        .assign(scope,element[0].files);
                    scope.$apply()
                });
            }
        };
    }]);