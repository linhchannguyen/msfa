<template>
  <div class="facilityWorking">
    <H01S03FacilityDetailsWorkingIndividualFilter
      :dropdown-filter="dropdownFilter"
      :department="department"
      :segmentlist="segment_list"
      @onFinishScreenFilter="onFinishFilter"
    />
    <div class="workingContent">
      <div ref="tableworking" class="tableworking table-hg100 scrollbar" @scroll="onScroll">
        <div v-if="listData.length > 0" class="application-btn">
          <button v-if="showScrollLeft" type="button" class="btn btn--prev" @click="onScrollLeft">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
          <button v-if="showScrollRight" type="button" class="btn btn--next" @click="onScrollRight">
            <ImageSVG :src-image="'icon_arrow-line-table.svg'" :alt-image="'icon_arrow-line-table'" />
          </button>
        </div>
        <table class="tableBox tableCustom">
          <thead>
            <tr>
              <th>個人名</th>
              <th>所属部科</th>
              <th>施設役職</th>
              <th>大学職位</th>
              <th v-for="item of segment_list" :key="item">{{ item.segment_name }}</th>
              <th>薬審メンバー</th>
            </tr>
          </thead>
          <tbody v-if="listData.length > 0">
            <tr v-for="(item, index) of listData" :key="item">
              <td>
                <span style="color: #448add; cursor: pointer" @click="pesonDetai(item.person_cd)">{{ item.person_name }}</span>
              </td>
              <td>{{ item.department_name }}</td>
              <td>{{ item.hospital_position_cd ? item.hospital_position_name : '' }}</td>
              <td>{{ item.academic_position_cd ? item.academic_position_name : '' }}</td>
              <td
                v-for="(item3, index3) of item.segment_list"
                :key="item3"
                class="center"
                :class="item3.flagChange ? 'changeBrg' : ''"
                @click="onCheckItem(item, index, item3, index3)"
              >
                <span>
                  <span>
                    <label class="custom-control custom-checkbox">
                      <input
                        :disabled="item3.uneditable_flag === 1 || !item.ofIsLoginUser"
                        class="custom-control-input"
                        type="checkbox"
                        :checked="item3.checked === 1 ? true : false"
                        @input="onCheckItem(item, index, item3, index3)"
                      />
                      <span class="custom-control-indicator"></span>
                    </label>
                  </span>
                </span>
              </td>

              <td class="center" :class="item.flagChange1 ? 'changeBrg' : ''" @click="onCheckItemPartType(item, index)">
                <label class="custom-control custom-checkbox">
                  <input
                    :disabled="!item.ofIsLoginUser"
                    :checked="item.part_type === 1 ? true : false"
                    class="custom-control-input"
                    type="checkbox"
                    @input="onCheckItemPartType(item, index)"
                  />
                  <span class="custom-control-indicator"></span>
                </label>
              </td>
            </tr>
          </tbody>
          <tr v-else>
            <td colspan="16">
              <div class="noData">
                <div class="noData-content">
                  <p v-if="!isLoadDefault" class="noData-text">該当するデータがありません。</p>
                  <div v-if="!isLoadDefault" class="noData-thumb">
                    <img src="@/assets/template/images/data/amico.svg" alt="icon" />
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div v-if="listData.length > 0" class="paginationWork">
      <div class="paginationWork-btn">
        <button :disabled="!arrCheck[0]" type="button" class="btn btn-primary" @click="submit">保存</button>
      </div>
      <div class="paginationWork-numbel">
        <div class="paginationSearch-cases">全 {{ pageSizelistData }} 件</div>
        <PaginationTable
          :page-size="100"
          layout="prev, pager, next"
          :total="pageSizelistData"
          :current-page="currentPage"
          @current-change="handleCurrentChange"
        />
      </div>
    </div>
  </div>
</template>

