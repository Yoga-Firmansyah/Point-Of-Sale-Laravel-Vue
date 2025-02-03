<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Purchases</h1>
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
                <div
                  class="col d-flex justify-content-start"
                  style="max-height: 30px"
                >
                    <RouterLink
                      :to="{ name: 'addPurchase' }"
                      class="btn btn-sm btn-primary"
                      >Add Purchase</RouterLink
                    >
                    <select v-model="filter" class="ml-2 rounded-sm">
                      <option value="All">All</option>
                      <option value="Day">Today</option>
                      <option value="Month">This Month</option>
                      <option value="Year">This Year</option>
                      <option value="Custom">Custom</option>
                    </select>
                    <div v-if="filter == 'Custom'" class="ml-2">
                      <VueDatePicker v-model="date" range :clearable="false" />
                    </div>
                  <div class="col d-flex justify-content-end">
                    <button
                      class="btn btn-sm btn-success"
                      @click="exportToExcel"
                    >
                      <i class="fas fa-file-excel"></i> Export as XLS
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <EasyDataTable
                show-index
                buttons-pagination
                alternating
                border-cell
                :rows-per-page="10"
                header-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="filteredPurchases"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-date="{ date }">
                  {{ formatDate(date) }}
                </template>
                <template #item-total_price="{ total_price }">
                  <div>Rp{{ moneyFormat(total_price) }}</div>
                </template>
                <template #item-discount="{ discount }">
                  <div>Rp{{ moneyFormat(discount) }}</div>
                </template>
                <template #item-grand_total="{ grand_total }">
                  <div>Rp{{ moneyFormat(grand_total) }}</div>
                </template>
                <template #item-detail="{ purchase_details }">
                  <ul v-for="item in purchase_details" :key="item.id">
                    <li>
                      {{ item.product_name }} ({{ item.qty }}) (Rp{{
                        moneyFormat(item.subtotal)
                      }})
                    </li>
                  </ul>
                </template>
                <template #item-action="item">
                  <RouterLink
                    :to="{
                      name: 'editPurchase',
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
import { ref, onMounted, computed } from "vue";
import { storeToRefs } from "pinia";
import { usePurchasesStore } from "../../../stores/purchases";
import ExcelJS from "exceljs";

const purchasesStore = usePurchasesStore();
const { purchases, filteredPurchaseData } = storeToRefs(purchasesStore);

const filter = ref("Month");

const headers = ref([
  { text: "DATE", value: "date", sortable: true },
  { text: "ITEMS", value: "total_item" },
  { text: "PRICE", value: "total_price" },
  { text: "DISCOUNT", value: "discount" },
  { text: "GRAND TOTAL", value: "grand_total" },
  { text: "DETAIL", value: "detail" },
  { text: "ACTION", value: "action" },
]);

const date = ref([new Date(), new Date()]);
const startDate = computed(() => {
  if (date.value[0]) {
    return date.value[0];
  } else {
    return null;
  }
});

const endDate = computed(() => {
  if (date.value[1]) {
    return date.value[1];
  } else {
    return startDate.value;
  }
});

const filteredPurchases = computed(() => {
  if (filter.value === "All") {
    return purchases.value;
  } else if (filter.value === "Day") {
    purchasesStore.day();
    return filteredPurchaseData.value;
  } else if (filter.value === "Month") {
    purchasesStore.month();
    return filteredPurchaseData.value;
  } else if (filter.value === "Year") {
    purchasesStore.year();
    return filteredPurchaseData.value;
  } else if (filter.value === "Custom") {
    purchasesStore.custom(startDate.value, endDate.value);
    return filteredPurchaseData.value;
  } else {
    return [];
  }
});

onMounted(() => {
  purchasesStore.getPurchases();
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
      purchasesStore.deleteData(id);
    } else {
      return true;
    }
  });
}

const groupedData = computed(() => {
  const grouped = {};

  filteredPurchaseData.value.forEach((purchase) => {
    const purchaseId = purchase.id;
    grouped[purchaseId] = {
      purchaseId: purchaseId,
      date: new Date(purchase.date),
      subTotal: purchase.total_price,
      discount: purchase.discount,
      grandTotal: purchase.grand_total,
      detail: purchase.purchase_details,
    };
  });

  return Object.values(grouped);
});

