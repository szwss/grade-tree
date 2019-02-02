<?php echo '<?php'?>

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
            $table->string('name', 80);

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
