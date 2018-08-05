function PageService($resource, $cacheFactory, lodash, Store){
    var PageService = {};
    var apiCache = $cacheFactory('Pages');
    var API = $resource(local_env.api_url+'/pages',{
            slug:'@slug'
        },
        { 
            'get': { method:'GET', cache: apiCache},
            'query': { method:'GET', cache: apiCache, isArray:true }

        });

    PageService.getAllPages = function(slug) {
        return new Promise(function (resolve, reject){
            var post;
            if (post = lodash.find(Store.page.items, function(item) { return item.slug == slug; }) ) {
                resolve(post);
            } else {
                API.query({per_page: 30}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        Store.page.items = lodash.concat(Store.page.items, data);
                        post = lodash.find(Store.page.items, function(item) { return item.slug == slug; })
                        resolve(post);
                    }
                });
            }
        });
    };

    PageService.getPage = function(slug) {
        return new Promise(function (resolve, reject){
            
            API.query({slug: slug}, function(data){
                if (!data) {
                    resolve(null);
                } else {
                    var post = data[0];
                    resolve(post);
                }
            },
            function(err){
                reject(undefined);
            });
           
        });
    }

    return PageService;
}

angular
    .module('com.module.pages')
    .factory('PageService', PageService);