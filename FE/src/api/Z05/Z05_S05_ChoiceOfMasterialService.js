import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataScreen: '/api/list-material',
  getDataByFilter: '/api/list-material/filter',
  getDataOrgGroup: '/api/list-material/filter-org'
};
const Z05_S05_ChoiceOfMasterialService = {
  getDataScreen() {
    return http.get(API_LIST.getDataScreen);
  },

  getDataByFilter(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataByFilter, { params });
  },

  getDataOrgGroup(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataOrgGroup, { params });
  }
};

export default Z05_S05_ChoiceOfMasterialService;
