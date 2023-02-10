import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListSearch: '/api/document-search/detail',
  getDataDetail: '/api/document-search/detail/profile',
  getDataDetailT2: '/api/document-search/detail/review-comment',
  getDataDetailT3: '/api/document-search/detail/custom'
};
const D01_S02_MaterialDetailsService = {
  getListSearch(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListSearch, { params });
  },
  getDataDetail(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataDetail, { params });
  },
  getDataDetailT2(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataDetailT2, { params });
  },
  getDataDetailT3(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataDetailT3, { params });
  }
};

export default D01_S02_MaterialDetailsService;
