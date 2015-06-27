angular.module('DataAdmin.login')
.controller('loginController', ['$scope', '$rootScope', '$state', 'ngDialog', 'authService', 'AUTH_EVENTS', function ($scope, $rootScope, $state, ngDialog, authService, AUTH_EVENTS) {

    $scope.credentials = {
        email: '',
        password: ''
    }

    $scope.login = function () {
        authService.login($scope.credentials).then(function (user) {
            if (user) {
                $rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
                $scope.setCurrentUser(user);
                $state.go('DataAdmin.database')
                ngDialog.close();
            } else {
                $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
            }
        });
    }

    $scope.logout = function () {
        authService.logout().then(function () {
            $state.go('DataAdmin');
            $scope.setCurrentUser('');
        })
    }
} ])