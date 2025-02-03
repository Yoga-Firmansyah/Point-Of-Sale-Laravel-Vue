<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1>Reports</h1>
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
                    <select v-model="filter" class="rounded-sm">
                      <option value="All">All</option>
                      <option value="Day">Today</option>
                      <option value="Month">This Month</option>
                      <option value="Year">This Year</option>
                      <option value="Custom">Custom</option>
                    </select>
                    <div v-if="filter == 'Custom'">
                      <VueDatePicker
                        v-model="date"
                        class="ml-2"
                        range
                        :clearable="false"
                      />
                    </div>                    
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
            <div class="card-body p-2">
              <EasyDataTable
                show-index
                buttons-pagination
                alternating
                border-cell
                header-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="filteredSales"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-date="{ created_at }">
                  {{ formatDate(created_at) }}
                </template>
                <template #item-grand_total="{ grand_total }">
                  <div>Rp{{ moneyFormat(grand_total) }}</div>
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
import { useSalesStore } from "../../../stores/sales";
import ExcelJS from "exceljs";

const salesStore = useSalesStore();
const { sales, filteredData } = storeToRefs(salesStore);

const filter = ref("Month");

const headers = ref([
  { text: "DATE", value: "date", sortable: true },
  { text: "ITEMS SOLD", value: "total_item" },
  { text: "REVENUE", value: "grand_total" },
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

const filteredSales = computed(() => {
  if (filter.value === "All") {
    return sales.value;
  } else if (filter.value === "Day") {
    salesStore.day();
    return filteredData.value;
  } else if (filter.value === "Month") {
    salesStore.month();
    return filteredData.value;
  } else if (filter.value === "Year") {
    salesStore.year();
    return filteredData.value;
  } else if (filter.value === "Custom") {
    salesStore.custom(startDate.value, endDate.value);
    return filteredData.value;
  } else {
    return [];
  }
});

onMounted(() => {
  salesStore.getSales();
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
      salesStore.deleteData(id);
    } else {
      return true;
    }
  });
}
const groupedData = computed(() => {
  const grouped = {};

  filteredSales.value.forEach((sale) => {
    const saleId = sale.id;    
      grouped[saleId] = {
        saleId: saleId,
        date: new Date(sale.created_at),
        itemsSold: sale.total_item,
        revenue: sale.grand_total,
      };
    }
  )

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
  const worksheet = workbook.addWorksheet("Revenue");

  const today = new Date();
  const year = today.getFullYear();
  const month = (today.getMonth() + 1).toString().padStart(2, "0");
  const day = today.getDate().toString().padStart(2, "0");

  const formattedDate = `${year}-${month}-${day}`;

  // Define columns
  worksheet.columns = [
    { header: "#", key: "index", width: 10 },
    { header: "Date", key: "date", width: 20 },
    { header: "Items Sold", key: "itemsSold", width: 20 },
    { header: "Revenue", key: "revenue", width: 20 },
  ];

  // Add a title
  worksheet.mergeCells(`A1:D1`);
  const titleCell = worksheet.getCell("A1");
  titleCell.value = filter.value + " Revenue (" + formattedDate + ") Report"; // Set title cell value
  titleCell.font = { size: 16, bold: true }; // Set title font size and bold

  // Add the header row (this will be row 2)
  const headerRow = worksheet.addRow();
  worksheet.columns.forEach((col) => {
    headerRow.getCell(col.key).value = col.header;
  });
  headerRow.font = { bold: true }; // Make header row bold

  // Add rows
  sortGroupedData.value.forEach((sale, index) => {
    worksheet.addRow({
      index: index + 1,
      date: sale.date,
      itemsSold: sale.itemsSold,
      revenue: sale.revenue,
    });
  });

  // Add a total sum row
  const totalRow = worksheet.addRow({
    index: "Total",
    date: "",
    itemsSold: "",
    revenue: { formula: `SUM(D3:D${sortGroupedData.value.length + 1})` },
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

   // Set currency format for the Total Sale column
  worksheet.getColumn("D").numFmt = "Rp#,###"; // Format Total Sale as Indonesian Rupiah


  // Save the workbook
  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: "application/octet-stream" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `${filter.value} Revenue (${formattedDate}) Report.xlsx`;
  a.click();
  window.URL.revokeObjectURL(url);
};
</script>
