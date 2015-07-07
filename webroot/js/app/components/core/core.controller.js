angular.module(appConfig.appName)
.controller('appController', ['$scope', '$rootScope', '$stateParams', '$state', 'USER_ROLES', 'AUTH_EVENTS', 'ngDialog', 'authService', 'databaseService', 'commonService', 'session',
function ($scope, $rootScope, $stateParams, $state, USER_ROLES, AUTH_EVENTS, ngDialog, authService, databaseService, commonService, session) {

    // stores the current database id.
    $rootScope.currentDatabase = {
        id: '',
        name: ''
    };

    // checks if user is Authenticated - if auth then go to database page else open login popup
    $scope.startApp = function () {
        authService.isAuthenticated().then(function (isAuthenticated) {
            if (isAuthenticated)
                $state.go('DataAdmin.database');
            else
                $scope.openLoginPopUp();
        });
    }

    // gets the list of databases.
    authService.isAuthenticated().then(function (isAuthenticated) {
        if (isAuthenticated) {
            databaseService.getDatabaseList().then(function (data) {
                $rootScope.databaseList = data
            }, function (fail) {
                alert(fail);
            });
        }
    })

    // current logged in user details 
    $scope.currentUser = null;

    // all the possible roles for a user.
    $scope.userRoles = USER_ROLES;

    // checks if user is authorized.
    $scope.isAuthorized = authService.isAuthorized;

    // sets the current user.
    $scope.setCurrentUser = function (user) {
        $scope.currentUser = user;
    };

    // listens to set current user.
    $scope.$on(AUTH_EVENTS.setCurrentUser, function (event, user) {
        $scope.currentUser = user;
    });

    // opens login popup.
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

    // Listens to not authenticated event.
    $scope.$on(AUTH_EVENTS.notAuthenticated, function () {

        $scope.currentUser = null;

        session.destroy();

    });

    $scope.changeDatabase = function (dbId) {
        authService.emptyUserDbRoles();
        $state.go('DataAdmin.databaseManagement', { dbId: dbId })
    }

} ]);