function SignupController($scope, $http) {
    $scope.register = function(record){
		record.action = local_env.registerAction;
		record.security = local_env.security;
		$http.post(local_env.ajaxurl, record, {
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			transformRequest: function(obj) {
				var str = [];
				for(var p in obj)
				str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
				return str.join("&");
			}
		}).then(function(response){		
			//SharedState.turnOff('register');
		});
		
	};
}

angular
    .module('com.module.core')
    .controller('SignupController', SignupController); 