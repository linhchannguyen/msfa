import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getCalendar: '/api/calendar',
  getDailyReport: '/api/daily-report',
  getListOrg: '/api/list-organization/user',
  getMode: '/api/daily-report/mode'
};

const A03_S01_JapaneseDailyReportListServices = {
  getCalendarService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getCalendar, { params });
  },
  getDailyReportService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDailyReport, { params });
  },
  getListOrgService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListOrg, { params });
  },
  getModeService() {
    return http.get(API_LIST.getMode);
  }
};

export default A03_S01_JapaneseDailyReportListServices;
