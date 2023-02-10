import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataScreen: '/api/custom-material-management/get-screen-data',
  getDataByCondition: '/api/custom-material-management/search',
  copyDataDocument: '/api/custom-material-management/copy'
};
const D01_S04_CustomMaterialManagementService = {
  getDataScreen(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataScreen, { params });
  },

  getDataByCondition(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getDataByCondition, params);
  },

  copyDataDocument(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.copyDataDocument, params);
  }
};

export default D01_S04_CustomMaterialManagementService;
