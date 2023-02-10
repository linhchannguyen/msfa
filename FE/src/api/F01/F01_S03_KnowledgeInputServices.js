import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getScreenData: '/api/knowledge-input/get-screen-data',
  getKnowledgeInput: '/api/knowledge-input',
  delete: '/api/knowledge-input/delete-knowledge',
  submit: '/api/knowledge-input/submit',
  approval: '/api/knowledge-input/approve',
  remand: '/api/knowledge-input/remand',
  public: '/api/knowledge-input/public',
  rejection: '/api/knowledge-input/rejection',
  noPublic: '/api/knowledge-input/none-public',
  createUpdate: '/api/knowledge-input/update-and-create'
};
const F01_S03_KnowledgeInputServices = {
  getScreenDataService() {
    return http.get(API_LIST.getScreenData);
  },
  getKnowledgeInputService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getKnowledgeInput, { params });
  },
  deleteService(params) {
    params = enCodeParams(params);
    return http.delete(API_LIST.delete, { params });
  },
  submitService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.submit, params);
  },
  approvalService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.approval, params);
  },
  remandService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.remand, params);
  },
  publicService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.public, params);
  },
  rejectionService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.rejection, params);
  },
  noPublicService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.noPublic, params);
  },
  createUpdateService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.createUpdate, params);
  }
};

export default F01_S03_KnowledgeInputServices;
