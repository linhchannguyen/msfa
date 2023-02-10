import getters from '@/store/modules/router/getters';
import actions from '@/store/modules/router/actions';
import mutations from '@/store/modules/router/mutations';

const state = {
  fromPath: null,
  toPath: null,
  modal: false,
  name: '',
  params: '',
  filter: null,
  back: false,
  listHistory: [],
  itemIndex: {
    screen: '',
    index: -1,
    x: 0,
    y: 0
  }
};

export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations
};