<script>
import H01_S03_FacilityDetailsWorkingIndividual from '../../../api/H01/H01_S03_FacilityDetailsWorkingIndividual';
import H01S03FacilityDetailsWorkingIndividualFilter from './H01_S03_FacilityDetailsWorkingIndividualFilter.vue';
import PaginationTable from '@/components/PaginationTable';
export default {
  name: 'H01_S03_FacilityDetailsWorkingIndividual',
  components: {
    PaginationTable,
    H01S03FacilityDetailsWorkingIndividualFilter
  },
  props: {
    changetab: {
      type: String,
      default: '2'
    },
    dropdownFilter: {
      type: String,
      default: ''
    }
  },
  data() {
    return {
      isLoading: false,
      listData: [],
      segment_list: [],
      department: [],
      departmentInput: '',
      pageSizelistData: 0,
      currentPage: 1,
      total_pages: 0,
      arrCheck: [],
      indexChangeBrg: 0,
      dataFilter: {},
      arrSegmenttype: [],
      listRole: null,
      showScrollLeft: false,
      showScrollRight: false,
      isLoadDefault: true
    };
  },
  watch: {
    // $route() {
    //   if (this.arrCheck[0]) {
    //     this.$msgboxConfirm({
    //       title: '',
    //       message: '変更内容が破棄されますが、よろしいですか？ ',
    //       callback: (action) => {
    //         if (action === 'confirm') {
    //           this.submit();
    //         }
    //         if (action === 'cancel') {
    //           this.arrCheck = [];
    //           this.getList1();
    //         }
    //       },
    //       customStyle: 'width:450px'
    //     });
    //   }
    // },
    changetab: function (value) {
      // eslint-disable-next-line eqeqeq
      if (value == 2) {
        this.getList1(true);
      }
    }
  },
  mounted() {
    this.emitter.emit('change-header', {
      title: '施設詳細',
      icon: 'icon-h01-s01-facitily.svg',
      isShowBack: true
    });
    this.facility_cd = this._route('H01_FacilityDetails') ? this._route('H01_FacilityDetails').params.facility_cd : '';

    // Setup data default
    const data = JSON.parse(localStorage.getItem('DataResH01_S03'));
    const dataFilter = JSON.parse(localStorage.getItem('DataH01_S03'));
    if (data && dataFilter) {
      this.isLoadDefault = false;
      this.segment_list = data?.segment_list;
      this.department = data?.department;
      this.listData = data?.listData;
      this.pageSizelistData = data?.pageSizelistData;
    } else {
      // eslint-disable-next-line eqeqeq
      if (this.changetab == 2) {
        this.$emit('changeLoading', true);
        this.isLoading = true;
        this.getList1(true);
      }
    }
  },
  methods: {
    onScrollLeft() {
      let content = document.querySelector('.tableworking');
      content.scrollLeft -= 200;
    },
    onScrollRight() {
      let content = document.querySelector('.tableworking');
      content.scrollLeft += 200;
    },
    onScroll({ target: { scrollLeft, clientWidth, scrollWidth } }) {
      this.showScrollLeft = false;
      this.showScrollRight = false;
      if ((scrollLeft <= 1 && clientWidth < scrollWidth - 2) || (scrollLeft > 1 && scrollLeft + clientWidth < scrollWidth - 1)) {
        this.showScrollRight = true;
      }

      if (scrollLeft > 1) {
        this.showScrollLeft = true;
      }

      if (this.isScrolling) {
        let content = document.querySelector('.tableworking');
        content.scrollTop += 1;
        this.isScrolling = false;
      }
    },
    onFinishFilter(e) {
      this.dataFilter = e;
      this.isLoading = true;
      this.$emit('changeLoading', true);
      this.getList2(false);
    },
    getList1(isLoadDefault) {
      // this.isLoading = true;
      // const data = {
      //   facility_cd: this.facility_cd
      // };
      H01_S03_FacilityDetailsWorkingIndividual.getIndex().then((res) => {
        // 200
        this.department = res.data.data.department;
        this.segment_list = res.data.data.segment_list;
        // res.data.data.segment_list.forEach((element) => {
        //   element.isCheck = true;

        //   if (element.isCheck) {
        //     this.arrSegmenttype.push(element.segment_type);
        //   }
        // });
        this.getList2(isLoadDefault);
      });
    },
    getList2(isLoadDefault) {
      this.isLoadDefault = isLoadDefault;
      const data = {
        facility_cd: this.facility_cd || localStorage.getItem('facility_cd_prev'),
        department_cd: this.dataFilter.department_cd ? this.dataFilter.department_cd : '',
        part_type: this.dataFilter.part_type ?? 0,
        person_name: this.dataFilter.person_name ? this.dataFilter.person_name : '',
        segment_type: this.dataFilter.segment_type ? this.dataFilter.segment_type.toString() : this.arrSegmenttype.toString()
      };
      // pend
      H01_S03_FacilityDetailsWorkingIndividual.getList(data)
        .then((res) => {
          // 200
          this.pageSizelistData = res.data.data.pagination.total_items;
          this.listData = res.data.data.records;
          this.listData.forEach((element) => {
            element.flagChange = false;
            element.flagChange1 = false;
            element.segment_list.forEach((element2) => {
              element2.flagChange = false;
            });
          });
          if (this.$refs.tableworking) {
            this.$refs.tableworking.scrollTop = 0;
          }
          this.checkData();

          // Store Data
          localStorage.setItem(
            'DataResH01_S03',
            JSON.stringify({
              segment_list: this.segment_list,
              department: this.department,
              listData: this.listData,
              pageSizelistData: this.pageSizelistData
            })
          );
        })
        .catch((err) => {
          // 404, 500
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          // ket thuc qua trinh
          this.isLoading = false;
          this.$emit('changeLoading', false);
          this.isLoadDefault = false;
        });
    },
    checkData() {
      this.segment_list.forEach((element) => {
        if (element.uneditable_flag === 1) {
          this.listData.forEach((element2) => {
            const userLogin = this.getCurrentUser().user_cd;
            if (userLogin === element2.user_cd) {
              element2.ofIsLoginUser = true;
            }
            element2.segment_list.forEach((element3) => {
              if (element.segment_name === element3.segment_name) {
                element3.uneditable_flag = 1;
              }
            });
          });
        }
      });
    },
    pesonDetai(person_cd) {
      this.$router.push({
        name: 'H02_PersonalDetails',
        params: {
          person_cd
        },
        query: { tab: '1' }
      });
    },
    handleCurrentChange(val) {
      if (this.arrCheck[0]) {
        this.$msgboxConfirm({
          title: '',
          message: '変更内容が破棄されますが、よろしいですか？ ',
          callback: (action) => {
            if (action === 'confirm') {
              this.submit();
              if (this.total_pages <= this.currentPage) {
                this.currentPage = val;
                this.getList2(false);
              } else {
                this.currentPage = val;
                this.getList2(false);
              }
            }
            if (action === 'cancel') {
              this.arrCheck = [];
              if (this.total_pages <= this.currentPage) {
                this.currentPage = val;
                this.getList2(false);
              } else {
                this.currentPage = val;
                this.getList2(false);
              }
            }
          },
          customStyle: 'width:450px'
        });
      }
    },
    onCheckItem(item, index, item3, index3) {
      if (item.ofIsLoginUser) {
        if (item3.uneditable_flag !== 1) {
          this.indexChangeBrg = index3;
          this.listData.forEach((element) => {
            if (element.person_cd === item.person_cd) {
              element.segment_list.forEach((element2) => {
                if (element2.segment_type === item3.segment_type) {
                  if (element2.checked === 0) {
                    element2.checked = 1;
                  } else {
                    element2.checked = 0;
                  }
                }
              });
            }
          });
          if (this.listData[index].segment_list[index3].checked === 1) {
            this.listData[index].segment_list[index3].delete_flag = 1;
            this.listData[index].flagChange = !this.listData[index].flagChange;
            this.listData[index].segment_list[index3].flagChange = !this.listData[index].segment_list[index3].flagChange;
          }
          if (this.listData[index].segment_list[index3].checked === 0) {
            this.listData[index].segment_list[index3].delete_flag = -1;
            this.listData[index].flagChange = !this.listData[index].flagChange;
            this.listData[index].segment_list[index3].flagChange = !this.listData[index].segment_list[index3].flagChange;
          }
          this.arrCheck = [];
          this.listData.forEach((element) => {
            let count = 0;
            element.segment_list.forEach((element2) => {
              if (element2.flagChange) {
                count = count + 1;
                return false;
              }
            });
            if (count > 0) {
              this.arrCheck.push(element);
            }
          });
          if (this.arrCheck.length === 0) {
            localStorage.setItem('checkChangTab', false);
          } else {
            localStorage.setItem('checkChangTab', true);
          }
        }
      }
    },

    submit() {
      if (this.arrCheck.length === 0) {
        this.$notify({ message: '勤務個人一覧を入力してください。', customClass: 'error' });
      } else {
        const data = {
          facility_list: this.arrCheck
        };
        H01_S03_FacilityDetailsWorkingIndividual.putList(data)
          .then(() => {
            this.$notify({ message: '正常に更新しました。', customClass: 'success' });
            this.arrCheck = [];
            this.getList2(false);
          })
          .catch((err) => {
            this.$notify({ message: err.response.data.message, customClass: 'error' });
          });
      }
    },
    onCheckItemPartType(item, index) {
      if (item.ofIsLoginUser) {
        this.listData.forEach((element) => {
          if (element.person_cd === item.person_cd) {
            if (element.part_type === 1) {
              element.part_type = 0;
              this.listData[index].flagChange = !this.listData[index].flagChange;
              this.listData[index].flagChange1 = !this.listData[index].flagChange1;
            } else {
              element.part_type = 1;
              this.listData[index].flagChange = !this.listData[index].flagChange;
              this.listData[index].flagChange1 = !this.listData[index].flagChange1;
            }
          }
        });
        this.listData.forEach((element) => {
          if (element.flagChange) {
            if (!this.arrCheck.includes(element)) {
              this.arrCheck.push(element);
            }
          }
        });

        this.listData.forEach((element) => {
          if (element.flagChange) {
            if (!this.arrCheck.includes(element)) {
              this.arrCheck.push(element);
            }
          }
        });
      }
    }
  }
};
</script>

