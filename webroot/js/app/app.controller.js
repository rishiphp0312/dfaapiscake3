angular.module(appConfig.appName)
.controller('appController', ['$scope', '$rootScope', 'USER_ROLES', 'AUTH_EVENTS', 'ngDialog', 'authService', function ($scope, $rootScope, USER_ROLES, AUTH_EVENTS, ngDialog, authService) {

    $scope.currentUser = null;

    $scope.userRoles = USER_ROLES;

    $scope.isAuthorized = authService.isAuthorized;

    $scope.setCurrentUser = function (user) {
        $scope.currentUser = user;
    };

    $scope.openLoginPopUp = function () {
        ngDialog.open({
            template: 'js/app/components/login/views/loginPopUp.html',
            showClose: false,
            closeByDocument: true,
            controller: 'loginController',
            className: 'login-popup',
            scope: $scope
        });
    }

    $scope.$on(AUTH_EVENTS.notAuthenticated, function () {
        ngDialog.open({
            template: 'js/app/components/login/views/loginPopUp.html',
            showClose: false,
            closeByDocument: true,
            controller: 'loginController',
            className: 'login-popup',
            scope: $scope
        });
    })

} ]);