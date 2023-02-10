import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getConditionArea: '/api/list-condition-area',
  getFacilityByCondition: '/api/list-facility-personal/facility',
  // getFacilityByCondition: '/api/list-condition-area/facility', // s03
  getFacilityPersonal: '/api/list-facility-personal',
  getMedicalStaff: '/api/list-facility-personal/medical-staff'
};
const Z05_S04_facilityCustomerService = {
  getConditionArea(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getConditionArea, { params });
  },

  getFacilityByCondition(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacilityByCondition, { params });
  },

  getFacilityPersonal(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFacilityPersonal, { params });
  },

  getMedicalStaff(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getMedicalStaff, { params });
  }
};

export default Z05_S04_facilityCustomerService;
