import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getDataScreen: 'api/knowledge-management/get-screen-data',
  getData: '/api/knowledge-management/search-pre'
};
const F01_S05_Pre_ReleaseKnowledgeManagementService = {
  getDataScreen(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataScreen, { params });
  },

  getData(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getData, params);
  }
};

export default F01_S05_Pre_ReleaseKnowledgeManagementService;
