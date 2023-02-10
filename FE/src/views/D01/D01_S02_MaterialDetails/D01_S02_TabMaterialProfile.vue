<template>
  <div class="wrapContent">
    <div class="box-tab1">
      <div v-if="dataList.document_id" class="boxtab1-wrap scrollbar">
        <div class="tabFlex">
          <div class="tabFlex-col">
            <div class="boxTitle">
              <ul>
                <li>
                  <span class="boxTitle-title">適用期間</span>
                  <span class="boxTitle-label">
                    {{ formatFullDate(dataList.start_date) }}～{{ dataList.end_date === '9999-12-31' ? '' : formatFullDate(dataList.end_date) }}
                  </span>
                </li>
                <li>
                  <span class="boxTitle-title">品目</span>
                  <span class="boxTitle-label">{{ dataList.product_name }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">対象疾患</span>
                  <span class="boxTitle-label">{{ dataList.disease }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">診療科</span>
                  <span class="boxTitle-label">{{ dataList.medical_subjects_group_name }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">安全性情報</span>
                  <span class="boxTitle-label">{{ dataList.safety_information_value }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">適応外情報</span>
                  <span class="boxTitle-label">{{ dataList.off_label_information_value }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">{{ dataList.document_type === '10' ? '資材区分' : '使用資材' }}</span>
                  <span class="boxTitle-label">
                    {{ dataList.document_type === '10' ? dataList.document_category_name : dataList.document_name + ' ' + dataList.count_page_document }}
                  </span>
                </li>
              </ul>
            </div>
          </div>
          <div class="tabFlex-col">
            <div class="boxTitle boxTitle--change">
              <ul>
                <li>
                  <span class="boxTitle-title">管理部署</span>
                  <span class="boxTitle-label">
                    {{ dataList.management_org_name }}
                  </span>
                </li>
                <li v-if="dataList.document_type === '10' && isRoleR40">
                  <span class="boxTitle-title">
                    <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/Icon-lockD01.svg')" alt="" />
                    使用可能部署
                  </span>
                  <span class="boxTitle-label"> {{ dataList.available_org_label ? dataList.available_org_label : ' (設定なし)' }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">作成者</span>
                  <span class="boxTitle-label">{{
                    (dataList.org_label ? dataList.org_label : ' (所属なし)') + ' ' + (dataList.create_user_name ? dataList.create_user_name : '')
                  }}</span>
                </li>
                <li>
                  <span class="boxTitle-title">資材概要</span>
                  <span style="white-space: break-spaces" class="boxTitle-label">{{ dataList.document_contents ? dataList.document_contents : '' }}</span>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="box-btn">
          <div class="box-btn-wrap">
            <button title_log="ビューワ起動ボタン" type="button" class="btn btn--icon log-icon" @click="openModalMaterialViewerD01S07()">
              <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/D01-S02-iconlive.svg')" alt="" />
            </button>
          </div>
          <div v-if="device !== 'Tablet' && (checkBtnEdit || (dataList.document_org_user === 1 && dataList.document_edit === 1))" class="box-btn-wrap">
            <button title_log="ビューワ起動ボタン" type="button" class="btn btn--icon log-icon" @click="opendoccumentViewer()">
              <img v-svg-inline svg-inline class="svg svg--change" :src="require('@/assets/template/images/D01-S02-iconedit.svg')" alt="" />
            </button>
          </div>
        </div>
      </div>
      <div v-if="!loading" class="noData">
        <div class="noData-content">
          <p v-if="!loading" class="noData-text">{{ loading ? '' : text }}</p>
          <div v-if="!loading" class="noData-thumb">
            <img src="@/assets/template/images/data/amico.svg" alt="icon" />
          </div>
        </div>
      </div>
      <el-dialog
        v-model="modalConfig.isShowModal"
        custom-class="custom-dialog"
        :title="modalConfig.title"
        :width="modalConfig.width"
        :destroy-on-close="modalConfig.destroyOnClose"
        :close-on-click-modal="modalConfig.closeOnClickMark"
        @close="clodeModal"
      >
        <template #default>
          <component :is="modalConfig.component" v-bind="{ ...modalConfig.props }" @onFinishScreen="onFinishScreen"></component>
        </template>
      </el-dialog>
    </div>
  </div>
</template>

<script>
import D01_S02_MaterialDetailsService from '../../../api/D01/D01_S02_MaterialDetailsService';
import { markRaw } from 'vue';
import D01_S07_MaterialViewer from '@/views/D01/D01_S07_MaterialViewer/D01_S07_MaterialViewer';
import _ from 'lodash';
import { Device } from '@/utils/common-function.js';
export default {
  name: 'D01_S02_TabMaterialProfile',
  components: { D01_S07_MaterialViewer },
  props: {
    changetab: {
      type: Number,
      default: 0
    },
    doccumentid: {
      type: Number,
      default: 0
    },
    checkLoading: [String]
  },

  data() {
    return {
      modalConfig: {
        title: '',
        isShowModal: false,
        component: null,
        props: {},
        width: null,
        destroyOnClose: true,
        closeOnClickMark: false
      },
      dataList: {},
      loading: false,
      isRoleR40: false,
      checkBtnEdit: false,
      text: '',
      doccument_id: '',
      device: ''
    };
  },
  watch: {
    changetab: function (value) {
      if (value === 1) {
        this.getDataDetail();
      }
    },
    doccumentid: function () {
      this.getDataDetail();
    }
  },

  created() {
    this.$watch(
      () => this.$route.params,
      (toParams, previousParams) => {
        let routeName = this.$route.name;
        if (!_.isEqual(toParams, previousParams) && routeName === 'D01_S02_MaterialDetails') {
          this.doccument_id = toParams.document_id;
          this.getDataDetail();
        }
      }
    );
  },
  mounted() {
    this.getDevice();
  },
  methods: {
    getDevice() {
      this.device = Device();
    },
    getDataDetail() {
      this.text = '該当するデータがありません。';
      this.loading = true;
      this.$emit('changeLoading', true);
      const policyRole = this.$store.state.auth.policyRole;
      if (policyRole) {
        policyRole.forEach((element) => {
          if (element === 'R40') {
            this.isRoleR40 = true;
          }
        });
      }
      const data = {
        document_id: this.doccument_id ? this.doccument_id : this.doccumentid
      };

      D01_S02_MaterialDetailsService.getDataDetail(data)
        .then((res) => {
          this.dataList = res.data.data;
          this.checkbtn(res.data.data);
        })
        .finally(() => {
          this.loading = false;
          this.$emit('changeLoading', false);
        });
    },
    checkbtn(item) {
      if (item.document_type === '10') {
        const policyRole = this.$store.state.auth.policyRole;
        if (policyRole) {
          policyRole.forEach((element) => {
            if (element === 'R40' || element === 'R90') {
              this.checkBtnEdit = true;
            }
          });
        }
      } else {
        if (item.document_org_user === 1 && item.document_edit === 1) {
          this.checkBtnEdit = true;
        }
      }
    },
    onFinishScreen() {
      this.modalConfig.isShowModal = false;
    },
    openModalMaterialViewerD01S07() {
      this.modalConfig = {
        ...this.modalConfig,
        title: '資材ビューア',
        isShowModal: true,
        component: markRaw(D01_S07_MaterialViewer),
        width: '60rem',
        props: { documentId: this.dataList.document_id }
      };
    },
    opendoccumentViewer() {
      let document_id = this.dataList.document_id;
      if (this.dataList.document_type === '10') {
        this.$router.push({ name: 'D01_S03_Registration', params: { document_id } });
      } else {
        this.$router.push({ name: 'D01_S05_CustomMaterialCreation', params: { document_id } });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import './style.scss';
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
$viewport_tablet1: 1024px;
.tabFlex {
  display: flex;
  flex-wrap: wrap;
  margin-left: -2%;
  .tabFlex-col {
    width: 50%;
    padding-left: 2%;
    @media (max-width: $viewport_tablet) {
      width: 100%;
    }
    .boxTitle {
      &.boxTitle--change {
        .boxTitle-title {
          position: relative;
          padding-left: 22px;
          .svg {
            position: absolute;
            left: 0;
            top: 3px;
          }
        }
      }
      > ul {
        > li {
          display: flex;
          flex-wrap: wrap;
          margin-bottom: 32px;
          @media (max-width: $viewport_tablet1) {
            margin-bottom: 18px;
          }
          @media (max-width: $viewport_tablet) {
            margin-bottom: 16px;
          }
          .boxTitle-title {
            font-weight: normal;
            font-size: 1rem;
            color: #5f6b73;
            width: 130px;
            padding-right: 10px;
            @media (max-width: $viewport_tablet) {
              padding-left: 22px;
            }
          }
          .boxTitle-label {
            width: calc(100% - 130px);
            font-weight: normal;
            font-size: 1rem;
            color: #2d3033;
          }
        }
      }
    }
  }
}
</style>
