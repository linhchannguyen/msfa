import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  loginAPI: '/api/login',
  loginDeveloperAPI: '/api/develop/login',
  loginProxyAPI: '/api/proxy-user-selection',
  changePasswordAPI: '/api/change-password',
  policyRoleAPI: '/api/policy/role',
  policyPermissionAPI: '/api/policy/permission'
};

const loginService = (params) => {
  params = enCodeParams(params);
  return http.post(API_LIST.loginAPI, { ...params });
};

const loginDeveloperService = (params) => {
  params = enCodeParams(params);
  return http.post(API_LIST.loginDeveloperAPI, { ...params });
};

const loginProxyService = (params) => {
  params = enCodeParams(params);
  return http.post(API_LIST.loginProxyAPI, { user_cd: params.user_cd });
};

const changePasswordFirstLoginService = (params) => {
  params = enCodeParams(params);
  return http.post(API_LIST.changePasswordAPI, { ...params });
};

//policyRole
const getPolicyRoleService = (params) => {
  params = enCodeParams(params);
  return http.get(API_LIST.policyRoleAPI, { params });
};

//policyPermission
const getPolicyPermissionService = (params) => {
  params = enCodeParams(params);
  return http.get(API_LIST.policyPermissionAPI, { params });
};

const logout = () => {
  return http.get('/api/logout');
};

const validateToken = () => {
  return http.get('validate-token');
};

export default {
  loginService,
  loginDeveloperService,
  loginProxyService,
  logout,
  changePasswordFirstLoginService,
  validateToken,
  getPolicyRoleService,
  getPolicyPermissionService
};
