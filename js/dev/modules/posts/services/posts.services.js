function PostService($resource, $cacheFactory, lodash, Store, $log){
    var PostService = {};
    var apiCache = $cacheFactory('Posts');
    var API = {
        post : $resource(local_env.api_url+'/posts/:id',{
            id:'@id',
            target:'@target'
        },
        { 
            save: { method:'POST', headers:{'X-WP-Nonce': local_env.nonce} },
            'get': { method:'GET', cache: apiCache},
            'query': { method:'GET', cache: apiCache, isArray:true }

        }),
        category : $resource(local_env.api_url+'/categories/',
        {},
        { 
            save: { method:'POST', headers:{'X-WP-Nonce': local_env.nonce} },
            'get': { method:'GET', cache: apiCache},
            'query': { method:'GET', cache: apiCache, isArray:true }

        })
    };

    var calculateHash = function (param) {
        if(param.cid == undefined){
             param.cid = "all";
        }
        var hash = param.page + '-' + param.cid;
        return hash;
    };

    
    PostService.getPosts = function(param = {}) {
        $log.log("params: ", param);
        if (!param === Object(param)){
            return Promise.resolve(new Error("argument must be an object"));
        }
        return new Promise(function (resolve, reject){
            var hash = calculateHash(param);
            var args = [];
            args.per_page = 12; //make this a config in backend and inject as constant
            args.page = (param.page)? param.page : 1;
            args._embed = true;
            if(param.cid && param.cid != "all") args.categories = param.cid;
            if (Store.posts.totalPosts == 0 || Store.posts.key != hash || (Store.posts.key == hash && Store.posts.items[param.page] === undefined)){				
                API.post.query(args, function(data, header){
                    if (!data) {
                        resolve([]);
                    } else {
                        if (Store.watch.key == hash) {
                         Store.posts.items[param.page] = data;
                        } else {
                            Store.posts.items=[];
                            Store.posts.key = hash;
                            Store.posts.items[param.page] = data;
                        }
                        Store.posts.totalPages = header('X-WP-TotalPages');
                        Store.posts.totalPosts = header('X-WP-Total');
                        resolve(data);                        
                    }
                }, function(err){
                    reject(err);
                });
                
            } else {
                resolve(Store.posts.items[param.page]);
            }
        });
        
    };
    
    PostService.getPost = function(param) {
        if (!param === Object(param)){
            return Promise.resolve(new Error("argument must be an object"));
        }
        return new Promise(function (resolve, reject){                        
            var entry = lodash.flatMap(
                Store.posts.items,
                function (val) {
                    return val;
                })
                .find(function (item) {
                    if (item === undefined) {return false;}
                    return item.id == param.id;
                });

            if (entry !== undefined) {
                resolve(entry);
            } else {
                API.post.get({id:param.id, _embed:true}, function(data){
                    if (!data) {
                        resolve(null);
                    } else {
                        resolve(data);
                    }
                }, function(err){
                    reject(err);
                });
            }
        });
    };
		
    PostService.getCategories = function() {
    };
    
    PostService.getPostByCategories = function() {
    };

    PostService.getPostAtIndex = function(index) {
        index = index + 1;
        if(Store.posts.totalPosts && Store.posts.totalPosts[index])
            return Store.posts.totalPosts[index];
        else
            return false;
    };

    PostService.getPostIndex = function(id) {
       return  _.findIndex(Store.posts.items, function(post) { return post.id == id; }) + 1;
    };

    PostService.getPostIdAtIndex = function(index) {
        index = index - 1;
        if(Store.posts.items && Store.posts.items[index])
            return Store.posts.items[index].id;
        else
            return false;
    };

    PostService.getPostsLength = function() {
        return lodash.size(Store.posts.items);
    };

    PostService.getTotalPost = function() {
        if(Store.posts.totalPosts)
            return Store.posts.totalPosts;
        else
            return false;
    };

    return PostService;
}

angular
    .module('com.module.posts')
    .factory('PostService', PostService);