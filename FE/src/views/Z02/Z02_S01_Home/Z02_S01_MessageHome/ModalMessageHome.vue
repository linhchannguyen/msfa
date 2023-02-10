<template>
  <div class="modal-header">
    <div class="header-wrap">
      <h5 id="exampleModalLongTitle" class="modal-title">{{ dataMessage.message_subject }}</h5>
      <p class="lstMessage-impor"><span v-if="dataMessage.important_flag === 1" class="span-label span-label--yellow">重要</span></p>
    </div>
  </div>
  <div v-loading="isloading" class="modal-body">
    <div class="modal-notifi">
      <p class="pt-3" v-html="dataMessage.message_contents"></p>
    </div>
  </div>
  <div class="btn-notifi">
    <button data-dismiss="modal" type="button" class="btn btn-primary" @click="loadData()">キャンセル</button>
  </div>
</template>

<script>
import Z02_S01_HomeServices from '@/api/Z02/Z02_S01_HomeServices';
export default {
  name: 'ModalMessageHome',
  components: {},
  props: {
    id: {
      type: Number,
      required: true
    },
    importantFlag: {
      type: Number,
      required: true
    },
    subject: {
      type: String,
      default: ''
    },
    contents: {
      type: String,
      default: ''
    }
  },
  emits: ['onFinishScreen'],
  data() {
    return {
      dataMessage: {
        message_subject: '',
        important_flag: 0,
        message_contents: ''
      },
      isloading: false
    };
  },
  mounted() {
    this.readMessage();
  },
  methods: {
    readMessage() {
      this.isloading = true;
      Z02_S01_HomeServices.readMessageService(this.id)
        .then(() => {
          this.getMessageById();
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        });
    },
    getMessageById() {
      // Z02_S01_HomeServices.getMessageByIdService(this.id)
      //   .then((res) => {
      //     this.dataMessage = res.data.data[0];
      //     this.isloading = false;
      //   })
      //   .catch((err) => {
      //     this.$notify({ message: err.response.data.message, customClass: 'error' });
      //   });
      this.dataMessage.message_subject = this.subject;
      this.dataMessage.important_flag = this.importantFlag;
      this.dataMessage.message_contents = this.contents;
      this.isloading = false;
    },
    loadData() {
      this.$emit('onFinishScreen');
    }
  }
};
</script>

<style lang="scss" scoped>
.modal-title {
  font-weight: 500;
  font-size: 22px;
}
.btn-notifi {
  text-align: center;
  margin-top: 36px;
  .btn {
    width: 180px;
  }
}
.header-wrap {
  display: flex;
  align-items: center;
}
.modal-header {
  padding: 1rem 0;
  border: none;
}
.lstMessage-impor {
  margin-left: 20px;
}
.span-label--yellow {
  background: #ffeab6;
  color: #e2633b;
  font-weight: 500;
  text-align: center;
  border-radius: 12px;
}
.modal-body {
  background: #ffffff;
  border-radius: 4px;
  border: 1px solid #e3e3e3;
  min-height: 378px;
  padding: 10px 32px;
  overflow-y: auto;
}
.btn-notifi {
  margin-top: 20px !important;
}
</style>
