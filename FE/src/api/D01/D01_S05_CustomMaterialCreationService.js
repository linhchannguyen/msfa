import http from '@/utils/httpClient';
import {
  encodeData
} from '@/api/base64_api';

const customDocument = '/api/document-custom/';
const API_LIST = {
  contentOriginalDocument: customDocument + 'content-original-document/',
  listOriginalDocument: customDocument + 'list-document-original',
  getScreenData: customDocument + 'create',
  create: customDocument + 'create',
  update: customDocument + 'update/',
  delete: customDocument + 'delete/',
  getDocumetUpdate: customDocument + 'update/',
  changeDisueFlagStatus: customDocument + 'change-status-disue-flag/'
};
const D01_S05_CustomMaterialCreationServices = {
  contentOriginalDocumentService(id) {
    id = encodeData(id);
    return http.get(API_LIST.contentOriginalDocument + id);
  },
  listOriginalDocumentService() {
    return http.get(API_LIST.listOriginalDocument);
  },
  getScreenData() {
    return http.get(API_LIST.getScreenData);
  },
  create(params) {
    // params = enCodeParams(params);\

    return http.post(API_LIST.create, params);
  },
  update(id, params) {
    id = encodeData(id);
    // params = enCodeParams(params);

    return http.post(API_LIST.update + id, params);
  },
  delete(id) {
    id = encodeData(id);
    return http.post(API_LIST.delete + id);
  },
  getDocumentUpdate(id) {
    id = encodeData(id);

    return http.get(API_LIST.getDocumetUpdate + id);
  },
  changeDisueFlagStatus(id, params) {
    params = encodeData(params);
    id = encodeData(id);

    return http.post(API_LIST.changeDisueFlagStatus + id + '?status=' + params);
  }
};

export default D01_S05_CustomMaterialCreationServices;