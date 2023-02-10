import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getFacilityGroups: '/api/list-facility-group',
  updateFacilityGroup: '/api/list-facility-group/update',
  deleteFacilityGroup: '/api/list-facility-group/delete',
  changeIndex: '/api/list-facility-group/change-select'
};
const Z03_S01_SelectFacilitySettingService = {
  getFacilityGroups(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacilityGroups, { params });
  },

  updateFacilityGroup(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.updateFacilityGroup, params);
  },

  deleteFacilityGroup(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.deleteFacilityGroup, { params });
  },

  changeIndex(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.changeIndex, params);
  }
};

export default Z03_S01_SelectFacilitySettingService;
