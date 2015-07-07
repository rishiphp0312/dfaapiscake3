angular.module('DataAdmin.userManagement')
.controller('userManagementController', ['$scope', '$rootScope', 'userManagementService', 'commonService', 'ngDialog', '$stateParams', '$filter', function ($scope, $rootScope, userManagementService, commonService, ngDialog, $stateParams, $filter) {

    $scope.deleteSingleUserId;

    $scope.allUsersSelected = false;

    $scope.selectedUsers = [];

    // key value pair for display of user Roles 
    commonService.getUserRolesList().then(function (res) {
        $rootScope.userRoles = res;
    }, function (fail) {
        alert('fail');
    })

    userManagementService.getUsersList($stateParams.dbId).then(function (data) {
        $scope.usersList = data;
    }, function (fail) {
        alert(fail);
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
        if ($scope.selectedUsers.indexOf(id) < 0) {
            $scope.selectedUsers.push(id);
        } else {
            $scope.selectedUsers.splice($scope.selectedUsers.indexOf(id), 1);
        }
    }

    $scope.deleteSelectedUsers = function () {
        if ($scope.selectedUsers.length > 0) {
            ngDialog.openConfirm({
                templateUrl: 'js/app/components/userManagement/views/deleteUserPopUp.html',
                showClose: false,
                className: 'confirm-popup',
                scope: $scope
            });
        }
    }

    $scope.deleteUser = function (userId) {

        $scope.deleteSingleUserId = userId;

        ngDialog.openConfirm({
            templateUrl: 'js/app/components/userManagement/views/deleteUserPopUp.html',
            showClose: false,
            className: 'confirm-popup',
            scope: $scope
        })
    }

    $scope.confirmDelete = function () {

        var usersList;

        if ($scope.deleteSingleUserId) {
            usersList = [$scope.deleteSingleUserId];
        } else {
            usersList = $scope.selectedUsers;
        }

        var data = {
            dbId: $stateParams.dbId,
            userIds: usersList
        }

        userManagementService.deleteUsers(data)
        .then(function (res) {
            $scope.usersList = $filter('filter')($scope.usersList, function (value, index) {
                return (usersList.indexOf(value.id) < 0);
            });
            $scope.deleteSingleUserId = '';
            $scope.selectedUsers = [];
        }, function (fail) {
            alert('fail');
        });
        return true;
    }
} ])
.controller('addModifyUserController', ['$scope', '$stateParams', '$state', '$timeout', '$filter', 'USER_ROLES', 'userManagementService', 'commonService', 'errorService', 'onSuccessDialogService', function ($scope, $stateParams, $state, $timeout, $filter, USER_ROLES, userManagementService, commonService, errorService, onSuccessDialogService) {

    $scope.modifyUser = $stateParams.userId ? true : false;

    $scope.createAnother = {
        checked: false
    };

    $scope.showEmailSuggestion = false;

    $scope.showNameSuggestion = false;

    $scope.onBlur = function (objType) {
        $timeout(function () {
            if (objType == 'Email') {
                $scope.showEmailSuggestion = false;
            }
            if (objType == 'Name') {
                $scope.showNameSuggestion = false;
            }
        }, 300);
    }

    $scope.roleClicked = function (roleId) {

        /*
        if (roleId == USER_ROLES.admin) {
        if ($scope.userDetails.roles.indexOf(USER_ROLES.templateUser) >= 0) {
        $scope.userDetails.roles.splice($scope.userDetails.roles.indexOf(USER_ROLES.templateUser), 1);
        }
        if ($scope.userDetails.roles.indexOf(USER_ROLES.dataUser) >= 0) {
        $scope.userDetails.roles.splice($scope.userDetails.roles.indexOf(USER_ROLES.dataUser), 1);
        }
        } else {
        if ($scope.userDetails.roles.indexOf(USER_ROLES.admin) >= 0) {
        $scope.userDetails.roles.splice($scope.userDetails.roles.indexOf(USER_ROLES.admin), 1);
        }
        }*/


        if ($scope.userDetails.roles.indexOf(roleId) < 0) {
            $scope.userDetails.roles.push(roleId);
        } else {
            $scope.userDetails.roles.splice($scope.userDetails.roles.indexOf(roleId), 1);
        }
    }

    if ($scope.modifyUser) {

        userManagementService.getUserDetails({ userId: $stateParams.userId, dbId: $stateParams.dbId })
        .then(function (data) {
            $scope.userDetails = {
                id: data.id,
                name: data.name,
                email: data.email,
                roles: data.roles,
                access: data.access,
                dbId: $stateParams.dbId
            };
        })
    } else {

        $scope.userDetails = {
            id: '',
            name: '',
            email: '',
            roles: [],
            access: '',
            dbId: $stateParams.dbId
        };

        commonService.getAllUsersList().then(function (res) {
            $scope.suggestionUsersList = res;
        }, function (fail) {
            alert(fail);
        })

    }

    $scope.saveUser = function (userDetails) {

        if (!$scope.modifyUser) {
            var suggestedUser = $filter('filter')($scope.suggestionUsersList, { id: userDetails.id })[0];
            if (!(suggestedUser.name === userDetails.name && suggestedUser.email === userDetails.email)) {
                userDetails.id = '';
            }
        }

        userDetails['isModified'] = $scope.modifyUser;

        var msg = $scope.modifyUser ? 'User modified successfully' : 'User added successfully';

        userManagementService.addModifyUser(userDetails)
        .then(function (res) {
            if (res) {
                if ($scope.createAnother.checked) {
                    onSuccessDialogService.show(msg, function () {
                        $state.go($state.current, { dbId: $stateParams.dbId }, { reload: true });
                    });

                } else {
                    onSuccessDialogService.show(msg, function () {
                        $state.go('DataAdmin.databaseManagement.userManagement', { dbId: $stateParams.dbId });
                    });
                }

            }
        }, function (fail) {
            alert(fail);
        });

    }

    $scope.autoSuggestionSelected = function (suggestedUserDetails) {
        $scope.userDetails.id = suggestedUserDetails.id;
        $scope.userDetails.name = suggestedUserDetails.name;
        $scope.userDetails.email = suggestedUserDetails.email;
    }

    $scope.$watch('userDetails.email', function (newValue) {
        var EMAIL_REGEXP = /^[a-z0-9!#$%&'*+=?^_`{|}~.-]+@[a-z0-9-]+(\.[a-z0-9-]+)*$/i;
        if (newValue != undefined && newValue != '' && newValue.match(EMAIL_REGEXP) == null) {
            $scope.emailInvalid = true;
        } else {
            $scope.emailInvalid = false;
        }
    })

} ])
.controller('confirmPasswordController', ['$scope', '$stateParams', '$state', 'userManagementService', 'onSuccessDialogService', 'errorService', function ($scope, $stateParams, $state, userManagementService, onSuccessDialogService, errorService) {

    $scope.key = $stateParams.key;

    $scope.password = '';

    $scope.confirmPassword = '';

    $scope.savePassword = function (password) {
        if (password !== $scope.confirmPassword) {
            return false;
        } else {
            userManagementService.confirmPassword({ password: password, key: $scope.key })
            .then(function (res) {
                onSuccessDialogService.show('Activation successful.',function(){
                    $state.go('DataAdmin');
                })
            }, function (err) {
                errorService.show(err);
            });
        }
    }

} ])