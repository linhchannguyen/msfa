import { authTypes } from '@/store/modules/auth/mutationTypes';
import { Auth } from '@/api';
import { ElNotification } from 'element-plus';
import Icons from '@/config/iconsMenu';
import { remove } from 'lodash';
import { Device, UUID } from '@/utils/common-function.js';
const loginAction = async (context, data) => {
  try {
    const res = await Auth.loginService(data);

    const { access_token } = res.data.data;

    const currentUser = res.data.data;

    delete currentUser.access_token;

    document.cookie = 'accessToken=' + access_token + '; path=/';

    let loginData = {
      user: currentUser,
      access_token
    };

    context.dispatch('afterLoginAction', loginData);

    return res;
  } catch (err) {
    throw err;
  }
};
const afterLoginAction = ({ commit, dispatch }, data) => {
  let { user, access_token } = data;

  if (!access_token) access_token = getCookie('accessToken');

  localStorage.setItem('isAuthorized', true);
  localStorage.setItem('currentUser', JSON.stringify(user));
  commit(authTypes.SET_AUTHORIZED, true);
  commit(authTypes.SET_CURRENT_USER, user);
  commit(authTypes.SET_ACCESS_TOKEN, access_token);

  //policy
  dispatch('setPolicyAction', user.user_cd);
};

const loginDeveloperAction = async (context, data) => {
  try {
    const res = await Auth.loginDeveloperService(data);
    const { access_token, currentUser } = res.data.data;
    localStorage.setItem('isAuthorized', true);
    document.cookie = 'accessToken=' + access_token + '; path=/';
    localStorage.setItem('currentUser', JSON.stringify(res.data.data));
    context.commit(authTypes.SET_AUTHORIZED, true);
    context.commit(authTypes.SET_CURRENT_USER, res.data.data);
    context.commit(authTypes.SET_ACCESS_TOKEN, access_token);
    //policy
    context.dispatch('setPolicyAction', currentUser?.user_cd);
    return res;
  } catch (err) {
    throw err;
  }
};
// Login Proxy
const loginProxyAction = async (context, data) => {
  const originalToken = getCookie('accessToken');
  if (originalToken) {
    try {
      const res = await Auth.loginProxyService(data);
      const { access_token_proxy } = res.data.data;
      localStorage.setItem('isLoginProxy', true);
      localStorage.setItem('isAuthorized', true);
      document.cookie = 'originalToken=' + getCookie('accessToken') + '; path=/';
      document.cookie = 'accessToken=' + access_token_proxy + '; path=/';
      localStorage.setItem('currentUserProxy', JSON.stringify(res.data.data));
      let params = {
        access_token_proxy,
        originalToken: originalToken,
        currentUser: res.data.data
      };

      context.dispatch('afterLoginProxyAction', params);

      return res;
    } catch (err) {
      throw err;
    }
  }
};
/**
 * @param {commit , dispatch} param0
 * @param {  user, access_token_proxy, originalToken} data
 */
const afterLoginProxyAction = async ({ commit, dispatch }, data) => {
  let { currentUser, access_token_proxy, originalToken } = data;
  commit(authTypes.SET_AUTHORIZED, true);
  commit(authTypes.SET_IS_LOGIN_PROXY, true);
  commit(authTypes.SET_ACCESS_TOKEN, access_token_proxy);
  commit(authTypes.SET_ORIGINAL_TOKEN, originalToken);
  commit(authTypes.SET_CURRENT_USER, currentUser);
  //policy
  dispatch('setPolicyAction', currentUser?.user_cd);
};

const changePasswordAction = async (context, data) => {
  const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
  const currentUser = JSON.parse(localStorage.getItem('currentUser'));
  let currentUserLocalStorage = currentUserProxy ? currentUserProxy : currentUser;
  try {
    const res = await Auth.changePasswordFirstLoginService(data);
    currentUserLocalStorage = { ...currentUserLocalStorage, require_change_flag: false };
    localStorage.setItem('currentUser', JSON.stringify(currentUserLocalStorage));
    context.commit(authTypes.SET_CURRENT_USER, currentUserLocalStorage);
    return res;
  } catch (err) {
    throw err;
  }
};

const setPolicyAction = async (context, user_cd) => {
  localStorage.setItem('isLoadingComponent', true);
  context.dispatch('setPolicyRoleAction', user_cd || null);
  context.dispatch('setPolicyPermissionAction', user_cd || null);
};

