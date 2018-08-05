function ArchiveService( $resource, $cacheFactory, lodash, Store ) {
	var ContestService = {};
    var apiCache = $cacheFactory('Archive');
    var API = $resource(local_env.api_url+'/:type/:id',{
            id:'@id',
            type:'@type'
        },
        { 
            save: { method:'POST', headers:{'X-WP-Nonce': local_env.nonce} },
            'get': { method:'GET', cache: apiCache},
            'query': { method:'GET', cache: apiCache, isArray:true }

        });
    
    ContestService.getContests = function(param = {}) {
        if (param === null) return new Error("argument must be an object");
        if (typeof param !== 'object') return new Error("argument must be an object");
        var arg = {};
        arg.per_page = param.per_page? param.per_page : 12; //make this a config in backend and inject as constant
        arg.page = param.page? param.page : 1;
        arg.type = param.type? param.type : '';
        arg. _embed =true;
        arg.type = "contests";
        //add type
        return new Promise(function (resolve, reject){           
            if (Store.archive.totalPosts == 0 || !lodash.includes(Store.archive.storedPages, arg.page)){				
                API.query(arg, function(data, header){
                    if (!data) {
                        reject("No response");
                    } else {
                        Store.archive.items = lodash.concat(Store.archive.items, data);
                        Store.archive.storedPages = lodash.concat(Store.archive.storedPages, arg.page);
                        Store.archive.totalPages = header('X-WP-TotalPages');
                        Store.archive.totalPosts = header('X-WP-Total');
                        resolve(data);
                        
                    }
                });
                
            } else {
                resolve(lodash.slice(Store.archive.items, (arg.page - 1) * arg.per_page, arg.per_page));
            }
        });
        
    };
    
    ContestService.getContest = function(id) {
        return new Promise(function (resolve, reject){
            var contest;
            if (contest = lodash.find(Store.archive.items, function(item) { return item.id == id; }) ) {
                resolve(contest);
            } else {
                API.get({type:"contests", id:id, _embed:true}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
            }
        });
    };

    ContestService.getTotalPost = function() {
        if(Store.posts.totalPosts)
            return Store.archive.totalPosts;
        else
            return false;
    };

    ContestService.getEntries = function(id) {
        return new Promise(function (resolve, reject){            
                API.query({type:"submissions", contest:id, _embed:true}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });            
        });
    };
		

    return ContestService;
}

angular
  .module('com.module.archive')
    .factory('ArchiveService', ArchiveService);