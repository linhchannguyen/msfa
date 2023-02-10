import { D01_Types } from '@/store/modules/D01/mutationTypes';

const mutations = {
  [D01_Types.drag](state, payload) {
    state.drag.push(payload);
  },
  [D01_Types.size](state, payload) {
    state.sizes.push(payload);
  },
  emptyState() {
    this.replaceState({ drag: [], sizes: [] });
  }
};

export default mutations;
