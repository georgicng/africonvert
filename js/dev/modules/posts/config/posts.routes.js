angular
    .module('com.module.posts')
    .config(function ($stateProvider) {
        $stateProvider
            .state("app.blog", {
                url: '/blog?cid&page',
                templateUrl: local_env.partials + 'blog-posts.html',
                controller: 'PostsController',
                resolve: {
                    posts: function (PostService, $stateParams) {
                        return PostService.getPosts($stateParams);
                    }
                },
                params: {
                    page: '1' //default page
                },
                data: {
                    pageTitle: "Blog Home"
                }
            })
            .state('app.post', {
                url: '/blog/:id',
                templateUrl: local_env.partials + 'blog-post.html',
                controller: 'PostController',
                resolve: {
                    post: function ($stateParams, PostService) {
                        return PostService.getPost($stateParams);
                    }
                },
                data: {
                    pageTitle: "Blog"
                }
            });
    });

