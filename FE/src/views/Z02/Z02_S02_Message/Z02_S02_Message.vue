<template>
  <div v-load-f5="true" v-loading="isLoadingMess" class="wrapContent page-zo z02-s02">
    <div class="groupContent">
      <div class="wrapBtn">
        <div class="btnInfo">
          <button class="btn btn-sign msfa-custom-btn-create" type="button" @click="onclickShowModal('createMessage', 0)">
            <span class="btn-iconLeft"><i class="el-icon-plus"></i> <span>新規登録</span></span>
          </button>
        </div>
        <div class="btnInfo btnInfo--change"></div>
      </div>
      <div ref="messageList" class="wrap scrollbar custom-message-list">
        <div v-if="listMessagesDisplay.length > 0" class="list-manag">
          <ul v-for="message of listMessagesDisplay" :key="message.message_id">
            <li :id="`className--${message.message_id}`" :class="message.checkBrg ? 'active' : ''">
              <div v-if="message.important_flag" class="list-hot">重要</div>
              <div class="list-title">
                <h2 class="tlt" @click="onclickShowModal('updateMessage', message.message_id)">
                  <span><img class="svg" src="@/assets/template/images/icon_pdf-manag.svg" alt="" /></span><a href="#">{{ message.message_subject }}</a>
                </h2>
              </div>
              <div class="list-info">
                <ul>
                  <li>
                    <span style="color: #99a5ae">表示対象組織：</span>
                    <span class="list-label">{{ message.org_name ? message.org_name : '指定なし' }}</span>
                  </li>
                  <li>
                    <span style="color: #99a5ae">掲載期間：</span>
                    <span class="list-label">{{ formatFullDate(message.release_start_date) }}～{{ formatFullDate(message.release_end_date) }}</span>
                  </li>
                  <li>
                    <span style="color: #99a5ae">発信者：</span>
                    <span class="list-label">{{ message.sender_name }}</span>
                  </li>
                  <li>
                    <span style="color: #99a5ae">最終更新日時：</span>
                    <span class="list-label">{{ formatFullDateTime(message.last_update_datetime) }}</span>
                  </li>
                </ul>
              </div>
            </li>
          </ul>
        </div>
        <EmptyData v-if="listMessagesDisplay.length === 0 && !isLoadingMess" />
      </div>
    </div>
    <EditorPlugin style="display: none" />
    <div v-if="listMessagesDisplay.length > 0" class="pagination">
      <div class="pagination-cases">全 {{ listMessages.length }} 件</div>
      <PaginationTable
        :page-size="pageSizelistMessages"
        :current-page="pagelistMessages"
        layout="prev, pager, next"
        :total="listMessages.length"
        @current-change="handleCurrentChange"
      />
    </div>

    <el-dialog
      v-model="modalConfig.isShowModal"
      :custom-class="modalConfig.customClass + ' handle-close'"
      :title="modalConfig.title"
      :width="modalConfig.width"
      :destroy-on-close="modalConfig.destroyOnClose"
      :close-on-click-modal="modalConfig.closeOnClickMark"
      :before-close="handleBeforeClose"
      @close="closeModal"
    >
      <template #default>
        <component :is="modalConfig.component" ref="modalChild" v-bind="{ ...modalConfig.props }" @onFinishScreen="loadData"></component>
      </template>
    </el-dialog>
  </div>
</template>

<script>
import EditorPlugin from '@/components/EditorPlugin';

