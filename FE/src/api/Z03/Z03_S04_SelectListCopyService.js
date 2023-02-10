import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getFacilityGroups: '/api/list-facility-group',
  getPersonGroups: '/api/list-person-group'
};
const Z03_S04_SelectListCopyService = {
  getFacilityGroups(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacilityGroups, { params });
  },

  getPersonGroups(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getPersonGroups, { params });
  }
};

export default Z03_S04_SelectListCopyService;
