<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Sales</h1>
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
        <div class="col">
          <div class="card border-0 shadow">
            <div class="card-header">
              <h6 class="m-0 font-weight-bold">
                <i class="fas fa-plus mr-2"></i>Add Item
              </h6>
            </div>

            <div class="card-body">
              <form @submit.prevent="addCart(productData.id)">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Code</label>
                      <div class="input-group mb-3">
                        <div
                          class="input-group-prepend btn btn-sm btn-primary text-center"
                          @click="barcodeModalState = true"
                          style="cursor: pointer"
                        >
                          <SvgIcon type="mdi" :path="icons.mdiBarcodeScan" />
                        </div>
                        <input
                          @change="fetchProduct"
                          name="code"
                          autocomplete="code"
                          type="text"
                          v-model="productCode"
                          class="form-control"
                          placeholder="Code"
                          required
                        />
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Name</label>
                      <div class="form-control bg-gray">
                        {{ productData.name }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Price @pcs</label>
                      <div class="form-control bg-gray">
                        Rp{{ moneyFormat(productData.sell_price || 0) }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Qty (stock : {{ productData.qty }})</label>
                      <input
                        name="qty"
                        autocomplete="qty"
                        type="number"
                        :min="1"
                        :max="productData.qty"
                        v-model="qty"
                        class="form-control"
                      />
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-center">
                  <button
                    class="btn btn-primary mr-1 btn-submit"
                    type="submit"
                    :disabled="addItemButton"
                  >
                    <i class="fa fa-paper-plane"></i>Add Item
                  </button>
                  <button
                    class="btn btn-warning btn-reset"
                    @click="resetItem"
                    type="reset"
                  >
                    <i class="fa fa-redo"></i>Reset
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="col">
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col d-flex justify-content-start">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-shopping-cart mr-2"></i>Cart
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <form @submit.prevent="addData" class="p-2">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Total Item</label>
                      <div class="form-control bg-gray">
                        {{ total_item }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Total Price</label>
                      <div class="form-control bg-gray">
                        Rp{{ moneyFormat(total_price || 0) }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Discount</label>
                      <div class="form-control bg-gray">
                        Rp{{ moneyFormat(discount || 0) }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6"></div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Grand Total</label>
                      <div class="form-control bg-blue">
                        Rp{{ moneyFormat(grand_total) }}
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Pay</label>
                      <vue-number
                        class="form-control"
                        v-model="pay"
                        v-bind="numberFormat"
                      ></vue-number>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Change</label>
                      <div class="form-control bg-green">
                        Rp{{ moneyFormat(change) }}
                      </div>
                    </div>
                  </div>
                  <div
                    class="col-md-6 d-flex justify-content-center align-items-center"
                  >
                    <button
                      class="btn btn-primary mr-1 btn-submit"
                      type="submit"
                    >
                      <i class="fa fa-paper-plane"></i>Submit
                    </button>
                    <button
                      class="btn btn-secondary"
                      @click.prevent="gotoSales"
                    >
                      <i class="fa fa-times"></i>Cancel
                    </button>
                  </div>
                </div>
              </form>
              <EasyDataTable
                show-index
                buttons-pagination
                alternating
                border-cell
                :rows-per-page="5"
                :rows-items="[5, 10, 15, 25]"
                :fixed-header="false"
                header-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="cart"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-price="{ price }">
                  <div>Rp{{ moneyFormat(price) }}</div>
                </template>
                <template #item-discount="{ discount }">
                  <div>Rp{{ moneyFormat(discount) }}</div>
                </template>
                <template #item-subtotal="{ subtotal }">
                  <div>Rp{{ moneyFormat(subtotal) }}</div>
                </template>
                <template #item-action="item">
                  <div class="d-flex justify-content-center">
                  <button
                    @click.prevent="deleteCart(item.id)"
                    class="btn btn-sm btn-danger"
                  >
                    <i class="fa fa-trash"></i>
                  </button>
                  </div>
                </template>
              </EasyDataTable>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <CameraScanner v-model="productCode" v-model:open-modal="barcodeModalState" />
</template>

<script setup>
import { reactive, ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "../../../plugins/axios";
import SvgIcon from "@jamescoyle/vue-icon";
import { mdiBarcodeScan } from "@mdi/js";
import CameraScanner from "../../../components/CameraScanner.vue";
import { component as VueNumber } from "@coders-tm/vue-number-format";
import { useProductsStore } from "../../../stores/products";
import { storeToRefs } from "pinia";

const productsStore = useProductsStore();
const { products } = storeToRefs(productsStore);

const router = useRouter();
const barcodeModalState = ref(false);
const icons = ref({
  mdiBarcodeScan,
});

const numberFormat = {
  decimal: ",",
  separator: ".",
  prefix: "Rp",
  precision: 0,
  masked: false,
};

const headers = ref([
  { text: "NAME", value: "name", sortable: true },
  { text: "PRICE", value: "price" },
  { text: "DISCOUNT", value: "discount" },
  { text: "QTY", value: "qty" },
  { text: "SUBTOTAL", value: "subtotal" },
  { text: "ACTION", value: "action" },
]);

const addItemButton = ref(true);

const cart = ref([]);

const qty = ref("1");
const total_item = computed(() => {
  if (cart.value.length) {
    const items = cart.value.reduce(
      (acc, current) => acc + parseInt(current.qty),
      0
    );
    return items;
  } else {
    return 0;
  }
});
const total_price = computed(() => {
  if (cart.value.length) {
    const items = cart.value.reduce(
      (acc, current) => acc + parseInt(current.subtotal),
      0
    );
    return items;
  } else {
    return 0;
  }
});
const discount = computed(() => {
  if (cart.value.length) {
    const items = cart.value.reduce(
      (acc, current) => acc + parseInt(current.discTotal),
      0
    );
    return items;
  } else {
    return 0;
  }
});
const grand_total = computed(() => total_price.value - discount.value || 0);
const pay = ref(0);
const change = computed(() => {
  let gt = pay.value - grand_total.value;
  if (gt >= 0) {
    return gt;
  } else {
    return 0;
  }
});
const productCode = ref("");
const productData = reactive({
  id: "",
  name: "",
  sell_price: "",
  discount: "",
  qty: "",
});

onMounted(() => {
  productsStore.getProducts();
});

function fetchProduct() {
  try {
    const obj = products.value.find((item) => item.code == productCode.value);
    if (obj) {
      productData.id = obj.id;
      productData.name = obj.name;
      productData.sell_price = obj.sell_price;
      productData.discount = obj.discount;
      productData.qty = obj.qty;
      qty.value = 1;
      addItemButton.value = false;
    } else {
      addItemButton.value = true;
      productCode.value = "";
      productData.id = "";
      productData.name = "";
      productData.sell_price = "";
      productData.discount = "";
      productData.qty = "";
      swal({
        title: "ERROR!",
        text: "Unregistered Data!",
        icon: "error",
        timer: 3000,
        buttons: false,
      });
    }
  } catch (error) {
    console.log(error);
  }
}

function gotoSales() {
  return router.push({
    name: "sales",
  });
}

async function addData() {
  if (pay.value < grand_total.value) {
    return swal({
      title: "ERROR!",
      text: "Pay Before Checkout!",
      icon: "error",
      timer: 5000,
      buttons: false,
    });
  }

  let formData = new FormData();

  //assign state value to formData
  formData.append("total_item", total_item.value);
  formData.append("total_price", total_price.value);
  formData.append("discount", discount.value);
  formData.append("grand_total", grand_total.value);
  formData.append("pay", pay.value);
  formData.append("change", change.value);
  cart.value.forEach((item, index) => {
    formData.append(`product_id[${index}]`, item.id);
    formData.append(`product_name[${index}]`, item.name);
    formData.append(`sell_price[${index}]`, item.price);
    formData.append(`qty[${index}]`, item.qty);
    formData.append(`product_discount[${index}]`, item.discount);
    formData.append(`subtotal[${index}]`, item.subtotal);
  });
  //send server with axios
  await axios
    .post("/sales", formData)
    .then((response) => {
      swal({
        title: "SUCCESS!",
        text: response.message,
        icon: "success",
        timer: 5000,
        buttons: false,
      });

      return router.push({
        name: "sales",
      });
    })
    .catch((err) => {
      let error;
      if (err.message) {
        error = err.message;
      } else {
        error = err.data.message;
      }

      swal({
        title: "ERROR!",
        text: error,
        icon: "error",
        timer: 5000,
        buttons: false,
      });
    });
}

function addCart(data) {
  const obj = cart.value.find((item) => item.id == data);
  if (obj) {
    let subDisc = parseInt(productData.discount) * parseInt(qty.value);
    let subTotPrice = parseInt(productData.sell_price) * parseInt(qty.value);
    let subTot = subTotPrice - subDisc;
    obj.discTotal = parseInt(obj.discTotal) + subDisc;
    obj.qty = parseInt(obj.qty) + parseInt(qty.value);
    obj.subtotprice = parseInt(obj.subtotprice) + subTotPrice;
    obj.subtotal = parseInt(obj.subtotal) + subTot;
  } else {
    const newData = {
      id: productData.id,
      name: productData.name,
      price: productData.sell_price,
      discount: productData.discount,
      discTotal: productData.discount * qty.value,
      qty: qty.value,
      subtotal: productData.sell_price * qty.value - productData.discount * qty.value,
    };
    cart.value.push(newData);
  }
}

function deleteCart(id) {
  let index = cart.value.findIndex((obj) => obj.id == id);
  if (index != -1) {
    cart.value.splice(index, 1);
  }
}

function resetItem() {
  productCode.value = "";
  productData.id = "";
  productData.name = "";
  productData.sell_price = "";
  productData.discount = "";
  productData.qty = "";
}
</script>
