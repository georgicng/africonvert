function CommentService($resource){
	var CommentService = {};
    var API =  $resource(local_env.api_url+'/comments/:id',{
			id:'@id'
		},{
			'update':{method:'PUT'},
			'save':{
				method:'POST',
			},
			'get': { method:'GET'},
            'query': { method:'GET', isArray:true }
		});

	CommentService.getComments = function(post, page = 1) {
        if (page === false)
            return; 
        return new Promise(function (resolve, reject){        
                API.query({post:post, page:page, parent:0, per_page:10, order:"asc"}, function(data, headerFn){
                    if (!data) {
                        resolve([]);
                    } else { 
                        var pages = parseInt(headerFn('x-wp-totalpages'));                       
                        if ( pages === page || pages< page)
                            var next_page = false;
                        else if (pages > page)
                            var next_page = page + 1;
                        resolve({data:data, page:next_page});
                    }
                }, function(err){
                        reject(err);
                });
        });
        
    };

	CommentService.getChildren = function(parent) {
        return new Promise(function (resolve, reject){        
                API.query({parent:parent, order:"asc"}, function(data){
                    if (!data) {
                        resolve([]);
                    } else {                      
                        resolve(data);
                    }
                }, function(err){
                        reject(err);
                });
        });
        
    };

	CommentService.saveComment = function(obj) {
        return new Promise(function (resolve, reject){            
                
				API.save(obj, function(data){
                    if (!data) {
                        resolve([]);
                    } else {
                        resolve(data);
                    }
                }, 
				
				function(err){
                    reject(err);
                });
        });
    };
	
	return CommentService;
}

angular
    .module('com.module.core')
    .factory('CommentService', CommentService);