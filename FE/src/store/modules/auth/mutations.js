import { authTypes } from '@/store/modules/auth/mutationTypes';

const mutations = {
  [authTypes.SET_AUTHORIZED](state, payload) {
    state.isAuthorized = payload;
  },
  [authTypes.SET_ACCESS_TOKEN](state, payload) {
    state.accessToken = payload;
  },
  [authTypes.SET_CURRENT_USER](state, payload) {
    state.currentUser = payload;
  },
  [authTypes.SET_IS_LOGIN_PROXY](state, payload) {
    state.isLoginProxy = payload;
  },
  [authTypes.SET_ORIGINAL_TOKEN](state, payload) {
    state.originalToken = payload;
  },

  [authTypes.SET_POLICY_ROLE](state, payload) {
    state.policyRole = payload;
  },
  [authTypes.SET_POLICY_PERMISSION](state, payload) {
    state.policyPermission = payload;
  },
  [authTypes.SET_MENUS](state, payload) {
    state.menus = payload;
  }
};

export default mutations;
