/**
 * @type: Array
 * @description: Import teamplate (HTML/CSS) into components
 *
 */
const components = [
  { Sample: import('../views/Sample/Sample') },
  { Layout: import(/* webpackChunkName: "layout" */ '@/layouts/Layout') },
  { AuthLayout: import(/* webpackChunkName: "authlayout" */ '@/layouts/AuthLayout') },
  { NotFound: import(/* webpackChunkName: "notfound" */ '@/views/error/NotFound') },
  { SystemError: import(/* webpackChunkName: "systemerror" */ '@/views/error/SystemError') },
  //Z01
  { Z01_S01_Login: import(/* webpackChunkName: "Z01_S01_Login" */ '@/views/Z01/Z01_S01_Login/Z01_S01_Login') },
  { Z01_S01_B_FirstLogin: import(/* webpackChunkName: "Z01_S01_B_FirstLogin" */ '@/views/Z01/Z01_S01_B_FirstLogin/Z01_S01_B_FirstLogin') },
  { Z01_S02_DeveloperLogin: import(/* webpackChunkName: "Z01_S02_DeveloperLogin" */ '@/views/Z01/Z01_S02_DeveloperLogin/Z01_S02_DeveloperLogin') },
  {
    Z01_S03_ProxyUserSelection: import(
      /* webpackChunkName: "Z01_S03_ProxyUserSelection" */
      '@/views/Z01/Z01_S03_ProxyUserSelection/Z01_S03_ProxyUserSelection'
    )
  },
  //Z02
  { Z02_S01_Home: import(/* webpackChunkName: "Z02_S01_Home" */ '@/views/Z02/Z02_S01_Home/Z02_S01_Home') },
  { Z02_S02_Message: import(/* webpackChunkName: "Z02_S02_Message" */ '@/views/Z02/Z02_S02_Message/Z02_S02_Message') },
  { Z02_S03_UserNotificationManagement: import(/* webpackChunkName: "Z02_S03_Notification" */ '@/views/Z02/Z02_S03_Notification/Z02_S03_Notification.vue') },
  { Z02_S02_MessageManagement: import(/* webpackChunkName: "Z02_S03_Notification" */ '@/views/Z02/Z02_S02_Message/Z02_S02_Message.vue') },
  // Z04
  { Z04_S01_AccountSettings: import(/* webpackChunkName: "Z04_S01_AccountSetting" */ '@/views/Z04/Z04_S01_AccountSetting/Z04_S01_AccountSetting') },

  //A02
  { A02_S01_Stock: import(/* webpackChunkName: "A02_S01_Stock" */ '@/views/A02/A02_S01_Stock/_A02_S01_Stock') },
  { A02_S02_SelectStock: import(/* webpackChunkName: "A02_S02_SelectStock" */ '@/views/A02/A02_S02_SelectStock/A02_S02_SelectStock') },
  { A02_S03_StockManagement: import(/* webpackChunkName: "A02_S03_StockManagement" */ '@/views/A02/A02_S03_StockManagement/A02_S03_StockManagement') },
  //Z05

  { Z05_S01_Organization: import(/* webpackChunkName: "Z05_S01_Organization" */ '@/views/Z05/Z05_S01_Organization/Z05_S01_Organization') },
  {
    Z05_S05_Choice_Of_Masterial: import(
      /* webpackChunkName: "Z05_S05_Choice_Of_Masterial" */ '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial'
    )
  },
  {
    Z05_S06_Material_Selection: import(/* webpackChunkName: "Z05_S06_Material_Selection" */ '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection')
  },
  { Z05_S03_FacilitySelection: import(/* webpackChunkName: "Z05_S03_FacilitySelection" */ '@/views/Z05/Z05_S03_FacilitySelection/Z05_S03_FacilitySelection') },
  {
    Z05_S07_Organization_Master: import(
      /* webpackChunkName: "Z05_S07_Organization_Master" */ '@/views/Z05/Z05_S07_Organization_Master/Z05_S07_Organization_Master'
    )
  },
  // Z03
  {
    Z03_S01_SelectFacilitySetting: import(
      /* webpackChunkName: "Z03_S01_SelectFacilitySetting" */ '@/views/Z03/Z03_S01_SelectFacilitySetting/Z03_S01_SelectFacilitySetting'
    )
  },
  {
    Z03_S02_SelectFacilityPersonalSetting: import(
      /* webpackChunkName: "Z03_S02_SelectFacilityPersonalSetting" */ '@/views/Z03/Z03_S02_SelectFacilityPersonalSetting/Z03_S02_SelectFacilityPersonalSetting'
    )
  },
  {
    Z03_S03_ConsultationContentSetting: import(
      /* webpackChunkName: "Z03_S03_ConsultationContentSetting" */
      '@/views/Z03/Z03_S03_ConsultationContentSetting/Z03_S03_ConsultationContentSetting'
    )
  },

  { Z03_S04_SelectListCopy: import(/* webpackChunkName: "Z03_S04_SelectListCopy" */ '@/views/Z03/Z03_S04_SelectListCopy/Z03_S04_SelectListCopy') },

  //A03
  {
    A03_S01_JapaneseDailyReportList: import(
      /* webpackChunkName: "A03_S01_JapaneseDailyReportList" */ '@/views/A03/A03_S01_JapaneseDailyReportList/A03_S01_JapaneseDailyReportList'
    )
  },
  { A03_S02_DailyReport: import(/* webpackChunkName: "A03_S02_DailyReport" */ '@/views/A03/A03_S02_DailyReport/A03_S02_DailyReport') },
  { A03_S03_NotApprovedList: import(/* webpackChunkName: "A03_S03_NotApprovedList" */ '@/views/A03/A03_S03_NotApprovedList/A03_S03_NotApprovedList') },
  // I01
  { I01_S01_UserManagement: import(/* webpackChunkName: "I01_S01_UserManagement" */ '@/views/I01/I01_S01_UserManagement/I01_S01_UserManagement') },
  { I01_S02_PermissionSetting: import(/* webpackChunkName: "I01_S02_PermissionSetting" */ '@/views/I01/I01_S02_PermissionSetting/I01_S02_PermissionSetting') },
  {
    I01_S03_ApplicationPeriodConfirmation: import(
      /* webpackChunkName: "I01_S03_ApplicationPeriodConfirmation" */ '@/views/I01/I01_S03_ApplicationPeriodConfirmation/I01_S03_ApplicationPeriodConfirmation'
    )
  },
  // I02
  { I02_S01_PasswordReissue: import(/* webpackChunkName: "I02_S01_PasswordReissue" */ '@/views/I02/I02_S01_PasswordReissue/I02_S01_PasswordReissue') },
  // A01

  { A01_S01_ScheduleInput: import(/* webpackChunkName: "A01_S01_ScheduleInput" */ '@/views/A01/A01_S01_ScheduleInput/A01_S01_ScheduleInput') },
  { A01_S02_Calendar: import(/* webpackChunkName: "A01_S02_Calendar" */ '@/views/A01/A01_S02_Calendar/A01_S02_Calendar') },
  { A01_S03_InterviewDetails: import(/* webpackChunkName: "A01_S03_InterviewDetails" */ '@/views/A01/A01_S03_InterviewDetails/A01_S03_InterviewDetails') },
  {
    A01_S04_InterviewDetailedInput: import(
      /* webpackChunkName: "A01_S04_InterviewDetailedInput" */ '@/views/A01/A01_S04_InterviewDetailedInput/A01_S04_InterviewDetailedInput'
    )
  },

  //H01
  { H01_S01_FacilitySearch: import(/* webpackChunkName: "H01_S01_FacilitySearch" */ '@/views/H01/H01_S01_FacilitySearch/H01_S01_FacilitySearch') },
  { H01_FacilityDetails: import(/* webpackChunkName: "H01_FacilityDetails" */ '@/views/H01/H01_FacilityDetails/H01_FacilityDetails') },
  //C01
  { C01_S01_LectureList: import(/* webpackChunkName: "C01_S01_LectureList" */ '@/views/C01/C01_S01_LectureList/C01_S01_LectureList') },
  {
    C01_S03_AttendantManagement: import(
      /* webpackChunkName: "C01_S03_AttendantManagement" */ '@/views/C01/C01_S03_AttendantManagement/_C01_S03_AttendantManagement'
    )
  },
  //D01
  { D01_S03_Registration: import(/* webpackChunkName: "D01_S03_Resignation" */ '@/views/D01/D01_S03_Registration/D01_S03_Registration') },
  {
    D01_S04_CustomMaterialManagement: import(
      /* webpackChunkName: "D01_S04_CustomMaterialManagement" */ '@/views/D01/D01_S04_CustomMaterialManagement/D01_S04_CustomMaterialManagement'
    )
  },
  {
    D01_S06_MaterialEvaluationInput: import(
      /* webpackChunkName: "D01_S06_MaterialEvaluationInput" */ '@/views/D01/D01_S06_MaterialEvaluationInput/D01_S06_MaterialEvaluationInput'
    )
  },
  { D01_S01_MaterialSearch: import(/* webpackChunkName: "D01_S01_MaterialSearch" */ '@/views/D01/D01_S01_MaterialSearch/D01_S01_MaterialSearch') },
  { D01_S07_MaterialViewer: import(/* webpackChunkName: "D01_S07_MaterialViewer" */ '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer') },
  { D01_S02_MaterialDetails: import(/* webpackChunkName: "D01_S02_Material Details" */ '@/views/D01/D01_S02_MaterialDetails/D01_S02_MaterialDetails') },
  {
    D01_S05_CustomMaterialCreation: import(
      /* webpackChunkName: "D01_S05_CustomMaterialCreation" */ '@/views/D01/D01_S05_CustomMaterialCreation/D01_S05_CustomMaterialCreation'
    )
  },
  //E01
  { E01_S01_QaSearch: import(/* webpackChunkName: "E01_S01_QaList" */ '@/views/E01/E01_S01_QaList/E01_S01_QaList') },
  { E01_S02_QaDetails: import(/* webpackChunkName: "E01_S02_QaDetails" */ '@/views/E01/E01_S02_QaDetails/_E01_S02_QaDetails') },
  { E01_S03_QaInput: import(/* webpackChunkName: "E01_S03_QaInput" */ '@/views/E01/E01_S03_QaInput/E01_S03_QaInput') },
  {
    E01_S04_PostingProhibitedUserManagement: import(
      /* webpackChunkName: "E01_S04_PostingProhibitedUserManagement" */ '@/views/E01/E01_S04_PostingProhibitedUserManagement/E01_S04_PostingProhibitedUserManagement'
    )
  },
  // F01
  { F01_S01_KnowledgeSearch: import(/* webpackChunkName: "F01_S01_KnowledgeSearch" */ '@/views/F01/F01_S01_KnowledgeList/F01_S01_KnowledgeList') },
  { F01_S02_KnowledgeDetails: import(/* webpackChunkName: "F01_S02_KnowledgeDetails" */ '@/views/F01/F01_S02_KnowledgeDetails/F01_S02_KnowledgeDetails') },
  { F01_S03_KnowledgeInput: import(/* webpackChunkName: "F01_S03_KnowledgeInput" */ '@/views/F01/F01_S03_KnowledgeInput/F01_S03_KnowledgeInput') },
  { F01_S04_TimelineSelection: import(/* webpackChunkName: "F01_S04_TimelineSelection" */ '@/views/F01/F01_S04_TimelineSelection/F01_S04_TimelineSelection') },
  {
    F01_S05_Pre_ReleaseKnowledgeManagement: import(
      /* webpackChunkName: "F01_S05_Pre_ReleaseKnowledgeManagement" */ '@/views/F01/F01_S05_Pre_ReleaseKnowledgeManagement/F01_S05_Pre_ReleaseKnowledgeManagement'
    )
  },
  // G01
  {
    G01_S01_TargetFacilityManagement: import(
      /* webpackChunkName: "G01_S01_TargetFacilityManagement" */ '@/views/G01/G01_S01_TargetFacilityManagement/G01_S01_TargetFacilityManagement'
    )
  },
  {
    G01_S02_In_FacilityTargetPersonalManagement: import(
      /* webpackChunkName: "G01_S02_In_FacilityTargetPersonalManagement" */
      '@/views/G01/G01_S02_In_FacilityTargetPersonalManagement/G01_S02_In_FacilityTargetPersonalManagement'
    )
  },
  {
    G01_S03_TargetFacilityPersonSetting: import(
      /* webpackChunkName: "G01_S03_TargetFacilityPersonSetting" */
      '@/views/G01/G01_S03_TargetFacilityPersonSetting/G01_S03_TargetFacilityPersonSetting'
    )
  },
  {
    G01_S04_TargetSelectionPeriodManagement: import(
      /* webpackChunkName: "G01_S04_TargetSelectionPeriodManagement" */
      '@/views/G01/G01_S04_TargetSelectionPeriodManagement/G01_S04_TargetSelectionPeriodManagement'
    )
  },
  //B01
  {
    B01_S02_BriefingSessionInput: import(
      /* webpackChunkName: "B01_S02_BriefingSessionInput" */ '@/views/B01/B01_S02_BriefingSessionInput/B01_S02_BriefingSessionInput.vue'
    )
  },
  {
    B01_S01_BriefingSearch: import(/* webpackChunkName: "B01_S01_BriefingSessionList" */ '@/views/B01/B01_S01_BriefingSessionList/B01_S01_BriefingSessionList')
  },
  //C01
  { C01_S02_LectureInput: import(/* webpackChunkName: "C01_S02_LectureInput" */ '@/views/C01/C01_S02_LectureInput/C01_S02_LectureInput.vue') },
  {
    C01_S04_LectureSeriesSelection: import(
      /* webpackChunkName: "C01_S04_LectureSeriesSelection" */ '@/views/C01/C01_S04_LectureSeriesSelection/C01_S04_LectureSeriesSelection.vue'
    )
  },
  {
    C01_S05_AttendantCollectiveRegistration: import(
      /* webpackChunkName: "C01_S05_AttendantCollectiveRegistration" */ '@/views/C01/C01_S05_AttendantCollectiveRegistration/C01_S05_AttendantCollectiveRegistration.vue'
    )
  },
  //H02
  { H02_S01_PersonalSearch: import(/* webpackChunkName: "H02_S01_PersonalSearch" */ '@/views/H02/H02_S01_PersonalSearch/_H02_S01_PersonalSearch') },
  { H02_PersonalDetails: import(/* webpackChunkName: "H02_PersonalDetails" */ '@/views/H02/H02_PersonalDetails/H02_PersonalDetails') },
  // Z05
  { Z05_S02_AreaSelection: import(/* webpackChunkName: "Z05_S02_AreaSelection" */ '@/views/Z05/Z05_S02_Area_Selection/Z05_S02_Area_Selection') },
  {
    Z05_S05_ChoiceOfMaterials: import(/* webpackChunkName: "Z05_S05_ChoiceOfMaterials" */ '@/views/Z05/Z05_S05_Choice_Of_Masterial/Z05_S05_Choice_Of_Masterial')
  },

  {
    Z05_S06_MaterialSelection: import(/* webpackChunkName: "Z05_S06_MaterialSelection" */ '@/views/Z05/Z05_S06_Material_Selection/Z05_S06_Material_Selection')
  },
  {
    Z05_S07_OrganizationSelectionMasterManagementRequired: import(
      /* webpackChunkName: "Z05_S07_OrganizationSelectionMasterManagementRequired" */
      '@/views/Z05/Z05_S07_Organization_Master/Z05_S07_Organization_Master'
    )
  }
];

const componentFilter = (screenName) => components.filter((s) => Object.keys(s).some((item) => item === screenName))[0];
/**
 * @param {String} screenName etc. D01-S01, Dashboard...
 * @returns component from import(@/view/... + screenName)
 */
const screen = (screenName) => (componentFilter(screenName) ? componentFilter(screenName)[screenName] : null);
// router default
const defaultRouter = [
  {
    path: '/',
    redirect: { name: 'Z02_S01_Home' },
    component: async () => await screen('Layout'),
    children: [
      {
        path: 'home',
        name: 'Z02_S01_Home',
        component: async () => await screen('Z02_S01_Home'),
        meta: {
          requireAuth: true,
          title: 'ホーム',
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
    redirect: { name: 'Z01_S01_Login' }
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
  }
];

export { screen, defaultRouter };
