<template>
  <section class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <!-- <h1 class="m-0">Dashboard</h1> -->
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
        <div class="col-8">
          <div class="card" style="border-top: 3px solid darkorchid">
            <div class="card-header bg-light p-2">
              <RouterLink :to="{ name: 'reports' }" class="">
                <h6
                  class="m-0 font-weight-bold d-flex justify-content-between"
                  @click="s"
                >
                  <span
                    ><i class="fas fa-chart-line mr-2"></i>THIS MONTH
                    SALES</span
                  >
                  <span><i class="fas fa-info-circle"></i></span>
                </h6>
              </RouterLink>
            </div>
            <div class="card-body" id="linechart">
              <Line
                id="my-chart-id"
                :options="chartOptions"
                :data="chartData"
                :height="250"
              />
            </div>
          </div>
          <div
            class="card"
            style="border-top: 3px solid darkcyan"
            v-if="role == 'Admin'"
          >
            <div class="card-header bg-light p-2">
              <h6 class="m-0 font-weight-bold">
                <i class="fab fa-hotjar mr-2"></i>
                BEST-SELLING PRODUCTS THIS MONTH
              </h6>
            </div>

            <div class="card-body">
              <div class="d-flex justify-content-end mb-2">
                <button class="btn btn-success" @click="exportToExcel">
                  <i class="fas fa-file-excel"></i> Export as XLS
                </button>
              </div>
              <EasyDataTable
                show-index
                buttons-pagination
                alternating
                border-cell
                header-text-direction="center"
                body-text-direction="center"
                table-class-name="customize-table"
                :headers="headers"
                :items="sortGroupedData"
                :rows-per-page="10"
                :rows-items="[10, 25, 50]"
              >
                <template #loading>
                  <img
                    src="https://i.pinimg.com/originals/94/fd/2b/94fd2bf50097ade743220761f41693d5.gif"
                    style="width: 100px; height: 80px"
                  />
                </template>
                <template #item-image="{ image }">
                  <img
                    :src="image"
                    style="
                      display: inline-block;
                      object-fit: cover;
                      box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 10%);
                      border-radius: 15%;
                      width: 60px;
                      height: 60px;
                    "
                  />
                </template>
              </EasyDataTable>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="card" style="border-top: 3px solid red">
            <div class="card-header bg-light p-2">
              <div class="row">
                <div class="col d-flex justify-content-start">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-bar mr-2"></i>SALES TODAY
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <h6>{{ totalSales }} Sales</h6>
              <hr />
              <h6>Rp{{ moneyFormat(saleValue) }}</h6>
            </div>
            <div class="card-footer text-center">
              <RouterLink :to="{ name: 'sales' }" class=""
                >More info <i class="fas fa-arrow-circle-right"></i
              ></RouterLink>
            </div>
          </div>
          <div class="card" style="border-top: 3px solid green">
            <div class="card-header bg-light p-2">
              <div class="row">
                <div class="col d-flex justify-content-start">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-bar mr-2"></i>REVENUE THIS MONTH
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <h6>Rp{{ moneyFormat(reportSaleValue) }}</h6>
            </div>
            <div class="card-footer text-center">
              <RouterLink :to="{ name: 'reports' }" class=""
                >More info <i class="fas fa-arrow-circle-right"></i
              ></RouterLink>
            </div>
          </div>
          <div
            class="card"
            style="border-top: 3px solid orange"
            v-if="role == 'Admin'"
          >
            <div class="card-header bg-light p-2">
              <div class="row">
                <div class="col d-flex justify-content-start">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-store mr-2"></i>PURCHASE THIS MONTH
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body p-2 flex">
              <h6>
                <span class="font-weight-bold">{{ totalPurchases }}</span>
                <span> Purchases</span>
              </h6>
              <hr />
              <h6>Rp{{ moneyFormat(purchasesValue) }}</h6>
            </div>
            <div class="card-footer text-center">
              <RouterLink :to="{ name: 'purchases' }" class=""
                >More info <i class="fas fa-arrow-circle-right"></i
              ></RouterLink>
            </div>
          </div>
          <div
            class="card"
            style="border-top: 3px solid blue"
            v-if="role == 'Admin'"
          >
            <div class="card-header bg-light p-2">
              <div class="row">
                <div class="col d-flex justify-content-start">
                  <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-boxes mr-2"></i>PRODUCT
                  </h6>
                </div>
              </div>
            </div>
            <div class="card-body p-2">
              <h6>
                <span class="font-weight-bold">{{ totalProducts }}</span>
                <span> Products</span>
              </h6>
              <hr />
              <h6>
                <span class="font-weight-bold" style="color: red">{{
                  outOfStock
                }}</span>
                <span> Out of stocks</span>
              </h6>
            </div>
            <div class="card-footer text-center">
              <RouterLink :to="{ name: 'products' }" class=""
                >More info <i class="fas fa-arrow-circle-right"></i
              ></RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useProductsStore } from "../../stores/products";
