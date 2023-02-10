import getters from '@/store/modules/auth/getters';
import actions from '@/store/modules/auth/actions';
import mutations from '@/store/modules/auth/mutations';
import { getCookie } from '@/utils/constants';

let currentUser;
let originalToken = null;
const isLoginProxy = JSON.parse(localStorage.getItem('isLoginProxy'));

if (isLoginProxy) {
  currentUser = JSON.parse(localStorage.getItem('currentUserProxy'));
  // originalToken = JSON.parse(localStorage.getItem('currentUser')).access_token;
  originalToken = getCookie('originalToken');
} else {
  currentUser = JSON.parse(localStorage.getItem('currentUser'));
}

const state = {
  isAuthorized: localStorage.getItem('isAuthorized') || false,
  accessToken: getCookie('accessToken') || null,
  currentUser: currentUser || null,
  isLoginProxy: isLoginProxy || null,
  originalToken: originalToken || null,
  policyRole: null,
  policyPermission: null,
  menus: []
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
