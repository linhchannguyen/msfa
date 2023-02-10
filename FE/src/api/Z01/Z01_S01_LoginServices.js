import http from '@/utils/httpClient';
const API_LIST = {
  getNameLogin: '/api/get-system-parameter',
};
const Z01_S01_LoginServices = {
  getNameLogin() {
    return http.get(API_LIST.getNameLogin);
  },
};
export default Z01_S01_LoginServices;
