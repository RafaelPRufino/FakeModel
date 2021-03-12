<?php

require __DIR__ . '/vendor/autoload.php';

use Punk\Query\Fake\Model;

class User extends Model {
    protected $primarykey = 'user_id';
    protected $table = 'users';
}

class Permission extends Model {

    protected $primarykey = 'permission_id';
    protected $table = 'permissions';

}

User::setConnection(['driver' => 'mysql',
    'database' => 'database',
    'port' => 'port',
    'username' => 'username',
    'password' => 'password']);

$users = User::where(['enabled' , 1]);

// listing the users || listando os usuários
foreach ($users as $user) {
    echo "<br>";
    echo "Nome:" . $user->first_name;
}

$user = User::find(1);
echo "<br>";
echo  "Meu sobrenome é:" . $user->last_name;

// listing the users || listando os usuários
foreach (User::all() as $user) {
    echo "<br>";
    echo  "Id:". $user->user_id;
}

// listing the users || listando os usuários
foreach (User::page(1, 10) as $user) {
    echo "<br>";
    echo "E-mail:". $user->email;
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


