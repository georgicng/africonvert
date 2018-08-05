angular
    .module('com.module.core')
    .factory('Auth', function ($http, $rootScope, $localStorage, env) {

        var serializeRequest = function (obj) {
            var str = [];
            for (var p in obj)
                str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
            return str.join("&");
        };

        return {
            isLoggedIn: function () {
                if ($rootScope.user === undefined)
                    return false;
                return true;
            },

            getUserId: function () {
                if ($rootScope.user)
                    return $rootScope.user.user_data.id;
                else
                    return false;
            },

            register: function (user, successFn, errorFn) {
                $http.post(env.api_url + "/users/register", user, {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    transformRequest: serializeRequest
                })
                    .then(
                    function (data) {
                        var user = JSON.parse(data.data.data);
                        $localStorage.user = user;
                        $rootScope.user = user;
                        successFn(user);
                    },
                    function (err) {
                        if (err.data) {
                            errorFn(err.data);
                        } else {
                            errorFn(err);
                        }
                    }
                    );
            },

            login: function (user, successFn, errorFn) {
                $http.post(env.jwt_url, user, {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    transformRequest: serializeRequest
                })
                    .then(
                    function (data) {
                        $localStorage.user = data.data;
                        $rootScope.user = data.data;
                        successFn(data);
                    },
                    function (err) {
                        if (err.data) {
                            errorFn(err.data);
                        } else {
                            errorFn(err);
                        }
                    }
                    );
            },

            reset: function (user, successFn, errorFn) {

                $http.post(env.api_url + "/users/reset", user, {
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    transformRequest: serializeRequest
                })
                    .success(function (data) {
                        successFn(data);
                    })
                    .error(function (err) {
                        if (err.data) {
                            errorFn(err.data);
                        } else {
                            errorFn(err);
                        }
                    });
            },

            logout: function () {
                $localStorage.user = {};
                $rootScope.user = undefined;
            },

        };
    });