/* eslint-disable no-useless-escape */
/* eslint-disable max-len */
import { routeType } from '@/store/modules/router/mutationTypes';
/**
 *
 * @param {*} param0
 * @param {modal, name, params} data
 */
const setScreenInfor = async ({ commit }, data) => {
  let { name, params, modal, filter } = data;
  if (modal) commit(routeType.SET_HISTORY_MODAL, { modal });
  if (name) commit(routeType.SET_HISTORY_SCREEN, { name });
  if (params) commit(routeType.SET_HISTORY_PARAMS, { params });
  if (filter) commit(routeType.SET_HISTORY_FILTER, { filter });
};

const setHistory = async ({ commit }, data) => {
  let { fromPath, toPath } = data;
  if (fromPath) commit(routeType.SET_HISTORY_FROMPATH, { fromPath });
  if (toPath) commit(routeType.SET_HISTORY_TOPATH, { toPath });

  if (toPath && fromPath) commit(routeType.SET_LIST_HISTORY, { toPath, fromPath });
};

const clearHistoryModal = async ({ commit }) => {
  commit(routeType.CLEAR_MODAL);
};

const itemHistoryIndex = async ({ commit }, params) => {
  commit(routeType.SET_ITEM_INDEX, params);
};

export default {
  setHistory,
  clearHistoryModal,
  setScreenInfor,
  itemHistoryIndex
};
