import http from '@/utils/httpClient';
// import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  importCsv: '/api/attendant-collective-registration/import-csv',
  exportCsv: '/api/attendant-bulk-registration-error-content-output/export-csv'
};
const C01_S05_AttendantCollectiveRegistrationServices = {
  importCsvService(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.importCsv, params);
  },
  exportCsvService(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.exportCsv, params);
  }
};

export default C01_S05_AttendantCollectiveRegistrationServices;
