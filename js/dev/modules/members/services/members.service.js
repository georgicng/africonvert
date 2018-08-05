function MemberService( $resource, $cacheFactory, lodash, Store, env ) {
	var MemberService = {};
    var apiCache = $cacheFactory('User');
    var API = $resource(env.api_url+'/:path/:id/:item/:mix/:verb',{
            id:'@id',
            path:'@path',
            item:'@item'
        },
        { 
            'save': { method:'POST' },
            'get': { method:'GET', cache: apiCache},
            'query': { method:'GET', cache: apiCache, isArray:true }

        });
    
    MemberService.getUserContests = function(id) {
        var path = "author";
        var item = "contests";
        return new Promise(function (resolve, reject){            
                API.query({path:path, id:id, item:item}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
        
    };
    
    MemberService.getUserContent = function(id) {
       var path = "submissions";
        return new Promise(function (resolve, reject){            
                API.query({path:path, author:id, _embed:true}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
    };

    MemberService.getUserActivities = function(id, page = 1) {
        if (page === false)
            return; 
        var path = "users";
        var item = "activities";
        return new Promise(function (resolve, reject){        
                API.get({path:path, id:id, item:item, page:page}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {                        
                        if (data.pages === page || data.pages < page)
                            next_page = false;
                        else if (data.pages > page)
                            next_page = page + 1;
                        resolve({data:data.activities, page:next_page});
                    }
                });
        });
        
    };

    MemberService.getUserNotifications = function(id) {
        var path = "users";
        var item = "notifications";
        return new Promise(function (resolve, reject){            
                API.query({path:path, id:id, item:item}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
        
    };

    MemberService.notificationUpdateSeen = function(id) {
        var path = "users";
        var item = "notifications";        
        var mix = 'seen';
        return new Promise(function (resolve, reject){            
                API.get({path:path, id:id, item:item, mix:mix}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
        
    };

    MemberService.notificationRead = function(id, notif) {
        var path = "users";
        var item = "notifications";
        var mix = notif;
        var verb = "read";
        return new Promise(function (resolve, reject){            
                API.get({path:path, id:id, item:item, mix:mix, verb:verb}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
        
    };

    MemberService.getUser = function(id) {
       var path = "users";
        return new Promise(function (resolve, reject){            
                API.get({path:path, id:id, _embed:true}, function(data){
                    if (!data) {
                        reject("No response");
                    } else {
                        resolve(data);
                    }
                });
        });
    };

    MemberService.me = function() {
       var path = "users";
       var id = "me";
        return new Promise(function (resolve, reject){            
                API.get(
                    {path:path, id:id, _embed:true}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    }, 
                    function(err){
                        reject(err);
                    });
        });
    };

     MemberService.changeInfo = function(obj) {
       obj.path = "users";
       //obj.id = "me";
        return new Promise(function (resolve, reject){            
                API.save(
                    obj, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

     MemberService.changePassword = function(obj) {
        obj.path = "users";
        return new Promise(function (resolve, reject){            
                API.save(
                    obj, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.getUserFollowers = function(id, page = 1) {
        if (page === false)
            return; 
        var path = "author", item = "followers";
        return new Promise(function (resolve, reject){         
                API.get(
                    {path:path, id:id, item:item, page:page}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            if (data.pages == page || data.pages < page)
                                next_page = false;
                            else if (data.pages > page)
                                next_page = page + 1;
                            resolve({data:data.followers, page:next_page});
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.getUserFollowing = function(id, page = 1) {
        if (page === false)
            return; 
        var path = "author", item = "following";
        return new Promise(function (resolve, reject){          
                API.get(
                    {path:path, id:id, item:item, page:page}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            if (data.pages == page || data.pages < page)
                                next_page = false;
                            else if (data.pages > page)
                                next_page = page + 1;
                            resolve({data:data.followings, page:next_page});
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.getUserAwards = function(id) {
        return Promise.resolve([]);
    };

    MemberService.followUser = function(id) {
        var path = "follow";
        return new Promise(function (resolve, reject){            
                API.save(
                    {path:path, follow:id}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.unfollowUser = function(id) {
        var path = "follow";
        return new Promise(function (resolve, reject){            
                API.save(
                    {path:path, unfollow:id}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.confirm = function(token) {
        var path = "users", id = "verify";
        return new Promise(function (resolve, reject){            
                API.get(
                    {path:path, id:id, token:token}, 
                    function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.verify = function(pid) {
        var path = "users", id = "verify";
        return new Promise(function (resolve, reject){            
               API.get(
                   {path:path, id:id, item:pid}, 
                   function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };

    MemberService.removeCover = function(){
        var path = "xprofile";
        return new Promise(function (resolve, reject){           
               API.save(
                   {path:path, type:"cover_pic", remove:"true"}, 
                   function(data){
                        if (!data) {
                            resolve(null);
                        } else {
                            resolve(data);
                        }
                    },
                    function(err){
                        reject(err);
                    }
                );
        });
    };
		

    return MemberService;
}

angular
  .module('com.module.members')
    .factory('MemberService', MemberService);