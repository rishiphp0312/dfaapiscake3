angular.module('DataAdmin.userManagement')
.controller('userManagementController', ['$scope', 'userManagementService', function ($scope, userManagementService) {

    $scope.allUsersSelected = false;

    $scope.selectedUsers = [];

    userManagementService.getUsersList($scope.databaseDetails.id).then(function (data) {
        $scope.usersList = data.data;
    });

    $scope.searchOption = "name";

    $scope.search = {
        roles: '',
        name: '',
        email: ''
    };

    $scope.selectAllUsers = function () {
        var data = [];

        if ($scope.allUsersSelected) {
            angular.forEach($scope.usersList, function (user) {
                data.push(user.id);
            });
        }

        $scope.selectedUsers = data;
    }

    $scope.userSelected = function (id) {
        $scope.selectedUsers.push(id);
    }

    $scope.deleteSelectedUsers = function () {
        if ($scope.selectedUsers.length > 0) {
            alert(JSON.stringify($scope.selectedUsers));
        }
    }

} ])