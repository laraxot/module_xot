<?php return array (
  'general' => 
  array (
    'actions' => 'Action',
    'all' => 'All',
    'yes' => 'Yes',
    'no' => 'No',
    'custom' => 'Custom',
    'active' => 'Active',
    'buttons' => 
    array (
      'save' => 'Save',
      'update' => 'Update',
    ),
    'hide' => 'Hide',
    'inactive' => 'Inactive',
    'none' => 'None',
    'show' => 'Show',
    'toggle_navigation' => 'Toggle Navigation',
  ),
  'backend' => 
  array (
    'dashboard' => 
    array (
      'merchant_registration_list' => 'Restaurant Registration List',
      'merchant_payment_list' => 'Restaurant Payment Manager',
      'order_list' => 'Order List',
    ),
    'voucher' => 
    array (
      'active' => 'Active Discounts',
      'voucher_management' => 'Voucher Management',
      'voucher_create' => 'Create Voucher',
      'table' => 
      array (
        'id' => 'Id',
        'voucher_name' => 'Name',
        'voucher_type' => 'Type',
        'discount' => 'Discount',
        'expiry_date' => 'Expiration',
        'applicable_to_merchant' => 'Applicable To Restaurant',
        'voucher_status' => 'Status',
        'use_only_once' => 'Use Only Once',
        'used' => 'Used',
        'created_at' => 'Created At',
      ),
    ),
    'takeaway' => 
    array (
      'smsAlertsetting' => 
      array (
        'management' => 'Sms Alert Setting Management',
        'active' => 'Sms Alert Setting',
      ),
      'customer-list' => 
      array (
        'table' => 
        array (
          'id' => 'ID',
          'customer_name' => 'Customer Name',
          'status' => 'Status',
          'created_date' => 'Created At',
        ),
        'active' => 'Customer List',
        'management' => 'Customer Management',
      ),
      'subscriber' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'subscribe_email' => 'Subscriber Email',
          'subscribe_ip' => 'Subscriber Ip',
          'created_at' => 'Created At',
          'status' => 'Status',
          'customer_id' => 'Customer Id',
          'total_price' => 'Total Price',
        ),
        'management' => 'Subscriber Management',
        'active' => 'Active Subscriber',
      ),
      'order' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'created_at' => 'Created At',
          'status' => 'Status',
          'customer_name' => 'Customer Name',
          'total_price' => 'Total Price',
        ),
      ),
      'withdrawal' => 
      array (
        'table' => 
        array (
          'date' => 'Date',
          'merchant' => 'Restaurant Name',
          'amount' => 'Amount',
          'payment_type' => 'Payment Type',
          'from_balance' => 'From Balance',
          'payment_method' => 'Payment Method',
          'account' => 'Account',
          'status' => 'Status',
          'date_to_process' => 'Date To Process',
          'created_at' => 'Created At',
          'merchant_id' => 'Merchant Name',
          'bank_name' => 'Bank Name',
        ),
      ),
      'smsPurchaseCredit' => 
      array (
        'table' => 
        array (
          'id' => 'REf#',
          'title' => 'Package Name',
          'price' => 'Package Price',
          'credits' => 'Package Credits',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Sms Purchase Credit Management',
        'active' => 'Active Sms Purchase Credit',
      ),
      'smsBroadCast' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'send_to' => 'Send To',
          'message' => 'Message',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Sms Broad Cast Management',
        'active' => 'Active Sms Broad Cast',
        'create' => 'Create Sms Broad Cast',
        'edit' => 'Edit Sms Broad Cast',
      ),
      'commission' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'payment_method' => 'Payment Method',
          'total_price' => 'Total Price',
          'comission_amount' => 'Commission Amount',
          'commission_price' => 'Commission Price',
          'net_amount' => 'Net Amount',
          'date' => 'Date',
          'delivery_date' => 'Delivery Date',
        ),
        'management' => 'Commission Management',
        'active' => 'Active Commission',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
      ),
      'gallerySettings' => 'Gallery Setting Management',
      'active' => 'Active Gallery Setting',
      'receipt' => 
      array (
        'management' => 'Receipt Management',
        'active' => 'Active Receipt',
      ),
      'tableBookingSetting' => 
      array (
        'management' => 'Table Booking Setting Management',
        'edit' => 'Edit Table Booking Setting',
        'maximum_tables_to_book_a_day' => 'Maximum Tables To Book A Day',
        'monday' => 'Monday',
      ),
      'tableBooking' => 
      array (
        'table' => 
        array (
          'person_name' => 'Guest Name',
          'number_guests' => 'No. of Guests',
          'date_of_booking' => 'Date Of Booking',
          'person_mobile' => 'Guest Mobile',
          'person_comments' => 'Guest Comments',
          'created_at' => 'Created At',
        ),
        'management' => 'Table Booking Management',
        'active' => 'Active Table Booking',
        'create' => 'Create Table Booking',
        'edit' => 'Edit Table Booking',
        'view' => 'View Table Booking',
      ),
      'smsTransaction' => 
      array (
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'merchant_id' => 'Restaurant Name',
              'credits' => 'Credits',
              'status' => 'Status',
              'created_at' => 'Created At',
              'desc' => 'Description',
              'last_updated' => 'Last Updated',
            ),
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'merchant_id' => 'Restaurant Name',
          'sms_package_id' => 'Package Name',
          'credits' => 'Credits',
          'status' => 'Status',
          'paid_by' => 'Paid By',
          'created_at' => 'Created At',
        ),
        'management' => 'Sms Transaction Management',
        'active' => 'Active Sms Transaction',
        'create' => 'Create Sms Transaction',
        'edit' => 'Edit Sms Transaction',
        'view' => 'View Sms Transaction',
      ),
      'smsLog' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'gateway' => 'Gateway',
          'merchant_id' => 'Restaurant Name',
          'phone' => 'Phone',
          'message' => 'Message',
          'response' => 'Response',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Sms Log Management',
        'active' => 'Active Sms Log',
        'create' => 'Create Sms Log',
        'edit' => 'Edit Sms Log',
        'view' => 'View Sms Log',
      ),
      'smsPackage' => 
      array (
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'title' => 'Title',
              'desc' => 'Description',
              'price' => 'Price',
              'dis_price' => 'Discount',
              'credit_limit' => 'Credit Limit',
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'type' => 'Type',
            ),
          ),
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'title' => 'Title',
          'description' => 'Description',
          'price' => 'Price',
          'dis_price' => 'Discount',
          'credit_limit' => 'Credit Limit',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'SmsPackage Management',
        'active' => 'Active SmsPackage',
        'create' => 'Create SmsPackage',
        'edit' => 'Edit SmsPackage',
        'view' => 'View SmsPackage',
      ),
      'deliveryChargeInfo' => 
      array (
        'set' => 'Deliver Charges',
      ),
      'deliveryCharge' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'min_distance' => 'Min Distance',
          'max_distance' => 'Max Distance',
          'distance_unit' => 'Distance Unit',
          'price' => 'Price',
          'created_at' => 'Created At',
          'free_delivery_above_sub_total' => 'Free Delivery Above Sub Total',
          'are_rates_enabled' => 'Enabled Table Rates',
        ),
        'create' => 'Create Delivery Charge',
        'edit' => 'Edit Delivery Charge',
        'active' => 'Active Delivery Charge',
        'management' => 'Delivery Charge Management',
      ),
      'customerReview' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'customer' => 'Customer',
          'rating' => 'Rating',
          'comments' => 'Comments',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Customer Review Management',
        'active' => ' Customer Review',
        'view' => 'View Customer Review',
        'create' => 'Create Customer Review',
        'edit' => 'Edit Customer Review',
      ),
      'offer' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'offer_percentage' => 'Offer %',
          'order_over' => 'Order Over',
          'valid_from' => 'Valid From',
          'valid_to' => 'Valid To',
          'status' => 'Offer Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Offer Management',
        'create' => 'Create Management',
        'active' => 'Active Offer',
        'edit' => 'Edit Management',
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
        ),
      ),
      'cuisine' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'name' => 'Cuisine',
          'created_at' => 'Created At',
        ),
        'management' => 'Cuisine Management',
        'create' => 'Create Cuisine',
        'edit' => 'Edit Cuisine',
        'active' => 'Active Cuisine',
        'update' => 'Update Cuisine',
        'view' => 'view Cuisine',
      ),
      'email' => 
      array (
        'active' => 'Active Email',
        'management' => 'Email Management',
      ),
      'merchant' => 
      array (
        'table' => 
        array (
          'id' => 'Restaurant Id',
          'user_id' => 'User',
          'package_id' => 'Package Name',
          'resturant_slug' => 'Restaurant Slug',
          'resturant_image' => 'Restaurant Image',
          'resturant_name' => 'Restaurant Name',
          'resturant_phone' => 'Restaurant Phone',
          'contact_name' => 'Contact Name',
          'contact_email' => 'Contact Email',
          'country' => 'Country',
          'street_address' => 'Address',
          'city' => 'City',
          'postcode' => 'Postcode',
          'state_region' => 'State Region',
          'pickup_delivery' => 'Pickup Delivery',
          'publish_merchant' => 'Publish Restaurant',
          'status' => 'Status',
          'package_price' => 'Package Price',
          'payment_type' => 'Patment Type',
          'created_at' => 'Created At',
          'trans_id' => 'Trans Id',
          'charges_type' => 'Charges Type',
        ),
        'management' => 'Restaurant Management',
        'active' => 'Active Restaurant',
        'create' => 'Create Restaurant',
        'edit' => 'Edit Restaurant',
        'view' => 'View Restaurant',
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'user_id' => 'User Id',
              'package_id' => 'Package Id',
              'resturant_slug' => 'Resturant Slug',
              'resturant_name' => 'Resturant Name',
              'resturant_phone' => 'Resturant Phone',
              'contact_name' => 'Contact Name',
              'contact_email' => 'Contact Email',
              'country' => 'Country',
              'street_address' => 'Address',
              'city' => 'City',
              'postcode' => 'Post code',
              'state_region' => 'State Region',
              'pickup_delivery' => 'Pickup Delivery',
              'publish_merchant' => 'Publish Restaurant',
              'status' => 'Status',
              'is_featured' => 'Featured Restaurant',
              'is_enable_comission' => 'Enable Commission',
              'order_comission' => 'Order Commission',
              'comission_amount' => 'Commission Amount',
              'is_disable_cashdelivery' => 'Cash Delivery Is Disable',
              'is_disable_offlinecc' => 'Offline Payment Is Disable',
              'is_disable_payondelivery' => 'Pay on Delivery Is Disable',
              'google_lat' => 'Google Lat',
              'google_lng' => 'Google Lng',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
        ),
      ),
      'rating' => 
      array (
        'view' => 'View',
        'table' => 
        array (
          'id' => 'Id',
          'range1' => 'Range 1',
          'range2' => 'Range 2',
          'rating' => 'Rating',
          'created_at' => 'Created At',
        ),
        'edit' => 'Edit Rating',
        'management' => 'Rating Management',
        'create' => 'Create Rating',
        'active' => 'active Rating',
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'range1' => 'Range 1',
              'range2' => 'Range 2',
              'rating' => 'Rating',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
            ),
          ),
        ),
      ),
      'dish' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'name' => 'Name',
          'status' => 'Status',
          'icon' => 'Icon',
          'created_at' => 'Created At',
        ),
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'management' => 'Dish Management',
        'view' => 'View Dish',
        'create' => 'Create Dish',
        'edit' => 'Edit Dish',
        'active' => 'Active Dish',
      ),
      'cookingReference' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'name' => 'Name',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Cooking Reference Management',
        'create' => 'Create Cooking Reference',
        'edit' => 'Edit Cooking Reference',
        'active' => 'Active Cooking Reference',
      ),
      'cms' => 
      array (
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'page_name' => 'Page Name',
              'page_title' => 'Page Title',
              'page_description' => 'Page Description',
              'page_type' => 'Page Type',
              'type' => 'Type',
              'expiry' => 'Expiry',
              'usage' => 'Usage',
              'foodcanadd' => 'Foodcanadd',
              'created_at' => 'Created At',
              'last_updated' => 'Last Apdated',
              'deleted_at' => 'Deleted At',
            ),
          ),
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'page_title' => 'Page Title',
          'page_type' => 'Page Type',
          'meta_title' => 'Meta Title',
          'meta_description' => 'Meta Description',
          'meta_keywords' => 'Meta Keywords',
          'page_status' => 'Page Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Cms Management',
        'create' => 'Cms Create',
        'edit' => 'Cms Edit',
        'active' => 'Cms Active',
        'view' => 'Cms View',
      ),
      'category' => 
      array (
        'management' => 'Category Management',
        'create' => 'Create Category',
        'edit' => 'Edit Category',
        'active' => 'Active Category',
        'view' => 'View Category',
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'title' => 'Title',
              'desc' => 'Description',
              'price' => 'Price',
              'dis_price' => 'Discount Price',
              'type' => 'Type',
              'expiry' => 'expiry',
              'created_at' => 'Created At',
              'last_updated' => 'last_updated',
              'deleted_at' => 'deleted_at',
            ),
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'resturant_id' => 'Resturant Id',
          'cat_name' => 'Cat Name',
          'cat_desc' => 'Cat Desc',
          'status' => 'Status',
          'cat_image' => 'Cat Image',
          'dish_item' => 'Dish Product',
          'created_at' => 'Created At',
        ),
      ),
      'addOnItem' => 
      array (
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'name' => 'Name',
              'category' => 'Category',
              'desc' => 'Description',
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
        ),
        'table' => 
        array (
          'id' => 'id',
          'addoncat_id' => 'AddOn Category Name',
          'addon_item_name' => 'AddOn Product Name',
          'addon_desc' => 'AddOn Description',
          'status' => 'Status',
          'addon_price' => 'AddOn Price',
          'addon_item_image' => 'AddOn Product Image',
          'created_at' => 'Created At',
        ),
        'management' => 'AddOn Product Management',
        'create' => 'AddOn Product Create',
        'edit' => 'AddOn Product Edit',
        'active' => 'AddOn Product Active',
        'view' => 'AddOn Product View',
      ),
      'sponserd' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'merchant_id' => 'Restaurant Name',
          'expiry_date' => 'Expiry Date',
          'created_at' => 'Created At',
        ),
        'management' => 'Sponsored Management',
        'create' => 'Create Sponsored',
        'edit' => 'Edit',
        'active' => 'Active Sponsored',
        'view' => 'View Sponsored',
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'ing_name' => 'Name',
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
        ),
      ),
      'productSize' => 
      array (
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'size_name' => 'Qty Name',
          'status' => 'Status',
          'created_at' => 'Created At',
          'view' => 'View',
        ),
        'management' => 'Product Qty Management',
        'create' => 'Create Product',
        'edit' => 'Edit',
        'active' => 'Active',
        'size_name' => 'Qty Name',
        'status' => 'Status',
        'view' => 'View Product Size',
      ),
      'package' => 
      array (
        'edit' => 'Edit Package',
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'size_name' => 'Qty Name',
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
              'title' => 'Title',
              'desc' => 'Description',
              'price' => 'Price',
              'dis_price' => 'Discount Price',
              'type' => 'Type',
              'expiry' => 'Expiration',
              'usage' => 'Usage',
              'foodcanadd' => 'Number of Food Product Can Add',
              'limitbysell' => 'Limit Restaurant By Sell',
            ),
          ),
        ),
        'management' => 'Package Management',
        'create' => 'Create Package',
        'active' => 'Active Package',
        'view' => 'View Package',
        'table' => 
        array (
          'id' => 'Id',
          'package_title' => 'Title',
          'package_desc' => 'Description',
          'package_price' => 'Price',
          'dis_price' => 'Discount Price',
          'type' => 'Type',
          'expiry' => 'Expiry Date',
          'usage' => 'Usage',
          'foodcanadd' => 'Number of Food Product Can Add',
          'limitbysell' => 'Limit By Sell',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
      ),
      'Ingredient' => 
      array (
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'ing_name' => 'ingredient Name',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Ingredient Management',
        'create' => 'Create Ingredient',
        'ing_name' => 'Ingredient Name',
        'status' => 'Ingredient Status',
        'edit' => 'Edit Ingredient',
        'active' => 'Active Ingredient',
        'view' => 'View Ingredient',
      ),
      'dishes' => 
      array (
        'view' => 'View',
        'table' => 
        array (
          'id' => 'Id',
          'name' => 'Name',
          'status' => 'status',
          'created_at' => 'Created At',
        ),
        'management' => 'Management',
        'create' => 'Create',
        'dish_name' => 'Dish Name',
        'type' => 'Type',
      ),
      'currency' => 
      array (
        'create' => 'Create currency',
        'currency_code' => 'Currency Code',
        'currency_symbol' => 'Currency Symbol',
        'convertion_rate' => 'Convertion Rate',
        'management' => 'Currency Management',
        'edit' => 'Edit',
        'view' => 'View',
        'currency_desc' => 'Currency Description',
        'active' => 'Active',
        'table' => 
        array (
          'id' => 'Id',
          'currency_code' => 'Currency Code',
          'currency_symbol' => 'Currency Symbol',
          'convertion_rate' => 'Convertion Rate',
          'created_at' => 'Created At',
          'view' => 'View',
        ),
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'code' => 'Code',
              'symbol' => 'Symbol',
              'rate' => 'Rate',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
        ),
      ),
      'voucher' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'voucher_name' => 'Voucher Name',
          'voucher_type' => 'Type',
          'discount' => 'Discount',
          'expiry_date' => 'Expiration',
          'applicable_to_merchant' => 'Applicable To Restaurant',
          'voucher_status' => 'Voucher Status',
          'use_only_once' => 'Use Only Once',
          'used' => 'Used',
        ),
      ),
      'item' => 
      array (
        'table' => 
        array (
          'id' => 'Id',
          'takeaway_id' => 'Restaurant Name',
          'item_name' => 'Food Name',
          'featured_image' => 'Featured Image',
          'item_desc' => 'Food Description',
          'item_price' => 'Food Price',
          'cooking_reference' => 'Cooking Id',
          'dish_ref' => 'Dish Reference',
          'points_earned' => 'Points Earned',
          'is_taxable' => 'Is Taxable',
          'is_disable_pointearn' => 'Is Disable Pointearn',
          'ingredient_ref' => 'Ingredient Name',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'active' => 'Active Product',
        'management' => 'Product Management',
        'edit' => 'Edit Product',
        'create' => 'Create Product',
        'AddOns' => 'AddOns',
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'takeaway_id' => 'User Id',
              'name' => 'Food Name',
              'item_desc' => 'Food Description',
              'dis_price' => 'Discount Price',
              'featured_image' => 'Featured Image',
              'item_price' => 'Food Price',
              'cooking_reference' => 'Cooking Id',
              'dish_ref' => 'Dish Id',
              'points_earned' => 'Points Earned',
              'is_taxable' => 'Is Taxable',
              'is_disable_pointearn' => 'Is Dishable Pointearn',
              'ingredient_ref' => 'Ingredient Id',
              'created_at' => 'Created At',
              'last_updated' => 'Last Update',
              'deleted_at' => 'Deleted At',
            ),
          ),
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
      ),
      'ingredient' => 
      array (
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'ing_name' => 'Ingredient Name',
              'status' => 'Ingredient Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
        'table' => 
        array (
          'id' => 'Id',
          'ing_name' => 'Ingredient Name',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'Ingredient Management',
        'create' => 'Create Ingredient',
        'ing_name' => 'Ingredient Name',
        'status' => 'Ingredient Status',
        'edit' => 'Edit Ingredient',
        'active' => 'Active',
        'view' => 'View',
      ),
      'orderStatus' => 
      array (
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History Overview',
          ),
        ),
        'create' => 'Create Order Status',
        'view' => 'View Order Status',
        'edit' => 'Edit Order Status',
        'updated' => 'Updated Order Status',
        'table' => 
        array (
          'management' => 'Management',
          'id' => 'ID',
          'addon_cat_item_name' => 'AddOn Category Name',
          'addon_cat_desc' => 'AddOn Category Description',
          'status' => 'Status',
          'created_at' => 'Created At',
          'order_status' => 'Order Status',
          'updated_at' => 'Updated On',
          'deleted_at' => 'Deleted On',
          'edit' => 'Edit',
          'create' => 'Create',
          'view' => 'View',
        ),
        'active' => 'Active Order Status',
        'management' => 'Order Status Management',
      ),
      'addOnCategory' => 
      array (
        'table' => 
        array (
          'id' => 'ID',
          'addon_cat_item_name' => 'AddOn Category Name',
          'addon_cat_desc' => 'AddOn Category Description',
          'status' => 'Status',
          'created_at' => 'Created At',
        ),
        'management' => 'AddOn Category Managment',
        'create' => 'Create AddOn Category ',
        'edit' => 'Edit AddOn Category',
        'active' => 'Active AddOn Category',
        'view' => 'View',
        'tabs' => 
        array (
          'content' => 
          array (
            'overview' => 
            array (
              'name' => 'Name',
              'desc' => 'Description',
              'status' => 'Status',
              'created_at' => 'Created At',
              'last_updated' => 'Last Updated',
              'deleted_at' => 'Deleted At',
            ),
          ),
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
        ),
      ),
    ),
    'access' => 
    array (
      'alertSetting' => 
      array (
        'management' => 'Alert Setting Management',
        'active' => 'Active Alert Setting',
        'create' => 'Create Alert Setting',
        'edit' => 'Edit Alert Setting',
        'view' => 'View Alert Setting',
      ),
      'roles' => 
      array (
        'create' => 'Create Role',
        'edit' => 'Edit Role',
        'management' => 'Role Management',
        'table' => 
        array (
          'number_of_users' => 'Number of Users',
          'permissions' => 'Permissions',
          'role' => 'Role',
          'sort' => 'Sort',
          'total' => 'role total|roles total',
        ),
      ),
      'users' => 
      array (
        'active' => 'Active Users',
        'all_permissions' => 'All Permissions',
        'change_password' => 'Change Password',
        'change_password_for' => 'Change Password for :user',
        'create' => 'Create User',
        'deactivated' => 'Deactivated Users',
        'deleted' => 'Deleted Users',
        'edit' => 'Edit User',
        'management' => 'User Management',
        'no_permissions' => 'No Permissions',
        'no_roles' => 'No Roles to set.',
        'permissions' => 'Permissions',
        'cuisine_list' => 'Cuisine List',
        'dishes' => 'Dishes',
        'table' => 
        array (
          'confirmed' => 'Confirmed',
          'created' => 'Created',
          'email' => 'E-mail',
          'id' => 'ID',
          'last_updated' => 'Last Updated',
          'name' => 'Name',
          'no_deactivated' => 'No Deactivated Users',
          'no_deleted' => 'No Deleted Users',
          'roles' => 'Roles',
          'total' => 'user total|users total',
        ),
        'tabs' => 
        array (
          'titles' => 
          array (
            'overview' => 'Overview',
            'history' => 'History',
          ),
          'content' => 
          array (
            'overview' => 
            array (
              'ing_name' => 'Ingredient',
              'avatar' => 'Avatar',
              'confirmed' => 'Confirmed',
              'created_at' => 'Created At',
              'deleted_at' => 'Deleted At',
              'email' => 'E-mail',
              'last_updated' => 'Last Updated',
              'name' => 'Name',
              'status' => 'Status',
              'desc' => 'Description',
              'code' => 'Code',
              'symbol' => 'symbol',
              'rate' => 'Rate',
              'icon' => 'Icon',
            ),
          ),
        ),
        'view' => 'View User',
      ),
    ),
    'settings' => 
    array (
      'settings' => 'Settings',
    ),
  ),
  'frontend' => 
  array (
    'auth' => 
    array (
      'login_box_title' => 'Log in to your account',
      'login_button' => 'Login',
      'login_with' => 'Login with :social_media',
      'register_box_title' => 'Register',
      'register_button' => 'Register',
      'remember_me' => 'Remember Me',
    ),
    'passwords' => 
    array (
      'forgot_password' => 'Forgot Your Password?',
      'reset_password_box_title' => 'Forgot Password',
      'reset_password_button' => 'Reset Password',
      'send_password_reset_link_button' => 'Send Password Reset Link',
    ),
    'macros' => 
    array (
      'country' => 
      array (
        'alpha' => 'Country Alpha Codes',
        'alpha2' => 'Country Alpha 2 Codes',
        'alpha3' => 'Country Alpha 3 Codes',
        'numeric' => 'Country Numeric Codes',
      ),
      'macro_examples' => 'Macro Examples',
      'state' => 
      array (
        'mexico' => 'Mexico State List',
        'us' => 
        array (
          'us' => 'US States',
          'outlying' => 'US Outlying Territories',
          'armed' => 'US Armed Forces',
        ),
      ),
      'territories' => 
      array (
        'canada' => 'Canada Province & Territories List',
      ),
      'timezone' => 'Timezone',
    ),
    'user' => 
    array (
      'passwords' => 
      array (
        'change' => 'Change Password',
      ),
      'profile' => 
      array (
        'avatar' => 'Avatar',
        'created_at' => 'Created At',
        'edit_information' => 'Edit Information',
        'email' => 'E-mail',
        'last_updated' => 'Last Updated',
        'name' => 'Name',
        'update_information' => 'Update Information',
        'phone' => 'Phone Number',
        'contact_name' => 'Contact Name',
        'country' => 'Country',
        'street_address' => 'Street Address',
        'city' => 'City',
        'detail' => 'Detail',
        'postcode' => 'Postcode',
        'state_region' => 'State Region',
        'google_lat' => 'Google Latitude',
        'google_lng' => 'Google Longitude',
      ),
    ),
  ),
);