import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getIndexTimeline: '/api/timeline-search',
  getListDataTimeline: '/api/timeline-search/list-data'
};
const F01_S04_TimelineSelectionServices = {
  getIndexTimelineService() {
    return http.get(API_LIST.getIndexTimeline);
  },

  getListDataTimelineService(params) {
    params = enCodeParams(params);
    return http.get(API_LIST.getListDataTimeline, { params });
  }
};

export default F01_S04_TimelineSelectionServices;
