<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearConfigCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'config:clear-cache {--key= : Xóa cache cho một key cụ thể}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Xóa cache cấu hình. Hữu ích khi thay đổi trực tiếp trong database (phpMyAdmin)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->option('key');
        
        if ($key) {
            // Clear cache cho một key cụ thể
            config_clear_key($key);
            $this->info("✅ Đã xóa cache cho key: {$key}");
        } else {
            // Clear toàn bộ cache config
            config_clear_cache();
            $this->info("✅ Đã xóa toàn bộ cache cấu hình");
        }
        
        return 0;
    }
}