const sortGroupedData = computed(() => {
  return groupedData.value.sort((a, b) => {
    return new Date(b.date) - new Date(a.date);
  });
});

const exportToExcel = async () => {
  // Create a new workbook
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet("Purchases");

  const today = new Date();
  const year = today.getFullYear();
  const month = (today.getMonth() + 1).toString().padStart(2, "0");
  const day = today.getDate().toString().padStart(2, "0");

  const formattedDate = `${year}-${month}-${day}`;

  // Define columns
  worksheet.columns = [
    { header: "#", key: "index", width: 10 },
    { header: "Date", key: "date", width: 20 },
    { header: "Detail", key: "detail", width: 20 },
    { header: "Sub Total", key: "subTotal", width: 20 },
    { header: "Discount", key: "discount", width: 20 },
    { header: "Grand Total", key: "grandTotal", width: 20 },
  ];

  // Add a title
  worksheet.mergeCells(`A1:F1`);
  const titleCell = worksheet.getCell("A1");
  titleCell.value = filter.value + " Purchases (" + formattedDate + ") Report"; // Set title cell value
  titleCell.font = { size: 16, bold: true }; // Set title font size and bold

  // Add the header row (this will be row 2)
  const headerRow = worksheet.addRow();
  worksheet.columns.forEach((col) => {
    headerRow.getCell(col.key).value = col.header;
  });
  headerRow.font = { bold: true }; // Make header row bold

  // Add rows
  sortGroupedData.value.forEach((purchase, index) => {
    let details = [];

    // Check if purchase.detail is an array and iterate through it
    if (Array.isArray(purchase.detail)) {
      for (let i = 0; i < purchase.detail.length; i++) {
        const product = purchase.detail[i];
        // Create a structured object for each product
        details.push({
          productName: product.product_name,
          qty: product.qty,
          grandTotal: product.subtotal,
        });
      }
    }

    const detailsString = JSON.stringify(details);

    worksheet.addRow({
      index: index + 1,
      date: purchase.date,
      detail: detailsString,
      subTotal: purchase.subTotal,
      discount: purchase.discount,
      grandTotal: purchase.grandTotal,
    });
  });

  // Add a total sum row
  const totalRow = worksheet.addRow({
    index: "Total",
    date: "",
    detail: "",
    subTotal: { formula: `SUM(D3:D${sortGroupedData.value.length + 1})` },
    discount: { formula: `SUM(E3:E${sortGroupedData.value.length + 1})` },
    grandTotal: { formula: `SUM(F3:F${sortGroupedData.value.length + 1})` },
  });

  // Merge the Transaction Id, Date and Products cells in the total sum row
  worksheet.mergeCells(`A${totalRow.number}:C${totalRow.number}`);

  // Apply styles
  worksheet.getRow(totalRow.number).font = { bold: true }; // Total row bold

  // Set cell styles
  worksheet.eachRow({ includeEmpty: true }, (row) => {
    row.eachCell((cell) => {
      cell.border = {
        top: { style: "thin" },
        left: { style: "thin" },
        bottom: { style: "thin" },
        right: { style: "thin" },
      };
      cell.alignment = { vertical: "middle", horizontal: "left" };
    });
  });

  // Center the title
  titleCell.alignment = { horizontal: "center", vertical: "middle" };

  // Center the header cells
  headerRow.eachCell((cell) => {
    cell.alignment = { horizontal: "center", vertical: "middle" }; // Center the header
  });

  // Set currency format for the Total purchase column
  worksheet.getColumn("D").numFmt = "Rp#,###"; // Format Total purchase as Indonesian Rupiah
  worksheet.getColumn("E").numFmt = "Rp#,###"; // Format Total purchase as Indonesian Rupiah
  worksheet.getColumn("F").numFmt = "Rp#,###"; // Format Total purchase as Indonesian Rupiah

  // Save the workbook
  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: "application/octet-stream" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `${filter.value} Purchases (${formattedDate}) Report.xlsx`;
  a.click();
  window.URL.revokeObjectURL(url);
};
</script>
