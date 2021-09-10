<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->truncate();
        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'Bí đỏ hồ lô',
                'price' => '9750',
                'thumbnail' => 'http://res.cloudinary.com/quando213/image/upload/v1630746030/gatgatexpress/z2397714459635_678eba187af4f0c9fea558852ab9601e_761390139f014619820f7dc1e892ad68_1024x1024_rejmtx.jpg',
                'category_id' => 2,
                'stock' => 100,
                'description' => 'Bí đỏ hồ lô là loại thực phẩm rất giàu carotene, trong cơ thể loại chất này được chuyển hóa thành vitamin A giúp duy trì thể lực. Cùng với chất khoáng và canxi, natri, kali có trong bí đỏ có tác dụng đặc biệt với người già và người bệnh huyết áp.',
                'unit' => 2,
                'images' => '',
                'quantity' => 450,
                'brand' => 'Nông sản nội địa',
                'origin' => 'Viet Nam',
                'created_at' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now()->format('Y-m-d'),
            ],
            [
                'id' => 2,
                'name' => 'Bắp cải trắng',
                'price' => '10000',
                'thumbnail' => 'http://res.cloudinary.com/nguy-n-ng-c-thu-n/image/upload/v1630769635/sem_2_project/f0ounycn2o1axi6lgrlb.jpg',
                'category_id' => 2,
                'stock' => 100,
                'description' => 'Bắp cải trắng hay còn được gọi là cải bắp trắng thuộc họ cải, nhóm 2 lá mầm, cây thân thảo, sống khoảng hai năm. Ở nước ta, cải bắp trắng xuất hiện nhiều ở Tây Nguyên, các tỉnh miền Trung và phía Bắc, được trồng chủ yếu trong vụ đông xuân.',
                'unit' => 2,
                'images' => '',
                'quantity' => 500,
                'brand' => 'Nông sản nội địa',
                'origin' => 'Viet Nam',
                'created_at' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now()->format('Y-m-d'),
            ],
            [
                'id' => 3,
                'name' => 'Cà rốt',
                'price' => '10000',
                'thumbnail' => 'http://res.cloudinary.com/nguy-n-ng-c-thu-n/image/upload/v1630769758/sem_2_project/uy8u7sbug7qirr0xgteb.jpg',
                'category_id' => 2,
                'stock' => 100,
                'description' => 'Hình sản phẩm chỉ mang tính chất minh họa, hình bao bì của sản phẩm tùy thời điểm sẽ khác so với thực tế.',
                'unit' => 2,
                'images' => '',
                'quantity' => 500,
                'brand' => 'Nông sản nội địa',
                'origin' => 'Viet Nam',
                'created_at' => Carbon::now()->format('Y-m-d'),
                'updated_at' => Carbon::now()->format('Y-m-d'),

            ],
        ]);
    }
}
