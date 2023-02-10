import http from '@/utils/httpClient';
import { encodeData } from '@/api/base64_api';
const API_LIST = {
  getListDocument: '/api/document/slide-show/'
};
const D01_S07_MaterialViewerServices = {
  getListDocumentService(id) {
    id = encodeData(id);
    return http.get(API_LIST.getListDocument + id);
  }
};

export default D01_S07_MaterialViewerServices;
