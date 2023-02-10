import store from '@/store';
import { getCookie } from '@/utils/constants';

const authorizeAuth = async (to, from, next) => {
  document.title = to.meta.title || 'ログイン';

  setPagesVisited(to, from, next);

  let user = JSON.parse(localStorage.getItem('currentUser'));

  let access_token = getCookie('accessToken');
  let originalToken = getCookie('originalToken');

  let loginData = {
    user,
    access_token
  };

  let { isAuthorized, accessToken, currentUser, policyPermission } = store.state.auth;
  if (user && access_token && !originalToken) {
    if (accessToken !== access_token) {
      await store.dispatch('auth/afterLoginAction', loginData);
    }
  }
  // eslint-disable-next-line eqeqeq
  const device = (name) => to.meta[name] == '1'; //params smartphone_visible_flag|tablet_visible_flag|pc_visible_flag = 1|0  return true|false;
  var ismobile = /iphone|ipod|android|blackberry|mini|palm|smartphone_visible_flag|iemobile/i.test(navigator.userAgent.toLowerCase());
  var istablet = /ipad|android|android 3.0|xoom|sch-i800|playbook|tablet|kindle/i.test(navigator.userAgent.toLowerCase());

  // let accessToken = Boolean(getCookie('accessToken'));
  // let isAuthorized = localStorage.getItem('isAuthorized');
  // let currentUser = await JSON.parse(localStorage.getItem('currentUser'));

  // console.log('to', to);
  // console.log('from', from);
  // console.log(next);
  /**
   * Logic
   * 1. Check to.matched -> requiredAuth
   * 1.1 Check !isAuthorized && accessToken
   * 1.2 Check current User
   * 1.2.1 Check First_Login
   * 1.2.2 Check Policy && Role if !policy && !role -> Z02_S01_Home else next()
   * 1.2.3 Check device
   * 2 Check RequiredFirstLogin
   * 2.1 !first_login -> redirect Homepage else next()
   * 2.2 check requireNotAuth
   * 2.2.1 check accessToken -> next
   * 2.2.2 else first_login -> next
   * 2.2.3 else redirect Homepage
   * 3 next()
   */

  try {
    if (user && user?.require_change_flag && access_token && !originalToken) {
      if (to.name === 'Z01_S01_Login') {
        clearAuthorized(to, from, next);
      } else {
        if (to.name !== 'Z01_S01_B_FirstLogin') {
          next({ name: 'Z01_S01_B_FirstLogin' });
          localStorage.removeItem('$_Flogin');
        }
      }
    }

    if (to.matched.some((record) => record.meta.requireAuth)) {
      if (!(isAuthorized && accessToken)) clearAuthorized(to, from, next);
      else {
        // else {
        if (to.meta.requireRoles) {
          if (!policyPermission) {
            await store.dispatch('auth/setPolicyPermissionAction');
            await checkRole(to, from, next);
          } else checkRole(to, from, next);
        }
        // is smartphone_visible_flag
        else if (ismobile)
          if (device('smartphone_visible_flag')) next();
          else next({ name: 'NotFound' });
        // is tablet
        else if (istablet)
          if (device('tablet_visible_flag')) next();
          else next({ name: 'NotFound' });
        // is PC
        else if (device('pc_visible_flag')) next();
        else next({ name: 'NotFound' });
        // }
      }
    } else if (to.matched.some((record) => record.meta.requireFirstLogin)) {
      if (!currentUser?.require_change_flag) next({ name: 'Z02_S01_Home' });
      else next();
    } else if (to.matched.some((record) => record.meta.requireNotAuth)) {
      if (!(isAuthorized && accessToken)) next();
      else {
        if (currentUser.require_change_flag) {
          next();
        } else {
          next({ name: 'Z02_S01_Home' });
        }
      }
    } else next();
  } catch (err) {
    // console.log(err);
  }
};

const clearAuthorized = (to, from, next) => {
  sessionStorage.setItem('redirectTo', to.name);
  store.dispatch('auth/clearAuthorized');
  next({ name: 'Z01_S01_Login' });
};

const checkRole = (to, from, next) => {
  const { policyPermission } = store.state.auth;
  if (policyPermission?.route.permissions[to.name]) {
    next();
  } else next({ name: 'Z02_S01_Home' });
};

const setPagesVisited = (to, from, next) => {
  /**
   * 1. Chuyển trang từ menu khác (a) sang menu mới (b)
   * - xóa menu (a) từ danh sách trang đã truy cập (pagesVisited)
   * - thêm menu mới (b) vào danh sách trang đã truy cập
   * 2. Nhập Url từ browser, hoặc refresh lại page
   * - kiểm tra danh sách trang đã truy cập có tồn tại hay không
   * + nếu có tồn tại => thêm mới menu vừa nhập vào danh sách trang đã truy cập
   * + nếu chưa tồn tại
   * +++ kiểm tra có phải là đang refresh lại trang hay không?
   * ++++++ đúng: thêm vào trang đã truy cập
   * ++++++ sai: trở về trang login
   */
  if (!['Z01_S01_Login', 'Z01_S02_DeveloperLogin'].includes(to.name)) {
    const isRefreshPage = sessionStorage.getItem('isRefreshPage');
    if (from.name) {
      const pagesVisited = JSON.parse(localStorage.getItem('pagesVisited')) || [];
      let f_index = pagesVisited.indexOf(from.name);
      pagesVisited.splice(f_index, 1);
      pagesVisited.push(to.name);
      localStorage.setItem('pagesVisited', JSON.stringify(pagesVisited));
    } else {
      const pagesVisited = JSON.parse(localStorage.getItem('pagesVisited'));
      if (pagesVisited) {
        pagesVisited.push(to.name);
        localStorage.setItem('pagesVisited', JSON.stringify(pagesVisited));
      } else {
        if (isRefreshPage) {
          localStorage.setItem('pagesVisited', JSON.stringify([to.name]));
        } else {
          store.dispatch('auth/clearAuthorized');
          localStorage.removeItem('currentUserProxy');
          localStorage.removeItem('isLoginProxy');
          next({ name: 'Z01_S01_Login' });
        }
      }
    }
  }
};

export { authorizeAuth };
