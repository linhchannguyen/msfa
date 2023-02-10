import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getListMessageApi: 'api/message',
  getMessageByIdApi: 'api/message',
  deleteMessageApi: 'api/message',
  editMessageApi: 'api/message',
  createMessageApi: 'api/message'
};
const Z02_S02_MessageServices = {
  getListMessageService() {
    return http.get(API_LIST.getListMessageApi);
  },
  getMessageByIdService(id) {
    id = encodeData(id);
    return http.get(API_LIST.getMessageByIdApi / { id }, { id });
  },
  deleteMessageService(id) {
    id = encodeData(id);
    return http.delete(API_LIST.getMessageByIdApi / { id }, { id });
  },
  createMessageService(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.getMessageByIdApi, params);
  },
  editMessageService(id) {
    id = encodeData(id);
    return http.put(API_LIST.editMessageApi / { id }, { id });
  }
};
export default Z02_S02_MessageServices;
