import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getOrganizations: '/api/list-organization-management',
  getUserByOrganization: '/api/list-organization-management/user'
};
const Z05_S07_OrganizationMasterService = {
  getOrganizations(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getOrganizations, { params });
  },

  getUserByOrganization(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getUserByOrganization, { params });
  }
};

export default Z05_S07_OrganizationMasterService;
