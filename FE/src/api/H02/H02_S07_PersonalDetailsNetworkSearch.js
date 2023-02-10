import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListSearch: '/api/person-detail-network/search'
};
const H02_S07_PersonalDetailsNetworkSearch = {
  getScreenData(personId) {
    personId = encodeData(personId);
    return http.get(`/api/person-detail-network/get-screen-data/${personId}`);
  },
  getListSearch(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getListSearch, params);
  },
  getListModal(personId) {
    personId = encodeData(personId);
    return http.get(`/api/person-detail-network/workplace-history/${personId}`);
  }
};

export default H02_S07_PersonalDetailsNetworkSearch;
