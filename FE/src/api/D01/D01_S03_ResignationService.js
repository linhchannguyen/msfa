import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getRegistration: '/api/document-search/registration',
  getRegistrationDetail: '/api/document-search/registration/detail',
  saveRegistration: '/api/document-search/registration/save'
};
const D01_S03_ResignationService = {
  getRegistration() {
    return http.get(API_LIST.getRegistration);
  },

  getRegistrationDetail(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getRegistrationDetail, { params });
  },

  saveRegistration(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.saveRegistration, params);
  },
  deleteRegistration(params) {
    params = enCodeParams(params);
    return http.delete('/api/document-search/registration/delete', { params });
  }
};

export default D01_S03_ResignationService;
