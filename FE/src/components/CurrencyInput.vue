<template>
  <el-input ref="inputRef" :model-value="formattedValue" :class="className" :placeholder="placeholder">
    <template v-if="suffix" #suffix> {{ suffix }} </template>
  </el-input>
</template>

<script>
import { useCurrencyInput } from 'vue-currency-input';
import { watch } from 'vue';

export default {
  name: 'CurrencyInput',
  props: {
    modelValue: {
      type: Number || String,
      required: false
    },
    precision: {
      type: Number || String, // number or NumberRange
      required: false,
      default: 0 // display Value As Integer; ex: 123.111 => 123,111
    },
    valueRange: {
      type: Object,
      required: false,
      default() {
        return {
          min: 0,
          max: 99999999
        };
      }
    },
    options: {
      type: Object,
      required: false,
      default() {
        return {
          currency: 'JPY',
          locale: 'ja',
          currencyDisplay: 'hidden', // $123,123 => hidden $ : 123,123
          hideGroupingSeparatorOnFocus: false // show currency on focus
        };
      }
    },
    className: {
      type: String,
      required: false
    },
    placeholder: {
      type: String,
      required: false
    },
    suffix: {
      type: String,
      required: false,
      default: ''
    },
    maxlength: {
      type: Number || String,
      required: false,
      default: 4
    }
  },
  setup(props) {
    const { inputRef, formattedValue, setValue } = useCurrencyInput({
      ...props.options,
      maxlength: props.maxlength,
      precision: props.precision
      // valueRange: props.options.valueRange || props.valueRange
    });
    watch(
      () => props.modelValue,
      (val) => {
        if (val) {
          let val2 = Number(val.toString().slice(0, +props.maxlength));
          if (val > 0 && val.toString().length >= +props.maxlength) {
            setValue(val2);
          } else {
            setValue(val);
          }
        }
      }
    );
    return { inputRef, formattedValue };
  }
};
</script>
