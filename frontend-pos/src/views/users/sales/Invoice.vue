<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Invoice</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right"></ol>
        </div>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="invoice p-3 mb-3">
            <div class="d-flex justify-content-center">
              <div class="receipt" id="printable-section">
                <div class="header">
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
                        <td>{{ addZero(id) }}</td>
                      </tr>
                      <tr>
                        <td>Date</td>
                        <td>:</td>
                        <td>{{ formatDate(date) }}</td>
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
          <tr v-for="(item, index) in cart.id" :key="index">
            <td>{{ cart.name[index] }}</td>
            <td>{{ cart.qty[index] }}</td>
            <td>Rp{{ moneyFormat(cart.price[index]) }}</td>
            <td>Rp{{ moneyFormat(cart.discount[index]) }}</td>
            <td>Rp{{ moneyFormat(cart.subtotal[index]) }}</td>
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
                        <td>{{ moneyFormat(qty) }}</td>
                      </tr>
                      <tr>
                        <td>Sub Total</td>
                        <td>:</td>
                        <td>Rp{{ moneyFormat(total_price) }}</td>
                      </tr>
                      <tr>
                        <td>Discount</td>
                        <td>:</td>
                        <td>Rp{{ moneyFormat(discount) }}</td>
                      </tr>
                      <tr>
                        <td>Grand Total</td>
                        <td>:</td>
                        <td>Rp{{ moneyFormat(grand_total) }}</td>
                      </tr>
                      <tr>
                        <td>Pay</td>
                        <td>:</td>
                        <td>Rp{{ moneyFormat(pay) }}</td>
                      </tr>
                      <tr>
                        <td>Change</td>
                        <td>:</td>
                        <td>Rp{{ moneyFormat(change) }}</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <hr />
                <div class="footer">* Thank You *</div>
              </div>
            </div>

            <div class="row no-print">
              <div class="col-12">
                <button @click="printSection" class="btn btn-default">
                  Print
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter, useRoute } from "vue-router";
import axios from "../../../plugins/axios";
const route = useRoute();
import printJS from "print-js";

const company = reactive({
  name: "Point Of Sale",
  address: "JL. Pahlawan No. 14 Rejomulyo-Kartohardjo, Madiun",
});

const cart = reactive({
  id: [],
  name: [],
  price: [],
  discount: [],
  qty: [],
  discTotal: [],
  subtotal: [],
});

const user = ref("");
const role = ref("");
const qty = ref(0);
const total_price = ref(0);
const discount = ref(0);
const grand_total = ref(0);
const pay = ref(0);
const change = ref(0);
const date = ref("");
const id = ref(0);

onMounted(async () => {
  await axios.get(`/sales/${route.params.id}`).then((response) => {
    id.value = response.data[0].id;
    user.value = response.user[0].name;
    role.value = response.user[0].roles[0].name;
    date.value = response.data[0].created_at;
    qty.value = response.data[0].total_item;
    total_price.value = response.data[0].total_price;
    discount.value = response.data[0].discount;
    grand_total.value = response.data[0].grand_total;
    pay.value = response.data[0].pay;
    change.value = response.data[0].change;
    console.log(change);
    const datas = response.data[0].sale_details;
    datas.forEach((data) => {
      cart.id.push(data.product_id);
      cart.name.push(data.product_name);
      cart.price.push(data.sell_price);
      cart.discount.push(data.discount);
      cart.qty.push(data.qty);
      cart.subtotal.push(data.subtotal);
      cart.discTotal.push(parseInt(data.discount) * parseInt(data.qty));
    });
    console.log(cart.discTotal);
  });
});

function printSection() {
  const printStyles = `
.receipt {
  background-color: white;
  font-family: monospace;
  width: 101mm;
  padding: 10px;
  border: 0.1mm solid black;
}

.header {
  text-align: center;
}

.details {
  margin-bottom: 40px;
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

.a {
  width: 17.5mm;
  font-size: small;
  word-wrap: break-word;

}

.b {
  width: 5mm;
  font-size: medium;
  word-wrap: break-word;
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

const modalActive = ref(false);
</script>

<style scoped>
.receipt {
  background-color: white;
  font-family: monospace;
  width: 101mm;
  padding: 10px;
  border: 0.1mm solid black;
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

.a {
  width: 17.5mm;
  font-size: medium;
  word-wrap: break-word;

}

.b {
  width: 5mm;
  font-size: medium;
  word-wrap: break-word;
}

.summary {
  margin-top: 10px;
  margin-bottom: 50px;
}

.footer {
  text-align: center;
  margin-top: 10px;
}
</style>
