import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/convention-search',
  getDataByFilter: '/api/convention-search/list'
};
const C01_S01_LectureListService = {
  getScreenData() {
    return http.get(API_LIST.getScreenData);
  },

  getDataByFilter(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataByFilter, { params });
  }
};

export default C01_S01_LectureListService;
