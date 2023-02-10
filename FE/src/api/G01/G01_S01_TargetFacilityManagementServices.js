import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/target-facility-management/get-screen-data',
  targetFacilityManagement: '/api/target-facility-management'
};
const G01_S01_TargetFacilityManagementServices = {
  getScreenDataService() {
    return http.get(API_LIST.getScreenData);
  },
  targetFacilityManagementService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.targetFacilityManagement, { params });
  }
};

export default G01_S01_TargetFacilityManagementServices;
