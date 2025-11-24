<h1 align="center">Shop ACC FC Mobile Việt Nam</h1>
<p align="center">
  Nền tảng mua bán tài khoản, gói dịch vụ cày thuê và vật phẩm FC Mobile dành riêng cho game thủ Việt.
</p>

---

## 1. Giới thiệu

Dự án cung cấp đầy đủ quy trình thương mại điện tử cho hệ sinh thái FC Mobile: từ niêm yết tài khoản, đặt mua dịch vụ cày thuê, mua tài khoản ngẫu nhiên, quay vòng may mắn cho tới nạp/rút tiền và quản trị đơn hàng. Toàn bộ hệ thống xây dựng trên Laravel 10 nên dễ mở rộng, bảo trì và tích hợp với các hệ thống thanh toán trong nước.

- **Đối tượng sử dụng:** chủ shop FC Mobile, admin vận hành và cộng đồng người chơi Việt.
- **Mục tiêu:** tự động hóa mua bán, minh bạch lịch sử giao dịch và dễ dàng triển khai thêm tính năng cho thị trường Việt Nam.

## 2. Kiến trúc & Công nghệ

- **Ngôn ngữ & framework:** PHP 8.1+, Laravel 10, Blade, Laravel Sanctum (API + SPA auth), Laravel Socialite (đăng nhập MXH), Laravel Breeze (scaffolding auth).
- **Frontend assets:** TailwindCSS, Vite + npm scripts, thư viện JS tuỳ chỉnh trong `public/assets`.
- **Cơ sở dữ liệu:** MySQL / MariaDB (migrations trong `database/migrations`, dữ liệu mẫu trong các seeder).
- **Hạ tầng:** Queue, Scheduler (cron artisan) cho các tác vụ nền như xử lý nạp thẻ, xoay vòng may mắn, gửi thông báo.
- **Tổ chức mã nguồn:** domain theo module (Account, Service, Random, Lucky Wheel), controller tách riêng cho user/admin, model Eloquent giàu quan hệ giúp truy vấn nhanh.

## 3. Chức năng nổi bật

- **Danh mục & sản phẩm**
  - Bán tài khoản FC Mobile theo từng hạng (`GameCategory`, `GameAccount`), hiển thị tồn kho, số lượng đã bán.
  - Danh mục random account: hệ thống tự gán tài khoản random từ pool khi người dùng mua.
  - Gói dịch vụ cày thuê, boost rank, nạp hộ; quản lý `ServicePackage`, tính giá theo server/gói.
- **Thanh toán & ví**
  - Nạp tiền bằng thẻ cào (`CardDepositController`) hoặc chuyển khoản ATM với cú pháp định danh (`BankDeposit`).
  - Lịch sử giao dịch ví, top nạp tháng, thông báo realtime.
  - Rút vàng/ngọc in-game, tracking trạng thái xử lý (`WithdrawalHistory`).
- **Mua hàng & trải nghiệm người dùng**
  - Trang chủ tổng hợp danh mục, dịch vụ, vòng quay may mắn, giao dịch gần nhất.
  - Trang profile gồm: đổi mật khẩu, lịch sử dịch vụ, lịch sử giao dịch, tài khoản đã mua/random, lịch sử vòng quay.
  - Form mua tài khoản/dịch vụ với áp mã giảm giá (`DiscountCodeController`).
- **Vòng quay may mắn & sự kiện**
  - Cấu hình nhiều vòng quay, phần thưởng tùy biến, log lịch sử quay (`LuckyWheel`, `LuckyWheelHistory`).
- **Quản trị**
  - Module admin riêng (routes `routes/admin.php`) để quản lý tài khoản, tồn kho, phê duyệt nạp/rút, cấu hình banner, thông báo, tỷ giá.

## 4. Luồng nghiệp vụ chính

1. **Người dùng đăng ký/đăng nhập** (email + mật khẩu hoặc MXH).
2. **Nạp tiền vào ví** qua thẻ/ATM, hệ thống tự đối soát và cộng số dư.
3. **Đặt mua**: chọn tài khoản cụ thể, tài khoản random hoặc gói dịch vụ → thanh toán bằng số dư → hệ thống trừ ví và ghi nhận hóa đơn.
4. **Nhận thông tin tài khoản/dịch vụ** tại mục `Tài khoản đã mua` hoặc `Dịch vụ đã thuê`.
5. **Tham gia vòng quay** và sự kiện để nhận thưởng; phần thưởng được cộng vào ví/vật phẩm.
6. **Rút vàng/ngọc** về nhân vật game khi cần, admin duyệt và cập nhật trạng thái.

## 5. Hướng dẫn cài đặt

### 5.1 Chuẩn bị môi trường

- PHP 8.1+, Composer 2.x
- Node.js 18+ & npm / pnpm
- MySQL/MariaDB, Redis (khuyến nghị cho queue)
- Các tiện ích: Git, Supervisor/PM2 cho queue worker

### 5.2 Thiết lập dự án

```bash
git clone <repo-url> fc-mobile-shop
cd fc-mobile-shop
cp .env.example .env
composer install
npm install
php artisan key:generate
```

Chỉnh `.env` cho DB, queue, mail, dịch vụ thanh toán và thông tin shop (tên, hotline, kênh CSKH).

### 5.3 Database & seed

```bash
php artisan migrate
php artisan db:seed
```

Seeder cung cấp dữ liệu mẫu cho danh mục, tài khoản demo, vòng quay và cấu hình ngân hàng. Có thể import `shopaccgamev1.sql` nếu muốn dữ liệu sản xuất mẫu.

