function HomeController(page) {
    $scope.post = page;
}

angular
    .module('com.module.core')
    .controller('HomeController', HomeController);