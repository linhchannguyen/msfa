import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getPersonGroups: '/api/list-person-group',
  updatePersonGroup: '/api/list-person-group/update',
  deletePersonGroup: '/api/list-person-group/delete',
  changeIndex: '/api/list-person-group/change-select'
};
const Z03_S02_SelectFacilityPersonalSettingService = {
  getPersonGroups(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getPersonGroups, { params });
  },

  updatePersonGroup(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.updatePersonGroup, params);
  },

  deletePersonGroup(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deletePersonGroup, { params });
  },

  changeIndex(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.changeIndex, params);
  }
};

export default Z03_S02_SelectFacilityPersonalSettingService;
