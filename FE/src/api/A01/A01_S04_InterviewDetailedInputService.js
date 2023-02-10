import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getInterviewDetailedInput: '/api/interview-detailed-input',
  checkInterviewDetailInput: '/api/interview-detailed-input/check-exist',
  getScreenDetail: '/api/interview-detailed-input/get-screen-data',
  savePlan: '/api/interview-detailed-input/save-plan',
  save: '/api/interview-detailed-input/save'
};
const A01_S04_InterviewDetailedInputService = {
  getInterviewDetailedInputService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getInterviewDetailedInput, { params });
  },
  checkInterviewDetailInput(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.checkInterviewDetailInput, { params });
  },
  getDetailAreaService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDetailArea, { params });
  },
  getScreenDetailService() {
    return http.get(API_LIST.getScreenDetail);
  },
  savePlanService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.savePlan, params);
  },
  saveService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.save, params);
  }
};

export default A01_S04_InterviewDetailedInputService;
