import { createStore } from 'vuex';
import auth from '@/store/modules/auth/store';
import A02 from '@/store/modules/A02/store';
import D01 from '@/store/modules/D01/store';
import router from '@/store/modules/router/store';
import modal from '@/store/modules/modal/store';

export default createStore({
  modules: { auth, A02, D01, router, modal },
  strict: true,
  deep: true
});
