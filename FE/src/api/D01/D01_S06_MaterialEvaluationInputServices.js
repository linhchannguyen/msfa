import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getMaterialEvaluationInput: '/api/material-evaluation-input',
  registerItem: '/api/material-evaluation-input/register-item'
};
const D01_S06_MaterialEvaluationInputServices = {
  getListService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getMaterialEvaluationInput, { params });
  },
  registerItemService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.registerItem, params);
  }
};

export default D01_S06_MaterialEvaluationInputServices;
