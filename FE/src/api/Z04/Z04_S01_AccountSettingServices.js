import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getUserInfo: '/api/account/info',
  getListPoint: '/api/account/point',
  changeAccountInfo: '/api/account/info',
  changePassword: '/api/change-password',
  getListTargetType: '/api/account/point_target_type',
  changeAvatar: '/api/account/avatar'
};

const Z04_S01_AccountSettingServices = {
  getUserInfoService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getUserInfo, { params });
  },
  getListPointService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListPoint, { params });
  },
  changeAccountInfoService(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.changeAccountInfo, params);
  },
  changePasswordService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.changePassword, params);
  },
  getListTargetTypeService() {
    return http.get(API_LIST.getListTargetType);
  },
  changeAvatarService(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.changeAvatar, params);
  }
};

export default Z04_S01_AccountSettingServices;
