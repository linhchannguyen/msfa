import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getDataRecommendPeriod: '/api/recommended-period-confirmation'
};
const I01_S03_ApplicationPeriodConfirmationService = {
  getDataRecommendPeriod(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getDataRecommendPeriod, { params });
  }
};

export default I01_S03_ApplicationPeriodConfirmationService;
