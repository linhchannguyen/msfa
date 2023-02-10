import http from '@/utils/httpClient';
import { encodeData, enCodeParams } from '@/api/base64_api';
const API_LIST = {
  registerAnswer: '/api/manage-qa/answer/register',
  questionRegisterUnsuitableReport: '/api/qa-management/search',
  answerUpdateUnsuitableReport: '/api/qa-management/get-screen-data',
  cancelInformAnswer: '/api/qa-management/search'
};
const E01_S02_QaDetailsServices = {
  infomationQuestion(question_id) {
    question_id = encodeData(question_id);
    return http.get(`/api/manage-qa/question/${question_id}`);
  },
  InfoAnswer(question_id, params) {
    question_id = encodeData(question_id);
    params = enCodeParams(params);
    return http.get(`/api/manage-qa/answer/list/${question_id}`, { params });
  },
  registerBestAnswer(answer_id) {
    answer_id = encodeData(answer_id);
    return http.post(`/api/manage-qa/answer/register-best-answer/${answer_id}`);
  },
  questionRegisterUnsuitableReport(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.questionRegisterUnsuitableReport, params);
  },
  answerUpdateUnsuitableReport(params) {
    params = enCodeParams(params);
    return http.post(API_LIST.answerUpdateUnsuitableReport, params);
  },
  openAcceptAnswer(question_id) {
    question_id = encodeData(question_id);
    return http.post(`/api/manage-qa/question/accept-answer/${question_id}`);
  },
  stopAcceptAnswer(question_id) {
    question_id = encodeData(question_id);
    return http.post(`/api/manage-qa/question/stop-accept-answer/${question_id}`);
  },
  deleteQuestion(question_id, params) {
    question_id = encodeData(question_id);
    params = enCodeParams(params);
    return http.post(`/api/manage-qa/question/delete/${question_id}`, params);
  },
  deleteAnswer(answer_id) {
    answer_id = encodeData(answer_id);
    return http.post(`/api/manage-qa/answer/delete/${answer_id}`);
  },
  registerQuestion(question_id, params) {
    question_id = encodeData(question_id);
    params = enCodeParams(params);
    return http.post(`/api/manage-qa/question/unsuitable-report/register/${question_id}`, params);
  },
  cancelInformQuestion(question_id) {
    question_id = encodeData(question_id);
    return http.post(`/api/manage-qa/question/cancel-inform-all/${question_id}`);
  },
  cancelInformAnswer(question_id) {
    question_id = encodeData(question_id);
    return http.post(`/api/manage-qa/answer/cancel-inform-all/${question_id}`);
  },
  listQuestionReport(question_id) {
    question_id = encodeData(question_id);
    return http.get(`/api/manage-qa/question/list-unsuitable-report/${question_id}`);
  },
  prohibitedPosting(user_cd) {
    user_cd = encodeData(user_cd);
    return http.post(`/api/manage-qa/register-posting-prohibited/${user_cd}`);
  },
  registerAnswer(answer_id, params) {
    answer_id = encodeData(answer_id);
    params = enCodeParams(params);
    return http.post(`/api/manage-qa/answer/unsuitable-report/register/${answer_id}`, params);
  },
  listAnswerReport(answer_id) {
    answer_id = encodeData(answer_id);
    return http.get(`/api/manage-qa/answer/list-unsuitable-report/${answer_id}`);
  },
  registerAnswerForQuestion(params) {
    params = enCodeParams(params);
    return http.post('/api/manage-qa/answer/register', params);
  },
  cancelInformQuestionUser(question_id) {
    question_id = encodeData(question_id);
    return http.post(`/api/manage-qa/question/cancel-inform/${question_id}`);
  },
  cancelInformAnswerUser(answer_id) {
    answer_id = encodeData(answer_id);
    return http.post(`/api/manage-qa/answer/cancel-inform/${answer_id}`);
  }
};

export default E01_S02_QaDetailsServices;
