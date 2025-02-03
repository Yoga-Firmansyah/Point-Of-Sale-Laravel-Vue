<template>
  <div
    v-if="isVisible"
    class="modal-overlay"
    style="max-height: 100vh; overflow-y: auto"
  >
    <div class="modal-dialog" style="max-height: 100vh">
      <div class="modal-content">
        <div class="modal-body">
          <div class="modal-header">
            <h4 class="modal-title">Invoice</h4>
            <button
            @click="closeModal"
            type="button"
            class="close"
            aria-label="Close"
            >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
          <div class="d-flex justify-content-center">
            <div class="receipt" id="printable-section">
              <div class="header" style="text-align: center">
                <div class="title">Point Of Sale</div>
                <div>Jl. Pahlawan No. 14 Rejomulyo-Kartoharjo, Madiun</div>
                <div>Telp: 081234567890</div>
              </div>
              <hr />
              <div class="details">
                <table>
                  <tbody>
                    <tr>
                      <td>Invoice</td>
                      <td>:</td>
                      <td>{{ addZero(data.id) }}</td>
                    </tr>
                    <tr>
                      <td>Date</td>
                      <td>:</td>
                      <td>{{ formatDate(data.date) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <hr />
              <table>
                <thead>
                  <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Disc</th>
                    <th>Total</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in data.sale_details" :key="index">
                    <td>{{ item.product_name }}</td>
                    <td>{{ item.qty }}</td>
                    <td>Rp{{ moneyFormat(item.sell_price) }}</td>
                    <td>Rp{{ moneyFormat(item.discount) }}</td>
                    <td>Rp{{ moneyFormat(item.subtotal) }}</td>
                  </tr>
                </tbody>
              </table>
              <hr />
              <div class="summary">
                <table>
                  <tbody>
                    <tr>
                      <td>Total Item</td>
                      <td>:</td>
                      <td>{{ moneyFormat(data.total_item) }}</td>
                    </tr>
                    <tr>
                      <td>Sub Total</td>
                      <td>:</td>
                      <td>Rp{{ moneyFormat(data.total_price) }}</td>
                    </tr>
                    <tr>
                      <td>Discount</td>
                      <td>:</td>
                      <td>Rp{{ moneyFormat(data.discount) }}</td>
                    </tr>
                    <tr>
                      <td>Grand Total</td>
                      <td>:</td>
                      <td>Rp{{ moneyFormat(data.grand_total) }}</td>
                    </tr>
                    <tr>
                      <td>Pay</td>
                      <td>:</td>
                      <td>Rp{{ moneyFormat(data.pay) }}</td>
                    </tr>
                    <tr>
                      <td>Change</td>
                      <td>:</td>
                      <td>Rp{{ moneyFormat(data.change) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <hr />
              <div class="footer">* Thank You *</div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button
              @click.prevent="printSection"
              class="btn btn-sm btn-primary"
            >
              Print
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {
  reactive,
  ref,
  onMounted,
  watch,
  nextTick,
} from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "../../../plugins/axios";
import printJS from "print-js";
import { useSalesStore } from "../../../stores/sales";
import { storeToRefs } from "pinia";

const salesStore = useSalesStore();
const { sales } = storeToRefs(salesStore);

const company = reactive({
  name: "Point Of Sale",
  address: "JL. Pahlawan No. 14 Rejomulyo-Kartoharjo, Madiun",
});

const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true,
  },
  isPrint: {
    type: Boolean,
    required: true,
  },
  itemId: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["close", "print"]);

const data = ref({});

const fetchData = (id) => {
  data.value = sales.value.find((item) => item.id === id) || {};
};

watch(
  () => props.itemId,
  (newId) => {
    if (newId) {
      fetchData(newId);
    }
  }
);

watch(
  () => props.isPrint,
  (newVal) => {
    if (newVal) {
      nextTick(() => {
        printSection();
      });
    }
  }
);

const closeModal = () => {
  emit("close");
};

const cart = reactive({
  id: [],
  name: [],
  price: [],
  discount: [],
  qty: [],
  discTotal: [],
  subtotal: [],
});

function printSection() {
  const printStyles = `
.receipt {
  background-color: white;
  font-family: monospace;
  width: 111mm;
  padding: 10px;
  border: 0.1mm solid black;
}

.header {
  text-align: center;
}

.details {
  margin-bottom: 35px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 5px;
  text-align: left;
}

.summary {
  margin-top: 10px;
  margin-bottom: 50px;
}

.footer {
  text-align: center;
  margin-top: 10px;
}
      `;
  printJS({
    printable: "printable-section", // The ID of the component to print
    type: "html",
    style: printStyles, // Pass the print styles here
  });
}

onMounted(() => {
  fetchData();
  salesStore.getSales();
});

</script>

<style scoped>
.receipt {
  background-color: white;
  font-family: monospace;
  width: 110mm;
  padding: 10px;
}

.header {
  text-align: center;
}

.details {
  margin-bottom: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th,
td {
  padding: 5px;
  text-align: left;
}

.summary {
  margin-top: 10px;
  margin-bottom: 50px;
}

.footer {
  text-align: center;
  margin-top: 10px;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}
</style>
