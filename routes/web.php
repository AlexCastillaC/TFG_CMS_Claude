<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\ProveedorProductoController;
use App\Http\Controllers\StandController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorProductController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// routes/web.php
// Autenticación
Route::get('/login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
Route::post('/login', 'App\Http\Controllers\Auth\LoginController@login');
Route::post('/logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');
Route::get('/registro', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
Route::post('/registro', 'App\Http\Controllers\Auth\RegisterController@register');


// Rutas del carrito
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{product_id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');


//Rutas protegidas
Route::middleware(['auth'])->group(function () {

    //Red social
    //Topicos

    // Ruta para el formulario de creación de un nuevo tema
    Route::get('/forums/{forum}/topics/create', [TopicController::class, 'create'])
        ->name('topics.create');

    // Ruta para almacenar un nuevo tema
    Route::post('/forums/{forum}/topics', [TopicController::class, 'store'])
        ->name('topics.store');

    // Ruta para mostrar un tema específico con sus comentarios
    Route::get('/forums/{forum}/topics/{topic}', [TopicController::class, 'show'])
        ->name('topics.show');

    Route::post('/forums/{forum}/topics/{topic}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    //Foros
    // Listar todos los foros
    Route::get('/forums', [ForumController::class, 'index'])->name('forums.index');

    // Mostrar formulario para crear un nuevo foro
    Route::get('/forums/create', [ForumController::class, 'create'])->name('forums.create');

    // Guardar un nuevo foro
    Route::post('/forums', [ForumController::class, 'store'])->name('forums.store');

    // Mostrar un foro específico con sus temas
    Route::get('/forums/{forum}', [ForumController::class, 'show'])->name('forums.show');

    //Mensajes
    // Lista de conversaciones
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');

    // Mostrar formulario para iniciar nueva conversación
    Route::get('/messages/create', [MessageController::class, 'create'])
        ->name('messages.create');

    // Procesar el inicio de una nueva conversación
    Route::post('/messages/start', [MessageController::class, 'startConversation'])
        ->name('messages.start');

    // Enviar un mensaje a un usuario
    Route::post('/messages/send', [MessageController::class, 'store'])->name('messages.store');

    // Ver conversación con usuario específico
    Route::get('/messages/{user}', [MessageController::class, 'show'])->name('messages.show');







    // Rutas para ver vendedor
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');

    // Rutas de checkout
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success/{order_number}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Rutas para pedidos (mencionado en la vista de success)
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index')->middleware('auth');
    Route::get('/orders/{order_number}', [OrdersController::class, 'show'])->name('orders.show')->middleware(middleware: 'auth');
    Route::get('/orders/invoice/{orderNumber}', [OrdersController::class, 'invoice'])->name('orders.invoice');
    Route::patch('/orders/{order}/cancel', [OrdersController::class, 'cancel'])->name('orders.cancel');

    //Mensages
    Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/create', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/messages/{message}', [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');
});

//Ordenes
Route::patch('/orders/{order}/status', [OrdersController::class, 'updateStatus'])->name('orders.update-status');
Route::patch('/orders/{order}/payment-status', [OrdersController::class, 'updatePaymentStatus'])->name('orders.update-payment-status');

// Rutas protegidas - Cliente
Route::group(['middleware' => ['auth', 'role:cliente']], function () {
    //Perfil
    Route::get('cliente/perfil', 'App\Http\Controllers\ClientController@profile')->name('client.profile');
    Route::put('cliente/perfil', 'App\Http\Controllers\ClientController@updateProfile')->name('client.profile.update');
    Route::post('cliente/perfil/change-password', [
        'uses' => 'App\Http\Controllers\ClientController@changePassword',
        'as' => 'client.profile.change-password'
    ]);

    // Otras rutas de cliente...
});

// Rutas protegidas - Vendedor
Route::group(['middleware' => ['auth', 'role:vendedor']], function () {
    //Perfil
    Route::get('/vendedor/perfil', 'App\Http\Controllers\VendorController@profile')->name('vendor.profile');
    Route::put('/vendedor/perfil', 'App\Http\Controllers\VendorController@updateProfile')->name('vendor.profile.update');
    Route::post('/vendedor/perfil/change-password', [
        'uses' => 'App\Http\Controllers\VendorController@changePassword',
        'as' => 'vendor.profile.change-password'
    ]);

    //Productos
    Route::get('vendedor/productos/{product}', [VendorProductController::class, 'show'])
        ->name('vendedor.productos.show');

    Route::resource('vendedor/productos', 'App\Http\Controllers\VendorProductController')
        ->names([
            'index' => 'vendedor.productos.index',
            'create' => 'vendedor.productos.create',
            'store' => 'vendedor.productos.store',
            'edit' => 'vendedor.productos.edit',
            'update' => 'vendedor.productos.update',
            'destroy' => 'vendedor.productos.destroy',
        ]);



    //Puesto
    Route::resource('vendedor/stands', StandController::class)->names([
        'index' => 'vendedor.stands.index',
        'create' => 'vendedor.stands.create',
        'store' => 'vendedor.stands.store',
        'show' => 'vendedor.stands.show',
        'edit' => 'vendedor.stands.edit',
        'update' => 'vendedor.stands.update',
        'destroy' => 'vendedor.stands.destroy',
    ]);
    Route::get('stands/category/{category}', [StandController::class, 'byCategory'])->name('stands.by_category');
    Route::get('stands/search', [StandController::class, 'search'])->name('stands.search');





    // Otras rutas de vendedor...
});

// Rutas protegidas - Proveedor
Route::group(['middleware' => ['auth', 'role:proveedor']], function () {
    //Perfil
    Route::get('/proveedor/perfil', 'App\Http\Controllers\ProviderController@profile')->name('provider.profile');
    Route::put('/proveedor/perfil', 'App\Http\Controllers\ProviderController@updateProfile')->name('provider.profile.update');
    Route::post('/proveedor/perfil/change-password', [
        'uses' => 'App\Http\Controllers\ProviderController@changePassword',
        'as' => 'provider.profile.change-password'
    ]);

    //Productos
    Route::resource('proveedor/productos', ProveedorProductoController::class)
        ->names('provider.products');

    //Puesto
    Route::resource('proveedor/stands', StandController::class)->names([
        'index' => 'provider.stands.index',
        'create' => 'provider.stands.create',
        'store' => 'provider.stands.store',
        'show' => 'provider.stands.show',
        'edit' => 'provider.stands.edit',
        'update' => 'provider.stands.update',
        'destroy' => 'provider.stands.destroy',
    ]);
    Route::get('stands/category/{category}', [StandController::class, 'byCategory'])->name('stands.by_category');
    Route::get('stands/search', [StandController::class, 'search'])->name('stands.search');
    // Otras rutas de proveedor...
});

// Rutas públicas
Route::get('/', 'App\Http\Controllers\HomeController@index')->name('home');
Route::get('/productos', 'App\Http\Controllers\ProductController@index')->name('products.index');
Route::get('/productos/{product}', 'App\Http\Controllers\ProductController@show')->name('products.show');
Route::get('/puestos', 'App\Http\Controllers\StandController@index')->name('stands.index');
Route::get('/puestos/{stand}', 'App\Http\Controllers\StandController@show')->name('stands.show');

