angular.module('DataAdmin.database')
.controller('databaseController', ['$scope', '$rootScope', '$filter', 'authService', 'USER_ROLES', 'databaseService', 'ngDialog', function ($scope, $rootScope, $filter, authService, USER_ROLES, databaseService, ngDialog) {

    $scope.currentDatabase = null;

    // selected database to be deleted.
    $scope.selectedDatabase = '';

    // set currentDatabase is empty.
    $rootScope.currentDatabase.id = '';

    $rootScope.currentDatabase.name = '';

    databaseService.getDatabaseList().then(function (data) {
        $rootScope.databaseList = data
    }, function (fail) {
        alert(fail.code);
    });

    // check if user is super Admin -- hide/show of add database button.
    $scope.isSuperAdmin = function () {
        return authService.isSuperAdmin();
    }

    // event bind to give popup for confirmation of deletion
    $scope.deleteDatabaseConnection = function (database) {

        $scope.selectedDatabase = database;

        ngDialog.openConfirm({
            templateUrl: 'js/app/components/database/views/deleteDatabasePopUp.html',
            showClose: false,
            className: 'confirm-popup',
            scope: $scope
        });

    }

    // confirms delete of database connection.
    $scope.confirmDelete = function () {
        databaseService
        .deleteDatabaseConnection($scope.selectedDatabase.id)
        .then(function (res) {
            if (res) {
                $rootScope.databaseList = $filter('filter')($rootScope.databaseList, function (value, index) {
                    return (value.id != $scope.selectedDatabase.id);
                })
                $scope.selectedDatabase = '';
            }
        }, function (fail) {
            alert(fail);
        });
        return true;
    }

} ])
.controller('newDatabaseConnectionController', ['$scope', '$state', 'databaseService', 'errorService', function ($scope, $state, databaseService, errorService) {

    $scope.testConnectionVerified;

    $scope.isConnectionNameChanged = false;

    $scope.connectionDetails = {
        connectionName: '',
        databaseType: 'mssql',
        hostAddress: '',
        databaseName: '',
        userName: '',
        password: '',
        port: '1433'
    }

    databaseService.getDbTypeList().then(function (dbtypeList) {
        $scope.dbTypeDetails = dbtypeList;
    })

    $scope.saveConnection = function (connectionDetails) {
        databaseService.addNewDatabaseConnection(connectionDetails).then(function (res) {
            $state.go('DataAdmin.database');
        }, function (err) {
            errorService.show(err);
        })
    }

    $scope.verifyConnectionName = function (connectionName) {
        if ($scope.isConnectionNameChanged) {
            databaseService.verifyConnectionName(connectionName).then(function (res) {
                $scope.isConnectionNameChanged = false;
                $scope.connectionNameUnique = true;
            }, function (fail) {
                $scope.connectionNameUnique = false;
            })
        }
    }

    $scope.testConnection = function (formValid, connectionDetails) {
        if (formValid) {
            databaseService.testDatabaseConnection(connectionDetails)
            .then(function (res) {
                $scope.testConnectionVerified = true;
            }, function (fail) {
                $scope.testConnectionVerified = false;
                errorService.show(fail);
            });
            return false;
        } else {
            $scope.showValidations = true;
        }

    }

    $scope.$watch('connectionDetails.connectionName', function (oldValue, newValue) {
        if (oldValue !== newValue) { $scope.isConnectionNameChanged = true; }
    })

    $scope.setDbDefaultPort = function () {
        if ($scope.connectionDetails.databaseType == 'mssql') {
            $scope.connectionDetails.port = '1433';
        } else {
            $scope.connectionDetails.port = '3306';
        }
    }

} ])