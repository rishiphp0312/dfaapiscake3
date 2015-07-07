angular.module('DataAdmin.databaseManagement')
.controller('databaseManagementController', ['$scope', '$rootScope', '$stateParams', 'databaseManagementService', 'authService', 'USER_ROLES', function ($scope, $rootScope, $stateParams, databaseManagementService, authService, USER_ROLES) {

    // get Database Details
    databaseManagementService.getDatabaseDetails($stateParams.dbId)
    .then(function (databaseDetails) {
        $rootScope.currentDatabase.id = databaseDetails.id;
        $rootScope.currentDatabase.name = databaseDetails.dbName;
    }, function (fail) {
        alert('');
    });

    // -- Check for Roles
    $scope.isSuperAdmin = function () {
        return authService.isSuperAdmin();
    }

    $scope.showDatabase = false;

    $scope.showData = false;

    $scope.showTemplate = false;

    authService.isAuthorized([USER_ROLES.admin], $stateParams.dbId).then(function (res) {
        $scope.showDatabase = res;
    })

    authService.isAuthorized([USER_ROLES.admin, USER_ROLES.dataUser], $stateParams.dbId).then(function (res) {
        $scope.showData = res;
    })

    authService.isAuthorized([USER_ROLES.admin, USER_ROLES.templateUser], $stateParams.dbId).then(function (res) {
        $scope.showTemplate = res;
    })

    // -- END: Check for Roles

    // ----- TODO: CLEAN CODE - Comment
    $scope.lftmenu = "visible";
    $scope.rgtwidth = "withleftmenu";
    $scope.changeClass = function () {
        if ($scope.lftmenu === "visible")
            $scope.lftmenu = "invisible";
        else
            $scope.lftmenu = "visible";

        if ($scope.rgtwidth === "withleftmenu")
            $scope.rgtwidth = "withoutleftmenu";
        else
            $scope.rgtwidth = "withleftmenu";

    };

} ]);