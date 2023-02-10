import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';

const API_LIST = {
  getListMessageApi: '/api/widget/message',
  getInformListApi: '/api/widget/inform',
  InformedApi: '/api/widget/informed',
  archivedApi: '/api/inform/archived',
  activityPlan: '/api/widget/activity-plan',
  externalUrl: '/api/widget/external-url',
  nonSubmission: '/api/widget/non-submission',
  ranKing: '/api/widget/actual-digestion-ranking',
  lineChart: '/api/widget/before-sales-results',
  barChart: '/api/widget/actual-digestion-process'
};
const Z02_S01_HomeServices = {
  getListMessageService() {
    return http.get(API_LIST.getListMessageApi);
  },
  readMessageService(id) {
    id = encodeData(id);

    return http.post(`/api/widget/message/${id}`);
  },
  getMessageByIdService(id) {
    id = encodeData(id);

    return http.get(`/api/message/${id}`);
  },
  getInformListService() {
    return http.get(API_LIST.getInformListApi);
  },
  InformedApiService() {
    return http.put(API_LIST.InformedApi);
  },
  archivedService(params) {
    params = enCodeParams(params);

    return http.put(API_LIST.archivedApi, params);
  },
  getactivityPlan() {
    return http.get(API_LIST.activityPlan);
  },
  getExternalUrl() {
    return http.get(API_LIST.externalUrl);
  },
  getNonSubmission() {
    return http.get(API_LIST.nonSubmission);
  },
  getRanKing(params) {
    params = enCodeParams(params);

    return http.get(API_LIST.ranKing, { params });
  },
  getWidget() {
    return http.get('/api/widget');
  },
  getSaleChartLine(params) {
    params = enCodeParams(params);

    return http.get(API_LIST.lineChart, { params });
  },
  getActualDigestionChartBar(params) {
    params = enCodeParams(params);

    return http.get(API_LIST.barChart, { params });
  }
};
export default Z02_S01_HomeServices;
