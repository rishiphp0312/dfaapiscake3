angular.module(appConfig.appName)
.constant('AUTH_EVENTS', {
    loginSuccess: 'auth-login-success',
    loginFailed: 'auth-login-failed',
    logoutSuccess: 'auth-logout-success',
    sessionTimeout: 'auth-session-timeout',
    notAuthenticated: 'auth-not-authenticated',
    notAuthorized: 'auth-not-authorized'
})
.constant('USER_ROLES', {
    all: '*',
    superAdmin: 'SuperAdmin',
    admin: 'Admin',
    templateUser: 'TemplateUser',
    dataUser: 'DataUser'
})