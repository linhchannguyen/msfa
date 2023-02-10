import { routeType } from '@/store/modules/router/mutationTypes';

const mutations = {
  [routeType.SET_HISTORY](state, payload) {
    // console.log('mutations', payload);
    let { href, origin, pathname, search } = payload;
    state.href = href;
    state.origin = origin;
    state.pathname = pathname;
    state.search = search;
  },
  [routeType.SET_HISTORY_FROMPATH](state, payload) {
    // console.log('mutations', payload);
    let { fromPath } = payload;
    state.fromPath = fromPath;
  },
  [routeType.SET_HISTORY_TOPATH](state, payload) {
    // console.log('mutations', payload);
    let { toPath } = payload;
    state.toPath = toPath;
  },
  [routeType.CLEAR_HISTORY](state) {
    // console.log('mutations', payload);
    state.href = null;
    state.origin = null;
    state.pathname = null;
    state.search = null;
  },
  [routeType.SET_HISTORY_MODAL](state, payload) {
    let { modal } = payload;
    state.modal = modal;
  },
  [routeType.CLEAR_MODAL](state) {
    state.modal = false;
    state.name = null;
    state.params = null;
    state.filter = null;
  },
  [routeType.SET_HISTORY_SCREEN](state, payload) {
    let { name } = payload;
    state.name = name;
  },
  [routeType.SET_HISTORY_PARAMS](state, payload) {
    let { params } = payload;
    state.params = params;
  },
  [routeType.SET_HISTORY_FILTER](state, payload) {
    let { filter } = payload;
    state.filter = filter;
  },
  [routeType.SET_LIST_HISTORY](state, payload) {
    let { toPath, fromPath } = payload;
    let len = state.listHistory.length;
    state.listHistory = [...state.listHistory.slice(len - 19, len), { toPath, fromPath }];
  },
  [routeType.SET_ITEM_INDEX](state, payload) {
    let { x, y, index, screen } = payload;
    state.itemIndex = {
      x,
      y,
      index,
      screen
    };
  }
};

export default mutations;
