<template>
  <div v-load-f5="true" class="groupNotes-filter">
    <div class="groupNotes-info">
      <div class="filter-wrapper">
        <button class="btn btn-link btn-filter" type="button" aria-expanded="false" @click="openFilter">
          <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter.svg')" alt="" />
        </button>
        <div :class="[isShowPopupFilter ? 'show' : '', 'dropdown-menu dropdown-filter dropdown-filter--Notes']">
          <div class="item-filter btn-close-filter" @click="isShowPopupFilter = false">
            <img v-svg-inline svg-inline class="svg" :src="require('@/assets/template/images/icon_filter-white.svg')" alt="" />
          </div>
          <h2 class="title-filter">検索条件</h2>
          <div class="groupFilter-notes">
            <div class="formNotes">
              <label class="formNotes-label">所属部科</label>
              <div class="formNotes-control">
                <el-select v-model="departmentInput" placeholder="未選択" class="form-control-select">
                  <el-option class="form-control-select" label="" value="">未選択 </el-option>
                  <el-option v-for="item in department" :key="item" :label="item.department_name" :value="item.department_cd"> </el-option>
                </el-select>
              </div>
              <div class="groupFilter-notes">
                <div class="formNotes">
                  <label class="formNotes-label">個人名</label>
                  <div class="formNotes-control">
                    <el-input v-model="personName" clearable placeholder="個人名を入力" class="form-control-input" />
                  </div>
                </div>
                <div class="formNotes-checkBox">
                  <label class="formNotes-label">セグメント</label>
                  <ul>
                    <li v-for="item of arrSegmentlist" :key="item">
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input :checked="item.isCheck" class="custom-control-input" type="checkbox" @input="onCheckItem(item, index)" />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">{{ item.segment_name }}</span>
                      </label>
                    </li>
                    <li>
                      <label class="custom-control custom-checkbox custom-control--bordGreen">
                        <input v-model="partType" class="custom-control-input" type="checkbox" />
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description">薬審メンバー</span>
                      </label>
                    </li>
                  </ul>
                </div>
                <div class="btnFilter-Notes">
                  <button type="button" class="btn btn-outline-primary btn-outline-primary--cancel" @click="isShowPopupFilter = false">キャンセル</button>
                  <button type="button" class="btn btn-primary btn-filter-submit" @click="submit">検索</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import H01_S03_FacilityDetailsWorkingIndividual from '../../../api/H01/H01_S03_FacilityDetailsWorkingIndividual';
import filter_popup from '@/utils/mixin_filter_popup';
export default {
  name: 'H01_S03_FacilityDetailsWorkingIndividualFilter',
  components: {},
  mixins: [filter_popup],
  props: {
    department: {
      type: Array,
      default: () => []
    },
    dropdownFilter: {
      type: String,
      default: ''
    },
    checkLoading: [Boolean]
  },
  emits: ['onFinishScreenFilter'],
  data() {
    return {
      departmentInput: '',
      personName: '',
      partType: false,
      arrSegmentlist: []
    };
  },
  mounted() {
    // Setup filter default
    const data = JSON.parse(localStorage.getItem('DataResH01_S03'));
    const dataFilter = JSON.parse(localStorage.getItem('DataH01_S03'));
    if (data && dataFilter) {
      this.departmentInput = dataFilter.departmentInput;
      this.personName = dataFilter.personName;
      this.partType = dataFilter.partType;
      this.arrSegmentlist = [...dataFilter.arrSegmentlist];
    } else {
      this.setScreen();
    }

    if (this._route('H01_FacilityDetails')) this.facility_cd = this._route('H01_FacilityDetails').params.facility_cd;
  },
  methods: {
    setScreen() {
      H01_S03_FacilityDetailsWorkingIndividual.getIndex()
        .then((res) => {
          this.arrSegmentlist = [...res.data.data.segment_list];
          this.arrSegmentlist.forEach((element) => {
            element.isCheck = false;
          });
        })
        .catch((err) => {
          this.$notify({ message: err.response.data.message, customClass: 'error' });
        })
        .finally(() => {
          this.isLoading = false;
        });
    },
    onCheckItem(item) {
      this.arrSegmentlist.forEach((element) => {
        if (element.segment_type === item.segment_type) {
          element.isCheck = !element.isCheck;
        }
      });
    },
    openFilter() {
      this.isShowPopupFilter = true;
    },
    closeFilter() {
      // Store filter
      localStorage.setItem(
        'DataH01_S03',
        JSON.stringify({
          departmentInput: this.departmentInput,
          personName: this.personName,
          partType: this.partType,
          arrSegmentlist: this.arrSegmentlist
        })
      );

      this.isShowPopupFilter = false;
    },
    submit() {
      this.closeFilter();
      this.$emit('onFinishScreenFilter', this.getParamsFilter());
    },
    getParamsFilter() {
      let arrSegmenttype = [];
      this.arrSegmentlist.forEach((element) => {
        if (element.isCheck) {
          arrSegmenttype.push(element.segment_type);
        }
      });
      this.isShowPopupFilter = false;
      // this.$emit('onFinishScreenFilter', data);
      return {
        department_cd: this.departmentInput ? this.departmentInput : '',
        person_name: this.personName ? this.personName : '',
        segment_type: arrSegmenttype,
        part_type: this.partType ? 1 : 0
      };
    }
  }
};
</script>
<style lang="scss" scoped>
$viewport_desktop: 1366px;
$viewport_tablet: 991px;
.formNotes-label {
  color: #5f6b73;
  margin-bottom: 6px;
}
.showFilter {
  display: block;
  position: absolute;
  top: 0;
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
            margin-bottom: 5px;
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
            text-align: center;
          }
        }
        th {
          font-size: 1rem;
          width: 150px;

          &:nth-child(5),
          &:nth-child(6),
          &:nth-child(7),
          &:nth-child(8) {
            width: 100px;
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
.show {
  display: block;
  position: absolute;
  top: 0;
}
</style>
