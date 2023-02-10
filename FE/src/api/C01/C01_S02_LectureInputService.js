import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenDataInfo: '/api/convention-search/index',
  getDataDetail: '/api/convention-search/detail',
  selectionSeries: '/api/convention-search/detail/selection-series',

  copyData: '/api/convention-search/detail/copy',
  savePlan: '/api/convention-search/detail/save-plan',
  submitPlan: '/api/convention-search/detail/submit-plan',
  saveResult: '/api/convention-search/detail/save-result',
  submitResult: '/api/convention-search/detail/submit-result',
  cancelSubmit: '/api/convention-search/detail/cancel-submit',
  remand: '/api/convention-search/detail/remand',
  approval: '/api/convention-search/detail/approval',
  pending: '/api/convention-search/detail/pending',
  delete: '/api/convention-search/detail/delete'
};
const C01_S02_LectureInputService = {
  getScreenDataInfo() {
    return http.get(API_LIST.getScreenDataInfo);
  },

  getDataDetail(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataDetail, { params });
  },

  selectionSeries(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.selectionSeries, { params });
  },

  copyData(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.copyData, { params });
  },

  savePlan(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.savePlan, params);
  },

  submitPlan(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.submitPlan, params);
  },

  saveResult(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.saveResult, params);
  },

  submitResult(params) {
    // params = enCodeParams(params);
    return http.post(API_LIST.submitResult, params);
  },

  cancelSubmit(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.cancelSubmit, params);
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
    return http.delete(API_LIST.pending, { params });
  },

  delete(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.delete, { params });
  }
};

export default C01_S02_LectureInputService;
