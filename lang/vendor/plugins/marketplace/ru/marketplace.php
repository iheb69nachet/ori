<?php

return [
    'name' => 'Торговая площадка',
    'email' => [
        'store_new_order_title' => 'Уведомление о новом заказе',
        'store_new_order_description' => 'Отправлять электронное письмо владельцу магазина при оформлении заказа',
        'verify_vendor_title' => 'Проверить поставщика',
        'verify_vendor_description' => 'Отправка сообщения на электронную почту администратору при регистрации продавца',
        'pending_product_approval_title' => 'Ожидается одобрение продукта',
        'pending_product_approval_description' => 'Отправлять электронное письмо администратору, когда продавец размещает свои товары',
        'vendor_account_approved_title' => 'Счет поставщика одобрен',
        'vendor_account_approved_description' => 'Отправить электронное письмо поставщику, когда его счет будет одобрен',
        'product_approved_title' => 'Продукт одобрен',
        'product_approved_description' => 'Отправить электронное письмо поставщику, когда его продукт одобрен',
        'withdrawal_approved_title' => 'Одобрен отзыв',
        'withdrawal_approved_description' => 'Отправить электронное письмо продавцу, когда его запрос на снятие средств одобрен',
    ],
    'current_balance' => 'Текущий баланс',
    'settings' => [
        'name' => 'Настройки',
        'title' => 'Настройки для торговой площадки',
        'description' => 'Комиссия за установку',
        'fee_per_order' => 'Комиссия за заказ (%), предложите: 2 или 3',
        'fee_withdrawal' => 'Снятие комиссии (Фиксированная сумма)',
        'check_valid_signature' => 'Проверка действительной подписи в доходах продавца',
        'verify_vendor' => 'Верификация поставщика (поставщик может размещать объявления о своей продукции только после прохождения верификации)',
        'enable_product_approval' => 'Обеспечение возможности одобрения продукции',
        'hide_store_phone_number' => 'Скрыть номер телефона магазина?',
        'add_new' => 'Добавить новое',
        'allow_vendor_manage_shipping' => 'Разрешить поставщику управлять доставкой?',
        'categories' => 'Категории',
        'commission_fee' => 'Комиссия (%)',
        'commission_fee_by_category' => 'Комиссия по категориям (%)',
        'commission_fee_each_category_fee_name' => 'Комиссионное вознаграждение установки комиссии :key',
        'commission_fee_each_category_name' => 'Категории настройки комиссии :key',
        'default_commission_fee' => 'Комиссия по умолчанию (%), рекомендуем: 2 или 3',
        'enable_commission_fee_for_each_category' => 'Включить комиссию для каждой категории?',
        'select_categories' => 'Выберите категории..',
    ],
    'theme_options' => [
        'name' => 'Торговая площадка',
        'description' => 'Параметры темы для торговой площадки',
        'logo_vendor_dashboard' => 'Логотип на приборной панели поставщика (по умолчанию - основной логотип)',
    ],
    'store_name' => 'Название магазина',
    'store_email' => 'Электронная почта магазина',
    'store_phone' => 'Телефон магазина',
    'product_name' => 'Наименование товара',
    'product_url' => 'URL продукта',
    'withdrawal_amount' => 'Сумма вывода',
    'helpers' => [
        'customer_status' => 'Если вы измените статус, отличный от ":status" магазин этого продавца также изменится на ":store"',
        'store_status' => 'Если вы измените статус, отличный от ":status" счет этого магазина также изменится на ":customer"',
    ],
    'tables' => [
        'earnings' => 'Заработок',
        'products_count' => 'Количество продуктов',
    ],
];