import { useAuthStore } from "../../stores/auth";
import { useSalesStore } from "../../stores/sales";
import { useReportsStore } from "../../stores/reports";
import { usePurchasesStore } from "../../stores/purchases";
import { storeToRefs } from "pinia";
import ExcelJS from "exceljs";
import { Line } from "vue-chartjs";
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  ChartDataLabels
);

const userAuth = useAuthStore();
const { role } = storeToRefs(userAuth);

const productsStore = useProductsStore();
const { products } = storeToRefs(productsStore);

const salesStore = useSalesStore();
const { filteredData } = storeToRefs(salesStore);

const reportsStore = useReportsStore();
const { filteredData2 } = storeToRefs(reportsStore);

const purchasesStore = usePurchasesStore();
const { filteredPurchaseData } = storeToRefs(purchasesStore);

const headers = ref([
  { text: "IMAGE", value: "image" },
  { text: "NAME", value: "productName" },
  { text: "QTY SOLD", value: "totalQty" },
]);

const totalProducts = computed(() => {
  if (products.value.length != 0) {
    return products.value.length;
  } else {
    return 0;
  }
});

const outOfStock = computed(() => {
  if (products.value.filter((item) => item.qty <= 0).length != 0) {
    return products.value.filter((item) => item.qty <= 0).length;
  } else {
    return 0;
  }
});

salesStore.day();
const totalSales = computed(() => {
  if (filteredData.value.length != 0) {
    return filteredData.value.length;
  } else {
    return 0;
  }
});

reportsStore.month();
const totalSales2 = computed(() => {
  if (filteredData2.value.length != 0) {
    return filteredData2.value.length;
  } else {
    return 0;
  }
});

const totalPurchases = computed(() => {
  if (role.value === "Admin") {
    return filteredPurchaseData.value.length != 0
      ? filteredPurchaseData.value.length
      : 0;
  }
  return 0;
});

const purchasesValue = computed(() => {
  if (role.value === "Admin") {
    if (filteredPurchaseData.value.length !== 0) {
      const data = filteredPurchaseData.value.reduce(
        (acc, current) => acc + parseInt(current.grand_total, 10) || 0,
        0
      );
      return data;
    }
    return 0; // Return 0 if there are no purchases
  }
  return 0; // Return 0 if the role is not Admin
});

const saleValue = computed(() => {
  if (filteredData.value.length != 0) {
    const data = filteredData.value.reduce(
      (acc, current) => acc + parseInt(current.grand_total),
      0
    );
    return data;
  } else {
    return 0;
  }
});

const reportSaleValue = computed(() => {
  if (filteredData2.value.length != 0) {
    const data = filteredData2.value.reduce(
      (acc, current) => acc + parseInt(current.grand_total),
      0
    );
    return data;
  } else {
    return 0;
  }
});

const countProductsSold = (datas) => {
  const productMap = {};

  datas.forEach((data) => {
    data.sale_details.forEach((detail) => {
      const productName = detail.product_name;
      const qty = detail.qty;

      if (productMap[productName]) {
        productMap[productName] += qty;
      } else {
        productMap[productName] = qty;
      }
    });
  });

  return Object.entries(productMap).map(([name, quantity]) => ({
    product_name: name,
    total_qty: quantity,
  }));
};

const totalProductsSold = computed(() => countProductsSold(filteredData.value));

const getWeekOfMonth = (date) => {
  const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
  const dayOfMonth = date.getDate();
  return Math.ceil((dayOfMonth + firstDayOfMonth.getDay()) / 7);
};

