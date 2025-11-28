# Cập nhật & Ghi chú phát hành

Tài liệu này ghi lại các thay đổi quan trọng sau mỗi lần fix/triển khai để đội vận hành tiện theo dõi. Mỗi mục nên bao gồm:

- **Ngày/phiên bản:** ngày merge vào main hoặc version tag.
- **Phạm vi:** mô tả ngắn gọn tính năng/bugfix chính.
- **Chi tiết kỹ thuật:** file/migration/helper nào được chạm tới, lưu ý migration/seed.
- **Hành động sau deploy:** những việc phải làm trên môi trường production (migrate, cache clear, seed…).

## 2025-11-24 – FC Mobile schema

- **Phạm vi:** chuyển đổi dữ liệu bán account từ Ngọc Rồng sang FC Mobile.
- **Chi tiết:**
  - Thêm migration `2025_11_24_000001_update_game_accounts_for_fc_mobile.php` để bổ sung các cột `account_version`, `login_method`, `team_value`, đồng thời loại bỏ `server`, `planet`, `registration_type`, `earring`.
  - Update các helper mới (`display_account_version`, `display_login_method`, `display_team_value`) và áp dụng trên mọi view user/admin.
  - Điều chỉnh `GameAccountSeeder`, controller và form admin để nhập dữ liệu mới.
  - Bổ sung filter “Bản”, “Login”, “Giá trị đội hình” cho trang danh mục người dùng.
- **Hành động sau deploy:**
  1. `php artisan migrate`
  2. (Tùy chọn) `php artisan db:seed --class=GameAccountSeeder`
  3. Kiểm tra lại trang admin tạo/sửa tài khoản và trang danh mục user xem hiển thị đúng.

## 2025-11-24 – Acc trắng Garena

- **Phạm vi:** thêm module bán acc trắng/reroll (dữ liệu chỉ gồm user & mật khẩu) cho cả admin và khách hàng.
- **Chi tiết:**
  - Migration mới `2025_11_24_120000_create_white_account_batches_table.php`, `120100_create_white_account_purchases_table.php`, `120200_create_white_accounts_table.php`.
  - Migration `2025_11_24_130000_add_type_to_game_categories_table.php` thêm cột `type` để gắn danh mục “Acc trắng” chung bảng `game_categories`.
  - Model + controller admin (`WhiteAccountBatchController`, `WhiteAccountController`) để tạo lô, import hàng loạt, quản lý kho.
  - Flow khách hàng: trang `white-accounts.index`, controller `User\WhiteAccountController`, mục “Acc trắng đã mua” trong profile (copy/tải TXT). Home page hiển thị banner “Acc trắng số lượng lớn”.
  - Seeder `WhiteAccountSeeder` tạo sẵn 2 lô demo và vài nick trắng/reroll; `ConfigSeeder` bổ sung config `white_account.*`.
- **Hành động sau deploy:**
  1. `php artisan migrate`
  2. `php artisan db:seed --class=WhiteAccountSeeder`
  3. Kiểm tra cấu hình `white_account.max_per_order`, `white_account.home_thumb` trong bảng `configs` (có thể chỉnh bằng trang Cài đặt chung).
  4. Import thêm acc trắng thật qua admin nếu cần trước khi mở bán.

> Khi có fix mới, thêm mục theo định dạng trên để mọi người dễ tracking.

