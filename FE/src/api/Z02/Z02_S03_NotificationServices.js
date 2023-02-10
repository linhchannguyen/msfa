import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getInformCategoryApi: '/api/inform-category',
  postInformApi: '/api/inform',
  putInformArchivedApi: '/api/inform/archived',
  getInformInstalledApi: '/api/inform/installed',
  postInformRegisterApi: '/api/inform/register',
  getListInformedApi: '/api/widget/inform'
};
const Z02_S03_NotificationServices = {
  getInformCategoryService() {
    return http.get(API_LIST.getInformCategoryApi);
  },
  postInformService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.postInformApi, params);
  },
  putInformArchivedService(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.putInformArchivedApi, params);
  },
  getInformInstalledService() {
    return http.get(API_LIST.getInformInstalledApi);
  },
  postInformRegisterService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.postInformRegisterApi, params);
  },
  getListInformedService() {
    return http.get(API_LIST.getListInformedApi);
  },
  readServies(id) {
    id = encodeData(id);
    return http.put(`/api/inform/read-inform/${id}`);
  }
};
export default Z02_S03_NotificationServices;
