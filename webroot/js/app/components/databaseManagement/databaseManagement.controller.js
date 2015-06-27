angular.module('DataAdmin.databaseManagement')
.controller('databaseManagementController', ['$scope', '$stateParams', 'databaseManagementService', function ($scope, $stateParams, databaseManagementService) {

    $scope.databaseDetails = {
        id: $stateParams.id,
        name: ''
    }

    databaseManagementService.getDatabaseDetails($scope.databaseDetails.id).then(function (databaseDetails) {
        $scope.databaseDetails.name = databaseDetails.name;
    });

} ]);