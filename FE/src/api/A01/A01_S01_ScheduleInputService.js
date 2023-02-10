import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScheduleByDate: '/api/schedule/get-schedule',
  getScreenData: '/api/schedule/get-screen-data-schedule',
  updateInfoSchedule: '/api/schedule/update-schedule',
  copySchedule: '/api/schedule/copy-schedule',

  // tab 1: 訪問先
  getDataVisit: '/api/schedule/search-visit',
  addVisit: '/api/schedule/add-visit',

  // tab 2:
  addOtherInterView: '/api/schedule/add-other-than-interview',

  // tab 3: ストック
  getDataStock: '/api/schedule/search-stock',
  editStock: '/api/stock/edit',

  // tab 4: セレクト
  getFacilityOrPersonGroup: '/api/schedule/select-facility-person-group',
  getDataFacility: '/api/schedule/get-facility-group',
  getDataPerson: '/api/schedule/get-person-group',
  addFacilitySelect: '/api/schedule/add-facility-select',
  addPersonSelect: '/api/schedule/add-facility-person-select'
};
const A01_S01_ScheduleInputService = {
  getScheduleByDate(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getScheduleByDate, { params });
  },

  getScreenData(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getScreenData, { params });
  },

  updateInfoSchedule(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.updateInfoSchedule, params);
  },

  copySchedule(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.copySchedule, params);
  },

  // tab 1: 訪問先
  getDataVisit(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getDataVisit, params);
  },

  addVisit(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addVisit, params);
  },

  // tab 2:
  addOtherInterView(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addOtherInterView, params);
  },

  // tab 3: ストック
  getDataStock(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataStock, { params });
  },

  editStock(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.editStock, params);
  },

  // tab 4: セレクト
  getFacilityOrPersonGroup(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacilityOrPersonGroup, { params });
  },

  getDataFacility(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataFacility, { params });
  },

  getDataPerson(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataPerson, { params });
  },

  addFacilitySelect(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addFacilitySelect, params);
  },

  addPersonSelect(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.addPersonSelect, params);
  }
};

export default A01_S01_ScheduleInputService;
