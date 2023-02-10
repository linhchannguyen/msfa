import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataScreen: '/api/interview-content-setting',
  getFilterInterviewContent: '/api/interview-content-setting/filter'
};
const Z03_S03_ConsultationContentSettingService = {
  getDataScreen() {
    return http.get(API_LIST.getDataScreen);
  },

  getFilterInterviewContent(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getFilterInterviewContent, { params });
  }
};

export default Z03_S03_ConsultationContentSettingService;
