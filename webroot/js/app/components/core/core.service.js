angular.module(appConfig.appName)

.service('session', function () {

    this.create = function (sessionId, userId, isSuperAdmin) {
        this.id = sessionId;
        this.userId = userId;
        this.isSuperAdmin = isSuperAdmin;
        this.dbRole = '';
    };

    this.destroy = function () {
        this.id = null;
        this.userId = null;
        this.isSuperAdmin = false;
        this.dbRole = null;
    };

    this.updateDbRole = function (dbRole) {
        this.dbRole = dbRole;
    }

    this.setSuperAdmin = function (isSA) {
        this.isSuperAdmin = isSA;
    }

})

.factory('authService', ['$http', '$q', 'session', '$rootScope', '$cookieStore', 'commonService', 'SERVICE_CALL', function ($http, $q, session, $rootScope, $cookieStore, commonService, SERVICE_CALL) {

    var authService = {};

    // login
    authService.login = function (credentials) {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(undefined, credentials, 'users/login')).success(function (res) {
            if (res.isAuthenticated) {

                var data = res.data;

                session.create(data.id, data.user.id, data.user.role);

                $cookieStore.put('globals', data.user);

                deferred.resolve(data.user);

            } else {
                deferred.resolve(res.err);
            }

        });

        return deferred.promise;

    }

    // check if user is logged in
    authService.isAuthenticated = function () {

        var deferred = $q.defer();

        var user = $cookieStore.get('globals');

        // if user id found in cookie than request is authetnicated else check via service call.
        if (angular.isUndefined(user) || angular.isUndefined(user.id)) {

            $http(commonService.createHttpRequestObject(SERVICE_CALL.system.checkSessionDetails))
            .success(function (res) {
                if (res.isAuthenticated) {

                    var data = res.data.usr;

                    session.create(data.id, data.user.id, res.isSuperAdmin);

                    if (res.usrDbRoles != undefined && res.usrDbRoles.length > 0) {
                        session.updateDbRole(res.usrDbRoles);
                    }

                    $rootScope.$broadcast('set-current-user', data.user);

                    $cookieStore.put('globals', data.user);

                    deferred.resolve(true);

                } else {
                    session.destroy();
                    $cookieStore.remove('globals');
                    deferred.resolve(false);
                }
            });
        } else {
            $rootScope.$broadcast('set-current-user', user);
            deferred.resolve(true);
        }

        return deferred.promise;

    };

    /* 
    * check if user is Authorized -- incase of super admin always authorized.
    * input param: authorizedRoles- required role to authorize.
    */
    authService.isAuthorized = function (authorizedRoles, dbId) {

        var deferred = $q.defer();

        var isAuthorized = false;

        if (session.isSuperAdmin || authorizedRoles.indexOf('*') >= 0) {
            isAuthorized = true;
            deferred.resolve(isAuthorized);
        } else {

            if (!angular.isArray(authorizedRoles)) {
                authorizedRoles = [authorizedRoles];
            }

            if (session.dbRole != undefined && session.dbRole.length > 0) {

                angular.forEach(session.dbRole, function (value) {
                    if (authorizedRoles != undefined && authorizedRoles.indexOf(value) >= 0) {
                        isAuthorized = true;
                    }
                })

                deferred.resolve(isAuthorized);

            } else {
                commonService.getUserDbRoles({ dbId: dbId })
                .then(function (res) {

                    session.setSuperAdmin(res.isSuperAdmin);

                    session.updateDbRole(res.data.usrDbRoles);
                    if (!res.isSuperAdmin) {
                        angular.forEach(session.dbRole, function (value) {
                            if (authorizedRoles != undefined && authorizedRoles.indexOf(value) >= 0) {
                                isAuthorized = true;
                            }
                        })
                    } else {
                        isAuthorized = true;
                    }

                    deferred.resolve(isAuthorized);
                },
                function (fail) {
                    isAuthorized = false;
                    deferred.resolve(isAuthorized);
                })
            }

        }

        return deferred.promise;
    };

    // checks for super Admin
    authService.isSuperAdmin = function () {
        return session.isSuperAdmin;
    }

    authService.emptyUserDbRoles = function () {
        session.updateDbRole([]);
    }

    // logout for user.
    authService.logout = function () {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(undefined, undefined, 'users/logout'))
        .then(function (res) {
            session.destroy();
            $cookieStore.remove('globals');
            deferred.resolve(res);
        });

        return deferred.promise;

    }

    return authService;

} ])

