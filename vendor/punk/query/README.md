# Query

Uma simples biblioteca PHP com a função de auxiliar nas execuções de Query's SQL.

```php
<?php
require __DIR__ . '/vendor/autoload.php';

use Punk\Query\Sql;
use Punk\Query\Capsule\Capsule;

// open the PDO connection and set it || Abrindo conexão com PDO
Sql::setConnection(['driver' => 'mysql',
    'database' => 'database',
    'port' => 'port',
    'username' => 'username',
    'password' => 'password']);

// Connect to an users table || Conectando a tabela de usuários
$users = Sql::from("users");

// listing the users || listando os usuários
foreach ($users as $user) {
    echo $user->full_name;
}

// listing some users || listando alguns usuários
foreach ($users->where(['enable' => true]) as $user) {
    echo $user->full_name;
}

// listing some users || listando alguns usuários
foreach ($users->where(['enable' => true]) as $user) {
    echo $user->full_name;
}

// diplay Query constructor user join permissions
echo $users->join('permissions');

// show CSV export
echo $users->join('permissions')->limit(12)->toCSV();

// Subselect activities to users
$activities = Sql::from("users_activities")->select('count(id)')->where(['user_id', 'id']);
$users->join('permissions')->select($activities);

class Permission extends Capsule {

    protected $primarykey = 'permission_id';
    protected $table = 'permissions';

}

class User extends Capsule {

    protected $primarykey = 'user_id';
    protected $table = 'users';

}

//Atribuindo os relacionamentos entre as classes
Permission::belongsTo('user', User::class);
User::hasMany('permissions', Permission::class);

//find permission by id || Encontrando uma permissão pelo Id
$permission = Permission::find(1);

//Get user || pegando dados do usuário
$user = $permission->user;
//Save new permission || Salvando a permissão para o usuário
$user->permissions(new Permission([]));
?>
```
