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
          <div class="card">
            <div class="card-header">
              <div class="row">
                <div class="col d-flex justify-content-start mb-2">
                  <RouterLink
                    :to="{ name: 'addProduct' }"
                    class="btn btn-sm btn-primary"
                    >Add Product</RouterLink
                  >
                </div>
                <div>
                      <span>search value: </span>
                      <input type="text" v-model="searchValue"/>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <EasyDataTable
                show-index
                buttons-pagination
                alternating
                border-cell
                header-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="products"
                :search-field="searchField"
                :search-value="searchValue"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-code="{ code }">
                  <vue-barcode :value="code" tag="svg" :options="{ width: 0.75, height: 40, fontSize: 16 }"></vue-barcode>
                </template>
                <template #item-purchase="{ purchase_price }">
                  <td>
                    Rp{{ moneyFormat(purchase_price) }}
                  </td>
                </template>
                <template #item-price="{ sell_price }">
                  <td>Rp{{ moneyFormat(sell_price) }}</td>
                </template>
                <template #item-discount="{ discount }">
                  <td>Rp{{ moneyFormat(discount) }}</td>
                </template>
                <template #item-action="item">
                  <RouterLink
                    :to="{
                      name: 'editProduct',
                      params: { id: item.id },
                    }"
                    class="btn btn-sm btn-primary mr-2"
                    ><i class="fa fa-pencil-alt"></i
                  ></RouterLink>
                  <button
                    @click.prevent="deleteData(item.id)"
                    class="btn btn-sm btn-danger"
                  >
                    <i class="fa fa-trash"></i>
                  </button>
                </template>
              </EasyDataTable>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useProductsStore } from "../../../stores/products";
import { storeToRefs } from "pinia";

const productsStore = useProductsStore();
const { products } = storeToRefs(productsStore);

const headers = ref([
  { text: "BARCODE", value: "code", sortable: true },
  { text: "NAME", value: "name", sortable: true },
  { text: "BUY PRICE", value: "purchase" },
  { text: "SELL PRICE", value: "price" },
  { text: "DISCOUNT", value: "discount" },
  { text: "STOCK", value: "qty", sortable: true },
  { text: "ACTION", value: "action" },
]);

const searchField = ["code", "name"];
const searchValue = ref("");

onMounted(() => {
  productsStore.getProducts();
});

function deleteData(id) {
  swal({
    title: "Are You Sure?",
    text: "This Data Will Be Deleted!",
    icon: "warning",
    buttons: ["Cancel", "Yes, Delete It!"],
    dangerMode: true,
  }).then(function (isConfirm) {
    if (isConfirm) {
      productsStore.deleteData(id);
    } else {
      return true;
    }
  });
}
</script>
