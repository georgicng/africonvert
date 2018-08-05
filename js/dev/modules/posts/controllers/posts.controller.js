function PostsController($scope, posts, $state, $stateParams, PostService, $log) {
    var getCategoryTitle = function (cid) {
        var title = "Blog";
        var found = false;
        for (var i = 0, len = posts.length; i < len; i++) {
            var tax = posts[i]._embedded['wp:term'];
            for (var j = 0, jlen = tax.length; j < jlen; j++) {
                var terms = tax[j];
                for (var k = 0, klen = terms.length; k < klen; k++) {
                    var term = terms[k];
                    if (term.id == $stateParams.cid) {
                        title = term.name;
                        found = true;
                        break;
                    }
                }
                if (found) break;
            }
            if (found) break;
        }
  
        return title;
    };

    $scope.totalItems = PostService.getTotalPost();
    $scope.currentPage = $stateParams.page;


    $scope.pageChanged = function () {
        $state.go('app.blog', { page: $scope.currentPage });
    };

    if ($stateParams.cid) {
        var title = getCategoryTitle($stateParams.cid);
        $state.current.data.pageTitle = title;
        $scope.data = { pageTitle: title };
    } else {
        $scope.data = { pageTitle: "Blog" };

    }
    $scope.data.posts = posts;

}

angular
    .module('com.module.posts')
    .controller('PostsController', PostsController);