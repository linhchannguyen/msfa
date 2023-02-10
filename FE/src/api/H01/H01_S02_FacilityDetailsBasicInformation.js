import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getList: '/api/facility-search/basic-information',
  saveList: '/api/facility-search/basic-information/consultation-time',
  getDetail: '/api/facility-search/information'
};
const H01_S02_FacilityDetailsBasicInformation = {
  getList(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getList, { params });
  },
  getDetail(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDetail, { params });
  },
  saveList(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.saveList, params);
  }
};

export default H01_S02_FacilityDetailsBasicInformation;
