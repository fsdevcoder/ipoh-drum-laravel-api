<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(ModuleTableSeeder::class);
        
        // $this->call(AccountModuleTableSeeder::class);
        // $this->call(BatchModuleTableSeeder::class);
        // $this->call(CompanyModuleTableSeeder::class);
        // // $this->call(GroupModuleTableSeeder::class);
        // $this->call(InventoryModuleTableSeeder::class);
        // // $this->call(PaymentModuleTableSeeder::class);
        // $this->call(PurchaseModuleTableSeeder::class);
        // $this->call(SaleModuleTableSeeder::class);
        // $this->call(StockTransferModuleTableSeeder::class);
        // $this->call(UserModuleTableSeeder::class);
        // // $this->call(CompanyTypeTableModuleTableSeeder::class);
        // $this->call(PriceListModuleTableSeeder::class);

        //Role Based Module
        $this->call(ModuleTableSeeder::class);
        $this->call(ModuleUserSeeder::class);
        $this->call(ModuleCompanySeeder::class);
        $this->call(ModuleCompanyTypeSeeder::class);
        $this->call(ModuleGroupSeeder::class);
        $this->call(ModuleRoleSeeder::class);

        //Store Management Module
        $this->call(ModuleInventorySeeder::class);
        $this->call(ModuleTicketSeeder::class);
        $this->call(ModulePaymentSeeder::class);
        $this->call(ModulePurchaseSeeder::class);
        $this->call(ModuleSaleSeeder::class);
        $this->call(ModuleCategorySeeder::class);
        $this->call(ModuleTypeSeeder::class);
        $this->call(ModuleBackOrderSeeder::class);
        $this->call(ModuleProductFeatureSeeder::class);
        $this->call(ModuleVerificationCodeSeeder::class);
        $this->call(ModuleStoreSeeder::class);

        //Video Management Module
        $this->call(ModuleChannelSeeder::class);
        $this->call(ModuleVideoSeeder::class);

        //Article Management Module
        $this->call(ModuleArticleSeeder::class);


        $this->call(RoleTableSeeder::class);
        $this->call(RoleAdminSeeder::class);
        $this->call(RoleSuperAdminSeeder::class);
        $this->call(RoleGroupManagerSeeder::class);


        //Testing Data
        $this->call(CompanyTypeTableSeeder::class);
        $this->call(GroupTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CategoryTableSeeder::class);
        $this->call(TypeTableSeeder::class);
        $this->call(ProductFeatureTableSeeder::class);
        $this->call(WarrantySeeder::class);
        $this->call(ShippingSeeder::class);
        $this->call(ProductPromotionSeeder::class);
        $this->call(ProductCharacteristicSeeder::class);
        $this->call(StoreTableSeeder::class);
        $this->call(TicketTableSeeder::class);
        $this->call(InventoryTableSeeder::class);
        $this->call(SaleTableSeeder::class);
        $this->call(ChannelTableSeeder::class);
        $this->call(BloggerTableSeeder::class);
    }
}