const setPolicyRoleAction = async (context) => {
  const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
  const currentUser = JSON.parse(localStorage.getItem('currentUser'));
  let user_cd = currentUserProxy ? currentUserProxy.user_cd : currentUser.user_cd;
  try {
    const res = await Auth.getPolicyRoleService({ user_cd });
    context.commit(authTypes.SET_POLICY_ROLE, res.data.data);
    return res;
  } catch (err) {
    throw err;
  }
};

const setPolicyPermissionAction = async (context) => {
  const currentUserProxy = JSON.parse(localStorage.getItem('currentUserProxy'));
  const currentUser = JSON.parse(localStorage.getItem('currentUser'));
  let currentUserLocalStorage = currentUserProxy ? currentUserProxy.user_cd : currentUser.user_cd;
  try {
    const user_cd = currentUserLocalStorage;
    const res = await Auth.getPolicyPermissionService({ user_cd });
    let MENUS = [];

    const { menu: menus, screen: screens } = res.data.data;

    let screenByDevice = menuByDevice(screens);

    MENUS = menus.map((item) => {
      const iconObj = Icons.find((el) => ~~el.menu_cd === ~~item.menu_cd);
      const sbMenu = screenByDevice.filter((el) => ~~el.menu_cd === ~~item.menu_cd);
      let objTemp = {
        icon: iconObj?.icon,
        menu_name: item?.menu_name,
        menu_cd: item?.menu_cd,
        id: UUID(),
        subMenu: sbMenu
      };

      if (objTemp.subMenu.length === 1) {
        objTemp = {
          ...objTemp,
          ...objTemp.subMenu[0]
          // subMenu: []
        };
      } else {
        objTemp.subMenu = objTemp.subMenu.map((el) => ({ ...el, id: UUID() }));
      }

      return objTemp;
    });

    const variable = {
      Mobile: 'smartphone_visible_flag',
      Tablet: 'tablet_visible_flag',
      Desktop: 'pc_visible_flag'
    }[Device()];

    remove(MENUS, (el) => !el.subMenu.length && !el[variable]);
    setRouterStorage(screens);
    context.commit(authTypes.SET_MENUS, MENUS);
    context.commit(authTypes.SET_POLICY_PERMISSION, res.data.data);

    return res;
  } catch (err) {
    throw err;
  }
};

const menuByDevice = (screens) => {
  let MenuList = [];
  switch (Device()) {
    case 'Mobile':
      MenuList = screens.filter((screen) => ~~screen['smartphone_visible_flag'] === 1);
      break;
    case 'Tablet':
      MenuList = screens.filter((screen) => ~~screen['tablet_visible_flag'] === 1);
      break;
    default:
      MenuList = screens.filter((screen) => ~~screen['pc_visible_flag'] === 1);
      break;
  }
  return MenuList;
};

const setRouterStorage = (data) => {
  const childRouter = data?.map((item) => ({
    // apply in layout
    path: item.url,
    name: item.component,
    component: item.component,
    meta: {
      requireAuth: true,
      title: item.screen_name,
      pc_visible_flag: item.pc_visible_flag,
      smartphone_visible_flag: item.smartphone_visible_flag,
      tablet_visible_flag: item.tablet_visible_flag
    }
  }));
  localStorage.setItem('router', JSON.stringify(childRouter));
};

const setCurrentUser = (context, currentUser) => {
  context.commit(authTypes.SET_CURRENT_USER, currentUser);
};

const updateAvatarUser = (context, currentUser) => {
  let isLoginProxy = JSON.parse(localStorage.getItem('isLoginProxy'));
  let currentUserLocalStorage = {};
  if (Boolean(isLoginProxy) === true) {
    currentUserLocalStorage = {
      ...JSON.parse(localStorage.getItem('currentUserProxy')),
      ...currentUser
    };
    localStorage.setItem('currentUserProxy', JSON.stringify(currentUserLocalStorage));
  } else {
    currentUserLocalStorage = {
      ...JSON.parse(localStorage.getItem('currentUser')),
      ...currentUser
    };
    localStorage.setItem('currentUser', JSON.stringify(currentUserLocalStorage));
  }
  context.dispatch('setCurrentUser', currentUserLocalStorage);
};

const logout = async (context) => {
  try {
    context.dispatch('clearAuthorized', true);
  } catch (err) {
    throw err;
  }
};

