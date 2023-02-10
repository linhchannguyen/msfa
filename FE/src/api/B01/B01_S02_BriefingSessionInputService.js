import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenDataInfo: '/api/briefing-search/index-detail',
  getDataDetail: '/api/briefing-search/detail',

  saveData: '/api/briefing-search/save-detail',
  submitData: '/api/briefing-search/submit',
  cancelSubmit: '/api/briefing-search/cancel-submit',
  approval: '/api/briefing-search/approval',
  remand: '/api/briefing-search/remand',
  pending: '/api/briefing-search/pending',
  delete: '/api/briefing-search/delete-detail'
};
const B01_S02_BriefingSessionInputService = {
  getScreenDataInfo() {
    return http.get(API_LIST.getScreenDataInfo);
  },

  getDataDetail(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataDetail, { params });
  },

  saveData(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.saveData, params);
  },

  submitData(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.submitData, params);
  },

  cancelSubmit(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.cancelSubmit, params);
  },

  remand(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.remand, params);
  },

  approval(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.approval, params);
  },

  pending(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.pending, params);
  },

  delete(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.delete, { params });
  }
};

export default B01_S02_BriefingSessionInputService;
