import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getListConditionArea: '/api/list-condition-area',
  getListConditionAreaFacility: '/api/list-condition-area/facility'
};

const Z05_S03_FacilitySelectionServices = {
  getListAreaService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListConditionArea, { params });
  },
  getListAreaFacilityService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListConditionAreaFacility, { params });
  }
};

export default Z05_S03_FacilitySelectionServices;
