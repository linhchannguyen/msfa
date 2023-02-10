import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListMessageApi: '/api/message',
  createMessageApi: '/api/message',

  deleteMessageApi: '/api/message',
  editMessageApi: '/api/message'
};
const Z02_S02_MessageServices = {
  getListMessageService() {
    return http.get(API_LIST.getListMessageApi);
  },
  getMessageByIdService(id) {
    id = encodeData(id);
    return http.get(`/api/message/${id}`);
  },
  createMessageService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.createMessageApi, params);
  },
  deleteMessageService(id) {
    id = encodeData(id);
    return http.delete(`/api/message/${id}`);
  },
  editMessageService(id, params) {
    params = enCodeParams(params);
    id = encodeData(id);
    return http.put(`/api/message/${id}`, params);
  }
};
export default Z02_S02_MessageServices;
