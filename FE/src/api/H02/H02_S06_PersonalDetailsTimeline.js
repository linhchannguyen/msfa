import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const personDetailTimeLineApi = '/api/person-detail-time-line';

const API_LIST = {
  getScreenData: personDetailTimeLineApi + '/get-screen-data',
  getTimeLine: personDetailTimeLineApi + '/search'
};

const H02S06PersonalDetailsTimelineServices = {
  getScreenDataService(params) {
    params = enCodeParams(params);

    return http

      .get(API_LIST.getScreenData, { params })
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  },
  fetchTimeLineService(params) {
    params = enCodeParams(params);

    return http
      .post(API_LIST.getTimeLine, params)
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }
};

export default H02S06PersonalDetailsTimelineServices;
