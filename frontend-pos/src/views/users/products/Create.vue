<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Products</h1>
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
                <i class="fas fa-plus mr-2"></i>Add Product
              </h6>
            </div>

            <div class="card-body">
              <form @submit.prevent="addData">
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
                          name="code"
                          autocomplete="code"
                          type="text"
                          v-model="product.code"
                          class="form-control"
                          placeholder="Code"
                          required
                        />
                      </div>
                    </div>
                    <div v-if="validation.code" class="mt-2 alert alert-danger">
                      {{ validation.code[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Name</label>
                      <input
                        name="name"
                        autocomplete="name"
                        type="text"
                        v-model="product.name"
                        class="form-control"
                        placeholder="Name"
                        required
                      />
                    </div>
                    <div v-if="validation.name" class="mt-2 alert alert-danger">
                      {{ validation.name[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Image</label>
                      <div class="input-group">
                        <input
                          type="file"
                          accept="image/*"
                          @change="handleFileChange($event)"
                        />
                      </div>
                    </div>
                    <div
                      v-if="validation.image"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.image[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Purchase Price</label>
                      <vue-number
                        class="form-control"
                        :min="0"
                        v-model="product.purchase_price"
                        v-bind="numberFormat"
                      ></vue-number>
                    </div>
                    <div
                      v-if="validation.purchase_price"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.purchase_price[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Sell Price</label>
                      <vue-number
                        class="form-control"
                        :min="0"
                        v-model="product.sell_price"
                        v-bind="numberFormat"
                      ></vue-number>
                    </div>
                    <div
                      v-if="validation.sell_price"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.sell_price[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Discount</label>
                      <vue-number
                        class="form-control"
                        :min="0"
                        :max="maxDiscount"
                        v-model="product.discount"
                        v-bind="numberFormat"
                      ></vue-number>
                    </div>
                    <div
                      v-if="validation.discount"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.discount[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Qty</label>
                      <input
                        name="qty"
                        autocomplete="qty"
                        type="number"
                        :min="0"
                        v-model="product.qty"
                        class="form-control"
                      />
                    </div>
                    <div
                      v-if="validation.qty"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.qty[0] }}
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Category</label>
                      <select
                        v-model="product.category_id"
                        class="form-control"
                        required
                      >
                        <option value="" disabled>Select Category</option>
                        <option
                          v-for="(category, index) in categories"
                          :key="index"
                          :value="category.id"
                        >
                          {{ category.name }}
                        </option>
                      </select>
                    </div>
                    <div
                      v-if="validation.category_id"
                      class="mt-2 alert alert-danger"
                    >
                      {{ validation.category_id[0] }}
                    </div>
                  </div>
                </div>

                <div class="d-flex justify-content-center">
                  <button class="btn btn-primary mr-1 btn-submit" type="submit">
                    <i class="fa fa-paper-plane"></i>Submit
                  </button>
                  <button
                    class="btn btn-secondary"
                    @click="gotoProducts"
                  >
                    <i class="fa fa-times"></i>Cancel
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <CameraScanner
    v-model="product.code"
    v-model:open-modal="barcodeModalState"
  />
</template>

<script setup>
import { reactive, ref, onMounted, computed } from "vue";
import { useRouter } from "vue-router";
import axios from "../../../plugins/axios";
import SvgIcon from "@jamescoyle/vue-icon";
import { mdiBarcodeScan } from "@mdi/js";
import CameraScanner from "../../../components/CameraScanner.vue";
import { component as VueNumber } from "@coders-tm/vue-number-format";

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

const product = reactive({
  code: "",
  name: "",
  image: "",
  purchase_price: 0,
  sell_price: 0,
  discount: 0,
  category_id: "",
  qty: 0,
});

const maxDiscount = computed(() => {
  if (product.sell_price) {
    return parseInt(product.sell_price);
  } else {
    return 0;
  }
});

const validation = ref([]);
const categories = ref([]);

//method for handle file changes
const handleFileChange = (e) => {
  //assign file to state
  product.image = e.target.files[0];
};

onMounted(async () => {
  await axios.get(`/categories`).then((response) => {
    categories.value = response.categories;
  });

  $(function () {
    bsCustomFileInput.init();
  });
});

function gotoProducts() {
  return router.push({
    name: "products",
  });
}

async function addData() {
  let formData = new FormData();

  //assign state value to formData
  formData.append("code", product.code);
  formData.append("name", product.name);
  formData.append("image", product.image);
  formData.append("purchase_price", product.purchase_price);
  formData.append("sell_price", product.sell_price);
  formData.append("discount", product.discount);
  formData.append("category_id", product.category_id);
  formData.append("qty", product.qty);
  //send server with axios
  await axios
    .post("/products", formData)
    .then((response) => {
      swal({
        title: "SUCCESS!",
        text: response.message,
        icon: "success",
        timer: 5000,
        buttons: false,
      });

      return router.push({
        name: "products",
      });
    })
    .catch((error) => {
      validation.value = error.data;
    });
}
</script>
