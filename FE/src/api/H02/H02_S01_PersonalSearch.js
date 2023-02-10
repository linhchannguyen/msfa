import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListSearch: '/api/person-search'
};
const H02_S01_PersonalSearch = {
  getListSearch(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListSearch, { params });
  }
};

export default H02_S01_PersonalSearch;
