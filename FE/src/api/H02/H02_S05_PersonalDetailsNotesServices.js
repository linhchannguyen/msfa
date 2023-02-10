import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';

const personDetailNotesApi = '/api/person-detail-notes';

const API_LIST = {
  getScreenData: personDetailNotesApi + '/get-screen-data',
  search: personDetailNotesApi + '/search',
  create: personDetailNotesApi + '/create',
  updateConsiderationConfirm: personDetailNotesApi + '/person-consideration-confirm',
  update: personDetailNotesApi + '/update',
  delete: personDetailNotesApi + '/delete'
};

const H02_S05_PersonalDetailsNotesServices = {
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
  fetchNotesService(id, params) {
    id = encodeData(id);
    params = enCodeParams(params);

    return http
      .post(API_LIST.search + `/${id}`, params)
      .then((res) => {
        return Promise.resolve(res.data.data);
      })
      .catch((err) => {
        return Promise.reject(err);
      });
  },
  createNote(params) {
    // params = enCodeParams(params);

    return http.post(API_LIST.create, params);
  },
  updateNote(params) {
    params = enCodeParams(params);

    return http.put(API_LIST.update, params);
  },
  deleteNote(params) {
    params = enCodeParams(params);

    return http.delete(API_LIST.delete, { params });
  },
  updateConsiderationConfirm(id) {
    id = encodeData(id);

    return http.put(API_LIST.updateConsiderationConfirm + `/${id}`);
  }
};

export default H02_S05_PersonalDetailsNotesServices;
