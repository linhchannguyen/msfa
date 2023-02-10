import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/in-facility-target-person-management/get-screen-data',
  targetPersonManagement: '/api/in-facility-target-person-management',
  saveTargetPerson: '/api/in-facility-target-person-management/save-in-facility-target-person',
  exportFile: '/api/in-facility-target-person-management/export'
};
const G01_S02_In_FacilityTargetPersonalManagementService = {
  getScreenDataService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getScreenData, { params });
  },

  targetPersonManagementService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.targetPersonManagement, params);
  },

  saveTargetPersonService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.saveTargetPerson, params);
  },

  exportFile(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.exportFile, params, { observe: 'response', responseType: 'blob' });
  }
};

export default G01_S02_In_FacilityTargetPersonalManagementService;
