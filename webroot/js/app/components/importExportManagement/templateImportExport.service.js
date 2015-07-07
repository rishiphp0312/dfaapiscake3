angular.module('DataAdmin.importExportManagement')
.factory('templateImportService', ['$q', 'SERVICE_CALL', 'commonService', 'Upload', function ($q, SERVICE_CALL, commonService, Upload) {

    var templateImportService = {};

    templateImportService.uploadFile = function (dbId, file, type, progressCallBack) {
        var deffered = $q.defer();

        Upload.upload({
            url: commonService.createServiceCallUrl(SERVICE_CALL.templateManagement.importFile),
            file: file,
            fields: { 'dbId': dbId, 'type': type },
            sendFieldsAs: 'form'
        }).progress(function (evt) {
            var progressPercentage = parseInt(100.0 * evt.loaded / evt.total);
            progressCallBack(progressPercentage);
        }).success(function (res) {
            if (res.success) {
                deffered.resolve(res.success);
            } else {
                deffered.reject(res.err);
            }
        })

        return deffered.promise;

    }

    return templateImportService;

} ])