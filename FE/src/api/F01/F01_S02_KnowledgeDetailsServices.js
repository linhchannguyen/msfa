import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  getData: '/api/knowledge-management/knowledge-detail',
  registerKnowledgeLike: '/api/knowledge-management/register-knowledge-nice',
  registerComment: '/api/knowledge-management/register-comment',
  deleteNice: '/api/knowledge-management/delete-knowledge-nice',
  registerKnowledge: '/api/knowledge-management/register-knowledge-request'
};

const F01_S02_KnowledgeDetailsServices = {
  getDetail(id, params) {
    id = encodeData(id);
    params = enCodeParams(params);
    return http.get(API_LIST.getData + `/${id}`, { params });
  },
  registerLike(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.registerKnowledgeLike, params);
  },
  registerComment(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.registerComment, params);
  },
  registerKnowledge(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.registerKnowledge, params);
  },
  deleteNice(params) {
    params = enCodeParams(params);
    return http.put(API_LIST.deleteNice, params);
  }
};

export default F01_S02_KnowledgeDetailsServices;
