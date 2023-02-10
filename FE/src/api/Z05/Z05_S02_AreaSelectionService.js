import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getRegions: '/api/list-region',
  getPrefectureByRegions: '/api/list-region/prefecture',
  getMunicipalityByPrefectures: '/api/list-region/prefecture/municipality'
};
const Z05_S02_AreaSelectionService = {
  getRegions(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getRegions, { params });
  },

  getPrefectureByRegions(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getPrefectureByRegions, { params });
  },

  getMunicipalityByPrefectures(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getMunicipalityByPrefectures, { params });
  }
};

export default Z05_S02_AreaSelectionService;
