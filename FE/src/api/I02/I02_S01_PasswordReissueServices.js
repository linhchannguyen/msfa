import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getListPasswordReissueApi: '/api/password-reissue',
  sendPasswordReissueApi: '/api/password-reissue/send-mail-password-reissue'
};
const I02_S01_PasswordReissueServices = {
  getListPassowrdReissueService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListPasswordReissueApi, { params });
  },
  sendPasswordReissueService(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.sendPasswordReissueApi, params);
  }
};
export default I02_S01_PasswordReissueServices;
