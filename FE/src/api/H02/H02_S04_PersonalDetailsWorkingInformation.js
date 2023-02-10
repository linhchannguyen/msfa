import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const personDetailWorking = '/api/personal-details-working-information';

const API_LIST = {
  getScreenData: personDetailWorking + '/get-screen-data',
  getData: personDetailWorking
};

const H02_S04_PersonalDetailsWorkingInformationService = {
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
  fetchData(params) {
    params = enCodeParams(params);
    return http
      .post(API_LIST.getData, params)
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  }
};

export default H02_S04_PersonalDetailsWorkingInformationService;
