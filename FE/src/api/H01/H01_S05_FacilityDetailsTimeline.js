import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const facilityDetailTimeLineApi = '/api/facility-details-time-line';

const API_LIST = {
  getScreenData: facilityDetailTimeLineApi + '/get-screen-data',
  getTimeLine: facilityDetailTimeLineApi
};

const H01_S05_FacilityDetailsTimelineServices = {
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
      .get(API_LIST.getTimeLine, { params })
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }
};

export default H01_S05_FacilityDetailsTimelineServices;
