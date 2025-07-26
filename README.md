# Point-Of-Sale-Laravel-Vue

## Tentang Project

Aplikasi Point of Sale (POS) ini dirancang untuk menyederhanakan dan mengoptimalkan semua transaksi jual beli di toko Anda. Dibangun dengan menggunakan Laravel untuk backend  dan Vue.js untuk antarmuka pengguna yang responsif, aplikasi ini menyediakan solusi untuk pengelolaan operasional toko yang efisien. 
### Beberapa Fitur yang tersedia:
- Otentikasi Multi-Pengguna (Admin, Kasir): Kelola akses berdasarkan peran
- Manajemen Produk & Barcode: Efisienkan inventaris dengan dukungan barcode
- Transaksi Cepat: Proses pembelian dan penjualan tanpa hambatan.
- Laporan Komprehensif: Dapatkan laporan harian, bulanan, dan tahunan.
- Nota Cetak: Hasilkan nota transaksi profesional.
- Manajemen Pengguna: Atur data pengguna aplikasi dengan mudah.
- Grafik Penjualan: Visualisasikan performa penjualan Anda.

---

## Instalasi dan Konfigurasi
- **Clone Repository**
    ```bash
    git clone https://github.com/Yoga-Firmansyah/Point-Of-Sale-Laravel-Vue.git
    cd Point-Of-Sale-Laravel-Vue
    ```
### Laravel (Backend)
1. **Buka Folder Laravel (backend-pos)**  
   ```bash
   cd backend-pos
   ```
2. **Install dependency PHP**  
   ```bash
   composer install
   ```
3. **Konfigurasi Environment**  
   Copy file .env dari .env.example
   ```bash
   cp .env.example .env
   ```
   Konfigurasi file `.env` sesuai konfigurasi lokal kamu:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_pos
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   Generate key
   ```bash
   php artisan key:generate
   ```
   Generate JWT key
   ```bash
   php artisan jwt:key
   ```**Migrasi & Seeder Database**  
   Jalankan migrasi database dan seeder
   ```bash
   php artisan migrate --seed
   ```
7. **Jalankan Aplikasi**  
   ```bash
   php artisan serve
   ```
### Vue.js (Frontend)
1. **Buka Folder Vue.js (frontend-pos)**  
   ```bash
   cd frontend-pos
   ```
2. **Install dependency Node.js**  
   ```bash
   npm install
   ```
3. **Ubah URL API sesuai dengan url API kamu**  
   Buka file `axios.ts` dan ubah `baseURL` sesuai dengan url API kamu
   ```bash
   const request = axios.create({
    baseURL: 'http://localhost:8000/api',
   })
   ```
4. **Jalankan**  
   Untuk development
   ```bash
   npm run dev
   ```
   Untuk production
   ```bash
   npm run build
   ```

---

Secara default email dan password untuk login admin adalah:
```
Email   : admin@example.com
Password: admin123
```
Email dan password dapat diubah di file `backend-pos/database/seeders/UserSeeder.php`




