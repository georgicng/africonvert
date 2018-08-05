function Password_ResetController($scope, $http) {
    $scope.reset = function(user){
		var record = {};
		record.action = local_env.resetAction;
		record.security = local_env.security;
		record.login = user.login;
		$http.post(local_env.ajaxurl, record, {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			}
		}).then(function(response){
			$state.go('home');
		});
		
	};
}


angular
    .module('com.module.core')
    .controller('Password_ResetController', Password_ResetController); 