const sortedFilteredData = computed(() => {
  return filteredData2.value.sort((a, b) => {
    return new Date(a.created_at) - new Date(b.created_at);
  });
});

const weeklySales = sortedFilteredData.value.reduce((acc, sale) => {
  const date = new Date(sale.created_at);
  const month = date.toLocaleString("default", {
    month: "long",
    year: "numeric",
  });
  const weekNumber = getWeekOfMonth(date);
  const monthWeekKey = `${month}-W${weekNumber}`;

  if (!acc[monthWeekKey]) {
    acc[monthWeekKey] = 0;
  }
  acc[monthWeekKey] += sale.grand_total;

  return acc;
}, {});

const weeklyLabels = [];
const weeklyData = [];

for (const [week, total] of Object.entries(weeklySales)) {
  weeklyLabels.push(week);
  weeklyData.push(total);
}

const chartData = ref({
  labels: weeklyLabels,
  datasets: [
    {
      label: "Weekly Sales",
      backgroundColor: "#f87979",
      data: weeklyData,
      fill: false,
      borderColor: "rgb(75, 192, 192)",
      tension: 0.1,
      // Enable data labels for this dataset
      datalabels: {
        anchor: "end",
        align: "top",
        formatter: (value, context) => {
          return "Rp" + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); // Display the value as the label
        },
      },
    },
  ],
});

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    datalabels: {
      color: "black",
      font: {
        weight: "bold",
        size: 12,
      },
    },
  },
  scales: {
    y: {
      beginAtZero: true,
    },
  },
});

const groupedData = computed(() => {
  const grouped = {};

  filteredData2.value.forEach((sale) => {
    sale.sale_details.forEach((detail) => {
      const productName = detail.product_name;

      if (grouped[productName]) {
        grouped[productName].totalQty += detail.qty;
      } else {
        grouped[productName] = {
          code: detail.product.code,
          productName: productName,
          totalQty: detail.qty,
          image: detail.product.image,
        };
      }
    });
  });

  return Object.values(grouped);
});

const sortGroupedData = computed(() => {
  return groupedData.value.sort((a, b) => b.totalQty - a.totalQty);
});

// Function to export data to Excel
const exportToExcel = async () => {
  // Create a new workbook
  const workbook = new ExcelJS.Workbook();
  const worksheet = workbook.addWorksheet("Best-Selling Products");

  const today = new Date();
  const year = today.getFullYear();
  const month = (today.getMonth() + 1).toString().padStart(2, "0");
  const day = today.getDate().toString().padStart(2, "0");

  const formattedDate = `${year}-${month}-${day}`;

  // Define columns
  worksheet.columns = [
    { header: "#", key: "index", width: 10 },
    { header: "Code", key: "code", width: 20 },
    { header: "Name", key: "productName", width: 20 },
    { header: "Qty", key: "totalQty", width: 20 },
  ];

  // Add a title
  worksheet.mergeCells(`A1:D1`);
  const titleCell = worksheet.getCell("A1");
  titleCell.value = "Best-Selling Products (" + formattedDate + ") Report"; // Set title cell value
  titleCell.font = { size: 16, bold: true }; // Set title font size and bold

  // Add the header row (this will be row 2)
  const headerRow = worksheet.addRow();
  worksheet.columns.forEach((col) => {
    headerRow.getCell(col.key).value = col.header;
  });
  headerRow.font = { bold: true }; // Make header row bold

  // Add rows
  sortGroupedData.value.forEach((product, index) => {
    worksheet.addRow({
      index: index + 1,
      code: product.code,
      productName: product.productName,
      totalQty: product.totalQty,
    });
  });

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

  // Save the workbook
  const buffer = await workbook.xlsx.writeBuffer();
  const blob = new Blob([buffer], { type: "application/octet-stream" });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement("a");
  a.href = url;
  a.download = `Best-Selling Product (${formattedDate}).xlsx`;
  a.click();
  window.URL.revokeObjectURL(url);
};

onMounted(() => {
  productsStore.getProducts();
  salesStore.getSales();
  reportsStore.getSales2();
  if (role.value == "Admin") {
    purchasesStore.getPurchases();
    purchasesStore.month();
  }
});
</script>
