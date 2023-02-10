import http from '@/utils/httpClient';
import { enCodeParams } from '@/api/base64_api';

const facilityDetailNotesApi = '/api/facility-details-notes';

const API_LIST = {
  getScreenData: facilityDetailNotesApi + '/get-screen-data',
  getNote: facilityDetailNotesApi,
  create: facilityDetailNotesApi + '/save-new-mode',
  updateConsiderationConfirm: facilityDetailNotesApi + '/save-consideration-confirm',
  update: facilityDetailNotesApi + '/update-mode',
  delete: facilityDetailNotesApi + '/delete-consideration'
};

const H01_S04_FacilityDetailsNotesServices = {
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
  fetchNotesService(params) {
    params = enCodeParams(params);

    return http
      .get(API_LIST.getNote, { params })
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  },
  createNote(params) {
    params = enCodeParams(params);

    return http.post(API_LIST.create, params);
  },
  updateNote(params) {
    params = enCodeParams(params);

    return http.post(API_LIST.update, params);
  },
  deleteNote(params) {
    params = enCodeParams(params);

    return http.delete(API_LIST.delete, { params });
  },
  updateConsiderationConfirm(params) {
    params = enCodeParams(params);

    return http.post(API_LIST.updateConsiderationConfirm, params);
  }
};

export default H01_S04_FacilityDetailsNotesServices;
