import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  // 面談詳細
  getActiveDate: '/api/interview-details/get-active-date',
  getInterviewContent: '/api/interview-details/get-interview-content',
  getDateTimeSetting: '/api/interview-details/get-date-time-setting',
  saveDateTimeSetting: '/api/interview-details/save-date-time-setting',
  deleteInterviewer: '/api/interview-details/delete-interviewer',
  deleteAllInterview: '/api/interview-details/delete-all-interviewer',
  addInterviewDestination: '/api/interview-details/add-interview-destination',
  checkPersonCd: '/api/interview-details/check-person-cd',
  addStock: '/api/interview-details/add-stock',
  createPlanSchedule: '/api/interview-details/create-plan-schedule',
  deleteSchedule: '/api/interview-details/delete-schedule',
  // 社内予定
  getListInhouse: '/api/interview-details/get-screen-data',
  internalScheduleInhouse: '/api/interview-details/internal-schedule',
  saveInhouse: '/api/interview-details/save-internal-schedule',
  deleteInhouse: '/api/interview-details/delete-internal-schedule',
  // プライベート
  privateSchedule: '/api/interview-details/private-schedule',
  savePrivate: '/api/interview-details/save-private-schedule',
  deletePrivate: '/api/interview-details/delete-private-schedule'
};
const A01_S03_InterviewDetailsServices = {
  // 面談詳細
  getActiveDateService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getActiveDate, { params });
  },
  getInterviewContentService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getInterviewContent, { params });
  },
  getDateTimeSettingService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDateTimeSetting, { params });
  },
  saveDateTimeSettingService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.saveDateTimeSetting, params);
  },
  deleteInterviewerService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteInterviewer, { params });
  },
  deleteAllInterviewService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteAllInterview, { params });
  },
  addInterviewDestinationService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addInterviewDestination, params);
  },
  checkPersonCdService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.checkPersonCd, params);
  },
  addStockService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addStock, params);
  },
  createPlanScheduleService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.createPlanSchedule, params);
  },
  deleteScheduleService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteSchedule, { params });
  },
  // 社内予定
  getListInhouseService() {
    return http.get(API_LIST.getListInhouse);
  },
  internalScheduleInhouseService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.internalScheduleInhouse, { params });
  },
  saveInhouseService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.saveInhouse, params);
  },
  deleteInhouseService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteInhouse, { params });
  },
  // プライベート
  privateScheduleService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.privateSchedule, { params });
  },
  savePrivateService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.savePrivate, params);
  },
  deletePrivateService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deletePrivate, { params });
  }
};

export default A01_S03_InterviewDetailsServices;
