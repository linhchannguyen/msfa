import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  postData: '/api/attendant-management/add-convention-attendee',
  getList: '/api/attendant-management/search',
  expFile: '/api/export/list-attendee',
  screen: '/api/attendant-management/get-screen-data'
};
const C01_S03_AttendantManagement = {
  getScreenData(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.screen, { params });
  },
  postData(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.postData, params);
  },
  getList(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getList, params);
  },
  expFile(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.expFile, params, { observe: 'response', responseType: 'blob' });
  }
};

export default C01_S03_AttendantManagement;