<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.tableworking {
  position: relative;
  min-height: unset;
  overflow: hidden;
  .application-btn {
    position: absolute;
    left: 0;
    right: 0;
    top: 0;
    bottom: 0;
    height: 0;
    margin: auto;
    z-index: 99;
    .btn {
      position: fixed;
      height: 46px;
      width: 52px;
      padding: 0;
      background: rgba(63, 75, 86, 0.4);
      z-index: 1;
      &.btn--prev {
        left: 84px;
        border-radius: 0px 60px 60px 0px;
        z-index: 9;
      }
      &.btn--next {
        right: 24px;
        border-radius: 60px 0px 0px 60px;
        z-index: 5;
        .svg {
          transform: rotate(-180deg);
        }
      }
    }
  }
}
.changeBrg {
  background: rgba(255, 234, 182, 0.5);
}
.facilityWorking {
  height: 100%;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  .groupNotes-filter {
    padding: 0 24px 10px;
    position: relative;
    .groupNotes-info {
      position: relative;
      display: flex;
      justify-content: flex-end;
    }
    .btn {
      &.btn-filter {
        padding: 0;
        width: 42px;
        height: 42px;
        border-radius: 0px 0px 8px 8px;
      }
    }
    .dropdown-filter--Notes {
      .item-filter {
        background: #448add;
        box-shadow: 0px 2px 4px rgba(13, 94, 153, 0.1), 0.5px 1px 5px 1px rgba(203, 225, 241, 0.3);
        border-radius: 0px 0px 0px 8px;
        width: 42px;
        height: 42px;
        right: 0;
        top: 0;
        position: absolute;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .title-filter {
        font-size: 1.125rem;
        font-weight: 700;
        color: #5f6b73;
        padding-bottom: 6px;
      }
      .formNotes {
        margin-bottom: 6px;
        .formNotes-label {
          color: #5f6b73;
          margin-bottom: 6px;
        }
      }
      .formNotes-checkBox {
        ul {
          display: flex;
          flex-wrap: wrap;
          margin-left: -24px;
          li {
            width: 50%;
            padding-left: 24px;
            margin-top: 5px;
            .custom-control,
            .custom-control-description {
              width: 100%;
            }
          }
        }
      }
      .btnFilter-Notes {
        text-align: center;
        margin-top: 24px;
        .btn {
          margin-right: 14px;
          width: 142px;
          &:last-child {
            margin-right: 0;
          }
        }
      }
    }
  }
  .workingContent {
    height: 100%;
    padding: 0 24px 8px;
    overflow: hidden;
    .tableworking {
      tr {
        th,
        td {
          &:nth-child(5),
          &:nth-child(6),
          &:nth-child(7),
          &:nth-child(8) {
            text-align: left;
          }
        }
        th {
          font-size: 1rem;
          width: 150px;

          &:nth-child(5),
          &:nth-child(6),
          &:nth-child(7),
          &:nth-child(8) {
            max-width: 120px;
            min-width: 120px;
          }
        }

        td {
          padding-top: 12px;
          padding-bottom: 12px;
          &.bg-active {
            background: rgba(255, 234, 182, 0.5);
          }
          .custom-control {
            padding-left: 19px;
          }
        }
      }
    }
  }

  .paginationWork {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 24px 18px;
    .paginationWork-btn {
      .btn {
        width: 180px;
      }
    }
    .paginationWork-numbel {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      .paginationSearch-cases {
        padding-right: 10px;
        color: #8e8e8e;
        font-weight: 500;
      }
    }
  }
}
.center {
  text-align: center !important;
}
</style>
