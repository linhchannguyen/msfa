import { screen, defaultRouter } from './components';

const childRouter = JSON.parse(localStorage.getItem('router'));
const childScreen = (item) => (screen(item.component) ? async () => await screen(item.component) : async () => await screen('NotFound'));

export const routesApi = [
  {
    path: '/',
    redirect: { name: 'Z02_S01_Home' },
    component: async () => await screen('Layout'),
    children: childRouter ? childRouter.map((item) => ({ ...item, component: childScreen(item), props: true })) : []
  },

  {
    path: '/auth',
    component: async () => await screen('AuthLayout'),
    redirect: { name: 'NotFound' },
    children: [
      {
        path: 'login',
        name: 'Z01_S01_Login',
        component: async () => await screen('Z01_S01_Login'),
        meta: {
          requireNotAuth: true,
          title: 'ログイン',
          pc_visible_flag: '1',
          smartphone_visible_flag: '1',
          tablet_visible_flag: '1'
        }
      },
      {
        path: 'first-login',
        name: 'Z01_S01_B_FirstLogin',
        component: async () => await screen('Z01_S01_B_FirstLogin'),
        meta: {
          requireFirstLogin: true,
          title: '最初ログイン',
          pc_visible_flag: '1',
          smartphone_visible_flag: '1',
          tablet_visible_flag: '1'
        }
      },
      {
        path: 'developer-login',
        name: 'Z01_S02_DeveloperLogin',
        component: async () => await screen('Z01_S02_DeveloperLogin'),
        meta: {
          title: '開発者ログイン',
          pc_visible_flag: '1',
          smartphone_visible_flag: '1',
          tablet_visible_flag: '1'
        }
      }
    ]
  },
  {
    path: '/page-not-found',
    name: 'NotFound',
    component: async () => await screen('NotFound'),
    meta: { title: '404エラー' }
  },
  {
    path: '/system-error',
    name: 'SystemError',
    component: async () => await screen('SystemError'),
    meta: { title: '500エラー' }
  },
  {
    path: '/:pathMatch(.*)',
    redirect: { name: 'NotFound' }
  }
];
const routes = childRouter ? routesApi : defaultRouter;

export default routes;
