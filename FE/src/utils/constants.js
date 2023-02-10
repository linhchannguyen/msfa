function getCookie(cname) {
  let name = cname + '=';
  let ca = document.cookie.split(';');
  for (let i = 0; i < ca.length; i++) {
    let c = ca[i];
    while (c.charAt(0) === ' ') {
      c = c.substring(1);
    }
    if (c.indexOf(name) === 0) {
      return c.substring(name.length, c.length);
    }
  }
  return '';
}

const roles = {
  R01: 'システム利用者', // SYS_USE_USER
  R10: '面談実施者', // CALL_IMPLEMENTER
  R20: '説明会実施者', // BRIEFING_IMPLEMENTER
  R30: '講演会実施者', // CONVENTION_IMPLEMENTER
  R31: '講演会出席者管理者', // CONVENTION_ATTENDEE_MG
  R40: '資材管理者', // CONVENTION_ATTENDEE_MG
  R50: 'QA管理者', // QA_MG
  R60: 'ナレッジ管理者', // KNOWLEDGE_MG
  R70: 'メッセージ作成者', // MESSAGE_MG
  R80: 'マスタ管理者', // MASTER_MG
  R90: 'システム管理者', // SYS_MG
  RDev: '開発者' // DEV
};

const TaxRate = [
  {
    tax_rate_id: 1,
    max_amount: 1000000,
    min_amount: 0,
    tax_rate: 0.1021
  },
  {
    tax_rate_id: 2,
    max_amount: 99999999,
    min_amount: 1000001,
    tax_rate: 0.2042
  }
];

const paramZ05S04Default = {
  non_facility_flag: 0, // (require) // =1 facility_cd not edit || =0 facility_cd edit
  non_medical_flag: 0, // (require) // =1 disabled tab メディカルスタッフ || =0 enabled tab メディカルスタッフ

  org_cd: '',
  user_cd: '',
  user_name: '',
  target_product_cd: '',
  select_group_id: '',
  facility_category_type: '',
  prefecture_cd: '',
  prefecture_name: '',
  municipality_cd: '',
  municipality_name: '',
  facility_cd: [],
  facility_name: [],
  person_cd: []
};

export { getCookie, roles, TaxRate, paramZ05S04Default };
