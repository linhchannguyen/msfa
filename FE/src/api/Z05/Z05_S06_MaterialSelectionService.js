import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataScreen: '/api/list-item',
  getDataByFilter: '/api/list-item/filter',
  register: '/api/list-item/change-select'
};

const Z05_S06_MaterialSelectionService = {
  getDataScreen() {
    return http.get(API_LIST.getDataScreen);
  },

  getDataByFilter(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataByFilter, { params });
  },

  register(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.register, params);
  }
};

export default Z05_S06_MaterialSelectionService;
