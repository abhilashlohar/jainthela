<?php
namespace App\Test\TestCase\Model\Table;

use App\Model\Table\WarehousesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\WarehousesTable Test Case
 */
class WarehousesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \App\Model\Table\WarehousesTable
     */
    public $Warehouses;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'app.warehouses',
        'app.jain_thela_admins',
        'app.item_ledgers',
        'app.drivers',
        'app.cities',
        'app.items',
        'app.item_categories',
        'app.banners',
        'app.carts',
        'app.customers',
        'app.referral_details',
        'app.from_customer',
        'app.jain_cash_points',
        'app.orders',
        'app.promo_codes',
        'app.order_details',
        'app.wallets',
        'app.plans',
        'app.auto_order_nos',
        'app.customer_addresses',
        'app.cancel_reasons',
        'app.cash_backs',
        'app.users',
        'app.franchises',
        'app.franchise_item_categories',
        'app.companies',
        'app.term_conditions',
        'app.company_details',
        'app.supplier_areas',
        'app.api_versions',
        'app.delivery_times',
        'app.order_details',
        'app.units',
        'app.transfer_inventory_vouchers',
        'app.transfer_inventory_voucher_rows'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('Warehouses') ? [] : ['className' => WarehousesTable::class];
        $this->Warehouses = TableRegistry::get('Warehouses', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->Warehouses);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
