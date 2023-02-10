import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getDataScreen: '/api/document-search',
  getData: '/api/document-search/list'
};
const D01_S01_ListOfMaterialsService = {
  getDataScreen(params) {
    return http.get(API_LIST.getDataScreen, { params });
  },

  getData(params) {
    params = enCodeParams(params);

    return http.get(API_LIST.getData, { params });
  }
};

export default D01_S01_ListOfMaterialsService;
