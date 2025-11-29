<?php
use App\Models\Config;
use Illuminate\Support\Facades\Cache;

function display_status($status)
{
    $statusClasses = [
        'success' => 'success',
        'error' => 'error',
        'processing' => 'processing',
    ];

    $statusText = [
        'success' => 'Thành công',
        'error' => 'Thất bại',
        'processing' => 'Đang xử lý',
    ];

    $class = $statusClasses[$status] ?? 'unknown';
    $text = $statusText[$status] ?? 'Không xác định';

    return "<span class=\"status-badge {$class}\">{$text}</span>";
}

function display_status_service($status)
{
    $statusClasses = [
        'pending' => 'warning',
        'processing' => 'info',
        'completed' => 'success',
        'cancelled' => 'error',
    ];

    $statusText = [
        'pending' => 'Chờ xử lý',
        'processing' => 'Đang xử lý',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ];

    $class = $statusClasses[$status] ?? 'secondary';
    $text = $statusText[$status] ?? 'Không xác định';

    return "<span class=\"status-badge {$class}\">{$text}</span>";
}
function display_status_admin($status)
{
    $statusClasses = [
        'processing' => 'bg-info',
        'success' => 'bg-success',
        'error' => 'bg-danger',
    ];

    $statusText = [
        'processing' => 'Chờ xử lý',
        'success' => 'Hoàn thành',
        'error' => 'Đã hủy',
    ];

    $class = $statusClasses[$status] ?? 'secondary';
    $text = $statusText[$status] ?? 'Không xác định';

    return "<span class=\"badges {$class}\">{$text}</span>";
}
function display_hanh_tinh($planet)
{
    switch ($planet) {
        case 'xayda':
            return 'Xayda';
        case 'earth':
            return 'Trái đất';
        case 'namek':
            return 'Namek';
        default:
            return 'Không xác định';
    }
}

function display_dang_ky($planet)
{
    return match ($planet) {
        'real' => 'Thật',
        'virtual' => 'Ảo',
        default => 'Không xác định',
    };
}

function display_account_version($server)
{
    if ($server === null || $server === '') {
        return 'Đang cập nhật';
    }

    $value = strtolower((string) $server);

    $globalValues = ['global', 'quoc-te', 'quoc te', 'qt', 'quocte', 'international', 'inter'];
    $vietnamValues = ['viet-nam', 'viet nam', 'vietnam', 'vn', 'vi'];

    if (is_numeric($server)) {
        return (int) $server === 1
            ? 'Bản Việt Nam'
            : ((int) $server === 2 ? 'Bản Quốc tế' : 'Server ' . $server);
    }

    if (in_array($value, $globalValues, true)) {
        return 'Bản Quốc tế';
    }

    if (in_array($value, $vietnamValues, true)) {
        return 'Bản Việt Nam';
    }

    return ucfirst($value);
}

function display_login_method($method, $fallback = 'Đang cập nhật')
{
    if (empty($method)) {
        return $fallback;
    }

    return match (strtolower($method)) {
        'garena' => 'Garena',
        'facebook' => 'Facebook',
        'google' => 'Google',
        'apple' => 'Apple ID',
        'ea' => 'EA ID',
        'email' => 'Email/Số điện thoại',
        'guest' => 'Khách/Guest',
        'virtual' => 'Tài khoản ảo',
        'real' => 'Tài khoản thật',
        default => ucfirst($method),
    };
}

function display_team_value($value, $fallback = 'Đang cập nhật')
{
    if ($value === null || $value === '') {
        return $fallback;
    }

    if (is_numeric($value)) {
        return number_format((float) $value) . ' BP';
    }

    return $value;
}

if (!function_exists('config_get')) {
    /**
     * Lấy giá trị cấu hình theo khóa
     *
     * @param string $key
     * @param mixed $default
     * @param bool $forceRefresh Bỏ qua cache và lấy trực tiếp từ database
     * @return mixed
     */
    function config_get($key, $default = null, $forceRefresh = false)
    {
        $cacheKey = 'config_' . $key;

        // Trong môi trường development, tự động bypass cache để dễ dàng test khi thay đổi trực tiếp trong phpMyAdmin
        $isDevelopment = config('app.env') === 'local' || config('app.debug');
        
        // Nếu là development mode và không force refresh, vẫn check cache nhưng ưu tiên database
        // Nếu forceRefresh = true, bỏ qua cache hoàn toàn
        if ($forceRefresh || $isDevelopment) {
            // Trong development mode hoặc force refresh, lấy trực tiếp từ database
            $config = Config::where('key', $key)->first();
            $value = $config ? $config->value : $default;
            
            // Vẫn cập nhật cache để production mode hoạt động tốt
            if ($value !== null) {
                Cache::put($cacheKey, $value, now()->addDay());
            }
            
            return $value;
        }

        // Production mode: Kiểm tra cache trước
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Nếu không có trong cache, lấy từ database
        $config = Config::where('key', $key)->first();
        $value = $config ? $config->value : $default;

        // Lưu vào cache để sử dụng sau
        Cache::put($cacheKey, $value, now()->addDay());

        return $value;
    }
}

