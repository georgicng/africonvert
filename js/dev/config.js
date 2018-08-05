angular.module('config', [])
    .config(function ($sceDelegateProvider, ngToastProvider, $httpProvider, $localStorageProvider, AnalyticsProvider) {
        $sceDelegateProvider.resourceUrlWhitelist([
            // Allow same origin resource loads.
            'self',
            // Allow loading from our assets domain.  Notice the difference between * and **.
            'http://*.wistia.com/**'
        ]);

        AnalyticsProvider.setAccount('UA-46879888-11');

        ngToastProvider.configure({
            verticalPosition: 'bottom',
            maxNumber: 3
        });

        var user = $localStorageProvider.get('user');

        if (user === undefined  || user === null) {
            $localStorageProvider.set('user', {});
        } else if ( user.ttl !== undefined || user.ttl !== null ) {
            if ((new Date().getTime()/1000|0) > user.ttl)
                $localStorageProvider.set('user', {}); 
        }                   

        $httpProvider.interceptors.push("myintercept");
    })

    .factory("myintercept", ['$rootScope', function ($rootScope) {
        return {
            'request': function (config) {
                /*console.log(config, $localStorage.user)
                if ($localStorage.user.token && config.url != "https://upload.wistia.com") {
                    config.headers['Authorization'] = 'Bearer ' + $localStorage.user.token;
                } */
                try {
                    if ($rootScope.user !== undefined && $rootScope.user.token && /https?:\/\/(.+)?(wistia.com|wi.st)\/.*/.test(config.url) != true) {
                        config.headers['Authorization'] = 'Bearer ' + $rootScope.user.token;
                    }
                } catch (err) {
                    console.log(err);
                } finally {
                    return config;
                }


            }
        };
    }])

    .run(function ($localStorage, $rootScope, $templateCache, Analytics, $timeout) {
        if ($localStorage.user && $localStorage.user.token) {
            $rootScope.user = $localStorage.user;
        }
               

        $rootScope.$on("$stateChangeError", function(event, toState, toParams, fromState, fromParams, error) {
            console.log('ui-router reject promise called', error);
            $rootScope.isNavCollapsed = true;
        });

        $templateCache.put("af/template/pagination/pagination.html",
        "<li style=\"float:right; display:block; padding:5px 20px;\">Page {{page}} of {{totalPages}}</li> <li role=\"menuitem\" ng-if=\"::boundaryLinks\" ng-class=\"{disabled: noPrevious()||ngDisabled}\" class=\"pagination-first\"><a href ng-click=\"selectPage(1, $event)\" ng-disabled=\"noPrevious()||ngDisabled\" uib-tabindex-toggle>{{::getText('first')}}</a></li>\n" +
        "<li role=\"menuitem\" ng-if=\"::directionLinks\" ng-class=\"{disabled: noPrevious()||ngDisabled}\" class=\"pagination-prev\"><a href ng-click=\"selectPage(page - 1, $event)\" ng-disabled=\"noPrevious()||ngDisabled\" uib-tabindex-toggle>{{::getText('previous')}}</a></li>\n" +
        "<li role=\"menuitem\" ng-repeat=\"page in pages track by $index\" ng-class=\"{active: page.active,disabled: ngDisabled&&!page.active}\" class=\"pagination-page\"><a href ng-click=\"selectPage(page.number, $event)\" ng-disabled=\"ngDisabled&&!page.active\" uib-tabindex-toggle>{{page.text}}</a></li>\n" +
        "<li role=\"menuitem\" ng-if=\"::directionLinks\" ng-class=\"{disabled: noNext()||ngDisabled}\" class=\"pagination-next\"><a href ng-click=\"selectPage(page + 1, $event)\" ng-disabled=\"noNext()||ngDisabled\" uib-tabindex-toggle>{{::getText('next')}}</a></li>\n" +
        "<li role=\"menuitem\" ng-if=\"::boundaryLinks\" ng-class=\"{disabled: noNext()||ngDisabled}\" class=\"pagination-last\"><a href ng-click=\"selectPage(totalPages, $event)\" ng-disabled=\"noNext()||ngDisabled\" uib-tabindex-toggle>{{::getText('last')}}</a></li>\n" +
        "");

        //ToDo: code to forward to error page on resolve error
        $rootScope.$on('$stateChangeError', 
            function(event, toState, toParams, fromState, fromParams, error){ 
                    // this is required if you want to prevent the $UrlRouter reverting the URL to the previous valid location
                    event.preventDefault();
            }
        );

        $rootScope.$on('$stateChangeStart', function(){ $rootScope.isNavCollapsed = true; });

        $rootScope.$on('$stateChangeSuccess', function() {
            document.body.scrollTop = document.documentElement.scrollTop = 0;
         });

        $rootScope.$on('$viewContentLoaded', function(event) {
            $timeout( function(){
                    jQuery( 'div.wpcf7 > form' ).each( function() {
                    var $form = $( this );
                    console.log($form);
                    wpcf7.initForm( $form );
        
                    if ( wpcf7.cached ) {
                        wpcf7.refill( $form );
                    }
                } );
                console.log('contact form init called from scope');
            }, 1000);
        });
        
    })

    .run(['$state', '$stateParams', function($state, $stateParams) {
        //this solves page refresh and getting back to state
     }])

    .value('user', {})

    .value('env', local_env)

    .filter('to_trusted', ['$sce', function ($sce) {
        return function (text) {
            return $sce.trustAsHtml(text);
        };
    }])

    .filter("hash", [function () {
        return function (arr) {
            if (!arr.url) return arr;
            return arr.url.replace(/^#\//i, '');
        }
    }]);

