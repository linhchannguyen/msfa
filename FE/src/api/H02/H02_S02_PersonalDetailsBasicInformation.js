import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  Information: '/api/person-search/information',
  BasicInformation: '/api/person-search/basic-information',
  putMedicalOffice: '/api/person-search/basic-information/medical-office',
  department: '/api/person-search/basic-information/department'
};
const H02_S02_PersonalDetailsBasicInformation = {
  getInformation(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.Information, { params });
  },
  getDepartment(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.department, { params });
  },
  getBasicInformation(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.BasicInformation, { params });
  },
  updateMedicalOffice(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.putMedicalOffice, params);
  }
};

export default H02_S02_PersonalDetailsBasicInformation;