if (!function_exists('config_set')) {
    /**
     * Cập nhật hoặc tạo mới cấu hình
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    function config_set($key, $value)
    {
        Config::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Cập nhật cache
        Cache::put('config_' . $key, $value, now()->addDay());
    }
}

if (!function_exists('config_all')) {
    /**
     * Lấy tất cả cấu hình
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    function config_all()
    {
        return Config::all();
    }
}

if (!function_exists('config_get_group')) {
    /**
     * Lấy một nhóm cấu hình theo tiền tố
     *
     * @param string $prefix
     * @return array
     */
    function config_get_group($prefix)
    {
        // Thêm dấu chấm nếu không có
        if (!empty($prefix) && !str_ends_with($prefix, '.')) {
            $prefix .= '.';
        }

        $configs = Config::where('key', 'LIKE', $prefix . '%')->get();
        $result = [];

        foreach ($configs as $config) {
            $key = str_replace($prefix, '', $config->key);
            $result[$key] = $config->value;
        }

        return $result;
    }
}

if (!function_exists('config_clear_cache')) {
    /**
     * Xóa cache cấu hình
     *
     * @param string|null $key Nếu có key, chỉ xóa cache của key đó. Nếu null, xóa toàn bộ cache
     * @return void
     */
    function config_clear_cache($key = null)
    {
        if ($key !== null) {
            // Chỉ xóa cache của một key cụ thể
            Cache::forget('config_' . $key);
        } else {
            // Xóa toàn bộ cache
            Cache::flush();
        }
    }
}

if (!function_exists('config_clear_key')) {
    /**
     * Xóa cache của một key cấu hình cụ thể
     * Tiện lợi hơn khi chỉ cần clear một key
     *
     * @param string $key
     * @return void
     */
    function config_clear_key($key)
    {
        Cache::forget('config_' . $key);
    }
}

function get_id_bank($prefix, $comment)
{
    // Tìm vị trí của prefix trong comment (không phân biệt chữ hoa thường)
    $pos = stripos($comment, $prefix);
    if ($pos === false) {
        // Nếu không tìm thấy prefix, trả về 0 (hoặc có thể trả về null tùy theo logic)
        return 0;
    }
    // Lấy phần chuỗi sau prefix
    $substr = substr($comment, $pos + strlen($prefix));
    // Tìm số nguyên đầu tiên xuất hiện sau prefix
    if (preg_match('/\d+/', $substr, $matches)) {
        return (int) $matches[0];
    }

    return 0;
}


function display_status_transactions_admin($status)
{
    $statusClasses = [
        'deposit' => 'bg-lightgreen',
        'withdraw' => 'bg-lightred',
        'purchase' => 'bg-lightyellow',
        'refund' => 'bg-lightpurple',
    ];

    $statusText = [
        'deposit' => 'Nạp tiền',
        'withdraw' => 'Rút tiền',
        'purchase' => 'Mua hàng',
        'refund' => 'Hoàn tiền',
    ];

    $class = $statusClasses[$status] ?? 'bg-secondary';
    $text = $statusText[$status] ?? 'Khác';

    return "<span class=\"badges {$class}\">{$text}</span>";
}

if (!function_exists('get_image_url')) {
    /**
     * Lấy URL đầy đủ cho ảnh từ config
     * Hỗ trợ cả URL đầy đủ, đường dẫn /storage, và đường dẫn tương đối
     *
     * @param string|null $path Đường dẫn ảnh
     * @return string URL đầy đủ
     */
    function get_image_url($path)
    {
        if (empty($path)) {
            return '';
        }

        // Nếu đã là URL đầy đủ (http/https), trả về nguyên bản
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return $path;
        }

        // Nếu bắt đầu với /storage, sử dụng asset() với URL hiện tại của request
        // asset() sẽ tự động sử dụng URL hiện tại (127.0.0.1:8000 hoặc domain thực tế)
        if (strpos($path, '/storage') === 0) {
            return asset($path);
        }

        // Các trường hợp khác, sử dụng asset() helper
        return asset($path);
    }
}

if (!function_exists('config_get_image')) {
    /**
     * Lấy URL ảnh từ config và tự động xử lý đường dẫn
     * Tương đương với get_image_url(config_get($key, $default))
     *
     * @param string $key Khóa config
     * @param string|null $default Giá trị mặc định
     * @return string URL đầy đủ của ảnh
     */
    function config_get_image($key, $default = null)
    {
        $path = config_get($key, $default);
        return get_image_url($path);
    }
}