import PaginationTable from '@/components/PaginationTable';
import ModalMessage from './ModalMessage';
import Z02_S02_MessageServices from '../../../api/Z02/Z02_S02_MessageServices';
import { markRaw } from 'vue';
export default {
  name: 'S02_Message',
  components: {
    PaginationTable,
    EditorPlugin
  },
  props: {
    checkLoading: [Boolean]
  },
  data() {
    return {
      listMessages: [],
      showModal: false,
      modal: '',
      id: '',
      pageSizelistMessages: 100,
      pagelistMessages: 1,
      isLoadingMess: false,
      messId: '',
      dataRes: '',
      today: new Date(),
      modalConfig: {
        title: '',
        customClass: 'custom-dialog modal-z02',
        isShowModal: false,
        component: null,
        props: {},
        width: 0,
        destroyOnClose: true,
        closeOnClickMark: false
      }
    };
  },
  computed: {
    listMessagesDisplay() {
      if (!this.listMessages || this.listMessages.length === 0) return [];
      return this.listMessages.slice(
        this.pageSizelistMessages * this.pagelistMessages - this.pageSizelistMessages,
        this.pageSizelistMessages * this.pagelistMessages
      );
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: 'メッセージ管理',
      icon: 'message-icon-color.svg',
      isShowBack: false
    });
    this.getListMessage();
  },
  methods: {
    handleBeforeClose(done) {
      try {
        this.$refs.modalChild?.confirmCancel();
      } catch (error) {
        done();
      }
    },
    closeModal() {
      this.getClassChangeBRG(this.messId, true);
    },
    getListMessage() {
      this.isLoadingMess = true;
      Z02_S02_MessageServices.getListMessageService()
        .then((res) => {
          if (res) {
            res.data.data.forEach((element) => {
              element.checkBrg = false;
              if (this.formatFullDate3(element.release_end_date) < this.formatFullDate3(this.today)) {
                element.checkBrg = true;
              }
            });
            this.listMessages = res.data.data;
            if (this.$refs.messageList) {
              this.$refs.messageList.scrollTop = 0;
            }
          }
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
          if (err.response.data.message === '指定された画面に対するアクセス権がありません。') {
            this.$router.push('/home');
          }
        })
        .finally(() => {
          this.isLoadingMess = false;
          this.js_pscroll(0.4);
        });
    },
    onclickShowModal(e, message_id) {
      this.dataRes = '';
      this.messId = '';
      if (e === 'updateMessage') {
        this.getClassChangeBRG(message_id, false);
        this.messId = message_id;
        this.modalConfig = {
          ...this.modalConfig,
          title: 'メッセージ編集',
          isShowModal: true,
          component: markRaw(ModalMessage),
          props: {
            modal: e,
            id: '',
            dataRaw: Z02_S02_MessageServices.getMessageByIdService(message_id)
          },
          width: '65rem',

          destroyOnClose: true
        };
      }
      if (e === 'createMessage') {
        this.getClassChangeBRG(message_id, false);
        this.messId = message_id;
        this.modalConfig = {
          ...this.modalConfig,
          title: 'メッセージ入力',
          isShowModal: true,
          component: markRaw(ModalMessage),
          props: {
            modal: e,
            id: ''
          },
          width: '65rem',
          destroyOnClose: true
        };
      }
    },
    loadData(e) {
      if (e === 1) {
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      } else {
        this.getListMessage();
        this.modalConfig = { ...this.modalConfig, isShowModal: false };
      }
    },
    handleCurrentChange(val) {
      this.pagelistMessages = val;
      if (this.$refs.messageList) {
        this.$refs.messageList.scrollTop = 0;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_tablet: 991px;
.pagination {
  box-shadow: inset 0px 7px 12px #e3e3e3;
}
.wrapContent {
  background: #f2f2f2;
}
.list-manag {
  padding-bottom: 8px;
  min-height: 100%;
  > ul {
    > li {
      background: #fff;
      margin-top: 15px;
      padding: 12px 12px 12px 56px;
      box-shadow: 0px 2px 5px rgba(183, 195, 203, 0.5);
      border-radius: 4px;
      position: relative;
      &.active {
        background: #e3e3e3;
        .list-title {
          .tlt {
            .svg {
              fill: #99a5ae;
            }
          }
        }
      }
    }
  }
  .list-hot {
    background: url(~@/assets/template/images/icon_hot-tag.png) no-repeat;
    width: 40px;
    height: 32px;
    display: block;
    position: absolute;
    top: -4px;
    left: 12px;
    font-size: 12px;
    color: #fcfcfc;
    font-weight: 700;
    padding: 4px 0 0 4px;
  }
  .list-title {
    .tlt {
      display: flex;
      span {
        min-width: 21px;
        margin-right: 8px;
      }
      a {
        font-size: 22px;
        font-weight: 500;
      }
    }
  }
  .list-info {
    ul {
      display: flex;
      flex-wrap: wrap;
      li {
        padding-right: 36px;
        margin-top: 16px;
        &:last-child {
          padding-right: 0;
        }
      }
    }
  }
  .active {
    background: #e3e3e3;
  }
}
</style>
