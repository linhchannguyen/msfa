import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getList: '/api/facility-search/working-individual',
  putList: '/api/facility-search/working-individual/segment-type',
  getIndex: '/api/facility-search/working-individual/index'
};
const H01_S03_FacilityDetailsWorkingIndividual = {
  getIndex() {
    return http.get(API_LIST.getIndex);
  },
  getList(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getList, { params });
  },
  putList(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.putList, params);
  }
};

export default H01_S03_FacilityDetailsWorkingIndividual;
