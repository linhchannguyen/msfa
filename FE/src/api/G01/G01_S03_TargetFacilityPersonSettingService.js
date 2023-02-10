import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/target-facility-person-setting/get-screen-data',
  targetFacilityPersonManagement: '/api/target-facility-person-setting',
  saveTargetFacilityPerson: '/api/target-facility-person-setting/save-target-facility-person-setting',
  exportFile: '/api/target-facility-person-setting/export'
};
const G01_S03_TargetFacilityPersonSettingService = {
  getScreenDataService() {
    return http.get(API_LIST.getScreenData);
  },

  targetFacilityPersonManagementService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.targetFacilityPersonManagement, params);
  },

  saveTargetFacilityPersonService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.saveTargetFacilityPerson, params);
  },

  exportFile(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.exportFile, params, { observe: 'response', responseType: 'blob' });
  }
};

export default G01_S03_TargetFacilityPersonSettingService;
