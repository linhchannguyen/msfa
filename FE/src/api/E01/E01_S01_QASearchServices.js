import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataScreen: '/api/qa-management/get-screen-data',
  getData: '/api/qa-management/search'
};
const E01_S01_QASearchServices = {
  getDataScreen() {
    return http.get(API_LIST.getDataScreen);
  },
  getData(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getData, params);
  }
};

export default E01_S01_QASearchServices;
