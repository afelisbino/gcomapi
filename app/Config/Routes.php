<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
/*
*TRUE para funcionar sem configurar a rota e FALSE para obrigar a configuração da rota
*/
$routes->setAutoRoute(false);


/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->group('api', ['namespace' => 'App\Api\v1'], function($routes){
	$routes->group('category', function($routes){
		$routes->post('new', 'Categoria::newCategory');
		$routes->get('list', 'Categoria::index');
		$routes->delete('delete', 'Categoria::deleteCategory');
		$routes->put('update', 'Categoria::updateCategory');
		$routes->get('find', 'Categoria::searchCategory');
		$routes->get('list_all', 'Categoria::listCategory');
	});

	$routes->group('provider', function($routes){
		$routes->post('new', 'Fornecedor::newProvider');
		$routes->get('list', 'Fornecedor::index');
		$routes->delete('delete', 'Fornecedor::deleteProvider');
		$routes->put('update', 'Fornecedor::updateProvider');
		$routes->get('find', 'Fornecedor::searchProvider');
		$routes->get('list_all', 'Fornecedor::listProvider');
	});

	$routes->group('product', function($routes){
		$routes->post('new', 'Produto::newProduct');
		$routes->get('list', 'Produto::index');
		$routes->put('update', 'Produto::updateProduct');
		$routes->delete('delete', 'Produto::deleteProduct');
		$routes->group('search', function($routes){
			$routes->get('id', 'Produto::searchProductId');
			$routes->get('barcode', 'Produto::searchProductCodBarra');
		});
	});

	$routes->group('store', function($routes){
		$routes->get('list', 'Estoque::index');
		$routes->get('find', 'Estoque::searchStore');
		$routes->post('new', 'Estoque::newStore');
		$routes->put('update', 'Estoque::updateStoreMin');
		$routes->group('history', function($routes){
			$routes->get('list', 'Estoque::listStoreHistory');
		});
		$routes->put('output', 'Estoque::outputProduct');
		$routes->put('input', 'Estoque::inputProduct');
	});

	$routes->group('nf', function($routes){
		$routes->get('list', 'EntradaNotaFiscal::index');
		$routes->post('new', 'EntradaNotaFiscal::newNf');
		$routes->get('find', 'EntradaNotaFiscal::viewInputDetail');
	});

	$routes->group('sale', function($routes){
		$routes->get('list', 'Venda::index');
		$routes->get('find', 'Venda::findDetailSale');
		$routes->post('new', 'Venda::newSale');
		$routes->group('view', function($routes){
			$routes->get('today', 'Venda::totalSaleToday');
		});
	});

	$routes->group('cash', function($routes){
		$routes->get('list', 'Caixa::index');
		$routes->post('open', 'Caixa::open');
		$routes->put('close', 'Caixa::close');
		$routes->group('view', function($routes){
			$routes->get('now', 'Caixa::totalCashOpen');
			$routes->get('last', 'Caixa::totalCashLast');
		});
	});
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}