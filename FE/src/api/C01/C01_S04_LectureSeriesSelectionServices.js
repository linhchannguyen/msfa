import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getLectureSeriesSelection: '/api/lecture-series-selection',
  register: '/api/lecture-series-selection/register'
};
const C01_S04_LectureSeriesSelectionServices = {
  getListService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getLectureSeriesSelection, { params });
  },
  registerService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.register, params);
  }
};

export default C01_S04_LectureSeriesSelectionServices;
