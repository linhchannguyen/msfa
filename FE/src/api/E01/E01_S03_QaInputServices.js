import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getDataScreen: '/api/qa-management/get-screen-data',
  getData: '/api/qa-management/info/',
  createQa: '/api/qa-management/create-qa'
};
const E01S03_QaInputService = {
  getDataScreen(params) {
    params = enCodeParams(params);

    return http.get(API_LIST.getDataScreen, { params });
  },

  getData(question_id) {
    question_id = encodeData(question_id);

    return http.get(API_LIST.getData + question_id);
  },
  createQa(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.createQa, params);
  }
};

export default E01S03_QaInputService;
