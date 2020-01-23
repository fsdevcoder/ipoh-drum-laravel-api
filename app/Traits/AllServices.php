<?php

namespace App\Traits;

//Functionality Services
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\LogServices;
use App\Traits\AssignDatabaseRelationship;
use App\Traits\ImageHostingServices;
use App\Traits\VideoHostingServices;

//Model Services
use App\Traits\ArticleServices;
use App\Traits\BloggerServices;
use App\Traits\CategoryServices;
use App\Traits\CommentServices;
use App\Traits\CompanyServices;
use App\Traits\CompanyTypeServices;
use App\Traits\ChannelSaleServices;
use App\Traits\ChannelServices;
use App\Traits\GroupServices;
use App\Traits\InventoryFamilyServices;
use App\Traits\InventoryServices;
use App\Traits\ModuleServices;
use App\Traits\PatternServices;
use App\Traits\PaymentServices;
use App\Traits\ProductFeatureServices;
use App\Traits\ProductPromotionServices;
use App\Traits\ProductReviewServices;
use App\Traits\RoleServices;
use App\Traits\SaleItemServices;
use App\Traits\SaleServices;
use App\Traits\SecondCommentServices;
use App\Traits\ShippingServices;
use App\Traits\StoreReviewServices;
use App\Traits\StoreServices;
use App\Traits\TicketServices;
use App\Traits\TypeServices;
use App\Traits\UserServices;
use App\Traits\VerificationCodeServices;
use App\Traits\VideoServices;
use App\Traits\VoucherServices;
use App\Traits\WarrantyServices;

trait AllServices {

    use 
    GlobalFunctions, 
    LogServices, 
    AssignDatabaseRelationship, 
    VideoHostingServices , 
    ImageHostingServices, 
    NotificationFunctions, 

    ArticleServices,
    ArticleImageServices,
    BloggerServices,
    CategoryServices, 
    CommentServices,
    CompanyServices, 
    CompanyTypeServices, 
    ChannelSaleServices,
    ChannelServices,
    GroupServices, 
    InventoryFamilyServices, 
    InventoryImageServices, 
    InventoryServices, 
    ModuleServices, 
    PatternServices, 
    PaymentServices, 
    ProductFeatureServices, 
    ProductPromotionServices, 
    ProductReviewServices, 
    RoleServices, 
    SaleItemServices, 
    SaleServices, 
    SecondCommentServices,
    ShippingServices, 
    StoreReviewServices, 
    StoreServices, 
    TicketServices, 
    TypeServices, 
    UserServices, 
    VerificationCodeServices, 
    VideoServices,
    VoucherServices, 
    WarrantyServices;

}