.factory('commonService', ['$http', '$q', 'SERVICE_CALL', function ($http, $q, SERVICE_CALL) {

    var commonService = {};

    commonService.createServiceCallUrl = function (serviceCall) {
        return appConfig.serviceCallUrl + serviceCall;
    }

    //creates HTTP request Object as per params Passed.
    commonService.createHttpRequestObject = function (serviceCall, data, url, method, headers) {

        var req = {};
        req['method'] = method || 'POST';

        if (serviceCall) {
            req['url'] = commonService.createServiceCallUrl(serviceCall);
        } else if (url) {
            req['url'] = url;
        }

        if (data) {
            req['data'] = $.param(data);
        }

        req['headers'] = headers || { 'Content-Type': 'application/x-www-form-urlencoded' };

        return req;
    }

    //gets list of roles.
    commonService.getUserRolesList = function () {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(SERVICE_CALL.system.getUserRolesList))
        .success(function (res) {
            if (res.success) {
                deferred.resolve(res.data.roleDetails);
            } else {
                deferred.reject(res.err);
            }
        })

        return deferred.promise;

    }

    // gets all users List
    commonService.getAllUsersList = function () {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(SERVICE_CALL.system.getAllUsersList))
        .success(function (res) {
            if (res.success) {
                deferred.resolve(res.data.usersList);
            } else {
                deferred.reject(res.err);
            }
        })

        return deferred.promise;

    }

    // get current database roles for a user
    commonService.getUserDbRoles = function (data) {

        var deferred = $q.defer();

        $http(commonService.createHttpRequestObject(SERVICE_CALL.system.getUserDbRoles, data))
        .success(function (res) {
            if (res.success) {
                deferred.resolve(res);
            } else {
                deferred.reject(res.err);
            }
        })

        return deferred.promise;
    }

    return commonService;

} ])

.factory('httpInterceptor', ['$rootScope', '$q', '$cookieStore', '$stateParams', 'session', 'AUTH_EVENTS', function ($rootScope, $q, $cookieStore, $stateParams, session, AUTH_EVENTS) {
    return {
        'request': function (request) {
            //alert($stateParams.dbId);
            return request;
        },

        // check if response is not authenticated.
        'response': function (response) {

            if (response.data != undefined) {

                // check for super admin and update session.
                if (response.data.isSuperAdmin != undefined) {
                    session.setSuperAdmin(response.data.isSuperAdmin);
                }

                // check for authentiacted and remove cookie if not authenticated
                if (response.data.isAuthenticated != undefined && response.data.isAuthenticated == false) {
                    $cookieStore.remove('globals')
                }

                // check for roles assigned to a user for a particalar DB.
                if (response.data.data != undefined && response.data.data.usrDbRoles != undefined) {
                    session.updateDbRole(response.data.data.usrDbRoles);
                }
            }

            return response;
        },

        //// optional method
        'responseError': function (rejection) {
            alert('Something went wrong: ' + rejection.statusText);
            return $q.reject(rejection);
        }
    };
} ])

.factory('errorService', ['ERROR_CODE', 'ngDialog', function (ERROR_CODE, ngDialog) {

    var errorService = {};

    errorService.resolve = function (errObj) {

        var errorMessage = '';

        if (errObj.code != undefined) {
            errorMessage = ERROR_CODE[errObj.code];
        } else if (errObj.msg) {
            errorMessage = errObj.msg;
        }

        return errorMessage;

    }

    errorService.show = function (errObj) {
        ngDialog.open({
            template: 'js/app/components/core/views/errorPopUp.html',
            showClose: false,
            className: 'info-popup',
            controller: ['$scope', function ($scope) {
                $scope.errorMessage = errorService.resolve(errObj);
            } ]
        })
    }

    return errorService;

} ])

.factory('onSuccessDialogService', ['ngDialog', function (ngDialog) {

    var onSuccessDialogService = {};

    onSuccessDialogService.show = function (msg, callBack) {

        ngDialog.open({
            template: 'js/app/components/core/views/onSuccessPopUp.html',
            showClose: false,
            className: 'info-popup',
            controller: ['$scope', function ($scope) {

                $scope.successMessage = msg;

                $scope.confirm = function () {
                    if (callBack != undefined) {
                        callBack();
                    }
                    return true;
                }

            } ]

        })

    }

    return onSuccessDialogService;

} ])