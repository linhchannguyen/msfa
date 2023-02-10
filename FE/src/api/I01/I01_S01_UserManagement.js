import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListUsserMangement: '/api/user-management/user-list',
  postCreateUsser: '/api/user-management/create-user',
  deleteUser: '/api/user-management/delete-user',
  updateUser: '/api/user-management/update-user',
  getOrganization: '/api/user-management/user-organization',
  getScreenData: '/api/user-management/get-screen-data',
  deleteOrganization: '/api/user-management/delete-user-organization',
  updateOrganization: '/api/user-management/update-user-organization',
  getapproval: '/api/user-management/get-approval-user',
  updateapproval: '/api/user-management/update-approval-user',
  deleteapproval: '/api/user-management/delete-approval-user'
};
const I01_S01_UserManagementServices = {
  getListUsserMangement(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListUsserMangement, { params });
  },
  postCreateUsser(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.postCreateUsser, params);
  },
  deleteUsser(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteUser, { params });
  },
  updateUser(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.updateUser, params);
  },
  getScreenData() {
    return http.get(API_LIST.getScreenData);
  },
  getOrganization(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getOrganization, { params });
  },
  deleteOrganization(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteOrganization, { params });
  },
  updateOrganization(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.updateOrganization, params);
  },
  getapproval(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getapproval, { params });
  },
  updateapproval(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.updateapproval, params);
  },
  deleteapproval(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteapproval, { params });
  }
};
export default I01_S01_UserManagementServices;
