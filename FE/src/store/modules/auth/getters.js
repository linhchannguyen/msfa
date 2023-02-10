export default {
  isAuthorized: (state) => {
    return state.isAuthorized;
  },
  currentUser: (state) => {
    return state.currentUser;
  },
  policyRole: (state) => {
    return state.policyRole;
  },
  policyPermission: (state) => {
    return state.policyPermission;
  },
  isLoginProxy: (state) => {
    return state.isLoginProxy;
  }
};
