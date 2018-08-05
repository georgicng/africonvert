angular
    .module('com.module.core')
    .directive('postsNavLink', function() {
        return {
            restrict: 'EA',
            templateUrl: local_env.partials + 'posts-nav-link.html',
            controller: ['$scope', '$element', '$stateParams', function( $scope, $element, $stateParams ){
                var currentPage = ( ! $stateParams.page ) ? 1 : parseInt( $stateParams.page ),
                linkPrefix = ( ! $stateParams.category ) ? 'page/' : 'category/' + $stateParams.category + '/page/';

                $scope.postsNavLink = {
                    prevLink: linkPrefix + ( currentPage - 1 ),
                    nextLink: linkPrefix + ( currentPage + 1 ),
                    sep: ( ! $element.attr('sep') ) ? '|' : $element.attr('sep'),
                    prevLabel: ( ! $element.attr('prev-label') ) ? 'Previous Page' : $element.attr('prev-label'),
                    nextLabel: ( ! $element.attr('next-label') ) ? 'Next Page' : $element.attr('next-label')
                };
            }]
        };
    });