angular
    .module('app', [
        'ui.router', 
        'ngResource', 
        'ngSanitize', 
        'ngAnimate', 
        'ngLodash',
        'infinite-scroll', 
        'ngToast', 
        'ui.bootstrap', 
        'ngFileUpload', 
        'angular-loading-bar', 
        'sn.addthis', 
        'vjs.video', 
        'vcRecaptcha',
        'angularMoment',
        'ngStorage',
        'ngPassword',
        'ngPageTitle',
        'angular-google-analytics',
        'config',       
        'com.module.core',
        'com.module.posts',
        'com.module.pages',
        'com.module.submit',
        'com.module.vote',
        'com.module.watch',
        'com.module.archive',
        'com.module.members'
    ])

    .run(function($rootScope){        
        $rootScope.$on('$stateChangeStart', function(){
            $rootScope.$broadcast('$routeChangeStart');
            
        });
    });
