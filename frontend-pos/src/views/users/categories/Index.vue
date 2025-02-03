<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Categories</h1>
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
                    :to="{ name: 'addCategory' }"
                    class="btn btn-sm btn-primary"
                    >Add Category</RouterLink
                  >
                </div>
                <div class="col d-flex justify-content-end">
                  <span>search value: </span>
                  <input type="text" v-model="searchValue" />
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
                body-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="categories"
                :search-field="searchField"
                :search-value="searchValue"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-image="{ image }">
                  <img :src="image" style="width: 100px" />
                </template>
                <template #item-action="item">
                  <RouterLink
                    :to="{
                      name: 'editCategory',
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
import { useCategoriesStore } from "../../../stores/categories";
import { storeToRefs } from "pinia";

const categoriesStore = useCategoriesStore();
const { categories } = storeToRefs(categoriesStore);

const headers = ref([
  { text: "NAME", value: "name", sortable: true },
  { text: "IMAGE", value: "image" },
  { text: "ACTION", value: "action" },
]);

const searchField = ["name"];
const searchValue = ref("");

onMounted(() => {
  categoriesStore.getCategories();
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
      categoriesStore.deleteData(id);
    } else {
      return true;
    }
  });
}
</script>
