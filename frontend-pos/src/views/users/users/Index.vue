<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Users</h1>
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
            <div class="card-header row">
              <div class="col d-flex justify-content-start">
                <RouterLink
                  :to="{ name: 'addUser' }"
                  class="btn btn-sm btn-primary"
                  >Add User</RouterLink
                >
              </div>
              <div>
                <span>search value: </span>
                <input type="text" v-model="searchValue" />
              </div>
            </div>
            <div class="container-fluid">
              <div class="card-body p-2">
                <EasyDataTable
                  show-index
                  buttons-pagination
                  alternating
                  border-cell
                  header-text-direction="center"
                  table-class-name="customize-table"
                  :headers="headers"
                  :items="users"
                  :search-field="searchField"
                  :search-value="searchValue"
                >
                  <template #loading>
                    <img
                      src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                      style="width: 100px; height: 80px"
                    />
                  </template>
                   <template #item-role="{roles}">
                    <div v-for="item in roles" :key="item.id">
                        {{ item.name }}
                      </div>
                  </template>
                  <template #item-action="item">
                    <RouterLink
                      :to="{
                        name: 'editUser',
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
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useUsersStore } from "../../../stores/users";
import { storeToRefs } from "pinia";

const userStore = useUsersStore();
const { users } = storeToRefs(userStore);

const headers = ref([
  { text: "NAME", value: "name", sortable: true },
  { text: "EMAIL", value: "email" },
  { text: "ROLE", value: "role", sortable: true },
  { text: "ACTION", value: "action" },
]);

const searchField = ["name", "email", "role"];
const searchValue = ref("");

onMounted(() => {
  userStore.getUsers();
});

function deleteData(id) {
  swal({
    title: "Are You Sure?",
    text: "This User Will Be Deleted!",
    icon: "warning",
    buttons: ["Cancel", "Yes, Delete It!"],
    dangerMode: true,
  }).then(function (isConfirm) {
    if (isConfirm) {
      userStore.deleteUser(id);
      swal({
        title: "DELETED!",
        text: "Your Data Has Been Deleted!",
        icon: "success",
        timer: 1000,
        buttons: false,
      });
    } else {
      return true;
    }
  });
}
</script>
