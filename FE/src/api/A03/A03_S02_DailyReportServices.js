import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getDailyReportDetail: '/api/daily-report/detail',
  registerVacationReport: '/api/daily-report/vacation',
  deleteVacationReport: '/api/daily-report/vacation',
  temporarilyVacationReport: '/api/daily-report/temporarily-save',
  submitReport: '/api/daily-report/submit',
  deleteReport: '/api/daily-report/submit',
  approvalReport: '/api/daily-report/approval',
  cancelApprovalReport: '/api/daily-report/approval'
};

const A03_S02_DailyReportServices = {
  getDailyReportDetailService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDailyReportDetail, { params });
  },
  registerVacationReportService(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.registerVacationReport, params);
  },
  deleteVacationReportService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteVacationReport, { params });
  },
  temporarilyVacationReportService(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.temporarilyVacationReport, params);
  },
  submitReportService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.submitReport, params);
  },
  deleteReportService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteReport, { params });
  },
  approvalReportService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.approvalReport, params);
  },
  cancelApprovalReportService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.cancelApprovalReport, { params });
  }
};

export default A03_S02_DailyReportServices;
