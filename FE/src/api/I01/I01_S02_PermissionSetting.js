import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreen: '/api/permission-setting/get-screen-data',
  getList: '/api/permission-setting',
  update: '/api/permission-setting/upsert-permission',
  delete: '/api/permission-setting/delete-permission'
};
const I01_S01_UserManagementServices = {
  getScreen() {
    return http.get(API_LIST.getScreen);
  },
  getList(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getList, { params });
  },
  update(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.update, params);
  },
  delete(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.delete, { params });
  }
};
export default I01_S01_UserManagementServices;
