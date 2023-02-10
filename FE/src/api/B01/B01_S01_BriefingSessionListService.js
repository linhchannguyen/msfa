import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/briefing-search',
  getDataByFilter: 'api/briefing-search/list-data'
};
const B01_S01_BriefingSessionListService = {
  getScreenData() {
    return http.get(API_LIST.getScreenData);
  },

  getDataByFilter(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataByFilter, { params });
  }
};

export default B01_S01_BriefingSessionListService;
