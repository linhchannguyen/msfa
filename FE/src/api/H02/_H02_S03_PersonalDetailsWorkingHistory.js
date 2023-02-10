import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getList: '/api/person-search/working-individual'
};
const H02_S03_PersonalDetailsWorkingHistory = {
  getList(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getList, { params });
  }
};

export default H02_S03_PersonalDetailsWorkingHistory;
