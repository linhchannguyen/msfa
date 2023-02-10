import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  allPostingProhibited: '/api/posting-prohibited-user-management/get-posting-prohibited',
  getUnsuitableReport: '/api/posting-prohibited-user-management/get-unsuitable-report/',
  cancelPostingProhibited: '/api/posting-prohibited-user-management/cancel-posting-prohibited/'
};
const E01_S04_PostingProhibitedUserManagementServices = {
  allPostingProhibitedService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.allPostingProhibited, { params });
  },
  getUnsuitableReportService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getUnsuitableReport, { params });
  },
  cancelPostingProhibitedService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.cancelPostingProhibited, { params });
  }
};

export default E01_S04_PostingProhibitedUserManagementServices;
