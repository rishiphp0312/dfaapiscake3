angular.module('DataAdmin.database')
.controller('databaseController', ['$scope', 'authService', 'USER_ROLES', function ($scope, authService, USER_ROLES) {

    $scope.databaseList = [{
        id: 1,
        dbName: 'Assam Tea Garden'
    }, {
        id: 2,
        dbName: 'Assam Tea Garden 2'
    }];

    $scope.isSuperAdmin = function () {
        return authService.isAuthorized(USER_ROLES.superAdmin);
    }

    $scope.deleteDatabaseConnection = function (databaseId) {

    }

    $scope.confirmDataseConnectionDeletion = function (databaseId) {

    }

    $scope.cancelDataseConnectionDeletion = function () {

    }

} ])
.controller('newDatabaseConnectionController', ['$scope', '$state', 'databaseService', function ($scope, $state, databaseService) {

    $scope.connectionDetails = {
        connectionName: '',
        databaseType: '',
        hostAddress: '',
        databaseName: '',
        userName: '',
        password: '',
        port: ''
    }

    $scope.saveConnection = function (connectionDetails) {
        alert(JSON.stringify(connectionDetails));
        $state.go('DataAdmin.database');
    }

    $scope.testConnection = function (connectionDetails) {
        alert('testing Connection');
    }

    databaseService.getDbTypeList().then(function (dbtypeList) {
        $scope.dbTypeDetails = dbtypeList;
    })

} ])