### 5.4 Chạy ứng dụng

```bash
php artisan serve        # backend
npm run dev              # build asset
```

Hoặc dùng Vite hot reload: `npm run dev -- --host`.

### 5.5 Queue & cron

- Queue worker: `php artisan queue:work` (có thể cấu hình Supervisor).
- Scheduler: thêm dòng `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`.

### 5.6 Kiểm thử

```bash
php artisan test        # PHPUnit Feature + Unit
php artisan pint        # Chuẩn hóa coding style
```

## 6. Triển khai (Deploy)

1. Build asset production: `npm run build`.
2. `php artisan config:cache && php artisan route:cache && php artisan view:cache`.
3. Thiết lập queue worker và horizon/supervisor.
4. Đồng bộ storage: `php artisan storage:link`.
5. Sao lưu `.env`, DB trước khi cập nhật.

## 7. Định hướng phát triển & mở rộng tính năng

- **Tích hợp cổng thanh toán nội địa**: liên kết Napas, Momo, ZaloPay để người dùng thanh toán trực tiếp.
- **Hệ thống KYC & chống gian lận**: xác thực tài khoản bán, tự động khóa tài khoản có dấu hiệu spam.
- **Đa ngôn ngữ & landing marketing**: hỗ trợ tiếng Anh/Thái để mở rộng khu vực SEA.
- **Marketplace cộng tác viên**: cho phép đối tác đăng tài khoản, chia sẻ doanh thu, quản lý danh mục riêng.
- **Chat & hỗ trợ realtime**: tích hợp chatbot hoặc LiveChat giúp xử lý đơn hàng nhanh.
- **API đối tác**: mở endpoint đồng bộ đơn hàng với fanpage, bot Discord/Telegram.
- **Ứng dụng mobile**: Flutter/React Native cho trải nghiệm mua nhanh trên smartphone.

## 8. Đóng góp

- Tạo branch mới cho từng tính năng/bug.
- Viết test/seed kèm theo khi chạm tới nghiệp vụ giao dịch.
- Gửi pull request với mô tả rõ ràng, screenshot (nếu thay đổi UI).
- Báo cáo lỗi hoặc đề xuất tính năng qua Issues, ưu tiên mô tả rõ bối cảnh và log liên quan.
- Ghi lại thay đổi vào `UPDATES.md` để đội vận hành nắm rõ mỗi lần fix.

## 9. Lược đồ tài khoản FC Mobile (2025-11)

Sau khi chuyển đổi từ dữ liệu Ngọc Rồng sang FC Mobile, bảng `game_accounts` hiện dùng 3 trường cốt lõi thay cho `server/planet/registration_type/earring`:

| Trường mới | Kiểu | Mô tả |
|-----------|------|-------|
| `account_version` | enum(`vietnam`,`global`) | Phân biệt “Bản Việt Nam” và “Bản Quốc tế” (hiển thị trong UI bằng helper `display_account_version()`). |
| `login_method` | string | Hình thức đăng nhập như Garena, Facebook, Email, EA ID… (được render bởi `display_login_method()`). |
| `team_value` | unsigned bigint, nullable | Giá trị đội hình (BP), dùng để xếp loại acc và lọc nâng cao (`display_team_value()`). |

### 9.1 Nâng cấp cơ sở dữ liệu hiện có

1. Kéo code mới nhất và chạy:
   ```bash
   php artisan migrate
   ```
   Migration `2025_11_24_000001_update_game_accounts_for_fc_mobile.php` sẽ thêm các cột mới và loại bỏ trường cũ.
2. Nếu đang dùng dump `shopaccgamev1.sql`, hãy import dump đó **trước**, sau đó chạy lại migrate để đồng bộ schema.
3. Cập nhật dữ liệu hiện hữu:
   - `account_version`: đặt `vietnam` cho acc server VN, `global` cho bản quốc tế.
   - `login_method`: ghi rõ chuỗi login thực tế (ví dụ `Garena`, `Facebook`…).
   - `team_value`: nhập số BP (ví dụ `120000000`). Có thể để trống nếu chưa biết.
4. Seeder `GameAccountSeeder` đã được điều chỉnh để tạo dữ liệu mẫu đúng định dạng mới. Có thể chạy lại:
   ```bash
   php artisan db:seed --class=GameAccountSeeder
   ```

### 9.2 Ảnh hưởng tới giao diện & filter

- Trang admin (tạo/sửa/index tài khoản) và trang người dùng (danh sách + chi tiết) đã hiển thị 3 trường này.
- Bộ lọc danh mục cho phép lọc theo “Bản”, từ khóa “Login” và khoảng “Giá trị đội hình”.
- Các helper mới nằm trong `app/helpers.php`, dùng chung ở mọi view.

Nếu tự chỉnh sửa giao diện hoặc API khác, hãy đảm bảo đọc/ghi đúng 3 field này để tránh lỗi hiển thị.

## 10. Liên hệ & bản quyền

- Tác giả gốc: Phạm Hoàng Tuấn – FPT University (2025).
- Liên hệ hỗ trợ shop: cập nhật trong phần cấu hình (`App\\Helpers\\ConfigHelper`).
- Giấy phép: mã nguồn dựa trên Laravel (MIT). Các nội dung dữ liệu (tài khoản game, hình ảnh) thuộc chủ sở hữu shop và chỉ sử dụng nội bộ.

---

> “Xây dựng shop FC Mobile chuẩn Việt: nhanh, an toàn, dễ mở rộng.”  
