<?php

return [
	'notfound' => [
		'classname' => 'Views\NotFound',
	],

	'main' => [
		'pattern' => '/^([\/\\\]+)?$/iu',
		'classname' => 'Views\Main',
	],

	'main2' => [
		'pattern' => '/^paymentId=\d+\&account=\d+$/iu',
		'classname' => 'Views\Main',
	],

	'login' => [
		'pattern' => '/^login(\/)?$/i',
		'classname' => 'Views\Login',
		'method' => 'GET',
		'action' => 'page'
	],

	'login_post' => [
		'pattern' => '/^login\/post(\/)?$/iu',
		'classname' => 'Views\Login',
		'method' => 'POST',
		'action' => 'post'
	],

	'login_logout' => [
		'pattern' => '/^logout(\/)?$/iu',
		'classname' => 'Views\Login',
		'action' => 'logout'
	],

	'monitoring' => [
		'pattern' => '/^monitoring(\/)?$/iu',
		'classname' => 'Views\Monitoring',
		'method' => 'POST',
		'action' => 'post'
	],

	'donate' => [
		'pattern' => '/^donate(\/)?$/iu',
		'classname' => 'Views\Donate',
	],

	'donate_last' => [
		'pattern' => '/^donate\/last(\/)?$/iu',
		'classname' => 'Views\Payments',
		'method' => 'POST',
		'action' => 'last'
	],

	'donate_balance_price' => [
		'pattern' => '/^donate\/balance\/price(\/)?$/iu',
		'classname' => 'Views\Payments',
		'method' => 'POST',
		'action' => 'balancePrice'
	],

	'donate_balance_post' => [
		'pattern' => '/^donate\/balance\/post(\/)?$/iu',
		'classname' => 'Views\Payments',
		'method' => 'POST',
		'action' => 'balancePost'
	],

	'donate_item_price' => [
		'pattern' => '/^donate\/item\/price(\/)?$/iu',
		'classname' => 'Views\Payments',
		'method' => 'POST',
		'action' => 'itemPrice'
	],

	'donate_item_post' => [
		'pattern' => '/^donate\/item\/post(\/)?$/iu',
		'classname' => 'Views\Payments',
		'method' => 'POST',
		'action' => 'itemPost'
	],

	'status' => [
		'pattern' => '/^donate\/status\/(.*)$/iu',
		'classname' => 'Views\Payments',
		'method' => ['GET', 'POST'],
		'action' => 'status'
	],

	'static_pages' => [
		'pattern' => '/^p=[\w\-\.]+(\&paymentId=\d+\&account=\d+)?$/i',
		'classname' => 'Views\Statics',
		'method' => 'GET',
	],

	'admin_home' => [
		'pattern' => '/^admin(\/)?$/i',
		'classname' => 'Views\Admin',
		'method' => 'GET',
	],

	// Sections +
	'admin_sections' => [
		'pattern' => '/^admin\/sections(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Sections',
		'method' => 'GET',
	],

	'admin_sections_add' => [
		'pattern' => '/^admin\/sections\/add(\/)?$/i',
		'classname' => 'Views\Admin\Sections',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_sections_edit' => [
		'pattern' => '/^admin\/sections(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Sections',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_sections_add_submit' => [
		'pattern' => '/^admin\/sections\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Sections',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_sections_edit_submit' => [
		'pattern' => '/^admin\/sections\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Sections',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_sections_remove' => [
		'pattern' => '/^admin\/sections(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Sections',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Sections -

	// Categories +
	'admin_categories' => [
		'pattern' => '/^admin\/categories(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Categories',
		'method' => 'GET',
	],

	'admin_categories_add' => [
		'pattern' => '/^admin\/categories\/add(\/)?$/i',
		'classname' => 'Views\Admin\Categories',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_categories_edit' => [
		'pattern' => '/^admin\/categories(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Categories',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_categories_add_submit' => [
		'pattern' => '/^admin\/categories\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Categories',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_categories_edit_submit' => [
		'pattern' => '/^admin\/categories\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Categories',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_categories_remove' => [
		'pattern' => '/^admin\/categories(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Categories',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Categories -

	// Items +
	'admin_items' => [
		'pattern' => '/^admin\/items(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Items',
		'method' => 'GET',
	],

	'admin_items_add' => [
		'pattern' => '/^admin\/items\/add(\/)?$/i',
		'classname' => 'Views\Admin\Items',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_items_edit' => [
		'pattern' => '/^admin\/items(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Items',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_items_add_submit' => [
		'pattern' => '/^admin\/items\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Items',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_items_edit_submit' => [
		'pattern' => '/^admin\/items\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Items',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_items_remove' => [
		'pattern' => '/^admin\/items(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Items',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Items -

	// Payments +
	'admin_payments' => [
		'pattern' => '/^admin\/payments(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Payments',
		'method' => 'GET',
	],

	'admin_payments_remove' => [
		'pattern' => '/^admin\/payments(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Payments',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Payments -

	// Promo +
	'admin_promo' => [
		'pattern' => '/^admin\/promo(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Promo',
		'method' => 'GET',
	],

	'admin_promo_add' => [
		'pattern' => '/^admin\/promo\/add(\/)?$/i',
		'classname' => 'Views\Admin\Promo',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_promo_edit' => [
		'pattern' => '/^admin\/promo(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Promo',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_promo_add_submit' => [
		'pattern' => '/^admin\/promo\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Promo',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_promo_edit_submit' => [
		'pattern' => '/^admin\/promo\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Promo',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_promo_remove' => [
		'pattern' => '/^admin\/promo(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Promo',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Promo -

	// Statics +
	'admin_statics' => [
		'pattern' => '/^admin\/statics(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Statics',
		'method' => 'GET',
	],

	'admin_statics_add' => [
		'pattern' => '/^admin\/statics\/add(\/)?$/i',
		'classname' => 'Views\Admin\Statics',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_statics_edit' => [
		'pattern' => '/^admin\/statics(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Statics',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_statics_add_submit' => [
		'pattern' => '/^admin\/statics\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Statics',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_statics_edit_submit' => [
		'pattern' => '/^admin\/statics\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Statics',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_statics_remove' => [
		'pattern' => '/^admin\/statics(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Statics',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Statics -

	// Users +
	'admin_users' => [
		'pattern' => '/^admin\/users(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Users',
		'method' => 'GET',
	],

	'admin_users_add' => [
		'pattern' => '/^admin\/users\/add(\/)?$/i',
		'classname' => 'Views\Admin\Users',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_users_edit' => [
		'pattern' => '/^admin\/users(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Users',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_users_add_submit' => [
		'pattern' => '/^admin\/users\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Users',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_users_edit_submit' => [
		'pattern' => '/^admin\/users\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Users',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_users_remove' => [
		'pattern' => '/^admin\/users(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Users',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Users -

	// Servers +
	'admin_servers' => [
		'pattern' => '/^admin\/servers(\/)?(&page=\d+)?$/i',
		'classname' => 'Views\Admin\Servers',
		'method' => 'GET',
	],

	'admin_servers_add' => [
		'pattern' => '/^admin\/servers\/add(\/)?$/i',
		'classname' => 'Views\Admin\Servers',
		'method' => 'GET',
		'action' => 'add'
	],

	'admin_servers_edit' => [
		'pattern' => '/^admin\/servers(\/)?&edit=\d+$/i',
		'classname' => 'Views\Admin\Servers',
		'method' => 'GET',
		'action' => 'edit'
	],

	'admin_servers_add_submit' => [
		'pattern' => '/^admin\/servers\/add\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Servers',
		'method' => 'POST',
		'action' => 'addPost'
	],

	'admin_servers_edit_submit' => [
		'pattern' => '/^admin\/servers\/edit\/post(\/)?$/iu',
		'classname' => 'Views\Admin\Servers',
		'method' => 'POST',
		'action' => 'editPost'
	],

	'admin_servers_remove' => [
		'pattern' => '/^admin\/servers(\/)?&remove=\d+$/iu',
		'classname' => 'Views\Admin\Servers',
		'method' => 'POST',
		'action' => 'remove'
	],
	// Servers -
];

?>