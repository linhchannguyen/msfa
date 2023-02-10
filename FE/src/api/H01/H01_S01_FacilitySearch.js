import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getFacility: '/api/facility-search'
};
const H01_S01_FacilitySearchService = {
  getFacility(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacility, { params });
  }
};

export default H01_S01_FacilitySearchService;