const clearAuthorized = async ({ commit, getters }, isLogOut) => {
  let isLoginProxy = JSON.parse(localStorage.getItem('isLoginProxy'));

  let developer_cd = getters['currentUser']?.developer_cd;

  // Logout

  document.cookie = 'accessToken=' + '' + '; path=/';
  document.cookie = 'originalToken=' + '' + '; path=/';

  // List remove item
  localStorage.removeItem('isAuthorized');
  localStorage.removeItem('currentUser');
  localStorage.removeItem('router');
  localStorage.removeItem('screen');
  // localStorage.removeItem('$_C');
  localStorage.removeItem('vue-svg-inline-plugin:2.2.0');
  localStorage.removeItem('activeParentOld');
  localStorage.removeItem('activeParent');
  localStorage.removeItem('isLoadingComponent');
  localStorage.removeItem('startActive');
  localStorage.removeItem('pagesVisited');
  sessionStorage.removeItem('isRefreshPage');

  commit(authTypes.SET_AUTHORIZED, false);
  commit(authTypes.SET_ACCESS_TOKEN, null);
  commit(authTypes.SET_ORIGINAL_TOKEN, null); // Add
  commit(authTypes.SET_CURRENT_USER, null);

  if (isLogOut) {
    if (isLoginProxy) {
      ElNotification({ message: '代行ログインは終了しました。', customClass: 'success' });
    }
    //  else ElNotification({ message: 'ログアウトしました。', customClass: 'success' });
  }
  commit(authTypes.SET_MENUS, []);
  await clearHomeData();
  if (developer_cd) return window.history.pushState({}, '', '/auth/developer-login');
  else return window.history.pushState({}, '', '/auth/login');
};

const getCookie = (name) => {
  var match = document.cookie.match(RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
  return match ? match[1] : null;
};

const clearProxy = (context) => {
  let currentUserLocalStorage = JSON.parse(localStorage.getItem('currentUser'));
  let isLoginProxy = JSON.parse(localStorage.getItem('isLoginProxy'));
  const originalToken = getCookie('originalToken');
  const accessToken = getCookie('accessToken');
  if (!originalToken && !accessToken) {
    window.location.reload(); // code my
  }
  if (Boolean(isLoginProxy) === true || originalToken) {
    // Have Login Proxy
    localStorage.removeItem('isLoginProxy');
    localStorage.removeItem('currentUserProxy');
    document.cookie = 'accessToken=' + originalToken + '; path=/';
    accessProxyUser(context); // => Access proxy
  } else {
    // Login normal
    localStorage.removeItem('isLoginProxy');
    localStorage.removeItem('currentUserProxy');
    quitProxyUser(context);
  }

  localStorage.setItem('isLoadingComponent', true);

  document.cookie = 'originalToken=' + '' + '; path=/';
  context.dispatch('setPolicyAction', currentUserLocalStorage?.user_cd);
  clearHomeData();
};

const accessProxyUser = (context) => {
  let currentUserLocalStorage = JSON.parse(localStorage.getItem('currentUser'));
  const originalToken = getCookie('originalToken');
  context.commit(authTypes.SET_AUTHORIZED, true);
  context.commit(authTypes.SET_IS_LOGIN_PROXY, false);
  context.commit(authTypes.SET_CURRENT_USER, currentUserLocalStorage);
  context.commit(authTypes.SET_ACCESS_TOKEN, originalToken); // back origin user login
  context.commit(authTypes.SET_ORIGINAL_TOKEN, null); // back origin user login
};

const quitProxyUser = (context) => {
  let currentUserLocalStorage = JSON.parse(localStorage.getItem('currentUser'));
  context.commit(authTypes.SET_CURRENT_USER, currentUserLocalStorage);
  context.commit(authTypes.SET_AUTHORIZED, true);
  context.commit(authTypes.SET_ACCESS_TOKEN, getCookie('accessToken'));
  context.commit(authTypes.SET_IS_LOGIN_PROXY, false);
  context.commit(authTypes.SET_ORIGINAL_TOKEN, null); // back origin user login
};

const clearHomeData = () => {
  localStorage.removeItem('z02-s03-notification');
  localStorage.removeItem('archive_flag');
  localStorage.removeItem('inform_contents');
  localStorage.removeItem('inform_datetime_from');
  localStorage.removeItem('inform_datetime_to');
  localStorage.removeItem('dataListCatelogyInform');
  localStorage.removeItem('DataH01');
  localStorage.removeItem('DataH02');
};

export default {
  loginAction,
  loginDeveloperAction,
  loginProxyAction,
  logout,
  setCurrentUser,
  updateAvatarUser,
  clearAuthorized,
  changePasswordAction,
  setPolicyAction,
  setPolicyRoleAction,
  setPolicyPermissionAction,
  clearProxy,
  afterLoginAction,
  afterLoginProxyAction
};
