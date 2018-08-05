angular
    .module('com.module.pages')
    .config(function($stateProvider) {       
        $stateProvider
        .state('app.home', {                
            url: '/home',
            templateUrl: local_env.partials + 'home.html',
            controller: 'PageController',
            data: {
                pageTitle: "Home"
            },
            resolve : {
                page : function (PageService){
                    return PageService.getPage('home');
                }
            },               
        })
        .state('app.about',{
            url: '/about',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('about');
                }
            },
            data: {
                    pageTitle: "About Us"
                }
        })
        .state('app.contact',{
            url: '/contact',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('contact');
                }
            },
            data: {
                    pageTitle: "Contact Us"
                }
        })
        .state('app.tou',{
            url: '/tou',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('terms-of-use');
                }
            },
            data: {
                    pageTitle: "Terms of Use"
                }
        })
        .state('app.advertise',{
            url: '/advertise',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('advertisement-sponsorship');
                }
            },
            data: {
                pageTitle: "Advertise with us"
            }
        })
        .state('app.faq',{
            url: '/faq',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('faq');
                }
            },
            data: {
                pageTitle: "FAQ"
            }
        })
        .state('app.privacy',{
            url: '/privacy',
            templateUrl: local_env.partials + 'page.html',
            controller: 'PageController',
            resolve : {
                page : function (PageService){
                    return PageService.getPage('privacy-policy');
                }
            },
            data: {
                pageTitle: "Privacy"
            },
        });
    });