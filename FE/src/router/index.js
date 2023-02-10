import { createRouter, createWebHistory } from 'vue-router';
import routes from '@/router/routes';
import { authorizeAuth } from '@/router/guards';
import { screen } from './components';
import store from '@/store';
const router = createRouter({
  history: createWebHistory(),
  routes
});
const getCookie = (name) => {
  var match = document.cookie.match(RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
  return match ? match[1] : null;
};
// custom $route.push
const originalPush = router.push;
const routerPush = function push(location, onResolve, onReject) {
  const typeOf = typeof location === 'string' ? location : location.name || location.path;
  const hasRoute = router.getRoutes().some((s) => s.name === typeOf || s.path === typeOf);
  const meta = router.getRoutes().find((s) => s.name === typeOf || s.path === typeOf)?.meta;
  const childRouter = JSON.parse(localStorage.getItem('router'));
  /** Khai báo thiết bị có thể truy cập */
  // eslint-disable-next-line eqeqeq
  var device = (name) => meta[name] == '1';
  var ismobile = /iphone|ipod|android|blackberry|mini|palm|smartphone|iemobile/i.test(navigator.userAgent.toLowerCase());
  var istablet = /ipad|android|android 3.0|xoom|sch-i800|playbook|tablet|kindle/i.test(navigator.userAgent.toLowerCase());
  if (onResolve || onReject) {
    return originalPush.call(this, location, onResolve, onReject);
  }

  if (getCookie('accessToken') === '') {
    router.addRoute({ name: 'Z01_S01_Login', component: async () => await screen('Z01_S01_Login') });
    return originalPush.call(this, { name: 'Z01_S01_Login' }, onResolve, onReject);
  }

  if (hasRoute) {
    let mobileFlag = device('smartphone_visible_flag');

    let tabletFlag = device('tablet_visible_flag');

    let pc = device('pc_visible_flag');

    if (ismobile || istablet) {
      if (mobileFlag || tabletFlag) {
        return originalPush.call(this, location, onResolve, onReject);
      } else {
        return originalPush.call(this, { name: 'NotFound' });
      }
    } else {
      if (pc) {
        return originalPush.call(this, location, onResolve, onReject);
      } else {
        return originalPush.call(this, location, onResolve, onReject).catch((err) => {
          if (err) {
            Promise.reject(err);
          }
          Promise.resolve(false);
        });
      }
    }
  } else {
    const parent = router.getRoutes().filter((s) => s.path === '/')[0];
    router.removeRoute(parent);
    if (childRouter && childRouter.length > 0) {
      parent.children = childRouter.map((s) => ({
        ...s,
        component: async () => await screen(s.component)
      }));
      router.addRoute(parent);
    } else {
      router.addRoute({ name: 'Z01_S01_Login', component: async () => await screen('Z01_S01_Login') });
    }

    if (location) {
      return originalPush.call(this, location, onResolve, onReject);
    }
    else {
      return originalPush.call(this, { name: 'NotFound' });
    }
  }
};

router.beforeEach(authorizeAuth);

router.afterEach((to, from) => {
  // console.log(store.state.listHistory);
  let { fullPath: fromPath, params } = from;
  let { fullPath: toPath } = to;
  store.dispatch('router/setHistory', { fromPath, toPath });
  store.dispatch('router/setScreenInfor', { params });
});

export { router, routerPush };
