<?php echo '<?php'?>

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class {{$className}} extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{{$table }}', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('parent_id');
            $table->unsignedTinyInteger('level');

            /*
            * 开始
            * */
            $table->string('name', 50)->unique()->comment('//显示名称');//链式调用 unique 方法来创建索引//unique()20180518改为唯一
            $table->string('title',60)->nullable()->comment('//SEO标题');//可以为空
            $table->string('keywords',80)->nullable()->comment('//SEO关键词');//可以为空
            $table->string('description',160)->nullable()->comment('//SEO描述');//可以为空
            $table->string('thumbnail')->nullable()->comment('//缩略图');//同上

            $table->text('content')->nullable()->comment('//内容');//可以为空,不设置可以为空，添加行时会说：没有默认值

            $table->Integer('view')->unsigned()->default(0)->comment('//查看次数');
            $table->boolean('forbidden')->default(0)->comment('//禁用0为不禁用1为禁用');//默认为0，不禁用

            $table->smallInteger('order')->unsigned()->default(0)->comment('//排序');//unsigned()正整数，0-65535
            /*
            * 结束
            * */

            //如果表的字符集是 utf8mb4 时，一个字符将占用 4 个字节。
            //这意味着索引前缀最大长度为 3072 字节时，只能容纳 3072 / 4 = 768 个字符。
            //因此只要将上面图中的建表语句索引字段的前缀长度设为 768 就行了
            //Syntax error or access violation: 1071 Specified key was too long;
            //max key length is 3072 bytes")
            $table->string('node', 768);

            $table->timestamps();
            @if($softDelete)
            $table->softDeletes();
            @endif
            
            //添加普通索引
            $table->index('parent_id');
            $table->index('name');
            $table->index('node');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('{{ $table }}');
    }

}
