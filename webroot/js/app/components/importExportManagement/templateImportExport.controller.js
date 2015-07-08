angular.module('DataAdmin.importExportManagement')
.controller('templateImportExportController', ['$scope', '$stateParams', 'templateImportService', 'commonService', 'SERVICE_CALL', 'errorService', 'onSuccessDialogService', function ($scope, $stateParams, templateImportService, commonService, SERVICE_CALL, errorService, onSuccessDialogService) {

    $scope.progressStatus = {
        ius: 0,
        area: 0
    }

    $scope.files = {
        iusFile: '',
        areaFile: ''
    }

    $scope.dbId = $stateParams.dbId;

    $scope.generateFileDataIcIus = {
        url: commonService.createServiceCallUrl(SERVICE_CALL.templateManagement.importFile),
        fields: { 'dbId':  $stateParams.dbId, type: 'ICIUS' },
        sendFieldsAs: 'form'
    };

    $scope.generateFileDataArea = {
        url: commonService.createServiceCallUrl(SERVICE_CALL.templateManagement.importFile),
        fields: { 'dbId':  $stateParams.dbId, type: 'AREA' },
        sendFieldsAs: 'form'
    };

    $scope.onFileSuccess = function (successObj, fileData) {

        var uploadType = fileData.fields.type;

        var msg = (uploadType == 'ICIUS' ? 'IUS and Indicator Classifications have been imported successfully.' : 'Geographic Areas have been imported successfully.');

        onSuccessDialogService.show(msg, function () {
            if (uploadType == 'ICIUS')
                $scope.progressStatus.ius = 0;

            if (uploadType == 'AREA')
                $scope.progressStatus.area = 0;
        });
    }

    $scope.onFileFail = function (err, fileData) {

        var uploadType = fileData.fields.type;

        errorService.show(err);

        if (uploadType == 'ICIUS') {
            $scope.progressStatus.ius = 0;
            $scope.files.iusFile = '';
        }

        if (uploadType == 'AREA') {
            $scope.progressStatus.area = 0;
            $scope.files.areaFile = '';
        }

    }

} ])