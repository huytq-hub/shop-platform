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

> Khi có fix mới, thêm mục theo định dạng trên để mọi người dễ tracking.

